<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Reporte de Ventas";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-700">Reporte de Ventas</h1>
            <a href="/perunet/admin/perunet/ventas" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition">← Volver</a>
        </div>

        <!-- Formulario de Filtros -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Filtros del Reporte</h2>
            <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-600 mb-1">Tipo de reporte:</label>
                    <select id="tipo" name="tipo" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" onchange="mostrarInputFecha()">
                        <option value="diario" <?= ($_GET['tipo'] ?? '') === 'diario' ? 'selected' : '' ?>>Diario</option>
                        <option value="mensual" <?= ($_GET['tipo'] ?? '') === 'mensual' ? 'selected' : '' ?>>Mensual</option>
                        <option value="anual" <?= ($_GET['tipo'] ?? '') === 'anual' ? 'selected' : '' ?>>Anual</option>
                    </select>
                </div>

                <div>
                    <label for="inputDiario" class="block text-sm font-medium text-gray-600 mb-1">Fecha:</label>
                    <input type="date" id="inputDiario" name="dia" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" value="<?= $_GET['dia'] ?? date('Y-m-d') ?>">
                </div>

                <div style="display:none;">
                    <label for="inputMensual" class="block text-sm font-medium text-gray-600 mb-1">Mes:</label>
                    <input type="month" id="inputMensual" name="mes" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" value="<?= $_GET['mes'] ?? date('Y-m') ?>">
                </div>

                <div style="display:none;">
                    <label for="inputAnual" class="block text-sm font-medium text-gray-600 mb-1">Año:</label>
                    <input type="number" id="inputAnual" name="anio" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" min="2000" max="2100" placeholder="Año" value="<?= $_GET['anio'] ?? date('Y') ?>">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-6 py-2 hover:bg-blue-200 transition">Buscar</button>
                    <a href="/perunet/app/perunet/components/perunet/reporteVentas_fecha.php?tipo=<?= $_GET['tipo'] ?? 'diario' ?>&dia=<?= $_GET['dia'] ?? '' ?>&mes=<?= $_GET['mes'] ?? '' ?>&anio=<?= $_GET['anio'] ?? '' ?>" class="bg-green-100 text-green-700 font-semibold rounded-full px-6 py-2 hover:bg-green-200 transition" target="_blank">📥 Descargar PDF</a>
                </div>
            </form>
        </div>

        <!-- Resumen de Ventas -->
        <?php if (!empty($ventas)): ?>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800">Total de Ventas: <span class="font-bold"><?= count($ventas) ?></span></p>
                        <p class="text-sm text-green-600">Período seleccionado</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-green-800">Total Vendido:</p>
                        <p class="text-2xl font-bold text-green-600">S/. <?= number_format($totalVentas ?? 0, 2) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tabla de Ventas -->
        <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Ventas del Período</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Cliente</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Método de Pago</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Sucursal</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Estado</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($ventas)): ?>
                        <?php foreach ($ventas as $venta): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['id']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['cliente']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= date('d/m/Y', strtotime($venta['fecha'])) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['metodo_pago'] ?? 'N/A') ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['sucursal'] ?? 'N/A') ?></td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full <?= ($venta['estado'] === 'completado') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                        <?= ucfirst(htmlspecialchars($venta['estado'])) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-700 font-semibold">S/. <?= number_format($venta['total'] ?? 0, 2) ?></td>
                                <td class="px-4 py-2">
                                    <a href="/perunet/admin/ventas/detalle/<?= htmlspecialchars($venta['id']) ?>" class="bg-blue-100 text-blue-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-blue-200 transition">Ver Detalle</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-400">No hay ventas registradas para el período seleccionado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- JS para mostrar el input correcto -->
    <script>
        function mostrarInputFecha() {

            const hoy = new Date();
            const mes = String(hoy.getMonth() + 1).padStart(2, '0'); // getMonth() es 0-indexado
            const dia = String(hoy.getDate()).padStart(2, '0');
            const anio = String(hoy.getFullYear());

            const fecha = `${anio}-${mes}-${dia}`;

            const tipo = document.getElementById('tipo').value;
            const dates = document.getElementById('inputDiario').parentElement;
            const month = document.getElementById('inputMensual').parentElement;
            const year = document.getElementById('inputAnual').parentElement;

            dates.style.display = 'none';
            month.style.display = 'none';
            year.style.display = 'none';

            if (tipo === 'diario') {
                //document.getElementById('inputDiario').value = fecha;
                dates.style.display = 'block';
                document.getElementById('inputMensual').value = '';
                document.getElementById('inputAnual').value = '';
            } else if (tipo === 'mensual') {

                //document.getElementById('inputMensual').value = `${anio}-${mes}`;
                month.style.display = 'block';
                document.getElementById('inputDiario').value = '';
                document.getElementById('inputAnual').value = '';
            } else if (tipo === 'anual') {
                //document.getElementById('inputAnual').value = anio;
                year.style.display = 'block';
                document.getElementById('inputDiario').value = '';
                document.getElementById('inputMensual').value = '';
            }
        }
        window.onload = mostrarInputFecha;
    </script>
</body>

</html>