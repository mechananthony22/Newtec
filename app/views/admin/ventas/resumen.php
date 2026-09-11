<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Resumen Estadístico de Ventas";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>

<?php
// Valor por defecto de fecha
if (!isset($fecha)) {
    if ($tipo === 'anual') {
        $fecha = date('Y');
    } elseif ($tipo === 'mensual') {
        $fecha = date('Y-m');
    } else {
        $fecha = date('Y-m-d');
    }
}
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-700">📊 Resumen Estadístico de Ventas</h1>
            <a href="/perunet/admin/perunet/ventas" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition">← Volver</a>
        </div>

        <!-- Formulario de Filtros -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Filtros de Estadísticas</h2>
            <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <input type="hidden" name="controlador" value="venta">
                <input type="hidden" name="accion" value="resumen_estadistico">

                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-600 mb-1">Tipo de reporte:</label>
                    <select name="tipo" id="tipo" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" onchange="this.form.submit()">
                        <option value="diario" <?= $tipo === 'diario' ? 'selected' : '' ?>>Diario</option>
                        <option value="mensual" <?= $tipo === 'mensual' ? 'selected' : '' ?>>Mensual</option>
                        <option value="anual" <?= $tipo === 'anual' ? 'selected' : '' ?>>Anual</option>
                    </select>
                </div>

                <?php if ($tipo === 'diario'): ?>
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-600 mb-1">Fecha:</label>
                        <input type="date" name="fecha" id="fecha" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" value="<?= htmlspecialchars($fecha) ?>">
                    </div>
                <?php elseif ($tipo === 'mensual'): ?>
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-600 mb-1">Mes:</label>
                        <input type="month" name="fecha" id="fecha" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" value="<?= htmlspecialchars($fecha) ?>">
                    </div>
                <?php elseif ($tipo === 'anual'): ?>
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-600 mb-1">Año:</label>
                        <input type="number" name="fecha" id="fecha" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" min="2000" max="<?= date('Y') ?>" value="<?= htmlspecialchars($fecha) ?>">
                    </div>
                <?php endif; ?>

                <button type="submit" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-6 py-2 hover:bg-blue-200 transition">🔍 Filtrar</button>
            </form>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Producto Más Vendido -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">📦 Producto Más Vendido</h3>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($productoMasVendido['nombre'] ?? 'No disponible') ?></p>
                    <p class="text-sm text-gray-600">Vendidos: <span class="font-semibold text-blue-600"><?= $productoMasVendido['total'] ?? 0 ?></span></p>
                </div>
            </div>

            <!-- Día con Más Ventas -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">📅 Día con Más Ventas</h3>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($diaMasVentas['fecha'] ?? 'No disponible') ?></p>
                    <p class="text-sm text-gray-600">Total: <span class="font-semibold text-green-600">S/. <?= isset($diaMasVentas['total']) ? number_format($diaMasVentas['total'], 2) : '0.00' ?></span></p>
                </div>
            </div>
        </div>

        <!-- Gráfico de Ventas -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Gráfico de Ventas <?= ucfirst($tipo) ?></h3>
            <div class="h-80">
                <canvas id="graficoVentas" height="120"></canvas>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('graficoVentas').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Ventas (S/.)',
                    data: <?= json_encode($valores) ?>,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'white',
                    pointBorderColor: 'rgba(59, 130, 246, 1)',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'S/. ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    </script>
</body>

</html>