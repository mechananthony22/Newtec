<?php
$title = "Carrito de Compras";
$style = "carrito";
ob_start();
$id_usuario = $_SESSION['usuario']['id_us'] ?? null;
require_once __DIR__ . '/../../models/DetalleCarrito.php';
$carrito = [];
if ($id_usuario) {
    $detalleCarrito = new DetalleCarrito();
    $carrito = $detalleCarrito->getItems($id_usuario);
}
$precio_total = 0;
if (!empty($carrito)) {
    foreach ($carrito as $item) {
        $precio_total += $item['precio_producto'] * $item['cantidad'];
    }
}
?>
<div class="w-full max-w-6xl flex flex-col md:flex-row gap-8 py-8 px-2 md:px-0 mx-auto">
    <!-- Columna izquierda: productos -->
    <section class="flex-1 bg-white rounded-lg shadow-lg p-6 mb-6 md:mb-0">
        <h2 class="text-2xl font-bold text-red-700 mb-4 flex items-center gap-2">
            <i class="fa fa-shopping-cart text-black text-2xl"></i>
            Tu Carrito
        </h2>
        <div id="carrito-contenido">
            <?php if (empty($carrito)): ?>
                <div class="text-center py-8">
                    <p class="text-gray-500 text-lg">Tu carrito está vacío.</p>
                    <a class="inline-block mt-2 px-4 py-2 bg-red-600 hover:bg-red-70 text-white rounded hover:from-black hover:to-red-700 transition"
                        href="/perunet/">Ver productos</a>
                </div>
            <?php endif; ?>
            <input type="hidden" id="id_usuario" value="<?= $id_usuario ?>">
            <?php if (isset($_SESSION['mensaje'])): ?>
                <input type="hidden" id="mensaje" value="<?= $_SESSION['mensaje'] ?>">
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>
            <div id="carrito-productos" class="space-y-4">
                <?php if ($id_usuario != null): ?>
                    <?php foreach ($carrito as $item): ?>
                        <div class="flex items-center gap-4 bg-gray-100 rounded-lg p-4 shadow">
                            <img src="/perunet/public/img/<?= $item['imagen_producto'] ?>"
                                alt="<?= htmlspecialchars($item['nombre_producto']) ?>"
                                class="w-20 h-20 object-contain rounded border border-gray-300 bg-white">
                            <div class="flex-1">
                                <p class="font-semibold text-black text-lg"><?= $item['nombre_producto'] ?></p>
                                <p class="text-gray-700">Precio: <span
                                        class="text-red-700 font-bold">$<?= $item['precio_producto'] ?></span></p>
                                <p class="text-gray-700">Cantidad: <span
                                        class="font-bold text-black"><?= $item['cantidad'] ?></span></p>
                            </div>
                            <button
                                class="ml-2 px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold shadow transition-colors btn-eliminar"
                                onclick="eliminarProducto(<?= $item['id_detalle'] ?>)">Eliminar</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <!-- Columna derecha: métodos de pago y total -->
    <aside class="w-full md:w-80 bg-white rounded-lg shadow-lg p-6 flex flex-col gap-6">
        <div class="payment-methods">
            <h3 class="text-xl font-bold text-black mb-3">Métodos de Pago</h3>
            <div class="grid grid-cols-2 gap-3 mb-2">
                <?php foreach ($metodos as $m): ?>
                    <?php
                    $nombrePago = $m['nombre'] ?? '';
                    $tipoPago = $m['tipo'] ?? $nombrePago;
                    ?>
                    <div class="flex flex-col items-center payment-item cursor-pointer border border-gray-200 rounded-lg p-2 hover:shadow-lg transition"
                        data-metodo="<?= $m['id_met'] ?>">
                        <img src="/perunet/public/img/EMPRESA/PAGOS/<?= htmlspecialchars(strtoupper($nombrePago)) ?>.png"
                            alt="<?= htmlspecialchars($tipoPago) ?>" class="h-10 mb-1">
                        <p class="text-xs text-black font-semibold"><?= htmlspecialchars(strtoupper($tipoPago)) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="text-xs text-gray-500 text-center">SIGUE COMPRANDO, APROVECHA LAS OFERTAS</p>
        </div>
        <div id="total" class="bg-gray-50 rounded-lg p-4 flex flex-col gap-3">
            <p class="text-lg font-bold text-black">Total: <span class="text-red-700" id="total-price">S/ <?= number_format($precio_total, 2) ?></span></p>
            <button id="vaciar-carrito"
                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold shadow transition-colors">Vaciar
                Carrito</button>
            <a href="/perunet/confirmar/compra"
                class="w-full block text-center px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg font-bold shadow transition-colors">Comprar</a>
        </div>
    </aside>
</div>
<!-- Promociones Especiales -->
<section class="w-full max-w-6xl mt-10 mx-auto">
    <h2 class="text-xl font-bold text-black mb-4">PROMOCIONES ESPECIALES</h2>
    <div class="flex flex-col md:flex-row gap-6">
        <div class="flex-1 bg-white rounded-lg shadow p-4 flex items-center gap-4">
            <img src="/perunet/public/img/EMPRESA/Categoria2.jpg" alt="Oferta en Cámaras IP"
                class="w-32 h-32 object-cover rounded">
            <div>
                <h3 class="font-bold text-red-700">Oferta Especial en Cámaras IP</h3>
                <p class="text-gray-700">Compra ahora y obtén un descuento del 20% en cámaras de seguridad IP.</p>
                <a href="/perunet/productos/CamarasIP"
                    class="inline-block mt-2 px-3 py-1 bg-red-600 hover:bg-red-700 to-black text-white rounded hover:from-black hover:to-red-700 transition btn-promotion">Ver
                    Promoción</a>
            </div>
        </div>
    </div>
</section>
<!-- Reseñas de Clientes -->
<section class="w-full max-w-6xl mt-10 mx-auto">
    <h2 class="text-xl font-bold text-black mb-4">Lo que nuestros clientes dicen</h2>
    <div class="flex flex-col md:flex-row gap-6">
        <div class="flex-1 bg-white rounded-lg shadow p-4">
            <p class="text-gray-700">"La página es muy fácil de usar, encontré todo lo que buscaba en minutos. ¡Me
                encanta su diseño y lo rápido que cargan las secciones!"</p>
            <p class="text-right text-sm text-black mt-2">- Estrella Flores</p>
        </div>
        <div class="flex-1 bg-white rounded-lg shadow p-4">
            <p class="text-gray-700">"Excelente servicio. Realicé mi pedido sin problemas, y los productos llegaron
                justo a tiempo. ¡Sin duda volveré a comprar aquí!"</p>
            <p class="text-right text-sm text-black mt-2">- Thalia Burga</p>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>

<!-- Scripts para carrito Lista -->
<script type="module" src="/perunet/public/js/carritoList.js"></script>