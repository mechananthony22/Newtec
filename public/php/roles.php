<?php
include_once __DIR__ . '/../../app/config/config.php';
include_once __DIR__ . '/../../app/core/Autoloader.php';
include_once __DIR__ . '/../../app/core/App.php';

// Initialize autoloader and application
Autoloader::getInstance();
App::getInstance();

// Include model files
include_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/models/RolesModel.php';

$roleModel = new RolesModel();

$accion = $_POST['accion'] ?? '';

if ($accion === 'create') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $estado = $_POST['estado'];

    if ($id) {
        $roleModel->updateRol($id, $nombre, $estado);
    } else {
        $roleModel->createRol($nombre, $estado);
    }
}

if ($accion === 'update') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $estado = $_POST['estado'];
    $roleModel->updateRol($id, $nombre, $estado);
}

if ($accion === 'delete') {
    $id = $_POST['id'];
    $roleModel->delete($id);
}

if ($accion === 'getRoles') {
    /* recargar el contenido de la tabla (actualizar) */
    $roles = $roleModel->getAll();

    foreach ($roles as $rol) {
        echo "<tr class='hover:bg-blue-50 transition'>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($rol['id_rol']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($rol['nombre']) . "</td>";
        echo "<td class='px-4 py-2'>";
        echo "<span class='inline-flex px-3 py-1 text-xs font-semibold rounded-full " . ((htmlspecialchars($rol['estado']) === 'activo') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . "'>";
        echo ucfirst(htmlspecialchars($rol['estado']));
        echo "</span>";
        echo "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . date('d/m/Y H:i', strtotime($rol['create_at'])) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . date('d/m/Y H:i', strtotime($rol['update_at'])) . "</td>";
        echo "<td class='px-4 py-2 flex gap-2'>";
        echo "<button class='bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition' onclick='editarRol(" . htmlspecialchars(json_encode($rol)) . ")'>Editar</button>";
        echo "<button class='bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition' onclick='eliminarRol(" . htmlspecialchars($rol['id_rol']) . ")'>Eliminar</button>";
        echo "</td>";
        echo "</tr>";
    }
}
