<?php
session_name('perunet_session'); // Usa el mismo nombre que en tu config
session_start();
require_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/models/UsuarioModel.php';

$id = $_SESSION['usuario']['id'] ?? null;
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'No autenticado']);
    exit;
}

$model = new UsuarioModel();
$usuarioActual = $model->getById($id);

$data = [
    'nombre' => trim($_POST['nombre'] ?? ''),
    'apellidos' => trim($_POST['apellidos'] ?? ''),
    'correo' => trim($_POST['correo'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? ''),
    'dni' => trim($_POST['dni'] ?? '')
];

// Validaciones extra
if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $data['nombre'])) {
    echo json_encode(['status' => 'error', 'message' => 'Nombre inválido']); exit;
}
if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $data['apellidos'])) {
    echo json_encode(['status' => 'error', 'message' => 'Apellidos inválidos']); exit;
}
if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Correo inválido']); exit;
}
if (!preg_match('/^\d{9}$/', $data['telefono'])) {
    echo json_encode(['status' => 'error', 'message' => 'Teléfono inválido']); exit;
}
if (!preg_match('/^\d{8}$/', $data['dni'])) {
    echo json_encode(['status' => 'error', 'message' => 'DNI inválido']); exit;
}

// Solo actualizar si hay cambios
$cambios = false;
foreach ($data as $k => $v) {
    if ($usuarioActual[$k] != $v) {
        $cambios = true;
        break;
    }
}

// Validar y actualizar contraseña solo si se envía y es diferente
if (!empty($_POST['password'])) {
    $newPass = $_POST['password'];
    if (strlen($newPass) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'La contraseña debe tener al menos 6 caracteres']); exit;
    }
    $data['contrasena'] = password_hash($newPass, PASSWORD_DEFAULT);
    $cambios = true;
}

if (!$cambios) {
    echo json_encode(['status' => 'info', 'message' => 'No hay cambios para actualizar']);
    exit;
}

$ok = $model->update($id, $data);

if ($ok) {
    echo json_encode(['status' => 'success', 'message' => 'Datos actualizados']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar']);
} 