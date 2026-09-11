<?php
$title = "Perunet | Tienda";
$style = "producto-detalle";
// include(__DIR__ . '/../components/head.php');
?>

<!-- Header -->


<body>

    <!-- Product Section -->
    <div class="producto-container">
        <div class="producto-imagen">
            <!-- Product Images -->
            <img src="/perunet/public/img/<?= htmlspecialchars($producto['imagen'] ?? 'EMPRESA/p.png') ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="img-producto">
        </div>

        <!-- descripcion corta -->
        <div class="producto-info">
            <input type="hidden" id="id_usuario"
                value="<?= isset($_SESSION['usuario']['id']) ? $_SESSION['usuario']['id'] : "" ?>">

            <input type="hidden" id="id_producto" value="<?= $producto['id_pro'] ?>">

            <!-- mostrar categoria y subcategoria -->
            <h2 class="producto-categoria"><?= $producto['categoria']; ?> / <?= $producto['subcategoria']; ?></h2>

            <!-- mostrar nombre y modelo -->
            <h1 class="producto-titulo"><?= $producto['nombre']; ?> - <?= $producto['modelo']; ?></h1>

            <!-- mostrar marca y stock -->
            <div class="etiquetas-container">
                <span class="etiqueta etiqueta-marca">
                    <?= $producto['marca']; ?>
                </span>
                <span id="stock" class="etiqueta etiqueta-stock">
                    <?= $msgStock; ?> Stock
                </span>
            </div>

            <!-- mostrar descripcion -->
            <p class="producto-descripcion"><?= $producto['descripcion']; ?></p>

            <!-- mostrar precio -->
            <p id="precio" class="producto-precio">$<?= number_format($producto['precio'], 2); ?></p>

            <!-- mostrar cantidad -->
            <span class="cantidad-label">Cantidad:</span>
            <div class="cantidad-container">
                <button id="restar-cantidad" class="cantidad-btn restar">-</button>
                <input id="cantidad" type="number" value="<?= ($producto['stock_disponible'] == 0) ? $producto['stock_disponible'] : 1 ?>" min="1" max="<?= $producto['stock_disponible'] ?>" class="cantidad-input">
                <button id="sumar-cantidad" class="cantidad-btn sumar">+</button>
            </div>

            <!-- boton agregar al carrito -->
            <button id="agregar-al-carrito" class="btn-agregar-carrito">
                <span>🛒</span> AGREGAR AL CARRITO
            </button>
        </div>
    </div>



    <script type="module" src="/perunet/public/js/carrito.js"></script>
    <script src="/perunet/public/js/funciones.js" defer></script>
</body>

</html>