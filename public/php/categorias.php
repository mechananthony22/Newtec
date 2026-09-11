<?php
include_once __DIR__ . '/../../app/config/config.php';
include_once __DIR__ . '/../../app/core/Autoloader.php';
include_once __DIR__ . '/../../app/core/App.php';

// Initialize autoloader and application
Autoloader::getInstance();
App::getInstance();

// Include model files
include_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/models/CategoriasModel.php';

$categoriasModel = new CategoriasModel();

$accion = $_POST['accion'] ?? '';

if ($accion === 'create') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $estado = $_POST['estado'];

    if ($id) {
        $categoriasModel->updateCategoria($id, $nombre, $estado);
    } else {
        $categoriasModel->createCategoria($nombre, $estado);
    }
}

if ($accion === 'update') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $estado = $_POST['estado'];
    $categoriasModel->updateCategoria($id, $nombre, $estado);
}

if ($accion === 'delete') {
    $id = $_POST['id'];
    $categoriasModel->delete($id);
}

if ($accion === 'getCategorias') {
    /* recargar el contenido de la tabla (actualizar) */
    $categorias = $categoriasModel->getAll();

    foreach ($categorias as $categoria) {
        echo "<tr class='hover:bg-blue-50 transition'>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($categoria['id_cat']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($categoria['nombre']) . "</td>";
        echo "<td class='px-4 py-2 flex gap-2'>";
        echo "<button class='bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition' onclick='editarCategoria(" . htmlspecialchars(json_encode($categoria)) . ")'>Editar</button>";
        echo "<button class='bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition' onclick='eliminarCategoria(" . htmlspecialchars($categoria['id_cat']) . ")'>Eliminar</button>";
        echo "</td>";
        echo "</tr>";
    }
}

if ($accion === 'getAllforSubCategorias') {
    /* recargar el contenido de la tabla (actualizar) */
    $categorias = $categoriasModel->getAll();
    $categoria_id = $_POST['id_categoria'];

    echo "<select id='categoria_id_form' name='categoria_id_form' class='w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700' required>";
    echo "<option value=''>-- Selecciona una categoría --</option>";
    foreach ($categorias as $categoria) {
        $selected = ($categoria['id_cat'] == $categoria_id) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($categoria['id_cat']) . "' " . $selected . ">" . htmlspecialchars($categoria['nombre']) . "</option>";
    }
    echo "</select>";
}
