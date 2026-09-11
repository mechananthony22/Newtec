<?php

include_once __DIR__ . '/../../app/config/config.php';
include_once __DIR__ . '/../../app/core/Autoloader.php';
include_once __DIR__ . '/../../app/core/App.php';

// Initialize autoloader and application
Autoloader::getInstance();
App::getInstance();

// Include model files
include_once __DIR__ . '/../../app/core/Model.php';
include_once __DIR__ . '/../../app/models/ModelosModel.php';
include_once __DIR__ . '/../../app/models/SubcategoriasModel.php';

// Create model instances
$modelosModel = new ModelosModel();
$subcategoriasModel = new SubcategoriasModel();

$accion = $_POST['accion'] ?? "";

if ($accion === "filtroMarcaModelo") {
    $marca_id = $_POST['marca_id'] ?? "";

    $modelos = $modelosModel->getMarcasById($marca_id);


    /* enviar json */
    echo json_encode($modelos);
}

if ($accion === "filtroCategoriaSubcategoria") {
    $categoria_id = $_POST['id_categoria'];

    $subcategorias = $subcategoriasModel->getSubCategoriasById($categoria_id);


    /* enviar json */
    echo json_encode($subcategorias);
}