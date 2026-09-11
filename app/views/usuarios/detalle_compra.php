<?php
$title = "Detalle de Compra";
ob_start();
?>
<style>
@media print {
    body * { visibility: hidden !important; }
    .comprobante-print, .comprobante-print * { visibility: visible !important; }
    .comprobante-print { position: absolute; left: 0; top: 0; width: 100vw; background: white; box-shadow: none; }
    .no-print { display: none !important; }
}
</style>
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8 mt-8 comprobante-print">
    <h2 class="text-2xl font-bold text-red-700 mb-6 flex items-center gap-2">
        <i class="fa fa-file-invoice"></i> Comprobante de Compra
    </h2>
    <div class="mb-4">
        <strong>Pedido #<?= htmlspecialchars($detalle[0]['id_ven']) ?></strong><br>
        Fecha: <?= date('d/m/Y', strtotime($detalle[0]['fecha_venta'])) ?><br>
        Estado: <?= ucfirst($detalle[0]['estado']) ?>
    </div>
    <table class="min-w-full bg-white border border-gray-200 rounded-lg mb-4">
        <thead>
            <tr class="bg-gray-100 text-gray-700 text-left">
                <th class="py-2 px-4">Producto</th>
                <th class="py-2 px-4">Cantidad</th>
                <th class="py-2 px-4">Precio</th>
                <th class="py-2 px-4">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalle as $item): ?>
            <tr>
                <td class="py-2 px-4"><?= htmlspecialchars($item['producto_nombre']) ?></td>
                <td class="py-2 px-4"><?= $item['cantidad'] ?></td>
                <td class="py-2 px-4">S/ <?= number_format($item['precio_unitario'], 2) ?></td>
                <td class="py-2 px-4">S/ <?= number_format($item['cantidad'] * $item['precio_unitario'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-right font-bold text-lg mb-4">
        Total: S/ <?= number_format($detalle[0]['total'], 2) ?>
    </div>
    <button onclick="window.print()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition no-print">Imprimir</button>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?> 