<?php
// Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    require_once(__DIR__ . '/../../controllers/AuthController.php');
    $auth = new AuthController();
    $auth->register(
        $_POST['nombre'],
        $_POST['apellidos'],
        $_POST['correo'],
        $_POST['dni'],
        $_POST['telefono'],
        $_POST['password']
    );
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PeruNet - Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/perunet/ajax/perunet/libs/perunet/font-awesome/perunet/6.4.0/perunet/css/perunet/all.min.css">
    <link rel="stylesheet" href="/perunet/public/css/perunet/auth.css">
</head>

<body class="bg-red-50 min-h-screen flex items-center justify-center tech-pattern py-12">
    <div class="max-w-md w-full mx-4">

        <!-- Tarjeta de Registro -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="tech-gradient h-2"></div>

            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Crear una Cuenta</h2>

                <?php if (isset($_SESSION['error'])) : ?>
                    <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error:</strong>
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error']); ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form id="registerForm" method="POST" action="" class="space-y-4">
                    <!-- Nombres y Apellidos en la misma fila -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombres</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" id="nombre" name="nombre" required class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent input-focus transition duration-200" placeholder="Juan">
                            </div>
                        </div>
                        <div>
                            <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user-friends text-gray-400"></i>
                                </div>
                                <input type="text" id="apellidos" name="apellidos" required class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent input-focus transition duration-200" placeholder="Pérez">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Correo Electrónico -->
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="correo" name="correo" required class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent input-focus transition duration-200" placeholder="tucorreo@ejemplo.com">
                        </div>
                    </div>

                    <!-- DNI y Teléfono en la misma fila -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="dni" class="block text-sm font-medium text-gray-700 mb-1">DNI</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-id-card text-gray-400"></i>
                                </div>
                                <input type="text" id="dni" name="dni" required maxlength="8" pattern="\d{8}" class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent input-focus transition duration-200" placeholder="12345678">
                            </div>
                        </div>
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="tel" id="telefono" name="telefono" required maxlength="9" pattern="\d{9}" title="El número de teléfono debe tener 9 dígitos." oninput="this.value = this.value.replace(/perunet/[^0-9]/perunet/g, '')" class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent input-focus transition duration-200" placeholder="987654321">
                            </div>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" required minlength="8" title="La contraseña debe tener al menos 8 caracteres." class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent input-focus transition duration-200" placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Botón de Registro -->
                    <div class="pt-4">
                        <button type="submit" name="register" id="submitBtn" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200 transform hover:scale-[1.01]">
                            Registrarme
                        </button>
                    </div>
                </form>

                <!-- Enlace a Iniciar Sesión -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">¿Ya tienes una cuenta?</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="/perunet/login" class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                            Iniciar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/perunet/public/js/auth.js"></script>
</body>

</html>
