<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Dashboard - Panel de Administración";
include __DIR__ . '/../../components/adminHead.php';
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../components/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../components/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64">
        <!-- Header del Dashboard -->
        <!-- <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-700 mb-2"></h1>
            <p class="text-gray-600">aaaaa</p>
        </div> -->

        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total de Productos -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Productos</p>
                        <p class="text-3xl font-bold text-blue-600"><?= $totalProductos ?? 0 ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600 font-medium">+12%</span>
                    <span class="text-sm text-gray-500">vs mes anterior</span>
                </div>
            </div>

            <!-- Total de Ventas -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Ventas</p>
                        <p class="text-3xl font-bold text-green-600"><?= $totalVentas ?? 0 ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600 font-medium">+8%</span>
                    <span class="text-sm text-gray-500">vs mes anterior</span>
                </div>
            </div>

            <!-- Total de Usuarios -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Usuarios</p>
                        <p class="text-3xl font-bold text-purple-600"><?= $totalUsuarios ?? 0 ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600 font-medium">+5%</span>
                    <span class="text-sm text-gray-500">vs mes anterior</span>
                </div>
            </div>

            <!-- Ingresos Totales -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Ingresos Totales</p>
                        <p class="text-3xl font-bold text-orange-600">S/ <?= number_format($ingresosTotales ?? 0, 2) ?></p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600 font-medium">+15%</span>
                    <span class="text-sm text-gray-500">vs mes anterior</span>
                </div>
            </div>
        </div>

        <!-- Gráficos y Tablas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Gráfico de Ventas -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Ventas del Mes</h3>
                <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                    <canvas id="grafico-ventas-mes" width="400" height="220"></canvas>
                </div>
                <script>
                const ventasPorDia = <?= json_encode($ventasPorDia ?? [
                    ['fecha' => '2025-07-01', 'total' => 120],
                    ['fecha' => '2025-07-02', 'total' => 80],
                    ['fecha' => '2025-07-03', 'total' => 150],
                    ['fecha' => '2025-07-04', 'total' => 60],
                    ['fecha' => '2025-07-05', 'total' => 200],
                ]) ?>;
                const labels = ventasPorDia.map(v => v.fecha.slice(8,10) + '/' + v.fecha.slice(5,7));
                const data = ventasPorDia.map(v => v.total);
                const ctx = document.getElementById('grafico-ventas-mes').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Ventas S/',
                            data: data,
                            backgroundColor: 'rgba(239,68,68,0.6)',
                            borderColor: 'rgba(239,68,68,1)',
                            borderWidth: 1,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
                </script>
            </div>

            <!-- Productos Más Vendidos -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Productos Más Vendidos</h3>
                <div class="space-y-3">
                    <?php if (!empty($productosMasVendidos)): ?>
                        <?php foreach ($productosMasVendidos as $index => $producto): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-500">#<?= $index + 1 ?></span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700"><?= htmlspecialchars($producto['nombre']) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($producto['marca'] ?? '') ?></p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-blue-600"><?= $producto['cantidad_vendida'] ?? 0 ?> vendidos</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p class="text-gray-500">No hay datos disponibles</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Últimas Actividades -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Últimas Ventas Mejorado -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Últimas Ventas</h3>
                <div class="space-y-3">
                    <?php if (!empty($ultimasVentas)): ?>
                        <?php foreach (array_slice($ultimasVentas, 0, 5) as $venta): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-bold text-gray-700"><?= htmlspecialchars($venta['cliente'] ?? 'Cliente') ?></p>
                                    <p class="text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($venta['fecha'] ?? 'now')) ?></p>
                                </div>
                                <div class="text-right">
                                    <?php if (($venta['total'] ?? 0) > 0): ?>
                                        <p class="text-sm font-bold text-green-600">S/ <?= number_format($venta['total'], 2) ?></p>
                                    <?php else: ?>
                                        <span class="inline-block px-2 py-1 text-xs rounded bg-gray-200 text-gray-500">Sin monto</span>
                                    <?php endif; ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        <?= ($venta['estado'] ?? '') === 'entregado' ? 'bg-green-100 text-green-800' :
                                            (($venta['estado'] ?? '') === 'cancelado' ? 'bg-red-100 text-red-800' :
                                            (($venta['estado'] ?? '') === 'enviado' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) ?>">
                                        <?= ucfirst($venta['estado'] ?? 'pendiente') ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-400">No hay ventas recientes</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Productos con Stock Bajo Mejorado -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Productos con Stock Bajo</h3>
                <div class="space-y-3">
                    <?php if (!empty($productosStockBajo)): ?>
                        <?php foreach ($productosStockBajo as $producto): ?>
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100">
                                <div class="flex items-center space-x-3">
                                    <img src="/perunet/public/img/<?= htmlspecialchars($producto['imagen'] ?? 'EMPRESA/p.png') ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="w-10 h-10 object-contain rounded border border-gray-200">
                                    <div>
                                        <p class="text-sm font-bold text-gray-700"><?= htmlspecialchars($producto['nombre']) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($producto['marca'] ?? '') ?></p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-red-600"><?= $producto['stock'] ?? 0 ?> unidades</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-8">
                            <svg class="w-16 h-16 text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-lg text-gray-500 font-semibold">¡Todo el stock está bien!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>

</html>