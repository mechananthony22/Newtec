<?php

include_once __DIR__ . '/../../app/config/config.php';
include_once __DIR__ . '/../../app/core/Autoloader.php';
include_once __DIR__ . '/../../app/core/App.php';
Autoloader::getInstance();
App::getInstance();

include_once __DIR__ . '/../../app/core/Model.php';
include_once __DIR__ . '/../../app/models/CarritoModel.php';
include_once __DIR__ . '/../../app/models/DetalleCarrito.php';
include_once __DIR__ . '/../../app/models/ProductoModel.php';
include_once __DIR__ . '/../../app/models/UsuarioModel.php';

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


// accion para crear carrito y agregar producto
try {
    if ($accion === 'create') {

        header('Content-Type: application/json');

        try {
            // $db = new Database();
            $db = App::getInstance()->getDatabase();
            $carritoModel = new CarritoModel($db);
            $detalleCarritoModel = new DetalleCarrito($db);
            $productoModel = new ProductoModel($db);



            // Validación de datos
            $id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : null;
            $id_producto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : null;
            $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
            $precio_unitario = isset($_POST['precio_unitario']) ? (float)$_POST['precio_unitario'] : null;

            if (!$id_usuario || !$id_producto || $precio_unitario === null) {
                sendResponse('error', 'Datos incompletos o inválidos', [
                    'id_usuario' => $id_usuario,
                    'id_producto' => $id_producto,
                    'precio_unitario' => $precio_unitario
                ]);
            }

            // Iniciar transacción
            $db->beginTransaction();

            try {
                // Buscar carrito activo
                $carrito = $carritoModel->getByCartValidationCliente($id_usuario);

                if ($carrito === null) {
                    throw new Exception('El usuario no existe');
                }

                $producto = $productoModel->getById($id_producto);
                if ($producto === null) {
                    throw new Exception('El producto no existe');
                } else {
                    if ($producto['stock'] < 1) {
                        throw new Exception('No hay stock suficiente del producto');
                    }
                }

                if ($carrito) {
                    try {
                        // Carrito existe, agregar producto
                        $detalle_id = $detalleCarritoModel->createDetalle(
                            $carrito['id_carrito'],
                            $id_producto,
                            $cantidad,
                            $precio_unitario
                        );

                        $db->commit();

                        sendResponse('success', 'Producto agregado al carrito exitosamente', [
                            'carrito_id' => $carrito['id_carrito'],
                            'detalle_id' => $detalle_id,
                            'es_nuevo' => false
                        ]);
                    } catch (Exception $e) {
                        if ($db->inTransaction()) {
                            $db->rollBack();
                        }
                        throw new Exception('Error al agregar producto al carrito: ' . $e->getMessage());
                    }
                } else {
                    try {
                        // Crear nuevo carrito
                        try {
                            $id_carrito = $carritoModel->create($id_usuario);

                            if (!$id_carrito) {
                                throw new Exception('No se pudo crear el nuevo carrito (ID no devuelto)');
                            }
                        } catch (Exception $e) {
                            throw new Exception('No se pudo crear el carrito: ' . $e->getMessage());
                        }

                        // Agregar producto al nuevo carrito
                        $detalle_id = $detalleCarritoModel->createDetalle(
                            $id_carrito,
                            $id_producto,
                            $cantidad,
                            $precio_unitario
                        );

                        $db->commit();

                        sendResponse('created', 'Nuevo carrito creado y producto agregado exitosamente', [
                            'carrito_id' => $id_carrito,
                            'detalle_id' => $detalle_id,
                            'es_nuevo' => true
                        ]);
                    } catch (Exception $e) {
                        if ($db->inTransaction()) {
                            $db->rollBack();
                        }
                        throw new Exception('Error al crear nuevo carrito: ' . $e->getMessage());
                    }
                }
            } catch (Exception $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                throw $e;
            }
        } catch (Exception $e) {
            sendResponse('error', 'Error en el servidor: ' . $e->getMessage());
        }
    }

    // accion para contar productos
    if ($accion === 'count') {
        $id_usuario = $_POST['id_usuario'];
        $carrito = (new DetalleCarrito())->getProductsCount($id_usuario);
        echo $carrito;
    }
    if ($accion === 'update_product') {
        $id_usuario = $_POST['id_usuario'];
        $id_producto = $_POST['id_producto'];
        $carrito = (new DetalleCarrito())->getProductsCount($id_usuario);
        $producto = (new ProductoModel())->getById($id_producto, $id_usuario);
        echo json_encode([
            'carrito' => $carrito,
            'producto' => $producto
        ]);
    }

    // accion para eliminar producto del carrito
    if ($accion === 'delete_product') {
        $id = $_POST['id_producto'];
        $carrito = (new DetalleCarrito())->delete($id);
        echo $carrito;
    }

    // accion para vaciar carrito
    if ($accion === 'empty_cart') {
        $id_usuario = $_POST['id_usuario'];
        $carrito = (new CarritoModel())->delete($id_usuario);
    }

    // accion para obtener productos del carrito
    if ($accion === 'get_products') {
        $id_usuario = $_POST['id_usuario'];
        $carrito = (new DetalleCarrito())->getItems($id_usuario);

        if (empty($carrito)) {
            echo "<span>No hay productos en el carrito</span>";
        } else {
            foreach ($carrito as $item) {
                echo "<div class='flex items-center gap-4 bg-gray-100 rounded-lg p-4 shadow'>";
                echo "<img src='/perunet/public/img/" . htmlspecialchars($item['imagen_producto']) . "' alt='" . htmlspecialchars($item['nombre_producto']) . "' class='w-20 h-20 object-contain rounded border border-gray-300 bg-white'>";
                echo "<div class='flex-1'>";
                echo "<p class='font-semibold text-black text-lg'>" . htmlspecialchars($item['nombre_producto']) . "</p>";
                echo "<p class='text-gray-700'>Precio: <span class='text-red-700 font-bold'>$" . htmlspecialchars($item['precio_producto']) . "</span></p>";
                echo "<p class='text-gray-700'>Cantidad: <span class='font-bold text-black'>" . htmlspecialchars($item['cantidad']) . "</span></p>";
                echo "</div>";
                echo "<button class='ml-2 px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold shadow transition-colors btn-eliminar' onclick='eliminarProducto(" . (int)$item['id_detalle'] . ")'>Eliminar</button>";
                echo "</div>";
            }
        }
    }
} catch (Exception $e) {
    sendResponse('error', 'Error en el servidor: ' . $e->getMessage());
}

if (isset($_GET['accion']) && $_GET['accion'] === 'countTipos') {
    session_start();
    if (!isset($_SESSION['usuario']['id_us'])) {
        echo json_encode(["count" => 0]);
        exit;
    }
    $id_usuario = $_SESSION['usuario']['id_us'];
    $items = (new DetalleCarrito())->getItems($id_usuario);
    $tipos = [];
    if ($items && is_array($items)) {
        foreach ($items as $item) {
            $tipos[$item['id_producto']] = true;
        }
    }
    echo json_encode(["count" => count($tipos)]);
    exit;
}
