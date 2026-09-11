<!-- <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - Admin' : 'Admin - ' . APP_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="<?= TAILWIND_CDN ?>"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/admin.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 min-h-screen flex-shrink-0">
            <div class="p-4">
                <div class="flex items-center space-x-2 mb-8">
                    <i class="fas fa-laptop text-blue-400 text-2xl"></i>
                    <span class="text-xl font-bold">PeruNet Admin</span>
                </div>
                
                <!-- Navigation -->
                <nav class="space-y-2">
                    <a href="<?= APP_URL ?>/admin" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/config') === false && strpos($_SERVER['REQUEST_URI'], '/admin/productos') === false && strpos($_SERVER['REQUEST_URI'], '/admin/usuarios') === false && strpos($_SERVER['REQUEST_URI'], '/admin/ventas') === false ? 'bg-blue-600' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="<?= APP_URL ?>/admin/productos" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/productos') !== false ? 'bg-blue-600' : '' ?>">
                        <i class="fas fa-box"></i>
                        <span>Productos</span>
                    </a>
                    
                    <a href="<?= APP_URL ?>/admin/usuarios" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/usuarios') !== false ? 'bg-blue-600' : '' ?>">
                        <i class="fas fa-users"></i>
                        <span>Usuarios</span>
                    </a>
                    
                    <a href="<?= APP_URL ?>/admin/ventas" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/ventas') !== false ? 'bg-blue-600' : '' ?>">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Ventas</span>
                    </a>
                    
                    <!-- Configuration Section -->
                    <div class="pt-4 border-t border-gray-700">
                        <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                            Configuración
                        </h3>
                        
                        <a href="<?= APP_URL ?>/admin/config/categorias" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/config/categorias') !== false ? 'bg-blue-600' : '' ?>">
                            <i class="fas fa-tags"></i>
                            <span>Categorías</span>
                        </a>
                        
                        <a href="<?= APP_URL ?>/admin/config/subcategorias" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/config/subcategorias') !== false ? 'bg-blue-600' : '' ?>">
                            <i class="fas fa-tag"></i>
                            <span>Subcategorías</span>
                        </a>
                        
                        <a href="<?= APP_URL ?>/admin/config/marcas" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/config/marcas') !== false ? 'bg-blue-600' : '' ?>">
                            <i class="fas fa-industry"></i>
                            <span>Marcas</span>
                        </a>
                        
                        <a href="<?= APP_URL ?>/admin/config/modelos" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/config/modelos') !== false ? 'bg-blue-600' : '' ?>">
                            <i class="fas fa-cube"></i>
                            <span>Modelos</span>
                        </a>
                        
                        <a href="<?= APP_URL ?>/admin/config/roles" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/config/roles') !== false ? 'bg-blue-600' : '' ?>">
                            <i class="fas fa-user-shield"></i>
                            <span>Roles</span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900"><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?></h1>
                        <p class="text-gray-600"><?= isset($pageDescription) ? $pageDescription : 'Panel de administración' ?></p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                3
                            </span>
                        </button>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <button class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 transition-colors">
                                <img src="<?= APP_URL ?>/public/assets/img/avatar.png" alt="Avatar" class="w-8 h-8 rounded-full">
                                <span><?= htmlspecialchars($session['user_name'] ?? 'Admin') ?></span>
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>
                            <!-- Dropdown menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                                <a href="<?= APP_URL ?>/admin/perfil" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Mi Perfil
                                </a>
                                <a href="<?= APP_URL ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-home mr-2"></i>Ver Sitio
                                </a>
                                <a href="<?= APP_URL ?>/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 p-6">
                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="<?= APP_URL ?>/public/assets/js/admin.js"></script>
    
    <!-- Additional Scripts -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>  -->
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>