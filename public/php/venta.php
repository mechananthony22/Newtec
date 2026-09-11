<?php
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/core/App.php';
require_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/models/VentaModel.php';
require_once __DIR__ . '/../../app/models/DetalleCarrito.php';
require_once __DIR__ . '/../../app/models/CarritoModel.php';
require_once __DIR__ . '/../../app/models/ProductoModel.php';

function sendResponse($status, $message, $data = [])
{
    http_response_code($status === 'error' ? 400 : 200);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

$accion = $_POST['accion'] ?? '';

if ($accion == 'create') {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    try {
        $db = App::getInstance()->getDatabase();
        $ventaModel = new VentaModel();
        $detalleCarrito = new DetalleCarrito();
        $carritoModel = new CarritoModel();
        $productoModel = new ProductoModel();

        // Validación de datos
        $usuario   = json_decode($_POST['usuario']   ?? 'null', true);
        $entrega   = json_decode($_POST['entrega']   ?? 'null', true);
        $metodoPago= json_decode($_POST['metodoPago']?? 'null', true);

        // Debug: mostrar qué se recibió
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("[VENTA] usuario=" . json_encode($usuario));
            error_log("[VENTA] entrega=" . json_encode($entrega));
            error_log("[VENTA] metodoPago=" . json_encode($metodoPago));
            error_log("[VENTA] token=" . ($_POST['token'] ?? 'NULL'));
        }

        if (!$usuario) {
            sendResponse('error', 'Datos de usuario inválidos o vacíos');
        }
        if (!$entrega) {
            sendResponse('error', 'Datos de entrega inválidos o vacíos');
        }
        if (!$metodoPago) {
            // Si no viene metodoPago, lo creamos por defecto (venta por tarjeta)
            $metodoPago = ['seleccionado' => 1, 'tipo' => 'tarjeta'];
        }

        // Validar tipo de entrega
        if (!isset($entrega['tipo']) || !in_array($entrega['tipo'], ['domicilio', 'tienda'])) {
            sendResponse('error', 'Tipo de entrega no válido: ' . ($entrega['tipo'] ?? 'no definido'));
        }

        // Método de pago default si no viene
        $metodoPago['seleccionado'] = $metodoPago['seleccionado'] ?? 1;

        // Obtener total del carrito
        $total = $detalleCarrito->getTotal((int)$usuario['id']);
        if (!$total || $total <= 0) {
            sendResponse('error', 'El carrito está vacío o no se pudo obtener el total');
        }

        // 1. PROCESAR PAGO CON MERCADO PAGO
        $accessToken = "TEST-2111198746304150-033019-5a041b9a6bacc7bc553b34b27baf215f-3303201883";
        $payment_data = [
            'token'              => $_POST['token'] ?? null,
            'issuer_id'          => $_POST['issuer_id'] ?? null,
            'payment_method_id'  => $_POST['payment_method_id'] ?? null,
            'transaction_amount' => (float)($_POST['transaction_amount'] ?? 0),
            'installments'       => (int)($_POST['installments'] ?? 1),
            'payer'              => json_decode($_POST['payer'] ?? '{}', true)
        ];

        // MODO PRUEBA LOCAL: En localhost con HTTP, MP no puede crear tokens (requiere HTTPS).
        // Cuando DEBUG_MODE=true y el token es 'PRUEBA_LOCAL', simulamos el pago aprobado
        // para poder probar el flujo completo de la orden. NUNCA usar en producción.
        $esModoPruebaLocal = defined('DEBUG_MODE') && DEBUG_MODE && $payment_data['token'] === 'PRUEBA_LOCAL';

        if ($esModoPruebaLocal) {
            // Simular pago aprobado localmente
            $idPagoMP = 'LOCAL_TEST_' . date('YmdHis') . '_' . rand(1000, 9999);
        } else {
            // Flujo real de Mercado Pago
            if (!$payment_data['token']) {
                sendResponse('error', 'Token de pago no recibido. Completa los datos de tarjeta correctamente.');
            }

            $ch = curl_init("https://api.mercadopago.com/v1/payments");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $accessToken",
                "Content-Type: application/json",
                "X-Idempotency-Key: " . uniqid()
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment_data));
            // SSL bypass para XAMPP local - NO usar en producción
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response  = curl_exec($ch);
            $curlError = curl_error($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($curlError) {
                sendResponse('error', 'Error de conexión con Mercado Pago: ' . $curlError);
            }

            $result = json_decode($response, true);

            if ($httpCode !== 200 && $httpCode !== 201) {
                $msg = $result['message'] ?? 'Error al procesar el pago con Mercado Pago';
                sendResponse('error', $msg, $result);
            }

            if ($result['status'] !== 'approved') {
                $status_detail = $result['status_detail'] ?? 'desconocido';
                sendResponse('error', "Pago no aprobado. Estado: {$result['status']} ({$status_detail})");
            }

            $idPagoMP = $result['id'];
        }

        // 2. INICIAR TRANSACCIÓN LOCAL
        $db->beginTransaction();

        try {

            // 1. Registrar venta principal (sin dirección al principio)
            $idVenta = $ventaModel->insertarVenta(
                $usuario['id'],
                null, // id_direccion se actualizará después
                $total,
                $metodoPago['seleccionado'],
                $entrega['tipo'],
                $entrega['tipo'] === 'tienda' ? $entrega['sucursal']['id'] : null
            );

            if (!$idVenta) {
                throw new Exception('No se pudo crear la venta (ID no devuelto)');
            }

            // 2. Registrar dirección de entrega (si aplica) y asociarla a la venta
            if ($entrega['tipo'] === 'domicilio') {
                $idDireccion = $ventaModel->guardarDireccion(
                    $usuario['id'], // <-- Este debe ser el id_us de la tabla usuario
                    $entrega['domicilio']['departamento'] ?? '',
                    $entrega['domicilio']['provincia'] ?? '',
                    $entrega['domicilio']['distrito'] ?? '',
                    $entrega['domicilio']['calle'] ?? '',
                    $entrega['domicilio']['numero'] ?? '',
                    $entrega['domicilio']['piso'] ?? '',
                    $entrega['domicilio']['referencia'] ?? ''
                );
                
                // 3. Actualizar la venta con el ID de la dirección
                if ($idDireccion) {
                    $ventaModel->updateDireccionVenta($idVenta, $idDireccion);
                }
            }

            // Registrar detalles de la venta
            $detalles_Carrito = $detalleCarrito->getItems($usuario['id']);

            // comprobar si hay carrito
            if (!$detalles_Carrito) {
                throw new Exception('No se ha creado el carrito');
            }

            // insertar y comprobar si hay detalles
            if (!$ventaModel->insertarDetalle($idVenta, $detalles_Carrito)) {
                throw new Exception('Error al registrar los detalles de la venta');
            }

            // actualizar stock productos del carrito
            foreach ($detalles_Carrito as $detalle) {
                $productoModel->actualizarStock($detalle['id_producto'], $detalle['stock_producto'] - $detalle['cantidad']);
            }

            // Registrar método de pago con el ID de Mercado Pago
            $ventaModel->guardarPago(
                $idVenta,
                null, // No guardamos tarjeta por seguridad
                $metodoPago['celular']['numero'] ?? null,
                $idPagoMP // Guardamos el ID de transacción de Mercado Pago
            );

            // Limpiar carrito
            $carritoModel->delete($usuario['id']);

            // Confirmar transacción
            $db->commit();

            // Respuesta exitosa
            sendResponse('success', 'Venta registrada exitosamente', [
                'idVenta' => $idVenta,
                'total' => $total
            ]);
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $db->rollBack();
            throw $e;
        }
    } catch (Exception $e) {
        sendResponse('error', $e->getMessage());
    }
}