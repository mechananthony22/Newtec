<?php
include_once __DIR__ . '/../models/DetalleCarrito.php';
?>

<header class="bg-white shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 md:text-xs flex items-center justify-between h-20 flex-nowrap">
        <!-- Logo -->
        <a href="/perunet/" class="flex items-center space-x-3 min-w-[60px]">
            <img src="/perunet/public/img/EMPRESA/newtec_logo.png?v=2.0" alt="NewTec Logo" class="object-contain max-h-12 w-auto max-w-[120px] sm:max-w-[160px] md:max-w-[200px]">
        </a>

        <!-- Buscador -->
        <form class="flex-1 mx-2 md:mx-8 flex items-center w-full max-w-[110px] md:max-w-2xl" id="form-busqueda">
            <input id="busqueda" name="busqueda" type="text" placeholder="Buscar productos, marcas..." class="w-full border-none rounded-l-lg px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 bg-gray-100 text-sm md:text-base" />
            <button id="buscar-btn" type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 md:px-4 py-2 rounded-r-lg transition-colors">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <!-- Iconos derecha -->
        <div class="flex items-center space-x-1 md:space-x-4 flex-shrink-0">
            <!-- Botón Dashboard Admin SOLO para admin -->
            <?php if (isset($_SESSION['usuario']['rol']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
                <a href="/perunet/admin" class="relative group mr-2" title="Dashboard Admin">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-red-600 rounded-full flex items-center justify-center hover:bg-red-700 transition-colors shadow-lg">
                        <i class="fas fa-gauge-high text-white text-xl md:text-2xl"></i>
                    </div>
                </a>
            <?php endif; ?>
            <!-- Carrito -->
            <a href="/perunet/carrito" class="relative group">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-red-600 rounded-full flex items-center justify-center hover:bg-red-700 transition-colors shadow-lg">
                    <i class="fas fa-shopping-cart text-white text-xl md:text-2xl"></i>
                </div>
                <span id="carrito-count" class="absolute -top-2 -right-2 bg-black text-white text-xs rounded-full px-2 py-0.5 font-bold shadow">
                    <?= isset($_SESSION['usuario']['id_us']) ? (new DetalleCarrito())->getProductsCount($_SESSION['usuario']['id_us']) : 0 ?>
                </span>
            </a>

            <!-- Perfil SIEMPRE visible -->
            <div class="relative flex-shrink-0">
                <button id="perfil-btn" class="w-10 h-10 md:w-12 md:h-12 bg-red-600 rounded-full flex items-center justify-center hover:bg-red-600 transition-colors shadow-lg focus:outline-none" title="Mi cuenta">
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <img
                            src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['usuario']['nombre'] . ' ' . ($_SESSION['usuario']['apellidos'] ?? '')) ?>&background=dc2626&color=fff&size=128"
                            alt="Avatar"
                            class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover border-2 border-red-100 shadow"
                            style="object-fit: cover;" />
                    <?php else: ?>
                        <i class="fas fa-user text-white text-xl md:text-2xl"></i>
                    <?php endif; ?>
                </button>
                <div id="perfil-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg py-2 z-50">
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <div class="px-4 py-2 text-gray-700 font-semibold border-b border-gray-100">
                            <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?>
                        </div>
                        <a href="/perunet/usuario/perfil" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Ver perfil</a>
                        <a href="/perunet/logout" class="block px-4 py-2 text-red-600 hover:bg-red-50">Cerrar sesión</a>
                    <?php else: ?>
                        <a href="/perunet/login" class="block px-4 py-2 text-red-600 hover:bg-red-50">Iniciar sesión</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Botón menú móvil SOLO visible en móvil -->
            <button id="menu-toggle" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-red-700 transition-colors shadow-lg focus:outline-none md:hidden">
                <i class="fas fa-bars text-white text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Menú principal (escritorio) -->
    <nav class="bg-gray-700 hidden md:block main-nav">
        <ul class="flex justify-center items-center al space-x-8" id="menu">
            <?php
            require_once __DIR__ . '/../models/SubcategoriasModel.php';
            require_once __DIR__ . '/../controllers/ProductoDetalleController.php';
            $categoriasModel = new SubcategoriasModel();
            $categorias_header = $categoriasModel->getCategoriesWithSubcategories();
            foreach ($categorias_header as $categoria) {
                if (empty($categoria['subcategorias'])) continue;
            ?>
                <li class="relative group">
                    <a href="<?= '/perunet/productos/' . ProductoDetalleController::slugify($categoria['nombre']) ?>" class="text-white py-4 px-6 block hover:bg-gray-800 transition-colors rounded-t">
                        <?= htmlspecialchars($categoria['nombre']) ?>
                    </a>
                    <ul class="absolute left-0 top-full bg-white shadow-lg rounded-b min-w-[220px] hidden group-hover:block z-50 animate-fade-in">
                        <?php foreach ($categoria['subcategorias'] as $subcategoria): ?>
                            <li>
                                <a href="<?= '/perunet/productos/' . ProductoDetalleController::slugify($categoria['nombre']) . '/' . ProductoDetalleController::slugify($subcategoria['nombre']) ?>" class="block px-6 py-3 text-gray-800 hover:bg-gray-100 transition-colors">
                                    <?= htmlspecialchars($subcategoria['nombre']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </nav>

    <!-- Menú móvil -->
    <nav id="menu-movil" class="bg-white border-t border-gray-200 fixed top-20 left-0 w-full hidden transition-all duration-300 z-50">
        <ul>
            <?php
            foreach ($categorias_header as $categoria) {
                if (empty($categoria['subcategorias'])) continue;
            ?>
                <li class="border-b border-gray-100">
                    <button class="w-full text-left px-6 py-4 text-gray-800 hover:bg-red-100 hover:text-red-600 text-lg font-medium flex justify-between items-center" onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <?= htmlspecialchars($categoria['nombre']) ?>
                        <span class="ml-2"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <ul class="hidden bg-gray-50">
                        <?php foreach ($categoria['subcategorias'] as $subcategoria): ?>
                            <li>
                                <a href="<?= '/perunet/productos/' . ProductoDetalleController::slugify($categoria['nombre']) . '/' . ProductoDetalleController::slugify($subcategoria['nombre']) ?>" class="block px-8 py-3 text-gray-700 hover:bg-gray-200 transition-colors">
                                    <?= htmlspecialchars($subcategoria['nombre']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </nav>

    <!-- Estilos personalizados -->
    <style>
        .group:hover .group-hover\:block {
            display: block !important;
        }

        .animate-fade-in {
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .main-nav {
                display: none !important;
            }

            /* Solo ocultamos el menú principal, NO el ícono de usuario */
        }
    </style>

    <!-- Script JS para el header -->
    <script>
        // Redirección desde el buscador
        document.getElementById('form-busqueda').addEventListener('submit', function(e) {
            e.preventDefault();
            var query = document.getElementById('busqueda').value.trim();
            if (query) {
                window.location.href = '/perunet/productos?busqueda=' + encodeURIComponent(query);
            }
        });

        // Dropdown de perfil
        document.addEventListener('DOMContentLoaded', function() {
            const perfilBtn = document.getElementById('perfil-btn');
            const perfilDropdown = document.getElementById('perfil-dropdown');
            const menuToggle = document.getElementById('menu-toggle');
            const menuMovil = document.getElementById('menu-movil');

            if (perfilBtn && perfilDropdown) {
                perfilBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    perfilDropdown.classList.toggle('hidden');
                });
                document.addEventListener('click', function(e) {
                    if (!perfilDropdown.contains(e.target) && !perfilBtn.contains(e.target)) {
                        perfilDropdown.classList.add('hidden');
                    }
                });
            }

            // Menú móvil toggle
            if (menuToggle && menuMovil) {
                menuToggle.addEventListener('click', function() {
                    menuMovil.classList.toggle('hidden');
                });
            }
        });

        // Función global para actualizar el contador del carrito por AJAX
        window.actualizarContadorCarrito = function() {
            $.get('/perunet/public/php/carrito.php', {
                accion: 'countTipos'
            }, function(res) {
                if (res && typeof res === 'object' && 'count' in res) {
                    $('#carrito-count').text(res.count);
                } else if (typeof res === 'string') {
                    try {
                        var data = JSON.parse(res);
                        if ('count' in data) $('#carrito-count').text(data.count);
                    } catch (e) {}
                }
            });
        }
        // Llamar al cargar la página
        $(document).ready(function() {
            window.actualizarContadorCarrito();
        });
        // Puedes llamar a window.actualizarContadorCarrito() después de agregar o eliminar productos para actualizar el contador en tiempo real.
    </script>
</header>