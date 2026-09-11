<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error del servidor - <?= APP_NAME ?></title>
    <script src="<?= TAILWIND_CDN ?>"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="mb-8">
            <i class="fas fa-server text-8xl text-red-500 mb-4"></i>
            <h1 class="text-6xl font-bold text-gray-900 mb-4">500</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Error del servidor</h2>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                Lo sentimos, ha ocurrido un error interno en el servidor. 
                Nuestro equipo técnico ha sido notificado.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="<?= APP_URL ?>" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-home mr-2"></i>
                Volver al inicio
            </a>
            
            <div class="text-sm text-gray-500">
                <p>Si el problema persiste, contacta con soporte:</p>
                <div class="mt-2">
                    <a href="mailto:soporte@perunet.com" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-envelope mr-1"></i>soporte@perunet.com
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 