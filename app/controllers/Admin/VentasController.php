<?php

namespace Admin;

use AdminVentasModel;

class VentasController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminVentasModel();
    }

    //  Listado de ventas con filtro de búsqueda
    public function index()
    {
        $buscar = $_GET['buscar'] ?? ''; // Captura del filtro si existe
        $ventas = $this->model->getAll($buscar); // Pasa el filtro al modelo
        require_once __DIR__ . '/../../views/admin/ventas/index.php';
    }

    //  Detalle de una venta
    public function detalle($id)
    {
        // Procesar cambio de estado
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['venta_id'], $_POST['estado'])) {
            $ventaId = $_POST['venta_id'];
            $nuevoEstado = $_POST['estado'];
            $this->model->actualizarEstado($ventaId, $nuevoEstado);
            // Si es AJAX, responde JSON
            if (
                isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) {
                header('Content-Type: application/perunet/json');
                echo json_encode(['success' => true]);
                exit;
            }
            // Si no es AJAX, redirige (fallback)
            header('Location: /perunet/admin/perunet/ventas/perunet/detalle/perunet/' . $ventaId);
            exit;
        }

        // Obtener datos generales y detalle de la venta
        $venta = $this->model->getById($id);
        $detalle = $this->model->getDetalle($id);
        require_once __DIR__ . '/../../views/admin/ventas/detalle.php';
    }

    // Cambiar el estado de una venta por AJAX
    public function cambiarEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['venta_id'], $_POST['estado'])) {
            $ventaId = $_POST['venta_id'];
            $nuevoEstado = $_POST['estado'];
            $this->model->actualizarEstado($ventaId, $nuevoEstado);
            header('Content-Type: application/perunet/json');
            echo json_encode(['success' => true]);
            exit;
        }
        // Si no es POST válido, responde error
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Petición inválida']);
        exit;
    }


    public function reportePorFecha()
    {
        $tipo = $_GET['tipo'] ?? 'diario';
        $anio = $_GET['anio'] ?? date('Y');
        $mes = $_GET['mes'] ?? date('Y-m');
        $dia = $_GET['dia'] ?? date('Y-m-d');

        $ventas = $this->model->getVentasPorFecha($tipo, $anio, $mes, $dia);
        $totalVentas = array_sum(array_column($ventas, 'total'));

        require_once __DIR__ . '/../../views/admin/ventas/reportes.php';
    }



    public function resumenEstadistico()
    {
        $ventaModel = new AdminVentasModel();

        $tipo = $_GET['tipo'] ?? 'diario';
        $fecha = $_GET['fecha'] ?? date($tipo === 'anual' ? 'Y' : ($tipo === 'mensual' ? 'Y-m' : 'Y-m-d'));

        $productoMasVendido = $ventaModel->productoMasVendido($tipo, $fecha);
        $diaMasVentas = $ventaModel->diaConMasVentas($tipo, $fecha);
        $datos = $ventaModel->datosGrafico($tipo, $fecha);

        // Formatear para Chart.js
        $labels = array_column($datos, 'etiqueta');
        $valores = array_map(fn($d) => (float) $d['total'], $datos);

        require_once __DIR__ . '/../../views/admin/ventas/resumen.php';
    }
}
