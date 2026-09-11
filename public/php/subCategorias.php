<?php
include_once __DIR__ . '/../../app/config/config.php';
include_once __DIR__ . '/../../app/core/Autoloader.php';
include_once __DIR__ . '/../../app/core/App.php';

// Initialize autoloader and application
Autoloader::getInstance();
App::getInstance();

// Include model files
include_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/models/SubCategoriasModel.php';

$subcategoriasModel = new SubCategoriasModel();

$accion = $_POST['accion'] ?? '';

if ($accion === 'create') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $id_categoria = $_POST['id_categoria'];

    if ($id) {
        $subcategoriasModel->updateSubCategoria($id, $nombre, $id_categoria);
    } else {
        $subcategoriasModel->createSubCategoria($nombre, $id_categoria);
    }
}

if ($accion === 'delete') {
    $id = $_POST['id'];
    $result = $subcategoriasModel->delete($id);
    header('Content-Type: application/json');
    echo json_encode($result);
}

if ($accion === 'getSubCategorias') {
    /* recargar el contenido de la tabla (actualizar) */
    $subcategorias = $subcategoriasModel->getAllWithSubCategoria();

    foreach ($subcategorias as $subcategoria) {
        echo "<tr class='hover:bg-blue-50 transition'>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($subcategoria['id']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($subcategoria['nombre']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($subcategoria['categoria']) . "</td>";
        echo "<td class='px-4 py-2 flex gap-2'>";
        echo "<button class='bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition' onclick='editarSubCategoria(" . htmlspecialchars(json_encode($subcategoria)) . ")'>Editar</button>";
        echo "<button class='bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition' onclick='eliminarSubCategoria(" . htmlspecialchars($subcategoria['id']) . ")'>Eliminar</button>";
        echo "</td>";
        echo "</tr>";
    }
}
