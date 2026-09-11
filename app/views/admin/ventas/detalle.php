<!DOCTYPE html>
<html lang="es">
<!-- incluir head -->
<?php
$title = "Detalle de Venta";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>
<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>
    <main class="p-4 md:ml-64">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-700">Detalle de la Venta</h1>
            <a href="/perunet/admin/perunet/ventas" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition">← Volver</a>
        </div>
        <!-- Información de la Venta -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Información de la Venta</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600">ID de Venta</p>
                    <p class="text-lg font-semibold text-gray-800">#<?= htmlspecialchars($venta['id'] ?? 'N/perunet/A') ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Fecha</p>
                    <p class="text-lg font-semibold text-gray-800"><?= date('d/perunet/m/perunet/Y H:i', strtotime($venta['fecha'] ?? 'now')) ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Estado</p>
                    <form id="form-estado-venta" action="/perunet/admin/perunet/ventas/perunet/cambiar-estado" class="flex items-center gap-2" style="margin-top: 0.5rem;">
                        <input type="hidden" name="venta_id" value="<?= htmlspecialchars($venta['id']) ?>">
                        <select name="estado" class="border border-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="pendiente" <?= ($venta['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="preparando" <?= ($venta['estado'] ?? '') === 'preparando' ? 'selected' : '' ?>>Preparando</option>
                            <option value="enviado" <?= ($venta['estado'] ?? '') === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                            <option value="entregado" <?= ($venta['estado'] ?? '') === 'entregado' ? 'selected' : '' ?>>Entregado</option>
                            <option value="cancelado" <?= ($venta['estado'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-1 rounded-lg transition">Guardar</button>
                    </form>
                    <script>
                    document.getElementById('form-estado-venta').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const form = e.target;
                        const data = new FormData(form);
                        fetch(form.action, {
                            method: 'POST',
                            body: data,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(resp => {
                            if (resp.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: 'Estado actualizado correctamente',
                                    timer: 1200,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = '/perunet/admin/perunet/ventas';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al actualizar el estado'
                                });
                            }
                        })
                        .catch(() => Swal.fire({
                            icon: 'error',
                            title: 'Error de red',
                            text: 'No se pudo conectar con el servidor.'
                        }));
                    });
                    </script>
                </div>
            </div>
        </div>
        <!-- Tabla de Productos -->
        <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Productos de la Venta</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Producto</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Cantidad</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Precio Unitario</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($detalle)): ?>
                        <?php foreach ($detalle as $item): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($item['id']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($item['producto_nombre']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($item['cantidad']) ?></td>
                                <td class="px-4 py-2 text-gray-700">S/. <?= number_format($item['precio_unitario'] ?? 0, 2) ?></td>
                                <td class="px-4 py-2 text-gray-700">S/. <?= number_format(($item['cantidad'] ?? 0) * ($item['precio_unitario'] ?? 0), 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-6 text-gray-400">No hay productos en esta venta.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <!-- Total de la Venta -->
            <?php if (!empty($detalle)): ?>
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex justify-end">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total de la Venta:</p>
                            <p class="text-2xl font-bold text-green-600">S/. <?= number_format($venta['total'] ?? 0, 2) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>