<?php
include_once __DIR__ . '/../../app/config/config.php';
include_once __DIR__ . '/../../app/core/Autoloader.php';
include_once __DIR__ . '/../../app/core/App.php';

// Initialize autoloader and application
Autoloader::getInstance();
App::getInstance();

// Include model files
include_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/models/ModelosModel.php';

$modelosModel = new ModelosModel();

$accion = $_POST['accion'] ?? '';

if ($accion === 'create') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $id_marca = $_POST['id_marca'];

    if ($id) {
        $modelosModel->updateModelo($id, $nombre, $id_marca);
    } else {
        $modelosModel->createModelo($nombre, $id_marca);
    }
}

if ($accion === 'update') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $id_marca = $_POST['id_marca'];
    $modelosModel->updateModelo($id, $nombre, $id_marca);
}

if ($accion === 'delete') {
    $id = $_POST['id'];
    $modelosModel->delete($id);
}

if ($accion === 'getModelos') {
    /* recargar el contenido de la tabla (actualizar) */
    $modelos = $modelosModel->getAllWithMarca();

    foreach ($modelos as $modelo) {
        echo "<tr class='hover:bg-blue-50 transition'>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($modelo['id_mod']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($modelo['nombre']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($modelo['marca']) . "</td>";
        echo "<td class='px-4 py-2 flex gap-2'>";
        echo "<button class='bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition' onclick='editarModelo(" . htmlspecialchars(json_encode($modelo)) . ")'>Editar</button>";
        echo "<button class='bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition' onclick='eliminarModelo(" . htmlspecialchars($modelo['id_mod']) . ")'>Eliminar</button>";
        echo "</td>";
        echo "</tr>";
    }
}
