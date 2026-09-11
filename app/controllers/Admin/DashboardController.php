<?php

require_once __DIR__ . '/../../models/ProductoModel.php';
require_once __DIR__ . '/../../models/AdminVentasModel.php';
require_once __DIR__ . '/../../models/UsuarioModel.php';

class DashboardController
{
    public function index()
    {
        try {
            // Modelos
            $productoModel = new ProductoModel();
            $ventasModel = new AdminVentasModel();
            $usuarioModel = new UsuarioModel();

            // Total de productos
            $productos = $productoModel->getAll();
            $totalProductos = count($productos);

            // Total de ventas y últimas ventas
            $ventas = $ventasModel->getAll();
            $totalVentas = count($ventas);
            $ultimasVentas = array_slice($ventas, 0, 5);

            // Total de usuarios
            $usuarios = $usuarioModel->getAll();
            $totalUsuarios = count($usuarios);

            // Ingresos totales (suma de todas las ventas)
            $ingresosTotales = 0;
            foreach ($ventas as $venta) {
                $ingresosTotales += floatval($venta['total'] ?? 0);
            }

            // Productos con stock bajo (menos de 10 unidades)
            $productosStockBajo = array_filter($productos, function($producto) {
                return ($producto['stock'] ?? 0) < 10;
            });

            // Productos más vendidos (top 5)
            $sql = "SELECT p.nombre, m.nombre AS marca, SUM(dv.cantidad) AS cantidad_vendida
                    FROM detalle_venta dv
                    JOIN producto p ON dv.id_producto = p.id_pro
                    LEFT JOIN marca m ON p.id_marca = m.id_mar
                    GROUP BY p.id_pro
                    ORDER BY cantidad_vendida DESC
                    LIMIT 5";
            $stmt = $ventasModel->getDb()->prepare($sql);
            $stmt->execute();
            $productosMasVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Gráfico de ventas del mes actual
            $mesActual = date('Y-m');
            $ventasPorDia = $ventasModel->datosGrafico('mensual', $mesActual);
            // Formatear para Chart.js
            $ventasPorDia = array_map(function($d) use ($mesActual) {
                $dia = str_pad($d['etiqueta'], 2, '0', STR_PAD_LEFT);
                return [
                    'fecha' => $mesActual . '-' . $dia,
                    'total' => floatval($d['total'])
                ];
            }, $ventasPorDia);

            // Pasar datos a la vista
            $data = [
                'totalProductos' => $totalProductos,
                'totalVentas' => $totalVentas,
                'totalUsuarios' => $totalUsuarios,
                'ingresosTotales' => $ingresosTotales,
                'ultimasVentas' => $ultimasVentas,
                'productosStockBajo' => $productosStockBajo,
                'productosMasVendidos' => $productosMasVendidos,
                'ventasPorDia' => $ventasPorDia
            ];

            extract($data);
            require_once __DIR__ . '/../../views/admin/dashboard.php';
        } catch (Exception $e) {
            error_log("Error en DashboardController: " . $e->getMessage());
            echo "<pre>Error en DashboardController: " . $e->getMessage() . "</pre>";
            $totalProductos = 0;
            $totalVentas = 0;
            $totalUsuarios = 0;
            $ingresosTotales = 0;
            $ultimasVentas = [];
            $productosStockBajo = [];
            $productosMasVendidos = [];
            $ventasPorDia = [];
            require_once __DIR__ . '/../../views/admin/dashboard.php';
        }
    }
}
