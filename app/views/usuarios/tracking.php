<?php
$title = "Seguimiento de Pedido #" . $venta['id_ven'];
$style = "perfil"; // Reutilizamos estilos si es necesario
ob_start();

// Definir los estados y su orden
$estados = [
    'pendiente' => ['label' => 'Pedido Recibido', 'icon' => 'fa-clipboard-list'],
    'preparando' => ['label' => 'Preparando', 'icon' => 'fa-box-open'],
    'enviado' => ['label' => 'En Camino', 'icon' => 'fa-shipping-fast'],
    'entregado' => ['label' => 'Entregado', 'icon' => 'fa-check-circle']
];

$estadoActual = strtolower($venta['estado']);
$estadoIndex = array_search($estadoActual, array_keys($estados));
if ($estadoIndex === false) $estadoIndex = -1; // Cancelado u otro
?>

<div class="min-h-screen flex flex-col items-center bg-gray-50 py-8">
    <div class="w-full max-w-4xl bg-white rounded-xl shadow-lg p-8 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fa fa-map-marker-alt text-red-600 mr-2"></i> Seguimiento de Pedido #<?= $venta['id_ven'] ?>
            </h2>
            <a href="/perunet/perfil" class="text-gray-600 hover:text-red-600 font-semibold transition">
                <i class="fa fa-arrow-left mr-1"></i> Volver
            </a>
        </div>

        <!-- Timeline -->
        <div class="relative w-full py-10 px-4">
            <!-- Barra de progreso -->
            <div class="absolute top-1/perunet/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/perunet/2 z-0"></div>
            <div class="absolute top-1/perunet/2 left-0 h-1 bg-red-600 -translate-y-1/perunet/2 z-0 transition-all duration-1000" 
                 style="width: <?= ($estadoIndex >= 0) ? (($estadoIndex /perunet/ (count($estados) - 1)) * 100) : 0 ?>%;"></div>

            <div class="relative z-10 flex justify-between w-full">
                <?php 
                $i = 0;
                foreach ($estados as $key => $info): 
                    $active = $i <= $estadoIndex;
                    $current = $i === $estadoIndex;
                ?>
                    <div class="flex flex-col items-center group">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center border-4 transition-all duration-500
                            <?= $active ? 'bg-red-600 border-red-600 text-white' : 'bg-white border-gray-300 text-gray-400' ?>
                            <?= $current ? 'ring-4 ring-red-200 scale-110' : '' ?>
                        ">
                            <i class="fa <?= $info['icon'] ?> text-lg"></i>
                        </div>
                        <p class="mt-4 font-bold text-sm <?= $active ? 'text-red-700' : 'text-gray-400' ?>">
                            <?= $info['label'] ?>
                        </p>
                    </div>
                <?php 
                $i++;
                endforeach; 
                ?>
            </div>
        </div>

        <?php if ($estadoActual === 'cancelado'): ?>
            <div class="mt-8 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg text-center font-bold">
                <i class="fa fa-times-circle mr-2"></i> Este pedido ha sido cancelado.
            </div>
        <?php endif; ?>

        <!-- Detalles del Pedido -->
        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Detalles de Entrega</h3>
                <p class="text-gray-600 mb-2"><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></p>
                <p class="text-gray-600 mb-2"><strong>Dirección:</strong> <?= htmlspecialchars($venta['direccion'] ?? 'Dirección registrada') ?></p> <!-- Asumiendo que traemos la dirección -->
                <p class="text-gray-600 mb-2"><strong>Método de Pago:</strong> <?= htmlspecialchars($venta['metodo_pago'] ?? 'Tarjeta') ?></p>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Resumen de Compra</h3>
                <ul class="space-y-2">
                    <?php foreach ($detalle as $item): ?>
                        <li class="flex justify-between text-gray-600">
                            <span><?= $item['cantidad'] ?>x <?= htmlspecialchars($item['producto_nombre']) ?></span>
                            <span class="font-semibold">S/. <?= number_format($item['cantidad'] * $item['precio_unitario'], 2) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="mt-4 pt-4 border-t flex justify-between text-xl font-bold text-red-700">
                    <span>Total</span>
                    <span>S/. <?= number_format($venta['total'], 2) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>
