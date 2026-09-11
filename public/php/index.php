<?php

include_once __DIR__ . '/../../app/models/ProductoModel.php';
include_once __DIR__ . '/../../app/controllers/ProductoDetalleController.php';

$productoModel = new ProductoModel();

$accion = $_POST['accion'] ?? '';

if ($accion === 'getCategorias') {
    $id_categoria = $_POST['id_categoria'] ?? null;
    $id_subcategoria = $_POST['id_subcategoria'] ?? null;

    $productos = $productoModel->getProductosDestacados(12, $id_categoria, $id_subcategoria);

    if (empty($productos)) {
        echo '<p>No hay productos disponibles.</p>';
        exit;
    }

    foreach ($productos as $producto) {
        echo '<div class="producto" data-id="1">
                    <img src="/perunet/public/img/' . htmlspecialchars($producto['imagen'] ?? 'EMPRESA/p.png') . '" alt="' . htmlspecialchars($producto['nombre']) . '" class="img-producto">
                    <h3>' . $producto['nombre'] . '</h3>
                    <p class="marca">' . $producto['marca'] . '</p>
                    <span class="precio">' . $producto['precio'] . '</span>
                    <h4>' . $producto['descripcion'] . '</h4>
                    <a class="btn-ver-mas"
                        href="/perunet/producto/' . ProductoDetalleController::slugify($producto['categoria']) . '/' . ProductoDetalleController::slugify($producto['subcategoria']) . '/' . $producto['id_pro'] . '" data-id="1" data-nombre="' . $producto['nombre'] . '" data-precio="' . $producto['precio'] . '">
                        <span class="icon-search" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="7" stroke="white" stroke-width="2"/><path d="M15 15L18 18" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
                        </span>
                        Ver más
                    </a>
                </div>';
    }
}
