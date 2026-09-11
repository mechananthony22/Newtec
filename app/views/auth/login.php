<?php
// Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NewTec - Inicio de Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/perunet/public/css/auth.css">

</head>

<body class="bg-red-50 min-h-screen flex items-center justify-center tech-pattern">
    <div class="max-w-md w-full mx-4">
        <!-- Logo y Título -->
        <div class="text-center mb-8">
            <img src="/perunet/public/img/EMPRESA/newtec_logo.png?v=2.0" alt="Logo NewTec" class="w-48 mx-auto mb-4">
        </div>

        <!-- Tarjeta de Login -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="tech-gradient h-2"></div>

            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Iniciar Sesión</h2>

                <?php if (isset($_SESSION['error'])): ?>
                    <div id="error-alert"
                        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error:</strong>
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error']); ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div id="success-alert"
                        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['mensaje']); ?></span>
                    </div>
                    <?php unset($_SESSION['mensaje']); ?>
                <?php endif; ?>


                <form id="loginForm" method="POST" action="" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo
                            Electrónico</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="email" name="email" required
                                class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent input-focus transition duration-200"
                                placeholder="tucorreo@ejemplo.com">
                        </div>
                        <p id="emailError" class="mt-1 text-sm text-red-600 hidden">Por favor ingresa un correo válido
                        </p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" required
                                class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent input-focus transition duration-200"
                                placeholder="••••••••">
                        </div>
                        <p id="passwordError" class="mt-1 text-sm text-red-600 hidden">La contraseña es requerida</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">Recordarme</label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-red-600 hover:text-red-500">¿Olvidaste tu
                                contraseña?</a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" name="login" id="submitBtn"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200 transform hover:scale-[1.01]">
                            <span id="btnText">Ingresar</span>
                            <i id="btnSpinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">¿No tienes una cuenta?</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="/perunet/registro"
                            class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                            Registrarse
                        </a>
                    </div>
                </div>
            </div>

            <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">
                    Al continuar, aceptas nuestros <a href="#" class="text-red-600 hover:text-red-500">Términos de
                        Servicio</a> y <a href="#" class="text-red-600 hover:text-red-500">Política de Privacidad</a>.
                </p>
            </div>
        </div>
    </div>

    <script src="/perunet/public/js/auth.js"></script>
</body>

</html>