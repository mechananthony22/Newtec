<?php
/**
 * Punto de entrada principal de la aplicación PeruNet
 * 
 * Este archivo carga el bootstrap y ejecuta la aplicación
 */

// Cargar el bootstrap de la aplicación
require_once __DIR__ . '/app/bootstrap.php';

// Obtener la instancia de la aplicación
$app = App::getInstance();

// Configurar las rutas
$router = $app->getRouter();

// ===========================
// 🌐 RUTAS PÚBLICAS
// ===========================

// Página principal
$router->addRoute('GET', '/', function() {
    $controller = new IndexController();
    return $controller->index();
});

// Contacto
$router->addRoute('GET', '/contacto', function() {
    $controller = new ContactoController();
    return $controller->index();
});

// Sedes
$router->addRoute('GET', '/sedes', function() {
    $controller = new SedesController();
    return $controller->index();
});

// Carrito
$router->addRoute('GET', '/carrito', function() {
    $controller = new CarritoController();
    return $controller->index();
});

// ===========================
// 📦 RUTAS DE PRODUCTOS (PÚBLICO)
// ===========================

// Lista de productos con búsqueda
$router->addRoute('GET', '/productos', function() {
    $controller = new ProductosController();
    return $controller->index();
});

// Lista de productos por categoría
$router->addRoute('GET', '/productos/:categoria', function($categoria) {
    $controller = new ProductosController();
    return $controller->indexCategoria($categoria);
});

// Lista de productos por subcategoría
$router->addRoute('GET', '/productos/:categoria/:subcategoria', function($categoria, $subcategoria) {
    $controller = new ProductosController();
    return $controller->indexSubcategoria($categoria, $subcategoria);
});

// Detalle de producto
$router->addRoute('GET', '/producto/:categoria/:subcategoria/:id_producto', function($categoria, $subcategoria, $id_producto) {
    $controller = new ProductoDetalleController();
    return $controller->index($categoria, $subcategoria, $id_producto);
});

// ===========================
// 🛒 RUTAS DE VENTAS (PÚBLICO)
// ===========================

// Confirmar compra
$router->addRoute('GET', '/confirmar/compra', function() {
    AuthMiddleware::checkAuth(); // <-- Proteger esta ruta
    $controller = new VentaController();
    return $controller->index();
});

// ===========================
// 🔐 RUTAS DE AUTENTICACIÓN
// ===========================

// Login
$router->addRoute('GET', '/login', function() {
    $controller = new AuthController();
    return $controller->index();
});

$router->addRoute('POST', '/login', function() {
    $controller = new AuthController();
    // Se corrige para pasar los parámetros directamente, como en el registro.
    return $controller->login($_POST['email'], $_POST['password']);
});

// Registro
$router->addRoute('GET', '/registro', function() {
    $controller = new AuthController();
    return $controller->showRegisterForm();
});

$router->addRoute('POST', '/registro', function() {
    $controller = new AuthController();
    return $controller->register(
        $_POST['nombre'],
        $_POST['apellidos'],
        $_POST['correo'],
        $_POST['dni'],
        $_POST['telefono'],
        $_POST['password']
    );
});

// Logout
$router->addRoute('GET', '/logout', function() {
    $controller = new AuthController();
    return $controller->logout();
});

$router->addRoute('GET', '/usuario/perfil', function() {
    AuthMiddleware::checkAuth(); // <-- Proteger esta ruta
    require_once __DIR__ . '/app/controllers/UsuarioController.php';
    $controller = new UsuarioController();
    return $controller->perfil();
});

$router->addRoute('GET', '/usuarios/compra/:id', function($id) {
    AuthMiddleware::checkAuth(); // <-- Proteger esta ruta
    require_once __DIR__ . '/app/controllers/UsuarioController.php';
    $controller = new UsuarioController();
    return $controller->detalleCompra($id);
});

$router->addRoute('GET', '/usuarios/tracking/:id', function($id) {
    AuthMiddleware::checkAuth(); // <-- Proteger esta ruta
    require_once __DIR__ . '/app/controllers/UsuarioController.php';
    $controller = new UsuarioController();
    return $controller->tracking($id);
});

// ===========================
// 🛠️ RUTAS BUILDER (PÚBLICO)
// ===========================

// Builder principal
$router->addRoute('GET', '/builder', function() {
    require_once __DIR__ . '/app/controllers/BuilderController.php';
    require_once __DIR__ . '/app/models/BuilderModel.php';
    require_once __DIR__ . '/app/models/ProductoModel.php';
    $controller = new BuilderController();
    return $controller->index();
});

// PC Builder
$router->addRoute('GET', '/builder/pc', function() {
    require_once __DIR__ . '/app/controllers/BuilderController.php';
    require_once __DIR__ . '/app/models/BuilderModel.php';
    require_once __DIR__ . '/app/models/ProductoModel.php';
    $controller = new BuilderController();
    return $controller->pc();
});

