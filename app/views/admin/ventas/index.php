<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Administrar Ventas";
include __DIR__ . '/../../../components/perunet/adminHead.php';
// FUNCIÓN PARA MOSTRAR EL ESTADO AMIGABLE
function estadoAmigable($estado) {
    switch ($estado) {
        case 'pendiente': return 'Pendiente';
        case 'enviado': return 'Enviado';
        case 'entregado': return 'Completado';
        case 'cancelado': return 'Cancelado';
        default: return ucfirst($estado);
    }
}
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-700">Administrar Ventas</h1>
            <form class="flex gap-2 w-full md:w-auto" method="get">
                <input type="text" name="buscar" placeholder="Buscar por cliente..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" class="rounded-full border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700 w-full md:w-64">
                <button type="submit" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-4 py-2 hover:bg-blue-200 transition">Buscar</button>
            </form>
        </div>

        <!-- Mensaje de confirmación -->
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mb-6 p-4 rounded-xl border border-green-200 bg-green-50 text-green-800">
                <?php
                switch ($_GET['mensaje']) {
                    case 'guardado':
                        echo "✅ Venta registrada correctamente.";
                        break;
                    case 'actualizado':
                        echo "✏️ Venta actualizada correctamente.";
                        break;
                    case 'eliminado':
                        echo "🗑️ Venta eliminada correctamente.";
                        break;
                    default:
                        echo "Ocurrió un error.";
                        break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Cliente</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Método de Pago</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Sucursal</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Estado</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($ventas)): ?>
                        <?php foreach ($ventas as $venta): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['id']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['cliente']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= date('d/perunet/m/perunet/Y H:i', strtotime($venta['fecha'])) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['metodo_pago'] ?? 'N/perunet/A') ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['sucursal'] ?? 'N/perunet/A') ?></td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full <?= ($venta['estado'] === 'entregado') ? 'bg-green-100 text-green-800' : (($venta['estado'] === 'cancelado') ? 'bg-red-100 text-red-800' : (($venta['estado'] === 'enviado') ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) ?>">
                                        <?= estadoAmigable($venta['estado']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2 flex gap-2">
                                    <a href="/perunet/admin/perunet/ventas/perunet/detalle/perunet/<?= $venta['id'] ?>" class="bg-blue-100 text-blue-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-blue-200 transition">Ver Detalle</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-6 text-gray-400">No hay ventas registradas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>