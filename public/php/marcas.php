<?php

require_once __DIR__ . '/../../app/models/MarcasModel.php';

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(dirname(__DIR__)) . '/app');
}

$marcasModel = new MarcasModel();

$accion = $_POST['accion'] ?? '';

if ($accion === 'create') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $estado = $_POST['estado'];

    if ($id) {
        $marcasModel->updateMarca($id, $nombre, $estado);
    } else {
        $marcasModel->createMarca($nombre, $estado);
    }
}

if ($accion === 'update') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $estado = $_POST['estado'];
    $marcasModel->updateMarca($id, $nombre, $estado);
}

if ($accion === 'delete') {
    $id = $_POST['id'];
    $marcasModel->delete($id);
}

if ($accion === 'getMarcas') {
    /* recargar el contenido de la tabla (actualizar) */
    $marcas = $marcasModel->getAll();

    foreach ($marcas as $marca) {
        echo "<tr class='hover:bg-blue-50 transition'>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($marca['id_mar']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($marca['nombre']) . "</td>";
        echo "<td class='px-4 py-2 flex gap-2'>";
        echo "<button class='bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition' onclick='editarMarca(" . htmlspecialchars(json_encode($marca)) . ")'>Editar</button>";
        echo "<button class='bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition' onclick='eliminarMarca(" . htmlspecialchars($marca['id_mar']) . ")'>Eliminar</button>";
        echo "</td>";
        echo "</tr>";
    }
}

if ($accion === 'getAllforModelos') {
    $marcas = $marcasModel->getAll();
    $marca_id = $_POST['marca_id'];

    echo "<select id='marca_id_form' name='marca_id_form' class='w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700' required>";
    echo "<option value=''>-- Selecciona una marca --</option>";
    foreach ($marcas as $marca) {
        $selected = ($marca['id_mar'] == $marca_id) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($marca['id_mar']) . "' $selected>" . htmlspecialchars($marca['nombre']) . "</option>";
    }
    echo "</select>";
}