// Setup Builder
$router->addRoute('GET', '/builder/setup', function() {
    require_once __DIR__ . '/app/controllers/BuilderController.php';
    require_once __DIR__ . '/app/models/BuilderModel.php';
    require_once __DIR__ . '/app/models/ProductoModel.php';
    $controller = new BuilderController();
    return $controller->setup();
});

// Agregar configuración al carrito
$router->addRoute('POST', '/builder/add-to-cart', function() {
    require_once __DIR__ . '/app/controllers/BuilderController.php';
    require_once __DIR__ . '/app/models/BuilderModel.php';
    require_once __DIR__ . '/app/models/ProductoModel.php';
    require_once __DIR__ . '/app/models/DetalleCarrito.php';
    $controller = new BuilderController();
    return $controller->addToCart();
});

// ===========================
// 🛠️ RUTAS DASHBOARD (ADMIN)
// ===========================

// Dashboard principal
$router->addRoute('GET', '/admin', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new DashboardController();
    return $controller->index();
});

// ===========================
// 🛠️ RUTAS CONFIGURACIÓN (ADMIN)
// ===========================

// Roles
$router->addRoute('GET', '/admin/config/roles', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\RolesController();
    return $controller->index();
});

// Marcas
$router->addRoute('GET', '/admin/config/marcas', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\MarcasController();
    return $controller->index();
});

// Categorías
$router->addRoute('GET', '/admin/config/categorias', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\CategoriasController();
    return $controller->index();
});

// Subcategorías
$router->addRoute('GET', '/admin/config/subcategorias', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\SubcategoriasController();
    return $controller->index();
});

// Modelos
$router->addRoute('GET', '/admin/config/modelos', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\ModelosController();
    return $controller->index();
});

// ===========================
// 📦 RUTAS CRUD PRODUCTOS (ADMIN)
// ===========================

// Lista de productos
$router->addRoute('GET', '/admin/productos', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\ProductosController();
    return $controller->index();
});

// Crear producto
$router->addRoute('GET', '/admin/productos/crear', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\ProductosController();
    return $controller->crear();
});

// Guardar producto
$router->addRoute('POST', '/admin/productos/guardar', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\ProductosController();
    return $controller->guardar();
});

// Editar producto
$router->addRoute('GET', '/admin/productos/editar/:id', function($id) {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\ProductosController();
    return $controller->editar($id);
});

// Actualizar producto
$router->addRoute('POST', '/admin/productos/actualizar', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\ProductosController();
    return $controller->actualizar();
});

// Eliminar producto
$router->addRoute('GET', '/admin/productos/eliminar/:id', function($id) {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\ProductosController();
    return $controller->eliminar($id);
});

// ===========================
// 👥 RUTAS USUARIOS (ADMIN)
// ===========================

// Lista de usuarios
$router->addRoute('GET', '/admin/usuarios', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\UsuariosController();
    return $controller->index();
});

// Crear usuario
$router->addRoute('GET', '/admin/usuarios/crear', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\UsuariosController();
    return $controller->crear();
});

// Guardar usuario
$router->addRoute('POST', '/admin/usuarios/guardar', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\UsuariosController();
    return $controller->guardar();
});

// Editar usuario
$router->addRoute('GET', '/admin/usuarios/editar/:id', function($id) {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\UsuariosController();
    return $controller->editar($id);
});

// Actualizar usuario
$router->addRoute('POST', '/admin/usuarios/actualizar', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\UsuariosController();
    return $controller->actualizar();
});

// Eliminar usuario
$router->addRoute('GET', '/admin/usuarios/eliminar/:id', function($id) {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\UsuariosController();
    return $controller->eliminar($id);
});

// ===========================
// 📦 RUTAS CRUD VENTAS (ADMIN)
// ===========================

// Lista de ventas
$router->addRoute('GET', '/admin/ventas', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\VentasController();
    return $controller->index();
});

// Detalle de venta
$router->addRoute('GET', '/admin/ventas/detalle/:id', function($id) {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\VentasController();
    return $controller->detalle($id);
});

// Cambiar estado de venta (AJAX)
$router->addRoute('POST', '/admin/ventas/cambiar-estado', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\VentasController();
    return $controller->cambiarEstado();
});

// Reporte de ventas
$router->addRoute('GET', '/admin/ventas/reporte', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\VentasController();
    return $controller->reportePorFecha();
});

// Resumen estadístico
$router->addRoute('GET', '/admin/ventas/resumen', function() {
    AuthMiddleware::checkAdmin(); // <-- Proteger esta ruta de admin
    $controller = new Admin\VentasController();
    return $controller->resumenEstadistico();
});

// ===========================
// 🚀 EJECUTAR LA APLICACIÓN
// ===========================

try {
    // Ejecutar el router
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Exception $e) {
    // Log del error
    error_log("Error en la aplicación: " . $e->getMessage());
    
    // Mostrar error apropiado
    if (DEBUG_MODE) {
        echo "<h1>Error de la aplicación</h1>";
        echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
        echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
        echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        include APP_VIEWS . '/errors/500.php';
    }
}
