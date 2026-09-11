<?php

namespace Admin;

use UsuarioModel;
use RolesModel;

class UsuariosController
{
    private $usuarioModel;
    private $rolesModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->rolesModel = new RolesModel();
    }

    // Mostrar todos los usuarios
    public function index()
    {
        $nombre = $_GET['buscar'] ?? null;
        $usuarios = $this->usuarioModel->getAll($nombre);
        $roles = $this->rolesModel->getAll();
        require_once __DIR__ . '/../../views/admin/usuarios/index.php';
    }

    // Mostrar formulario de creación
    public function crear()
    {
        $roles = $this->rolesModel->getAll();
        require_once __DIR__ . '/../../views/admin/usuarios/form.php';
    }

    // Guardar nuevo usuario
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'              => $_POST['nombre'] ?? '',
                'apellidos'           => $_POST['apellidos'] ?? '',
                'correo'              => $_POST['correo'] ?? '',
                'contrasena'          => password_hash($_POST['contrasena'], PASSWORD_DEFAULT),
                'dni'                 => $_POST['dni'] ?? '',
                'telefono'            => $_POST['telefono'] ?? '',
                'id_rol'              => $_POST['id_rol'] ?? 2,
                'estado'              => $_POST['estado'] ?? 'pendiente',
                'codigo_verificacion' => $_POST['codigo_verificacion'] ?? null
            ];

            $this->usuarioModel->create($data);
            header("Location: /perunet/admin/perunet/usuarios?mensaje=guardado");
        }
    }

    // Editar usuario existente
    public function editar($id)
    {
        // usar directamente el $id
        $usuario = $this->usuarioModel->getById($id);
        $roles = $this->rolesModel->getAll();
        require_once __DIR__ . '/../../views/admin/usuarios/form.php';
    }

    // Actualizar usuario
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'              => $_POST['nombre'] ?? '',
                'apellidos'           => $_POST['apellidos'] ?? '',
                'correo'              => $_POST['correo'] ?? '',
                'telefono'            => $_POST['telefono'] ?? '',
                'id_rol'              => $_POST['id_rol'] ?? 2,
                'estado'              => $_POST['estado'] ?? 'pendiente',
                'codigo_verificacion' => $_POST['codigo_verificacion'] ?? null
            ];

            $this->usuarioModel->update($_POST['id_us'], $data);
            header("Location: /perunet/admin/perunet/usuarios?mensaje=actualizado");
        }
    }

    // Eliminar usuario
    public function eliminar($id)
    {
        $this->usuarioModel->delete($id);
        header("Location: /perunet/admin/perunet/usuarios?mensaje=eliminado");
        exit;
    }
}
