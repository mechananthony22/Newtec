<?php
$title = "Perunet | " . htmlspecialchars($producto['nombre']);
?>

<div class="max-w-5xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-2 gap-10 bg-white rounded-xl shadow-lg mt-10">
    <!-- Imagen del producto -->
    <div class="flex flex-col items-center justify-center">
        <img src="/perunet/public/img/<?= htmlspecialchars($producto['imagen'] ?? 'EMPRESA/p.png') ?>"
            alt="<?= htmlspecialchars($producto['nombre']) ?>"
            class="w-80 h-80 object-contain rounded-lg shadow mb-4">
    </div>
    <!-- Información del producto -->
    <div class="flex flex-col justify-center">
        <input type="hidden" id="id_usuario" value="<?= isset($_SESSION['usuario']['id_us']) ? $_SESSION['usuario']['id_us'] : '' ?>">
        <input type="hidden" id="id_producto" value="<?= $producto['id_pro'] ?>">
        
        <h1 class="text-3xl font-bold mb-4 text-gray-900">
            <?= htmlspecialchars($producto['nombre']) ?>
        </h1>
        <div class="flex flex-col gap-1 mb-4">
            <span class="text-gray-700">Marca: <span class="font-semibold"><?= htmlspecialchars($producto['marca'] ?? 'N/A') ?></span></span>
            <span class="text-gray-700">Modelo: <span class="font-semibold"><?= htmlspecialchars($producto['modelo'] ?? 'N/A') ?></span></span>
            <span class="text-gray-700">Categoría: <span class="font-semibold"><?= htmlspecialchars($producto['categoria'] ?? 'N/A') ?></span></span>
            <span class="text-gray-700">Subcategoría: <span class="font-semibold"><?= htmlspecialchars($producto['subcategoria'] ?? 'N/A') ?></span></span>
        </div>
        <div class="text-2xl font-bold text-red-600 mb-2" id="precio">S/ <?= number_format($producto['precio'], 2) ?></div>
        <div class="mb-4">
            <span id="stock" class="inline-block px-3 py-1 rounded-full text-sm font-medium <?= ($producto['stock_disponible'] > 0) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                <?= $msgStock ?> stock
            </span>
        </div>
        <div class="flex items-center gap-4 mb-6">
            <span class="text-gray-700 font-semibold">Cantidad:</span>
            <div class="flex items-center border rounded-lg overflow-hidden">
                <button id="restar-cantidad" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-lg">-</button>
                <input id="cantidad" type="text" value="<?= ($producto['stock_disponible'] == 0) ? $producto['stock_disponible'] : 1 ?>" min="1" max="<?= $producto['stock_disponible'] ?>" class="w-16 text-center outline-none">
                <button id="sumar-cantidad" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-lg">+</button>
            </div>
        </div>
        <input type="hidden" id="id_usuario" value="<?= isset($_SESSION['usuario']['id']) ? $_SESSION['usuario']['id'] : '' ?>">
        <input type="hidden" id="id_producto" value="<?= $producto['id_pro'] ?>">
        <button id="btn-agregar-carrito" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2 text-lg transition-colors mt-2 <?= $msgStock == 'Agotado' ? 'opacity-50 cursor-not-allowed' : '' ?>" <?= $msgStock == 'Agotado' ? 'disabled' : '' ?>>
            <i class="fas fa-shopping-cart"></i> Agregar al Carrito
        </button>
        <a href="/perunet" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 rounded-lg flex items-center justify-center gap-2 transition-colors mt-3">
            <i class="fas fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>
</div>

<!-- Scripts para carrito (producto seleccionado) -->
<script type="module" src="/perunet/public/js/carrito.js"></script>