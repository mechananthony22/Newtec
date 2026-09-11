<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Eliminar require_once de modelos y base de datos

class AuthController
{

    public function index()
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function showRegisterForm()
    {
        require_once __DIR__ . '/../views/auth/registro.php';
    }

    //PONER PREVENCION DE ATAKES SQL , XSSS
    public function login($email, $password)
    {
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->findByEmail($email);

        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            $_SESSION['usuario'] = [
                'id_us'  => $usuario['id_us'],
                'nombre' => $usuario['nombre'],
                'correo' => $usuario['correo'],
                'rol'    => $usuario['rol']
            ];

            // Redirección según el rol
            if ($usuario['rol'] === 'admin') {
                header('Location: /perunet/admin');
            } else {
                header('Location: /perunet/perunet');
            }
            exit;
        } else {
            $_SESSION['error'] = "Correo o contraseña incorrectos.";
            header('Location: /perunet/login');
            exit;
        }
    }

    public function register($nombre, $apellidos, $correo, $dni, $telefono, $password)
    {
        // Validación de la contraseña
        if (strlen($password) < 8) {
            $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres.";
            header('Location: /perunet/registro');
            exit;
        }

        $usuarioModel = new UsuarioModel();

        // Preparar los datos para el modelo
        $data = [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'correo' => $correo,
            'contrasena' => password_hash($password, PASSWORD_BCRYPT),
            'dni' => $dni,
            'telefono' => $telefono,
            'id_rol' => 2, // Rol de usuario por defecto
            'estado' => 'activo', // O 'pendiente' si requiere verificación
            'codigo_verificacion' => str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT)
        ];

        try {
            if ($usuarioModel->create($data)) {
                $_SESSION['mensaje'] = "Registro exitoso. Ahora puedes iniciar sesión.";
                header('Location: /perunet/login');
                exit;
            } else {
                $_SESSION['error'] = "No se pudo completar el registro. Inténtalo de nuevo.";
                header('Location: /perunet/registro');
                exit;
            }
        } catch (PDOException $e) {
            // El error más común es por duplicidad de correo o DNI
            $_SESSION['error'] = "Este correo o DNI ya están registrados.";
            header('Location: /perunet/registro');
            exit;
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /perunet/perunet');
        exit;
    }
}
