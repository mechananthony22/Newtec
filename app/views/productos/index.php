<?php
$title = "Perunet | " . ($nombreCategoria ?? 'Productos');
$style = "productos";
?>
<!-- Cabecera de categoría -->
<div class="w-full bg-gradient-to-r from-red-600 to-pink-500 py-8 px-4 text-center text-white mb-8">
    <h1 class="text-3xl md:text-4xl font-bold mb-2 capitalize">
        <?= htmlspecialchars($nombreCategoria ?? 'Categoría') ?><?= !empty($nombreSubcategoria) ? ' / ' . htmlspecialchars($nombreSubcategoria) : '' ?>
    </h1>
    <p class="text-lg md:text-xl font-light">
        Descubre nuestra selección de productos
        <?= htmlspecialchars($nombreCategoria ?? '') ?><?= !empty($nombreSubcategoria) ? ' / ' . htmlspecialchars($nombreSubcategoria) : '' ?>
    </p>
</div>

<div class="max-w-7xl mx-auto px-2 md:px-6 flex flex-col md:flex-row gap-6">
    <!-- Filtros laterales -->
    <aside class="w-full md:w-64 bg-white rounded-xl shadow p-6 mb-4 md:mb-0 flex-shrink-0">
        <h2 class="text-xl font-semibold mb-4">Filtrar por</h2>
        <!-- Subcategorías como enlaces -->
        <?php if (!empty($subcategorias)): ?>
            <div class="mb-6">
                <h3 class="text-lg font-bold mb-2">Subcategorías</h3>
                <div class="flex flex-col gap-1">
                    <?php foreach ($subcategorias as $subcategoria): ?>
                        <?php
                        $isActive = isset($nombreSubcategoria) && $nombreSubcategoria === $subcategoria['nombre'];
                        $subcatUrl = '/perunet/productos/' . ProductoDetalleController::slugify($nombreCategoria) . '/' . ProductoDetalleController::slugify($subcategoria['nombre']);
                        ?>
                        <a href="<?= $subcatUrl ?>"
                            class="px-3 py-2 rounded transition-colors text-base <?= $isActive ? 'bg-red-600 text-white font-bold' : 'hover:bg-gray-100 text-gray-800' ?>">
                            <?= htmlspecialchars($subcategoria['nombre']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <!-- Marcas como checkboxes -->
        <div class="mb-6">
            <h3 class="text-lg font-bold mb-2">Marcas</h3>
            <form id="marca-form" class="flex flex-col gap-1">
                <?php
                $marcasUnicas = [];
                if (!empty($productos)) {
                    foreach ($productos as $producto) {
                        if (!empty($producto['marca']) && !in_array($producto['marca'], $marcasUnicas)) {
                            $marcasUnicas[] = $producto['marca'];
                        }
                    }
                    sort($marcasUnicas);
                }
                foreach ($marcasUnicas as $marca): ?>
                    <label class="flex items-center gap-2 text-gray-700">
                        <input type="checkbox" value="<?= htmlspecialchars($marca) ?>"
                            class="marca-checkbox rounded border-gray-300 focus:ring-red-500">
                        <span><?= htmlspecialchars($marca) ?></span>
                    </label>
                <?php endforeach; ?>
            </form>
        </div>
        <!-- Rango de precio -->
        <div class="mb-6">
            <h3 class="text-lg font-bold mb-2">Rango de Precio</h3>
            <div class="flex flex-col gap-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Mínimo: S/. <span id="valor-min">0</span></span>
                    <span>Máximo: S/. <span id="valor-max">1000</span></span>
                </div>
                <input type="range" id="precio-min" name="precio-min" min="0" max="1000" step="1" value="0"
                    class="w-full accent-red-600">
                <input type="range" id="precio-max" name="precio-max" min="0" max="1000" step="1" value="1000"
                    class="w-full accent-red-600">
            </div>
        </div>
        <!-- Acciones de filtro -->
        <div class="flex gap-2 mt-4">
            <button id="aplicar-filtros"
                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg flex items-center justify-center gap-2 transition-colors">
                <i class="fas fa-filter"></i> Aplicar Filtros
            </button>
            <button id="reset-filters"
                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 rounded-lg flex items-center justify-center gap-2 transition-colors">
                <i class="fas fa-sync-alt"></i> Restablecer
            </button>
        </div>
    </aside>

    <!-- Grid de productos -->
    <section id="productos" class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $producto): ?>
                <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
                    <div class="w-full flex justify-center mb-4">
                        <img src="/perunet/public/img/<?= htmlspecialchars($producto['imagen'] ?? 'EMPRESA/p.png') ?>"
                            alt="<?= htmlspecialchars($producto['nombre']) ?>" class="w-28 h-28 object-contain rounded">
                    </div>
                    <h3 class="text-lg font-bold text-center mb-1"><?= htmlspecialchars($producto['nombre']) ?></h3>
                    <p class="text-gray-500 text-sm mb-1"><?= htmlspecialchars($producto['marca'] ?? '') ?></p>
                    <p class="text-red-600 font-bold text-lg mb-1">S/ <?= number_format($producto['precio'], 2) ?></p>
                    <span
                        class="text-green-600 font-semibold mb-2"><?= (int) $producto['stock'] > 0 ? 'En stock' : 'Agotado' ?></span>
                    <a href="/perunet/producto/<?= ProductoDetalleController::slugify($producto['categoria']) ?>/<?= ProductoDetalleController::slugify($producto['subcategoria']) ?>/<?= $producto['id_pro'] ?>"
                        class="mt-auto bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors w-full text-center">
                        Ver más
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-16">
                <h3 class="text-2xl font-bold mb-2">No se encontraron productos</h3>
                <p class="text-gray-600 mb-4">Lo sentimos, no hay productos disponibles en esta categoría en este momento.
                </p>
                <a href="/perunet/"
                    class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">Volver
                    al inicio</a>
            </div>
        <?php endif; ?>
    </section>
</div>

<!-- JS de filtros embebido -->
<script>
    // Actualizar valores de rango de precio
    const minInput = document.getElementById('precio-min');
    const maxInput = document.getElementById('precio-max');
    const minVal = document.getElementById('valor-min');
    const maxVal = document.getElementById('valor-max');
    if (minInput && maxInput && minVal && maxVal) {
        minInput.addEventListener('input', () => { minVal.textContent = minInput.value; });
        maxInput.addEventListener('input', () => { maxVal.textContent = maxInput.value; });
    }
    // Aquí puedes agregar más JS para filtros si lo necesitas
</script>