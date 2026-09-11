# CODIGO FUENTE - PERUNET (Tienda Online)

Sistema de tienda online "Perunet" desarrollado en PHP (patrón MVC).

**Estructura del proyecto:**

```
Perunet/
├── index.php              → Punto de entrada principal (front controller)
├── router.php             → Router para el servidor de desarrollo PHP
├── .htaccess              → Configuración de Apache
├── app/                   → Núcleo de la aplicación (MVC)
│   ├── bootstrap.php       → Arranque de la aplicación
│   ├── core/               → Clases base (App, Controller, Model, Autoloader)
│   ├── config/             → Configuración (BD, rutas)
│   ├── middleware/         → Middleware de autenticación
│   ├── controllers/        → Controladores (lógica de negocio)
│   ├── models/             → Modelos (acceso a datos)
│   ├── views/              → Vistas (HTML)
│   └── components/         → Componentes reutilizables (header, footer, etc.)
├── public/                 → Archivos públicos (assets, AJAX)
│   └── php/                → Endpoints AJAX
└── lib/                    → Librerías externas (FPDF)
```

## 1. RAÍZ DEL PROYECTO

### index.php

```php
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

```

### router.php

```php
<?php

class Router
{
    private $routes = [];
    private $route_root;
    private $route_404;

    public function __construct(string $route_root, string $route_404 = '')
    {
        $this->route_root = rtrim($route_root, '/');
        $this->route_404 = $route_404;
    }

    public function addRoute(string $method, string $path, callable $callback)
    {
        // Crear patrón regex con parámetros nombrados (ej: :id => (?P<id>[^/]+))
        $pattern = preg_replace('/:([a-zA-Z0-9_]+)/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '/?$#';

        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'callback' => $callback,
            'original_path' => $path
        ];
    }

    public function dispatch(string $method, string $uri)
    {
        $method = strtoupper($method);
        $path = rtrim(parse_url($uri, PHP_URL_PATH), '/') ?: '/';

        // Quitar el prefijo base (ej: /perunet)
        if ($this->route_root !== '/' && stripos($path, $this->route_root) === 0) {
            $path = substr($path, strlen($this->route_root));
            $path = $path === '' ? '/' : $path;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['pattern'], $path, $matches)) {
                // Extraer solo parámetros con nombre (ej: id)
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return call_user_func_array($route['callback'], $params);
            }
        }

        return $this->notFound();
    }

    private function notFound()
    {
        http_response_code(404);
        if ($this->route_404 && file_exists($this->route_404)) {
            include($this->route_404);
        } else {
            echo "<h1>404 - Página no encontrada</h1>";
            echo "<p>La ruta solicitada no existe.</p>";
            echo "<a href='/perunet'>Volver al inicio</a>";
        }
    }
}

// error_log('Router cargado');
// $router->addRoute('GET', '/usuario/perfil', function() {
//     error_log('Ruta /usuario/perfil registrada');
//     require_once __DIR__ . '/app/controllers/UsuarioController.php';
//     $controller = new UsuarioController();
//     $controller->perfil();
// });

// error_log('Dispatch ejecutado: ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI']);
// $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

```

### .htaccess

```php
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    
    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Disable directory browsing
Options -Indexes

# Protect against XSS attacks
<IfModule mod_headers.c>
    Header set X-XSS-Protection "1; mode=block"
    Header always append X-Frame-Options SAMEORIGIN
    Header set X-Content-Type-Options nosniff
</IfModule>

```

## 2. app (NÚCLEO DE LA APLICACIÓN)

### 2.1 bootstrap.php

```php
<?php
/**
 * Bootstrap de la aplicación - Punto de entrada principal
 */

// Definir la ruta raíz de la aplicación
define('APP_ROOT', dirname(__DIR__));

// Cargar configuración
require_once APP_ROOT . '/app/config/config.php';

// Inicializar autoloader
require_once APP_ROOT . '/app/core/Autoloader.php';
Autoloader::getInstance();

// Cargar clases core
require_once APP_ROOT . '/app/core/App.php';
require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Model.php';

// Cargar router
require_once APP_ROOT . '/router.php';

// Cargar el middleware de autenticación
require_once __DIR__ . '/middleware/AuthMiddleware.php';

// Función helper para obtener la instancia de la aplicación
function app()
{
    return App::getInstance();
}

// Función helper para obtener la base de datos
function db()
{
    return app()->getDatabase();
}

// Función helper para obtener la sesión
function session()
{
    return app()->getSession();
}

// Función helper para redirigir
function redirect($url)
{
    header("Location: " . APP_URL . $url);
    exit();
}

// Función helper para generar URL
function url($path = '')
{
    return APP_URL . '/' . ltrim($path, '/');
}

// Función helper para generar asset URL
function asset($path = '')
{
    return APP_URL . '/public/assets/' . ltrim($path, '/');
}

// Función helper para escapar HTML
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Función helper para formatear precio
function formatPrice($price)
{
    return number_format($price, 2, '.', ',');
}

// Función helper para formatear fecha
function formatDate($date, $format = 'd/m/Y')
{
    return date($format, strtotime($date));
}

// Función helper para validar si el usuario está autenticado
function isAuthenticated()
{
    return isset(session()['user_id']);
}

// Función helper para validar si el usuario es admin
function isAdmin()
{
    return isAuthenticated() && isset(session()['user_role']) && session()['user_role'] === 'admin';
}

// Función helper para obtener el usuario actual
function currentUser()
{
    if (!isAuthenticated()) {
        return null;
    }
    
    $userModel = new UsuarioModel();
    return $userModel->find(session()['user_id']);
}

// Función helper para generar token CSRF
function csrfToken()
{
    $app = app();
    return $app->generateCSRFToken();
}

// Función helper para validar token CSRF
function validateCSRF()
{
    $app = app();
    return $app->validateCSRF();
}

// Función helper para mostrar mensajes flash
function flash($key, $message = null)
{
    if ($message === null) {
        $message = session()['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    
    $_SESSION['flash'][$key] = $message;
}

// Función helper para mostrar errores de validación
function errors($field = null)
{
    $errors = session()['errors'] ?? [];
    
    if ($field === null) {
        return $errors;
    }
    
    return $errors[$field] ?? null;
}

// Función helper para obtener datos antiguos del formulario
function old($field, $default = '')
{
    $old = session()['old'] ?? [];
    return $old[$field] ?? $default;
}

// Función helper para verificar si hay errores
function hasErrors()
{
    return !empty(session()['errors']);
}

// Función helper para verificar si hay mensajes flash
function hasFlash()
{
    return !empty(session()['flash']);
}

// Función helper para obtener el carrito
function cart()
{
    return new CarritoModel();
}

// Función helper para obtener el total del carrito
function cartTotal()
{
    return cart()->getTotal();
}

// Función helper para obtener la cantidad de items en el carrito
function cartCount()
{
    return cart()->getCount();
}

// Función helper para verificar si el carrito está vacío
function cartIsEmpty()
{
    return cartCount() === 0;
}

// Función helper para obtener categorías
function getCategories()
{
    $model = new CategoriasModel();
    return $model->all();
}

// Función helper para obtener marcas
function getBrands()
{
    $model = new MarcasModel();
    return $model->all();
}

// Función helper para obtener productos destacados
function getFeaturedProducts($limit = 8)
{
    $model = new ProductoModel();
    return $model->getFeatured($limit);
}

// Función helper para obtener productos por categoría
function getProductsByCategory($categoryId, $limit = 12)
{
    $model = new ProductoModel();
    return $model->getByCategory($categoryId, $limit);
}

// Función helper para obtener un producto por ID
function getProduct($id)
{
    $model = new ProductoModel();
    return $model->find($id);
}

// Función helper para obtener estadísticas del admin
function getAdminStats()
{
    if (!isAdmin()) {
        return [];
    }
    
    $stats = [];
    
    // Productos
    $productModel = new ProductoModel();
    $stats['total_products'] = $productModel->count();
    
    // Usuarios
    $userModel = new UsuarioModel();
    $stats['total_users'] = $userModel->count();
    
    // Ventas
    $ventaModel = new VentaModel();
    $stats['total_sales'] = $ventaModel->count();
    $stats['total_revenue'] = $ventaModel->getTotalRevenue();
    
    // Carrito
    $carritoModel = new CarritoModel();
    $stats['cart_items'] = $carritoModel->getCount();
    
    return $stats;
}

// Función helper para logging
// function log($message, $level = 'info')
// {
//     if (DEBUG_MODE) {
//         $logFile = APP_ROOT . '/logs/app.log';
//         $timestamp = date('Y-m-d H:i:s');
//         $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        
//         if (!is_dir(dirname($logFile))) {
//             mkdir(dirname($logFile), 0755, true);
//         }
        
//         file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
//     }
// }

// Función helper para debugging
function dd($var)
{
    if (DEBUG_MODE) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
        exit;
    }
}

// Función helper para debugging sin exit
function dump($var)
{
    if (DEBUG_MODE) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}

// Configurar manejo de errores
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configurar timezone
date_default_timezone_set('America/Lima');

// Configurar locale
setlocale(LC_ALL, 'es_PE.UTF-8');

// Inicializar la aplicación
$app = App::getInstance(); 

```

## 3. app/core (CLASES BASE)

### app/core/App.php

```php
<?php
/**
 * Clase principal de la aplicación MVC
 */
class App
{
    private static $instance = null;
    private $router;
    private $db;
    private $session;

    private function __construct()
    {
        // Cargar configuración
        require_once __DIR__ . '/../config/config.php';
        // Incluir clase Router
        require_once __DIR__ . '/../../router.php';
        
        // Inicializar componentes
        $this->initSession();
        $this->initDatabase();
        $this->initRouter();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
        $this->session = $_SESSION;
    }

    private function initDatabase()
    {
        try {
            $this->db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
            } else {
                die("Error de conexión a la base de datos");
            }
        }
    }

    private function initRouter()
    {
        $this->router = new Router(ROUTE_BASE, APP_VIEWS . '/errors/404.php');
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getDatabase()
    {
        return $this->db;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function setSession($key, $value)
    {
        $_SESSION[$key] = $value;
        $this->session[$key] = $value;
    }

    public function unsetSession($key)
    {
        unset($_SESSION[$key]);
        unset($this->session[$key]);
    }

    public function destroySession()
    {
        session_destroy();
        $this->session = [];
    }

    public function run()
    {
        try {
            $this->router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
        } catch (Exception $e) {
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
    }
} 
```

### app/core/Autoloader.php

```php
<?php
/**
 * Autoloader eficiente para la aplicación MVC
 */
class Autoloader
{
    private static $instance = null;
    private $classMap = [];
    private $loadedClasses = [];

    private function __construct()
    {
        $this->buildClassMap();
        spl_autoload_register([$this, 'loadClass']);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construye el mapa de clases para carga rápida
     */
    private function buildClassMap()
    {
        $this->classMap = [
            // Core classes
            'App' => APP_ROOT . '/app/core/App.php',
            'Controller' => APP_ROOT . '/app/core/Controller.php',
            'Model' => APP_ROOT . '/app/core/Model.php',
            'Router' => APP_ROOT . '/router.php',
            'Database' => APP_ROOT . '/app/core/Database.php',
            
            // Controllers públicos
            'IndexController' => APP_ROOT . '/app/controllers/IndexController.php',
            'ProductosController' => APP_ROOT . '/app/controllers/ProductosController.php',
            'ProductoDetalleController' => APP_ROOT . '/app/controllers/ProductoDetalleController.php',
            'CarritoController' => APP_ROOT . '/app/controllers/CarritoController.php',
            'VentaController' => APP_ROOT . '/app/controllers/VentaController.php',
            'AuthController' => APP_ROOT . '/app/controllers/AuthController.php',
            'ContactoController' => APP_ROOT . '/app/controllers/ContactoController.php',
            'SedesController' => APP_ROOT . '/app/controllers/SedesController.php',
            'MarcaController' => APP_ROOT . '/app/controllers/MarcaController.php',
            'SubcategoriaController' => APP_ROOT . '/app/controllers/SubcategoriaController.php',
            
            // Controllers del admin
            'DashboardController' => APP_ROOT . '/app/controllers/Admin/DashboardController.php',
            'Admin\ProductosController' => APP_ROOT . '/app/controllers/Admin/ProductosController.php',
            'Admin\UsuariosController' => APP_ROOT . '/app/controllers/Admin/UsuariosController.php',
            'Admin\VentasController' => APP_ROOT . '/app/controllers/Admin/VentasController.php',
            'Admin\RolesController' => APP_ROOT . '/app/controllers/Admin/RolesController.php',
            'Admin\MarcasController' => APP_ROOT . '/app/controllers/Admin/MarcasController.php',
            'Admin\CategoriasController' => APP_ROOT . '/app/controllers/Admin/CategoriasController.php',
            'Admin\SubcategoriasController' => APP_ROOT . '/app/controllers/Admin/SubcategoriasController.php',
            'Admin\ModelosController' => APP_ROOT . '/app/controllers/Admin/ModelosController.php',
            
            // Models
            'ProductoModel' => APP_ROOT . '/app/models/ProductoModel.php',
            'CategoriasModel' => APP_ROOT . '/app/models/CategoriasModel.php',
            'SubcategoriasModel' => APP_ROOT . '/app/models/SubcategoriasModel.php',
            'MarcasModel' => APP_ROOT . '/app/models/MarcasModel.php',
            'ModelosModel' => APP_ROOT . '/app/models/ModelosModel.php',
            'RolesModel' => APP_ROOT . '/app/models/RolesModel.php',
            'UsuarioModel' => APP_ROOT . '/app/models/UsuarioModel.php',
            'CarritoModel' => APP_ROOT . '/app/models/CarritoModel.php',
            'VentaModel' => APP_ROOT . '/app/models/VentaModel.php',
            'AdminVentasModel' => APP_ROOT . '/app/models/AdminVentasModel.php',
            'DetalleCarrito' => APP_ROOT . '/app/models/DetalleCarrito.php',
            'MetodoPagoModel' => APP_ROOT . '/app/models/MetodoPagoModel.php',
            'SucursalModel' => APP_ROOT . '/app/models/SucursalModel.php',
        ];
    }

    /**
     * Carga una clase automáticamente
     */
    public function loadClass($className)
    {
        // Evitar cargar la misma clase múltiples veces
        if (isset($this->loadedClasses[$className])) {
            return;
        }

        // Buscar en el mapa de clases
        if (isset($this->classMap[$className])) {
            $file = $this->classMap[$className];
            if (file_exists($file)) {
                require_once $file;
                $this->loadedClasses[$className] = true;
                return;
            }
        }

        // Buscar por convención de nombres
        $file = $this->findClassFile($className);
        if ($file && file_exists($file)) {
            require_once $file;
            $this->loadedClasses[$className] = true;
            return;
        }

        // Si no se encuentra, registrar para debugging
        if (DEBUG_MODE) {
            error_log("Clase no encontrada: $className");
        }
    }

    /**
     * Busca un archivo de clase por convención de nombres
     */
    private function findClassFile($className)
    {
        // Patrones de búsqueda
        $patterns = [
            // Controllers
            '/Controller$/' => APP_ROOT . '/app/controllers/{name}.php',
            '/Controller$/' => APP_ROOT . '/app/controllers/Admin/{name}.php',
            
            // Models
            '/Model$/' => APP_ROOT . '/app/models/{name}.php',
            
            // Core classes
            '/^(App|Controller|Model|Router)$/' => APP_ROOT . '/app/core/{name}.php',
            '/^(App|Controller|Model|Router)$/' => APP_ROOT . '/{name}.php',
        ];

        foreach ($patterns as $pattern => $template) {
            if (preg_match($pattern, $className)) {
                $name = preg_replace($pattern, '', $className);
                $file = str_replace('{name}', $name, $template);
                if (file_exists($file)) {
                    return $file;
                }
            }
        }

        return null;
    }

    /**
     * Registra una clase manualmente
     */
    public function registerClass($className, $filePath)
    {
        $this->classMap[$className] = $filePath;
    }

    /**
     * Obtiene estadísticas de carga
     */
    public function getLoadStats()
    {
        return [
            'loaded_classes' => count($this->loadedClasses),
            'class_map_size' => count($this->classMap),
            'loaded_classes_list' => array_keys($this->loadedClasses)
        ];
    }
} 
```

### app/core/Controller.php

```php
<?php
/**
 * Clase base para todos los controladores
 */
abstract class Controller
{
    protected $app;
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->app = App::getInstance();
        $this->db = $this->app->getDatabase();
        $this->session = $this->app->getSession();
    }

    /**
     * Renderiza una vista con datos
     */
    protected function render($view, $data = [])
    {
        // Extraer los datos para que estén disponibles en la vista
        extract($data);
        
        // Incluir la vista
        $viewPath = APP_VIEWS . '/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new Exception("Vista no encontrada: " . $viewPath);
        }
        
        ob_start();
        include $viewPath;
        $content = ob_get_clean();
        
        return $content;
    }

    /**
     * Renderiza una vista completa con layout
     */
    protected function renderWithLayout($view, $data = [], $layout = 'default')
    {
        $content = $this->render($view, $data);
        
        // Incluir el layout
        $layoutPath = APP_VIEWS . '/layouts/' . $layout . '.php';
        
        if (!file_exists($layoutPath)) {
            throw new Exception("Layout no encontrado: " . $layoutPath);
        }
        
        // Hacer el contenido disponible en el layout
        $layoutData = array_merge($data, ['content' => $content]);
        extract($layoutData);
        
        include $layoutPath;
    }

    /**
     * Redirige a otra URL
     */
    protected function redirect($url)
    {
        $fullUrl = APP_URL . $url;
        header("Location: " . $fullUrl);
        exit();
    }

    /**
     * Devuelve respuesta JSON
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Valida si el usuario está autenticado
     */
    protected function requireAuth()
    {
        if (!isset($this->session['user_id'])) {
            $this->redirect('/login');
        }
    }

    /**
     * Valida si el usuario tiene un rol específico
     */
    protected function requireRole($role)
    {
        $this->requireAuth();
        
        if (!isset($this->session['user_role']) || $this->session['user_role'] !== $role) {
            $this->redirect('/admin');
        }
    }

    /**
     * Obtiene datos POST limpios
     */
    protected function getPostData()
    {
        return array_map('trim', $_POST);
    }

    /**
     * Valida token CSRF
     */
    protected function validateCSRF()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST[CSRF_TOKEN_NAME]) || 
                !isset($this->session[CSRF_TOKEN_NAME]) || 
                $_POST[CSRF_TOKEN_NAME] !== $this->session[CSRF_TOKEN_NAME]) {
                $this->redirect('/error/csrf');
            }
        }
    }

    /**
     * Genera token CSRF
     */
    protected function generateCSRFToken()
    {
        $token = bin2hex(random_bytes(32));
        $this->app->setSession(CSRF_TOKEN_NAME, $token);
        return $token;
    }

    /**
     * Valida y sanitiza datos de entrada
     */
    protected function validateInput($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) || empty($data[$field])) {
                if (strpos($rule, 'required') !== false) {
                    $errors[$field] = "El campo $field es requerido";
                }
                continue;
            }
            
            $value = $data[$field];
            
            // Validar email
            if (strpos($rule, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "El campo $field debe ser un email válido";
            }
            
            // Validar longitud mínima
            if (preg_match('/min:(\d+)/', $rule, $matches)) {
                $min = $matches[1];
                if (strlen($value) < $min) {
                    $errors[$field] = "El campo $field debe tener al menos $min caracteres";
                }
            }
            
            // Validar longitud máxima
            if (preg_match('/max:(\d+)/', $rule, $matches)) {
                $max = $matches[1];
                if (strlen($value) > $max) {
                    $errors[$field] = "El campo $field debe tener máximo $max caracteres";
                }
            }
        }
        
        return $errors;
    }
} 
```

### app/core/Model.php

```php
<?php
/**
 * Clase base para todos los modelos
 */
abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct()
    {
        require_once __DIR__ . '/App.php';
        $this->db = App::getInstance()->getDatabase();
    }

    /**
     * Obtiene todos los registros
     */
    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un registro por ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtiene registros con paginación
     */
    public function paginate($page = 1, $perPage = ITEMS_PER_PAGE)
    {
        $offset = ($page - 1) * $perPage;
        
        // Contar total de registros
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute();
        $total = $countStmt->fetch()['total'];
        
        // Obtener registros
        $sql = "SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }

    /**
     * Crea un nuevo registro
     */
    public function create($data)
    {
        $data = $this->filterFillable($data);
        
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $fieldList = implode(', ', $fields);
        
        $sql = "INSERT INTO {$this->table} ({$fieldList}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza un registro
     */
    public function update($id, $data)
    {
        $data = $this->filterFillable($data);
        
        $fields = array_keys($data);
        $setClause = implode(' = :', $fields) . ' = :' . implode(', ', $fields);
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Elimina un registro
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Busca registros por condiciones
     */
    public function where($conditions, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Busca un registro por condiciones
     */
    public function whereFirst($conditions, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions} LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Ejecuta una consulta personalizada
     */
    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ejecuta una consulta personalizada que devuelve un solo registro
     */
    public function queryFirst($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Filtra los datos por los campos fillable
     */
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Obtiene el último ID insertado
     */
    public function getLastInsertId()
    {
        return $this->db->lastInsertId();
    }

    /**
     * Inicia una transacción
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    /**
     * Confirma una transacción
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     * Revierte una transacción
     */
    public function rollback()
    {
        return $this->db->rollback();
    }

    /**
     * Verifica si existe un registro
     */
    public function exists($id)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch()['count'] > 0;
    }

    /**
     * Cuenta registros
     */
    public function count($conditions = null, $params = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }

    /**
     * Obtiene la conexión a la base de datos
     */
    public function getDb()
    {
        return $this->db;
    }
} 
```

## 4. app/config (CONFIGURACIÓN)

### app/config/config.php

```php
<?php
/**
 * Configuración principal de la aplicación MVC
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'tienda_online');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de la aplicación
define('APP_NAME', 'NewTec');
define('APP_URL', 'http://localhost/perunet');
if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__ . '/../');
}
define('APP_VIEWS', APP_ROOT . '/app/views');
define('APP_CONTROLLERS', APP_ROOT . '/app/controllers');
define('APP_MODELS', APP_ROOT . '/app/models');
define('APP_COMPONENTS', APP_ROOT . '/app/components');

// Configuración de rutas
define('ROUTE_BASE', '/perunet');

// Configuración de sesión
define('SESSION_NAME', 'perunet_session');
define('SESSION_LIFETIME', 3600); // 1 hora

// Configuración de seguridad
define('CSRF_TOKEN_NAME', 'csrf_token');

// Configuración de archivos
define('UPLOAD_PATH', APP_ROOT . '/public/uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Configuración de paginación
define('ITEMS_PER_PAGE', 10);

// Configuración de Tailwind
define('TAILWIND_ENABLED', true);
define('TAILWIND_CDN', 'https://cdn.tailwindcss.com');

// Configuración de desarrollo
define('DEBUG_MODE', true);
define('ERROR_REPORTING', E_ALL);

if (DEBUG_MODE) {
    error_reporting(ERROR_REPORTING);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
} 
```

## 5. app/middleware

### app/middleware/AuthMiddleware.php

```php
<?php

class AuthMiddleware
{
    /**
     * Verifica si el usuario ha iniciado sesión.
     * Si no está autenticado, lo redirige a la página de login.
     */
    public static function checkAuth()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            header('Location: /perunet/login');
            exit;
        }
    }

    /**
     * Verifica si el usuario es un administrador.
     * Si no está autenticado o no tiene el rol 'admin', lo redirige.
     */
    public static function checkAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Primero, verifica si está logueado.
        if (!isset($_SESSION['usuario'])) {
            header('Location: /perunet/login');
            exit;
        }

        // Luego, verifica si es administrador.
        if ($_SESSION['usuario']['rol'] !== 'admin') {
            // Si no es admin, lo redirigimos a la página principal (o a una de error).
            header('Location: /');
            exit;
        }
    }
} 
```

## 6. app/controllers (CONTROLADORES - BACKEND)

### app/controllers/AuthController.php

```php
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

```

### app/controllers/IndexController.php

```php
<?php

class IndexController extends Controller
{
    private $productoModel;
    private $categoriasModel;
    private $subcategoriasModel;

    public function __construct()
    {
        parent::__construct();
        $this->productoModel = new ProductoModel();
        $this->categoriasModel = new CategoriasModel();
        $this->subcategoriasModel = new SubcategoriasModel();
    }

    public function index()
    {
        $id_categoria = $_GET['categoria'] ?? null;
        $id_subcategoria = $_GET['subcategoria'] ?? null;
        
        // Cargar productos destacados
        $productos = $this->productoModel->getProductosDestacados(12, $id_categoria, $id_subcategoria);
        
        // Cargar categorías
        $categorias = $this->categoriasModel->getAll();
        
        // Cargar subcategorías
        $subcategorias = $this->subcategoriasModel->getAll();
        
        // Obtener estadísticas del carrito
        $cartCount = cartCount();
        
        // Renderizar la vista usando el layout default
        $this->renderWithLayout('public/index', [
            'productos' => $productos,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'cartCount' => $cartCount,
            'session' => $this->session
        ]);
    }
}

```

### app/controllers/ProductosController.php

```php
<?php
require_once __DIR__ . '/../models/ProductoModel.php';
require_once __DIR__ . '/../models/SubcategoriasModel.php';

class ProductosController extends Controller
{
    private $productoModel;
    private $categoriasModel;
    private $subcategoriasModel;

    public function __construct()
    {
        parent::__construct();
        $this->productoModel = new ProductoModel();
        $this->categoriasModel = new CategoriasModel();
        $this->subcategoriasModel = new SubcategoriasModel();
    }

    // Lista de productos con búsqueda
    public function index()
    {
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
        $marcas = isset($_GET['marca']) ? (array)$_GET['marca'] : [];
        $precioMin = isset($_GET['precio_min']) ? (float)$_GET['precio_min'] : null;
        $precioMax = isset($_GET['precio_max']) ? (float)$_GET['precio_max'] : null;

        if ($busqueda) {
            $productos = $this->productoModel->search($busqueda, $marcas, $precioMin, $precioMax);
        } else {
            $productos = $this->productoModel->getAll();
        }

        $categorias = $this->categoriasModel->getAll();
        $marcasList = $this->productoModel->getMarcas();

        // Filtrar subcategorías para mostrar solo las que tienen productos (solo cuando hay productos)
        if (!empty($productos)) {
            $subcategoriasConProductos = [];
            foreach ($productos as $prod) {
                if (!empty($prod['subcategoria']) && !in_array($prod['subcategoria'], array_column($subcategoriasConProductos, 'nombre'))) {
                    $subcategoriasConProductos[] = ['nombre' => $prod['subcategoria']];
                }
            }
            $subcategorias = $subcategoriasConProductos;
        } else {
            // Si no hay productos, no mostrar subcategorías
            $subcategorias = [];
        }

        // Variables para la vista
        $nombreCategoria = $busqueda ?: null;
        $nombreSubcategoria = null;
        
        // Generar el contenido de la vista
        ob_start();
        require_once __DIR__ . '/../views/productos/index.php';
        $content = ob_get_clean();
        
        // Incluir el layout con el contenido
        include __DIR__ . '/../views/layouts/default.php';
    }

    public function indexCategoria($categoria)
    {
        // Obtener filtros de la URL
        $marcas = isset($_GET['marca']) ? (array)$_GET['marca'] : [];
        $precioMin = isset($_GET['precio_min']) ? (float)$_GET['precio_min'] : null;
        $precioMax = isset($_GET['precio_max']) ? (float)$_GET['precio_max'] : null;

        // Obtener información de la categoría
        $categoriaInfo = $this->categoriasModel->getBySlug($categoria);
        
        if (!$categoriaInfo) {
            $this->redirect('/404');
        }
        
        // Obtener productos de la categoría
        $categoriaId = isset($categoriaInfo['id']) ? $categoriaInfo['id'] : (isset($categoriaInfo['id_cat']) ? $categoriaInfo['id_cat'] : null);
        if ($categoriaId === null) {
            $this->redirect('/404');
        }
        $productos = $this->productoModel->getByCategory($categoriaId, $marcas, $precioMin, $precioMax);
        
        // Obtener subcategorías de esta categoría
        $subcategorias = $this->subcategoriasModel->getByCategory($categoriaId);
        // Normalizar estructura
        $subcategorias = array_map(function($s) {
            return is_array($s) && isset($s['nombre']) ? $s : ['nombre' => is_array($s) ? ($s['nombre'] ?? '') : $s];
        }, $subcategorias);
        
        // Obtener estadísticas del carrito
        $cartCount = cartCount();
        
        $this->renderWithLayout('productos/index', [
            'title' => $categoriaInfo['nombre'] . ' - PeruNet',
            'description' => 'Productos de ' . $categoriaInfo['nombre'],
            'productos' => $productos,
            'categoria' => $categoriaInfo,
            'subcategorias' => $subcategorias,
            'cartCount' => $cartCount,
            'session' => $this->session,
            'nombreCategoria' => $categoriaInfo['nombre'],
            'nombreSubcategoria' => null
        ]);
    }

    public function indexSubcategoria($categoria, $subcategoria)
    {
        // Obtener filtros de la URL
        $marcas = isset($_GET['marca']) ? (array)$_GET['marca'] : [];
        $precioMin = isset($_GET['precio_min']) ? (float)$_GET['precio_min'] : null;
        $precioMax = isset($_GET['precio_max']) ? (float)$_GET['precio_max'] : null;

        // Debug temporal
        error_log('Slug recibido: categoria=' . $categoria . ' | subcategoria=' . $subcategoria);
        // Obtener información de la categoría
        $categoriaInfo = $this->categoriasModel->getBySlug($categoria);
        
        if (!$categoriaInfo) {
            error_log('Categoría no encontrada para slug: ' . $categoria);
            $this->redirect('/404');
        }
        
        // Obtener información de la subcategoría
        $catId = isset($categoriaInfo['id']) ? $categoriaInfo['id'] : (isset($categoriaInfo['id_cat']) ? $categoriaInfo['id_cat'] : null);
        $subcategoriaInfo = $this->subcategoriasModel->getBySlug($subcategoria, $catId);
        
        if (!$subcategoriaInfo) {
            error_log('Subcategoría no encontrada para slug: ' . $subcategoria . ' en categoría ID: ' . $catId);
            $this->redirect('/404');
        }
        
        // Obtener productos de la subcategoría
        $subcategoriaId = isset($subcategoriaInfo['id']) ? $subcategoriaInfo['id'] : null;
        if ($subcategoriaId === null) {
            $this->redirect('/404');
        }
        $productos = $this->productoModel->getBySubcategory($subcategoriaId, $marcas, $precioMin, $precioMax);
        
        // Obtener subcategorías de esta categoría
        $subcategorias = $this->subcategoriasModel->getByCategory($catId);
        // Normalizar estructura
        $subcategorias = array_map(function($s) {
            return is_array($s) && isset($s['nombre']) ? $s : ['nombre' => is_array($s) ? ($s['nombre'] ?? '') : $s];
        }, $subcategorias);
        
        // Obtener estadísticas del carrito
        $cartCount = cartCount();
        
        $this->renderWithLayout('productos/index', [
            'title' => $subcategoriaInfo['nombre'] . ' - ' . $categoriaInfo['nombre'] . ' - PeruNet',
            'description' => 'Productos de ' . $subcategoriaInfo['nombre'],
            'productos' => $productos,
            'categoria' => $categoriaInfo,
            'subcategoria' => $subcategoriaInfo,
            'cartCount' => $cartCount,
            'session' => $this->session,
            'nombreCategoria' => $categoriaInfo['nombre'],
            'nombreSubcategoria' => $subcategoriaInfo['nombre']
        ]);
    }
}

```

### app/controllers/ProductoDetalleController.php

```php
<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/ProductoModel.php';


class ProductoDetalleController extends Controller
{

    private $productoModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
    }

    public function index($categoria, $subcategoria, $id_producto)
    {

        // validar si es un numero
        if (!filter_var($id_producto, FILTER_VALIDATE_INT)) {
            include(__DIR__ . '/../views/page_404.php');
            exit;
        }

        // obtener datos del producto
        $id_usuario = isset($_SESSION['usuario']['id_us']) ? $_SESSION['usuario']['id_us'] : null;
        $producto = $this->productoModel->getById($id_producto, $id_usuario);


        if (!$producto || (self::slugify($producto['categoria']) != $categoria || self::slugify($producto['subcategoria']) != $subcategoria)) {
            include(__DIR__ . '/../views/page_404.php');
            exit;
        }

        // si el stock es menor a 1, mostrar "Agotado"
        $msgStock = $producto['stock_disponible'] < 1 ? 'Agotado' : $producto['stock_disponible'];

        $this->renderWithLayout('productos/productoSelecionado', [
            'producto' => $producto,
            'msgStock' => $msgStock
        ]);
    }

    // limpiar los nombres de la url dinamicas
    public static function slugify(string $text): string
    {
        // Convertir a minúsculas usando mb_string para soporte UTF-8
        $text = mb_strtolower(trim($text), 'UTF-8');

        // Reemplazar caracteres con tilde o especiales
        $text = strtr($text, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ñ' => 'n',
            'Á' => 'a',
            'É' => 'e',
            'Í' => 'i',
            'Ó' => 'o',
            'Ú' => 'u',
            'Ñ' => 'n',
            'ä' => 'a',
            'ë' => 'e',
            'ï' => 'i',
            'ö' => 'o',
            'ü' => 'u',
            'Ä' => 'a',
            'Ë' => 'e',
            'Ï' => 'i',
            'Ö' => 'o',
            'Ü' => 'u',
            'ç' => 'c',
            'Ç' => 'c'
        ]);

        // Reemplazar cualquier cosa que no sea letra o número por un guion
        $text = preg_replace('/[^a-z0-9]+/u', '-', $text);

        // Eliminar guiones repetidos y al inicio/final
        $text = preg_replace('/-+/', '-', $text);
        $text = trim($text, '-');

        return $text;
    }
}

```

### app/controllers/CarritoController.php

```php
<?php

class CarritoController
{
    private $detalleCarrito;
    private $metodoPago;

    public function __construct()
    {
        $this->detalleCarrito = new DetalleCarrito();
        $this->metodoPago = new MetodoPagoModel();
    }

    public function index()
    {
        $id_usuario = isset($_SESSION['usuario']['id']) ? $_SESSION['usuario']['id'] : null;

        // si el usuario esta logueado
        if ($id_usuario != null) {

            // eliminar productos sin stock del carrito del usuario
            $remove_out_of_stock = $this->detalleCarrito->removeOutOfStockItems($id_usuario);

            // mensaje si los productos ya no cumplen con stock suficiente
            if ($remove_out_of_stock > 0) {
                $_SESSION['mensaje'] = "Se eliminaron {$remove_out_of_stock} productos sin stock suficiente.";
            }

            // obtener los productos del carrito del usuario
            $carrito = $this->detalleCarrito->getItems($id_usuario);
            $precio_total = $this->detalleCarrito->getTotal($id_usuario);
        }

        $metodos = $this->metodoPago->getAll();
        include(__DIR__ . '/../views/ventas/carrito.php');
    }

    public function vaciarCarrito()
    {
        $this->detalleCarrito->delete($_SESSION['usuario']['id']);
        header("Location: /perunet/carrito");
    }
}

```

### app/controllers/VentaController.php

```php
<?php


// falta implementar - implementar
class VentaController
{
    private $model;
    private $detalleCarrito;
    private $metodoPago;
    private $usuario;
    private $sucursal;

    public function __construct()
    {
        $this->model = new VentaModel();
        $this->detalleCarrito = new DetalleCarrito();
        $this->metodoPago = new MetodoPagoModel();
        $this->usuario = new UsuarioModel();
        $this->sucursal = new SucursalModel();
    }

    public function index()
    {

        // verificar si el carrito esta vacio
        $empty_cart = $this->detalleCarrito->isEmpty($_SESSION['usuario']['id_us']);

        if ($empty_cart) {
            $_SESSION['mensaje'] = "El carrito esta vacio.";
            header("Location: /perunet/carrito");
            exit();
        }

        try {
            $usuarioId = isset($_SESSION['usuario']['id_us']) ? $_SESSION['usuario']['id_us'] : null;
            if ($usuarioId === null) {
                // Manejar el caso donde no hay usuario logueado
                $usuario = null;
            } else {
                $usuario = $this->usuario->getById($usuarioId);
            }
            $metodos = $this->metodoPago->getAll();
            $sucursales = $this->sucursal->getAll();
        } catch (Exception $e) {
            $usuario = null;
            $metodos = [];
            $sucursales = [];
            // Aquí podrías loguear el error o mostrar un mensaje
        }
        require_once __DIR__ . '/../views/ventas/index.php';
    }
}

```

### app/controllers/UsuarioController.php

```php
<?php
class UsuarioController {
    public function perfil() {
        // Verificar sesión
        $usuarioId = $_SESSION['usuario']['id_us'] ?? null;
        if (!$usuarioId) {
            header('Location: /perunet/login');
            exit;
        }
        require_once __DIR__ . '/../models/UsuarioModel.php';
        require_once __DIR__ . '/../models/VentaModel.php';
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->getById($usuarioId);
        $ventaModel = new VentaModel();
        $compras = $ventaModel->getByUsuario($usuarioId);
        require __DIR__ . '/../views/usuarios/perfil.php';
    }

    public function detalleCompra($id) {
        $usuarioId = $_SESSION['usuario']['id_us'] ?? null;
        if (!$usuarioId) {
            header('Location: /perunet/login');
            exit;
        }
        require_once __DIR__ . '/../models/VentaModel.php';
        $ventaModel = new VentaModel();
        $detalle = $ventaModel->getDetalleById($id, $usuarioId);
        if (!$detalle) {
            echo "No tienes acceso a este comprobante.";
            exit;
        }
        require __DIR__ . '/../views/usuarios/detalle_compra.php';
    }

    public function tracking($id) {
        $usuarioId = $_SESSION['usuario']['id_us'] ?? null;
        if (!$usuarioId) {
            header('Location: /perunet/login');
            exit;
        }
        require_once __DIR__ . '/../models/VentaModel.php';
        $ventaModel = new VentaModel();
        $detalle = $ventaModel->getDetalleById($id, $usuarioId);
        
        if (!$detalle || empty($detalle)) {
            // Redirigir o mostrar error si no se encuentra la venta
            header('Location: /perunet/perfil');
            exit;
        }

        // Extraer datos generales de la venta del primer registro
        $venta = $detalle[0];
        
        require __DIR__ . '/../views/usuarios/tracking.php';
    }
}
```

### app/controllers/ContactoController.php

```php
<?php

/* falta implementar */
class ContactoController
{
    public function index()
    {
        include(__DIR__ . '/../views/public/contacto.php');
    }
}

```

### app/controllers/SedesController.php

```php
<?php

/* falta implementar */
class SedesController {
    public function index() {
        include(__DIR__ . '/../views/public/sedes.php');
    }
    
}

?>
```

### app/controllers/MarcaController.php

```php
<?php

class MarcaController
{
    private $model;

    public function __construct()
    {
        $this->model = new MarcasModel();
    }

    public function index()
    {
        $marcas = $this->model->getAll();
        require_once __DIR__ . '/../views/marca_list.php'; // Solo si deseas listar
    }
}

```

### app/controllers/SubcategoriaController.php

```php
<?php

class SubcategoriaController
{
    private $model;

    public function __construct()
    {
        $this->model = new SubcategoriasModel();
    }

    public function index()
    {
        $subcategorias = $this->model->getAll();
        require_once __DIR__ . '/../views/subcategoria_list.php'; // Solo si deseas listar
    }
}

```

### app/controllers/BuilderController.php

```php
<?php

class BuilderController
{
    private $model;
    private $productoModel;

    public function __construct()
    {
        $this->model = new BuilderModel();
        $this->productoModel = new ProductoModel();
    }

    /**
     * Vista principal del builder
     */
    public function index()
    {
        require_once __DIR__ . '/../views/builder/index.php';
    }

    /**
     * PC Builder
     */
    public function pc()
    {
        $categories = $this->model->getCategoriesByType('pc');
        $currentStep = isset($_GET['step']) ? (int)$_GET['step'] : 1;
        
        // Obtener la categoría actual
        $currentCategory = null;
        if ($currentStep > 0 && $currentStep <= count($categories)) {
            $currentCategory = $categories[$currentStep - 1];
            $products = $this->model->getProductsByBuilderCategory($currentCategory['id_cat']);
        } else {
            $products = [];
        }

        require_once __DIR__ . '/../views/builder/pc_builder.php';
    }

    /**
     * Setup Builder
     */
    public function setup()
    {
        $categories = $this->model->getCategoriesByType('setup');
        $currentStep = isset($_GET['step']) ? (int)$_GET['step'] : 1;
        
        // Obtener la categoría actual
        $currentCategory = null;
        if ($currentStep > 0 && $currentStep <= count($categories)) {
            $currentCategory = $categories[$currentStep - 1];
            $products = $this->model->getProductsByBuilderCategory($currentCategory['id_cat']);
        } else {
            $products = [];
        }

        require_once __DIR__ . '/../views/builder/setup_builder.php';
    }

    /**
     * Obtener productos por categoría (AJAX)
     */
    public function getProducts()
    {
        header('Content-Type: application/json');
        
        if (!isset($_GET['category_id'])) {
            echo json_encode(['error' => 'Category ID required']);
            return;
        }

        $categoryId = $_GET['category_id'];
        $products = $this->model->getProductsByBuilderCategory($categoryId);
        
        echo json_encode($products);
    }

    /**
     * Agregar configuración al carrito
     */
    public function addToCart()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /perunet/builder');
            exit;
        }

        $usuarioId = $_SESSION['usuario']['id_us'] ?? null;
        if (!$usuarioId) {
            $_SESSION['mensaje'] = "Debes iniciar sesión para agregar al carrito.";
            header('Location: /perunet/login');
            exit;
        }

        // Obtener productos seleccionados del POST (ahora con cantidades)
        $productsData = json_decode($_POST['products'] ?? '[]', true);
        
        if (empty($productsData)) {
            $_SESSION['mensaje'] = "No has seleccionado ningún producto.";
            header('Location: /perunet/builder');
            exit;
        }

        try {
            $carritoModel = new DetalleCarrito();
            $addedCount = 0;
            
            // Agregar cada producto al carrito con su cantidad
            foreach ($productsData as $item) {
                $productId = $item['id'] ?? $item;
                $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                
                // Validar cantidad
                if ($quantity < 1) $quantity = 1;
                if ($quantity > 10) $quantity = 10;
                
                $producto = $this->model->getProductById($productId);
                if ($producto && $producto['stock'] >= $quantity) {
                    $carritoModel->agregarProducto($usuarioId, $productId, $quantity);
                    $addedCount++;
                }
            }

            if ($addedCount > 0) {
                $_SESSION['mensaje'] = "Se agregaron $addedCount productos al carrito exitosamente.";
            } else {
                $_SESSION['mensaje'] = "No se pudo agregar ningún producto. Verifica el stock.";
            }
            
            header('Location: /perunet/carrito');
            exit;
        } catch (Exception $e) {
            $_SESSION['mensaje'] = "Error al agregar al carrito: " . $e->getMessage();
            header('Location: /perunet/builder');
            exit;
        }
    }
}

```

## 7. app/controllers/Admin (ADMINISTRACIÓN)

### app/controllers/Admin/DashboardController.php

```php
<?php

require_once __DIR__ . '/../../models/ProductoModel.php';
require_once __DIR__ . '/../../models/AdminVentasModel.php';
require_once __DIR__ . '/../../models/UsuarioModel.php';

class DashboardController
{
    public function index()
    {
        try {
            // Modelos
            $productoModel = new ProductoModel();
            $ventasModel = new AdminVentasModel();
            $usuarioModel = new UsuarioModel();

            // Total de productos
            $productos = $productoModel->getAll();
            $totalProductos = count($productos);

            // Total de ventas y últimas ventas
            $ventas = $ventasModel->getAll();
            $totalVentas = count($ventas);
            $ultimasVentas = array_slice($ventas, 0, 5);

            // Total de usuarios
            $usuarios = $usuarioModel->getAll();
            $totalUsuarios = count($usuarios);

            // Ingresos totales (suma de todas las ventas)
            $ingresosTotales = 0;
            foreach ($ventas as $venta) {
                $ingresosTotales += floatval($venta['total'] ?? 0);
            }

            // Productos con stock bajo (menos de 10 unidades)
            $productosStockBajo = array_filter($productos, function($producto) {
                return ($producto['stock'] ?? 0) < 10;
            });

            // Productos más vendidos (top 5)
            $sql = "SELECT p.nombre, m.nombre AS marca, SUM(dv.cantidad) AS cantidad_vendida
                    FROM detalle_venta dv
                    JOIN producto p ON dv.id_producto = p.id_pro
                    LEFT JOIN marca m ON p.id_marca = m.id_mar
                    GROUP BY p.id_pro
                    ORDER BY cantidad_vendida DESC
                    LIMIT 5";
            $stmt = $ventasModel->getDb()->prepare($sql);
            $stmt->execute();
            $productosMasVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Gráfico de ventas del mes actual
            $mesActual = date('Y-m');
            $ventasPorDia = $ventasModel->datosGrafico('mensual', $mesActual);
            // Formatear para Chart.js
            $ventasPorDia = array_map(function($d) use ($mesActual) {
                $dia = str_pad($d['etiqueta'], 2, '0', STR_PAD_LEFT);
                return [
                    'fecha' => $mesActual . '-' . $dia,
                    'total' => floatval($d['total'])
                ];
            }, $ventasPorDia);

            // Pasar datos a la vista
            $data = [
                'totalProductos' => $totalProductos,
                'totalVentas' => $totalVentas,
                'totalUsuarios' => $totalUsuarios,
                'ingresosTotales' => $ingresosTotales,
                'ultimasVentas' => $ultimasVentas,
                'productosStockBajo' => $productosStockBajo,
                'productosMasVendidos' => $productosMasVendidos,
                'ventasPorDia' => $ventasPorDia
            ];

            extract($data);
            require_once __DIR__ . '/../../views/admin/dashboard.php';
        } catch (Exception $e) {
            error_log("Error en DashboardController: " . $e->getMessage());
            echo "<pre>Error en DashboardController: " . $e->getMessage() . "</pre>";
            $totalProductos = 0;
            $totalVentas = 0;
            $totalUsuarios = 0;
            $ingresosTotales = 0;
            $ultimasVentas = [];
            $productosStockBajo = [];
            $productosMasVendidos = [];
            $ventasPorDia = [];
            require_once __DIR__ . '/../../views/admin/dashboard.php';
        }
    }
}

```

### app/controllers/Admin/CategoriasController.php

```php
<?php

namespace Admin;

use CategoriasModel;

class CategoriasController {
    private $categoriaModel;

    public function __construct() {
        $this->categoriaModel = new CategoriasModel();
    }
    
    public function index() {
        $categorias = $this->categoriaModel->getAll();
        require_once __DIR__ . '/../../views/admin/config/categorias.php';
    }
}

```

### app/controllers/Admin/SubcategoriasController.php

```php
<?php

namespace Admin;

use SubcategoriasModel;
use CategoriasModel;

class SubcategoriasController {
    private $subcategoriaModel;
    private $categoriaModel;

    public function __construct() {
        $this->subcategoriaModel = new SubcategoriasModel();
        $this->categoriaModel = new CategoriasModel();
    }
    
    public function index() {
        $subCategorias = $this->subcategoriaModel->getAllWithSubCategoria();
        $categorias = $this->categoriaModel->getAll();
        require_once __DIR__ . '/../../views/admin/config/subcategorias.php';
    }
}

```

### app/controllers/Admin/MarcasController.php

```php
<?php

namespace Admin;

use MarcasModel;

class MarcasController {
    private $marcaModel;

    public function __construct() {
        $this->marcaModel = new MarcasModel();
    }
    
    public function index() {
        $marcas = $this->marcaModel->getAll();
        require_once __DIR__ . '/../../views/admin/config/marcas.php';
    }
}
```

### app/controllers/Admin/ModelosController.php

```php
<?php

namespace Admin;

use ModelosModel;
use MarcasModel;

class ModelosController
{
    private $model;
    private $marcaModel;

    public function __construct()
    {
        $this->model = new ModelosModel();
        $this->marcaModel = new MarcasModel();
    }

    public function index()
    {
        $marcas = $this->marcaModel->getAll();
        $modelos = $this->model->getAllWithMarca();
        require_once __DIR__ . '/../../views/admin/config/modelos.php'; 
    }
}

```

### app/controllers/Admin/ProductosController.php

```php
<?php

namespace Admin;

use ProductoModel;
use SubcategoriasModel;
use CategoriasModel;
use MarcasModel;
use ModelosModel;

require_once __DIR__ . '/../../models/ProductoModel.php';
require_once __DIR__ . '/../../models/SubcategoriasModel.php';
require_once __DIR__ . '/../../models/CategoriasModel.php';
require_once __DIR__ . '/../../models/MarcasModel.php';
require_once __DIR__ . '/../../models/ModelosModel.php';

class ProductosController
{
    private $model;

    public function __construct()
    {
        $this->model = new ProductoModel();
    }

    // Mostrar todos los productos
    public function index()
    {
        // Parámetros de paginación
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;
        $search = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
        
        // Obtener productos paginados
        $productos = $this->model->getPaginated($page, $perPage, $search);
        
        // Obtener total de productos para paginación
        $totalProductos = $this->model->getTotalCount($search);
        
        // Calcular información de paginación
        $totalPages = ceil($totalProductos /perunet/ $perPage);
        $currentPage = max(1, min($page, $totalPages));
        
        // Datos para la vista
        $pagination = [
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalProductos' => $totalProductos,
            'perPage' => $perPage,
            'hasNextPage' => $currentPage < $totalPages,
            'hasPrevPage' => $currentPage > 1,
            'nextPage' => $currentPage + 1,
            'prevPage' => $currentPage - 1
        ];
        
        $subcategorias = (new SubcategoriasModel())->getAll();
        $categorias = (new CategoriasModel())->getAll();
        $marcas = (new MarcasModel())->getAll();
        $modelos = (new ModelosModel())->getAll();
        
        require_once __DIR__ . '/../../views/admin/productos/index.php';
    }

    // Mostrar formulario de creación
    public function crear()
    {
        $subcategorias = (new SubcategoriasModel())->getAll();
        $categorias = (new CategoriasModel())->getAll();
        $marcas = (new MarcasModel())->getAll();
        $modelos = (new ModelosModel())->getAll();

        require_once __DIR__ . '/../../views/admin/productos/form.php';
    }

    // Guardar nuevo producto
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombreImagen = null;

            // subir la imagen
            $nombreImagen = $this->subirImagen() ?? null;

            $data = [
                'nombre'          => $_POST['nombre'],
                'descripcion'     => $_POST['descripcion'],
                'precio'          => $_POST['precio'],
                'stock'           => $_POST['stock'],
                'id_subcategoria' => $_POST['id_subcategoria'],
                'id_marca'        => $_POST['id_marca'],
                'id_modelo'       => $_POST['id_modelo'],
                'imagen'          => $nombreImagen
            ];

            $this->model->create($data);
            header("Location: /perunet/admin/perunet/productos?mensaje=guardado");
            exit;
        }
    }

    // Mostrar formulario de edición
    public function editar($id)
    {
        $producto = $this->model->getById($id);

        $subcategorias = (new SubcategoriasModel())->getAllWithSubCategoria();
        $categorias = (new CategoriasModel())->getAll();
        $marcas = (new MarcasModel())->getAll();
        $modelos = (new ModelosModel())->getAll();

        require_once __DIR__ . '/../../views/admin/productos/form.php';
    }

    // Actualizar producto
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $nombreImagen = $_POST['imagen_actual'] ?? null;

            // subir la imagen
            $nombreImagen = $this->subirImagen() ?? null;

            $data = [
                'nombre'          => $_POST['nombre'],
                'descripcion'     => $_POST['descripcion'],
                'precio'          => $_POST['precio'],
                'stock'           => $_POST['stock'],
                'id_subcategoria' => $_POST['id_subcategoria'],
                'id_marca'        => $_POST['id_marca'],
                'id_modelo'       => $_POST['id_modelo'],
                'imagen'          => $nombreImagen
            ];

            $this->model->update($id, $data);
            header("Location: /perunet/admin/perunet/productos?mensaje=actualizado");
            exit;
        }
    }

    // Eliminar producto
    public function eliminar($id)
    {
        $this->model->delete($id);
        header("Location: /perunet/admin/perunet/productos?mensaje=eliminado");
        exit;
    }

    public function subirImagen()
    {
        if (isset($_FILES['imagen_file']) && $_FILES['imagen_file']['error'] === UPLOAD_ERR_OK) {
            $nombreTemporal = $_FILES['imagen_file']['tmp_name'];
            $nombreArchivo = basename($_FILES['imagen_file']['name']);
            
            // ruta relativa a la carpeta uploads
            $carpetaRelativa = '/perunet/public/img/uploads/';
            $carpetaAbsoluta = __DIR__ . '/../../..' . $carpetaRelativa;
            
            // crear la carpeta uploads si no existe
            if (!is_dir($carpetaAbsoluta)) {
                if (!mkdir($carpetaAbsoluta, 0755, true)) {
                    die('Error: No se pudo crear el directorio de subidas');
                }
            }
            $rutaDestino = $carpetaAbsoluta . $nombreArchivo;
            
            // revisala si es una imagen el archivo
            $check = getimagesize($nombreTemporal);
            if($check === false) {
                die('El archivo no es una imagen');
            }

            // sube el archivo a la carpeta uploads
            if (move_uploaded_file($nombreTemporal, $rutaDestino)) {
                $nombreImagen = 'uploads/perunet/' . $nombreArchivo;
            } else {
                die('Error al subir el archivo. Por favor, inténtalo de nuevo.');
            }
        }
        return $nombreImagen;
    }
}

```

### app/controllers/Admin/RolesController.php

```php
<?php

namespace Admin;

use RolesModel;

class RolesController {
    private $rolesModel;

    public function __construct() {
        $this->rolesModel = new RolesModel();
    }
    
    public function index() {
        $roles = $this->rolesModel->getAll();
        $estados = ['activo', 'suspendido'];
        require_once __DIR__ . '/../../views/admin/config/roles.php';
    }
}
```

### app/controllers/Admin/UsuariosController.php

```php
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

```

### app/controllers/Admin/VentasController.php

```php
<?php

namespace Admin;

use AdminVentasModel;

class VentasController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminVentasModel();
    }

    //  Listado de ventas con filtro de búsqueda
    public function index()
    {
        $buscar = $_GET['buscar'] ?? ''; // Captura del filtro si existe
        $ventas = $this->model->getAll($buscar); // Pasa el filtro al modelo
        require_once __DIR__ . '/../../views/admin/ventas/index.php';
    }

    //  Detalle de una venta
    public function detalle($id)
    {
        // Procesar cambio de estado
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['venta_id'], $_POST['estado'])) {
            $ventaId = $_POST['venta_id'];
            $nuevoEstado = $_POST['estado'];
            $this->model->actualizarEstado($ventaId, $nuevoEstado);
            // Si es AJAX, responde JSON
            if (
                isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) {
                header('Content-Type: application/perunet/json');
                echo json_encode(['success' => true]);
                exit;
            }
            // Si no es AJAX, redirige (fallback)
            header('Location: /perunet/admin/perunet/ventas/perunet/detalle/perunet/' . $ventaId);
            exit;
        }

        // Obtener datos generales y detalle de la venta
        $venta = $this->model->getById($id);
        $detalle = $this->model->getDetalle($id);
        require_once __DIR__ . '/../../views/admin/ventas/detalle.php';
    }

    // Cambiar el estado de una venta por AJAX
    public function cambiarEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['venta_id'], $_POST['estado'])) {
            $ventaId = $_POST['venta_id'];
            $nuevoEstado = $_POST['estado'];
            $this->model->actualizarEstado($ventaId, $nuevoEstado);
            header('Content-Type: application/perunet/json');
            echo json_encode(['success' => true]);
            exit;
        }
        // Si no es POST válido, responde error
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Petición inválida']);
        exit;
    }


    public function reportePorFecha()
    {
        $tipo = $_GET['tipo'] ?? 'diario';
        $anio = $_GET['anio'] ?? date('Y');
        $mes = $_GET['mes'] ?? date('Y-m');
        $dia = $_GET['dia'] ?? date('Y-m-d');

        $ventas = $this->model->getVentasPorFecha($tipo, $anio, $mes, $dia);
        $totalVentas = array_sum(array_column($ventas, 'total'));

        require_once __DIR__ . '/../../views/admin/ventas/reportes.php';
    }



    public function resumenEstadistico()
    {
        $ventaModel = new AdminVentasModel();

        $tipo = $_GET['tipo'] ?? 'diario';
        $fecha = $_GET['fecha'] ?? date($tipo === 'anual' ? 'Y' : ($tipo === 'mensual' ? 'Y-m' : 'Y-m-d'));

        $productoMasVendido = $ventaModel->productoMasVendido($tipo, $fecha);
        $diaMasVentas = $ventaModel->diaConMasVentas($tipo, $fecha);
        $datos = $ventaModel->datosGrafico($tipo, $fecha);

        // Formatear para Chart.js
        $labels = array_column($datos, 'etiqueta');
        $valores = array_map(fn($d) => (float) $d['total'], $datos);

        require_once __DIR__ . '/../../views/admin/ventas/resumen.php';
    }
}

```

## 8. app/models (MODELOS - ACCESO A DATOS)

### app/models/ProductoModel.php

```php
<?php
require_once __DIR__ . '/../core/Model.php';

class ProductoModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener todos los productos con categoría, subcategoría, marca y modelo
    public function getAll()
    {
        $sql = "
            SELECT p.*, 
                   sc.nombre AS subcategoria,
                   c.nombre AS categoria,
                   m.nombre AS marca,
                   mo.nombre AS modelo
            FROM producto p
            LEFT JOIN subcategoria sc ON p.id_subcategoria = sc.id
            LEFT JOIN categoria c ON sc.id_categoria = c.id_cat
            LEFT JOIN marca m ON p.id_marca = m.id_mar
            LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
            ORDER BY p.fecha_creacion DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar productos por nombre, descripción o marca
    public function search($busqueda, $marcas = [], $precioMin = null, $precioMax = null)
    {
        $busquedaTerm = '%' . $busqueda . '%';
        
        $sql = "
            SELECT p.*, 
                   sc.nombre AS subcategoria,
                   c.nombre AS categoria,
                   m.nombre AS marca,
                   mo.nombre AS modelo
            FROM producto p
            LEFT JOIN subcategoria sc ON p.id_subcategoria = sc.id
            LEFT JOIN categoria c ON sc.id_categoria = c.id_cat
            LEFT JOIN marca m ON p.id_marca = m.id_mar
            LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
            WHERE (p.nombre LIKE ? OR p.descripcion LIKE ? OR m.nombre LIKE ?)
        ";
        
        $params = [$busquedaTerm, $busquedaTerm, $busquedaTerm];
        
        if (!empty($marcas) && is_array($marcas) && count($marcas) > 0) {
            $marcasArr = array_values($marcas);
            $placeholders = array_fill(0, count($marcasArr), '?');
            $sql .= " AND m.nombre IN (" . implode(',', $placeholders) . ")";
            $params = array_merge($params, $marcasArr);
        }
        
        if ($precioMin !== null && $precioMin !== '' && is_numeric($precioMin)) {
            $sql .= " AND p.precio >= ?";
            $params[] = (float)$precioMin;
        }
        
        if ($precioMax !== null && $precioMax !== '' && is_numeric($precioMax)) {
            $sql .= " AND p.precio <= ?";
            $params[] = (float)$precioMax;
        }
        
        $sql .= " ORDER BY p.fecha_creacion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todas las marcas únicas de productos
    public function getMarcas()
    {
        $sql = "SELECT DISTINCT m.id_mar, m.nombre 
                FROM marca m
                INNER JOIN producto p ON p.id_marca = m.id_mar
                ORDER BY m.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener productos paginados
    public function getPaginated($page = 1, $perPage = 10, $search = '')
    {
        $offset = ($page - 1) * $perPage;

        $whereClause = '';
        $params = [];

        if (!empty($search)) {
            $whereClause = "WHERE p.nombre LIKE :search1 OR p.descripcion LIKE :search2";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }

        $sql = "
        SELECT p.*, 
               sc.nombre AS subcategoria,
               c.nombre AS categoria,
               m.nombre AS marca,
               mo.nombre AS modelo
        FROM producto p
        LEFT JOIN subcategoria sc ON p.id_subcategoria = sc.id
        LEFT JOIN categoria c ON sc.id_categoria = c.id_cat
        LEFT JOIN marca m ON p.id_marca = m.id_mar
        LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
        {$whereClause}
        ORDER BY p.fecha_creacion DESC
        LIMIT :limit OFFSET :offset
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        if (!empty($search)) {
            $stmt->bindParam(':search1', $params[':search1']);
            $stmt->bindParam(':search2', $params[':search2']);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Obtener total de productos para paginación
    public function getTotalCount($search = '')
    {
        $whereClause = '';
        $params = [];

        if (!empty($search)) {
            $whereClause = "WHERE p.nombre LIKE :search1 OR p.descripcion LIKE :search2";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }

        $sql = "
        SELECT COUNT(*) as total
        FROM producto p
        {$whereClause}
    ";

        $stmt = $this->db->prepare($sql);

        if (!empty($search)) {
            $stmt->bindParam(':search1', $params[':search1']);
            $stmt->bindParam(':search2', $params[':search2']);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }


    // Obtener productos por nombre de categoría
    public function getByCategoria($nombreCategoria)
    {
        $sql = "
            SELECT p.*, 
                   sc.nombre AS subcategoria,
                   c.nombre AS categoria,
                   m.nombre AS marca,
                   mo.nombre AS modelo
            FROM producto p
            LEFT JOIN subcategoria sc ON p.id_subcategoria = sc.id
            LEFT JOIN categoria c ON sc.id_categoria = c.id_cat
            LEFT JOIN marca m ON p.id_marca = m.id_mar
            LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
            WHERE c.nombre = :categoria
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':categoria', $nombreCategoria);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener productos por nombre de subcategoria
    public function getBySubcategoria($nombreCategoria, $nombreSubcategoria)
    {
        try {
            $sql = "
                SELECT p.*, sc.nombre as subcategoria, c.nombre as categoria, mo.nombre as modelo, ma.nombre as marca
                FROM producto as p
                LEFT JOIN subcategoria as sc ON sc.id = p.id_subcategoria
                LEFT JOIN categoria as c ON c.id_cat = sc.id_categoria
                LEFT JOIN modelo as mo ON mo.id_mod = p.id_modelo
                LEFT JOIN marca as ma ON ma.id_mar = p.id_marca
                WHERE REPLACE(LOWER(c.nombre), ' ', '-') = :categoria
                AND REPLACE(LOWER(sc.nombre), ' ', '-') = :subcategoria;
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':categoria', $nombreCategoria, PDO::PARAM_STR);
            $stmt->bindParam(':subcategoria', $nombreSubcategoria, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener productos por subcategoria: " . $e->getMessage());
        }
    }

    // Obtener producto por ID
    public function getById($id, $id_usuario = null)
    {
        try {
            $sql = "
            SELECT 
                p.*, 
                cat.id_cat AS id_categoria,
                sc.nombre AS subcategoria,
                cat.nombre AS categoria,
                m.nombre AS marca,
                mo.nombre AS modelo,
                GREATEST(
                    p.stock - COALESCE(SUM(
                        CASE 
                            WHEN :id_usuario1 IS NOT NULL AND c.id_usuario = :id_usuario2 THEN dc.cantidad
                            ELSE 0
                        END
                    ), 0),
                0) AS stock_disponible
            FROM producto p
            LEFT JOIN subcategoria sc ON p.id_subcategoria = sc.id
            LEFT JOIN categoria cat ON sc.id_categoria = cat.id_cat
            LEFT JOIN marca m ON p.id_marca = m.id_mar
            LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
            LEFT JOIN detalle_carrito dc ON p.id_pro = dc.id_producto
            LEFT JOIN carrito c ON dc.id_carrito = c.id_carrito
            WHERE p.id_pro = :id
            GROUP BY p.id_pro
            LIMIT 1
        ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':id_usuario1', $id_usuario, PDO::PARAM_INT);
            $stmt->bindValue(':id_usuario2', $id_usuario, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el producto: " . $e->getMessage());
        }
    }

    /**
     * Obtener productos por categoría con filtros
     */
    public function getByCategory($id_categoria, $marcas = [], $precioMin = null, $precioMax = null)
    {
        $sql = "SELECT p.*, c.nombre AS categoria, s.nombre AS subcategoria, m.nombre AS marca
                FROM producto p
                JOIN subcategoria s ON p.id_subcategoria = s.id
                JOIN categoria c ON s.id_categoria = c.id_cat
                JOIN marca m ON p.id_marca = m.id_mar
                WHERE c.id_cat = :id_categoria";

        $params = [':id_categoria' => $id_categoria];

        if (!empty($marcas)) {
            $marcas_placeholders = [];
            foreach ($marcas as $key => $marca) {
                $placeholder = ":marca" . $key;
                $marcas_placeholders[] = $placeholder;
                $params[$placeholder] = $marca;
            }
            $sql .= " AND m.nombre IN (" . implode(',', $marcas_placeholders) . ")";
        }

        if ($precioMin !== null) {
            $sql .= " AND p.precio >= :precio_min";
            $params[':precio_min'] = $precioMin;
        }

        if ($precioMax !== null) {
            $sql .= " AND p.precio <= :precio_max";
            $params[':precio_max'] = $precioMax;
        }

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => &$val) {
            // Determinar el tipo de dato para bindParam
            if (is_int($val)) {
                $type = PDO::PARAM_INT;
            } elseif (is_bool($val)) {
                $type = PDO::PARAM_BOOL;
            } elseif (is_null($val)) {
                $type = PDO::PARAM_NULL;
            } else {
                $type = PDO::PARAM_STR;
            }
            $stmt->bindParam($key, $val, $type);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener productos por subcategoría con filtros
     */
    public function getBySubcategory($id_subcategoria, $marcas = [], $precioMin = null, $precioMax = null)
    {
        $sql = "SELECT p.*, c.nombre AS categoria, s.nombre AS subcategoria, m.nombre AS marca
                FROM producto p
                JOIN subcategoria s ON p.id_subcategoria = s.id
                JOIN categoria c ON s.id_categoria = c.id_cat
                JOIN marca m ON p.id_marca = m.id_mar
                WHERE p.id_subcategoria = :id_subcategoria";

        $params = [':id_subcategoria' => $id_subcategoria];

        if (!empty($marcas)) {
            $marcas_placeholders = [];
            foreach ($marcas as $key => $marca) {
                $placeholder = ":marca" . $key;
                $marcas_placeholders[] = $placeholder;
                $params[$placeholder] = $marca;
            }
            $sql .= " AND m.nombre IN (" . implode(',', $marcas_placeholders) . ")";
        }

        if ($precioMin !== null) {
            $sql .= " AND p.precio >= :precio_min";
            $params[':precio_min'] = $precioMin;
        }

        if ($precioMax !== null) {
            $sql .= " AND p.precio <= :precio_max";
            $params[':precio_max'] = $precioMax;
        }

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Crear nuevo producto
    public function create($data)
    {
        $sql = "INSERT INTO producto 
                (nombre, descripcion, precio, stock, imagen, id_subcategoria, id_marca, id_modelo)
                VALUES (:nombre, :descripcion, :precio, :stock, :imagen, :id_subcategoria, :id_marca, :id_modelo)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':precio', $data['precio']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':imagen', $data['imagen']);
        $stmt->bindParam(':id_subcategoria', $data['id_subcategoria']);
        $stmt->bindParam(':id_marca', $data['id_marca']);
        $stmt->bindParam(':id_modelo', $data['id_modelo']);
        return $stmt->execute();
    }

    // Actualizar producto existente
    public function update($id, $data)
    {
        $sql = "UPDATE producto SET 
                    nombre = :nombre,
                    descripcion = :descripcion,
                    precio = :precio,
                    stock = :stock,
                    id_subcategoria = :id_subcategoria,
                    id_marca = :id_marca,
                    id_modelo = :id_modelo
               ";

        /* si la imagen es distinta de null la actualiza */
        if ($data['imagen'] !== null) {
            $sql .= ", imagen = :imagen";
        }

        $sql .= " WHERE id_pro = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':precio', $data['precio']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':id_subcategoria', $data['id_subcategoria']);
        $stmt->bindParam(':id_marca', $data['id_marca']);
        $stmt->bindParam(':id_modelo', $data['id_modelo']);

        /* si la imagen es distinta de null la actualiza */
        if ($data['imagen'] !== null) {
            $stmt->bindParam(':imagen', $data['imagen']);
        }
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Eliminar producto
    public function delete($id)
    {
        $sql = "DELETE FROM producto WHERE id_pro = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // actualizar stock
    public function actualizarStock($id, $stock)
    {
        try {
            $sql = "UPDATE producto SET stock = :stock WHERE id_pro = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el stock del producto: " . $e->getMessage());
        }
    }

    // Obtener productos destacados (últimos productos agregados)
    public function getProductosDestacados($limite = 12, $id_categoria = null, $id_subcategoria = null)
    {
        try {
            $sql = "
            SELECT p.*, 
                   sc.nombre AS subcategoria,
                   c.nombre AS categoria,
                   m.nombre AS marca,
                   mo.nombre AS modelo
            FROM producto p
            LEFT JOIN subcategoria sc ON p.id_subcategoria = sc.id
            LEFT JOIN categoria c ON sc.id_categoria = c.id_cat
            LEFT JOIN marca m ON p.id_marca = m.id_mar
            LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
            WHERE 1 = 1
        ";

            // Filtros condicionales
            if ($id_categoria !== null) {
                $sql .= " AND c.id_cat = :id_categoria";
            }
            if ($id_subcategoria !== null) {
                $sql .= " AND sc.id = :id_subcategoria";
            }

            $sql .= " ORDER BY p.fecha_creacion DESC LIMIT :limite";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);

            if ($id_categoria !== null) {
                $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
            }
            if ($id_subcategoria !== null) {
                $stmt->bindParam(':id_subcategoria', $id_subcategoria, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener productos destacados: " . $e->getMessage());
        }
    }
}

```

### app/models/CategoriasModel.php

```php
<?php

require_once __DIR__ . '/../core/Model.php';

class CategoriasModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM categoria";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createCategoria($nombre)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO categoria (nombre) VALUES (:nombre)");
            $stmt->bindParam(':nombre', $nombre);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido crear la categoria: " . $e->getMessage();
        }
    }

    public function updateCategoria($id, $nombre)
    {
        try {
            $stmt = $this->db->prepare("UPDATE categoria SET nombre = :nombre WHERE id_cat = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido actualizar la categoria: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM categoria WHERE id_cat = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido eliminar la marca: " . $e->getMessage();
        }
    }

    public function getBySlug($slug)
    {
        // Obtener todas las categorías y buscar la que coincida con el slug
        $categorias = $this->getAll();
        foreach ($categorias as $categoria) {
            if (function_exists('slugify')) {
                if (slugify($categoria['nombre']) === $slug) {
                    return $categoria;
                }
            } else {
                // Fallback simple si no existe slugify
                if (strtolower(str_replace(' ', '-', $categoria['nombre'])) === $slug) {
                    return $categoria;
                }
            }
        }
        return null;
    }
}

```

### app/models/SubcategoriasModel.php

```php
<?php

//require_once __DIR__ . '/../config/database.php';

class SubcategoriasModel extends Model
{
    private $conn;

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM subcategoria";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithSubCategoria()
    {
        try {
            $query = "SELECT mo.id, mo.nombre, ma.id_cat, ma.nombre as categoria 
            FROM subcategoria as mo 
            JOIN categoria as ma ON mo.id_categoria = ma.id_cat";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener las subcategorias: " . $e->getMessage();
        }
    }

    public function getSubCategoriasById($id)
    {
        try {
            $query = "SELECT * FROM subcategoria WHERE id_categoria = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener las subcategorias: " . $e->getMessage();
        }
    }

    // Cambiar la firma para que sea compatible con Model
    public function create($data)
    {
        // Puedes usar el método padre si quieres la funcionalidad base
        return parent::create($data);
    }

    // Método personalizado para crear subcategoría
    public function createSubcategoria($nombre, $id_categoria)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO subcategoria (nombre, id_categoria) VALUES (:nombre, :id_categoria)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id_categoria', $id_categoria);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido crear la subcategoria: " . $e->getMessage();
        }
    }

    public function update($id, $data)
    {
        return parent::update($id, $data);
    }

    // Método personalizado para actualizar subcategoría
    public function updateSubcategoria($id, $nombre, $id_categoria)
    {
        try {
            $stmt = $this->db->prepare("UPDATE subcategoria SET nombre = :nombre, id_categoria = :id_categoria WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id_categoria', $id_categoria);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido actualizar la subcategoria: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM subcategoria WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ["success" => true, "message" => "Subcategoria eliminada correctamente"];
        } catch (Exception $e) {
            return ["success" => false, "message" => "No se ha podido eliminar la subcategoria: " . $e->getMessage()];
        }
    }

    /**
     * Obtiene todas las categorías con sus subcategorías para el menú de navegación
     * @return array Estructura de categorías con sus subcategorías
     */
    public function getCategoriesWithSubcategories()
    {
        try {
            // Usamos una sola consulta con JOIN para obtener todo en una sola llamada
            $query = "SELECT 
                        c.id_cat as id, 
                        c.nombre as categoria_nombre,
                        s.id as subcategoria_id,
                        s.nombre as subcategoria_nombre
                      FROM categoria c
                      LEFT JOIN subcategoria s ON c.id_cat = s.id_categoria
                      ORDER BY c.nombre, s.nombre";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $result = [];
            $currentCategory = null;
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Si es una nueva categoría o la primera iteración
                if ($currentCategory === null || $currentCategory['id'] != $row['id']) {
                    if ($currentCategory !== null) {
                        $result[] = $currentCategory;
                    }
                    $currentCategory = [
                        'id' => $row['id'],
                        'nombre' => $row['categoria_nombre'],
                        'subcategorias' => []
                    ];
                }
                
                // Agregar subcategoría si existe
                if ($row['subcategoria_id'] !== null) {
                    $currentCategory['subcategorias'][] = [
                        'id' => $row['subcategoria_id'],
                        'nombre' => $row['subcategoria_nombre']
                    ];
                }
            }
            
            // Agregar la última categoría procesada
            if ($currentCategory !== null) {
                $result[] = $currentCategory;
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Error al obtener categorías con subcategorías: " . $e->getMessage());
            return [];
        }
    }

    public function getBySlug($slug, $id_categoria = null)
    {
        $sql = "SELECT * FROM subcategoria WHERE REPLACE(LOWER(nombre), ' ', '-') = :slug";
        if ($id_categoria !== null) {
            $sql .= " AND id_categoria = :id_categoria";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        if ($id_categoria !== null) {
            $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener subcategorías por id de categoría
    public function getByCategory($id_categoria)
    {
        $sql = "SELECT * FROM subcategoria WHERE id_categoria = :id_categoria";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

```

### app/models/MarcasModel.php

```php
<?php

require_once __DIR__ . '/../core/Model.php';

class MarcasModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM marca";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createMarca($nombre)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO marca (nombre) VALUES (:nombre)");
            $stmt->bindParam(':nombre', $nombre);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido crear la marca: " . $e->getMessage();
        }
    }

    public function updateMarca($id, $nombre)
    {
        try {
            $stmt = $this->db->prepare("UPDATE marca SET nombre = :nombre WHERE id_mar = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido actualizar la marca: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM marca WHERE id_mar = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido eliminar la marca: " . $e->getMessage();
        }
    }
}

```

### app/models/ModelosModel.php

```php

<?php

// require_once __DIR__ . '/../config/database.php';

class ModelosModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM modelo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithMarca()
    {
        try {
            $query = "SELECT mo.id_mod, mo.nombre, ma.id_mar, ma.nombre as marca 
            FROM modelo as mo 
            JOIN marca as ma ON mo.id_marca = ma.id_mar";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener los modelos: " . $e->getMessage();
        }
    }

    public function getMarcasById($id_marca)
    {
        try {
            $query = "SELECT * FROM modelo WHERE id_marca = :id_marca";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_marca', $id_marca, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener los modelos: " . $e->getMessage();
        }
    }

    public function createModelo($nombre, $id_marca)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO modelo (nombre, id_marca) VALUES (:nombre, :id_marca)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id_marca', $id_marca);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido crear el modelo: " . $e->getMessage();
        }
    }

    public function updateModelo($id, $nombre, $id_marca)
    {
        try {
            $stmt = $this->db->prepare("UPDATE modelo SET nombre = :nombre, id_marca = :id_marca WHERE id_mod = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id_marca', $id_marca);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido actualizar el modelo: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM modelo WHERE id_mod = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido eliminar el modelo: " . $e->getMessage();
        }
    }
}

```

### app/models/CarritoModel.php

```php
<?php

//require_once __DIR__ . '/../config/database.php';
//require_once __DIR__ . '/../models/UsuarioModel.php';

class CarritoModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {

        try {
            $sql = "SELECT * FROM carrito";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los carritos: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        try {
            $sql = "SELECT * FROM carrito WHERE id_carrito = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el carrito: " . $e->getMessage());
        }
    }

    public function getByCartValidationCliente($id_usuario)
    {
        try {
            $userExist = (new UsuarioModel())->getById($id_usuario);
            // Primero verificar si el usuario existe
            if (!$userExist) {
                return null;
            }

            // Buscar carrito activo de usuario
            $sql = "SELECT * FROM carrito WHERE id_usuario = ? AND estado = 'activo' LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_usuario]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener validación de usuario con carrito: " . $e->getMessage());
        }
    }

    public function create($id_usuario)
    {
        try {
            // Verificar primero que el usuario existe
            $sqlCheck = "SELECT id_us FROM usuario WHERE id_us = ?";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([$id_usuario]);

            if (!$stmtCheck->fetch()) {
                throw new Exception("El usuario con ID $id_usuario no existe");
            }

            // Crear el carrito
            $sql = "INSERT INTO carrito (id_usuario, estado, fecha_creacion) 
                    VALUES (?, 'activo', NOW())";

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$id_usuario]);

            if ($result) {
                $id_carrito = $this->db->lastInsertId();
                error_log("Carrito creado exitosamente con ID: " . $id_carrito);
                return $id_carrito;
            } else {
                $error = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la consulta: " . json_encode($error));
            }
        } catch (PDOException $e) {
            error_log("Error en CarritoModel::create - " . $e->getMessage());
            throw new Exception("Error al crear el carrito: " . $e->getMessage());
        }
    }


    // Cambiar la firma para que sea compatible con Model
    public function update($id, $data)
    {
        return parent::update($id, $data);
    }

    // Método personalizado para actualizar carrito
    public function updateCarrito($id, $id_usuario, $estado)
    {
        try {
            $sql = "UPDATE carrito SET id_usuario = ?, estado = ? WHERE id_carrito = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $id_usuario,
                $estado,
                $id
            ]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el carrito: " . $e->getMessage());
        }
    }

    public function delete($id_usuario)
    {
        try {
            $sql = "DELETE FROM carrito
                    WHERE carrito.id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_usuario]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el carrito: " . $e->getMessage());
        }
    }

    // Devuelve la cantidad de carritos (puedes ajustar para contar productos de un usuario si es necesario)
    public function getCount($id_usuario = null)
    {
        if ($id_usuario !== null) {
            $sql = "SELECT COUNT(*) as count FROM carrito WHERE id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_usuario]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM carrito";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Obtener o crear carrito activo del usuario
     */
    public function getOrCreateCart($id_usuario)
    {
        try {
            // Buscar carrito activo existente
            $carrito = $this->getByCartValidationCliente($id_usuario);
            
            if ($carrito) {
                return $carrito;
            }
            
            // Si no existe, crear uno nuevo
            $id_carrito = $this->create($id_usuario);
            
            if ($id_carrito) {
                return $this->getById($id_carrito);
            }
            
            throw new Exception("No se pudo crear el carrito");
            
        } catch (Exception $e) {
            throw new Exception("Error al obtener o crear carrito: " . $e->getMessage());
        }
    }
}

```

### app/models/DetalleCarrito.php

```php
<?php

//require_once __DIR__ . '/../config/database.php';

class DetalleCarrito extends Model
{
    protected $table = 'detalle_carrito';
    // Obtiene un producto del carrito
    public function getByCartAndProduct($id_carrito, $id_producto)
    {
        try {
            // obtener datos de detalle_carrito y precio del producto
            $sql = "SELECT dc.*, p.precio
                    FROM detalle_carrito dc
                    INNER JOIN producto p ON dc.id_producto = p.id_pro
                    WHERE dc.id_carrito = ? AND dc.id_producto = ? 
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_carrito, $id_producto]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el ítem del carrito: " . $e->getMessage());
            return false;
        }
    }

    // Cambiar la firma para que sea compatible con Model
    public function create($data)
    {
        return parent::create($data);
    }

    // Método personalizado para crear detalle de carrito
    public function createDetalle($id_carrito, $id_producto, $cantidad, $precio_unitario)
    {
        try {
            // Verificar si el producto ya existe en el carrito
            $existing = $this->getByCartAndProduct($id_carrito, $id_producto);

            if ($existing) {
                // Si el producto ya existe, actualizar la cantidad
                $nueva_cantidad = $existing['cantidad'] + $cantidad;
                $nuevo_precio_total = $existing['precio'] * $nueva_cantidad;
                $actualizado = $this->updateQuantity(
                    $existing['id_detalle'],
                    $nueva_cantidad,
                    $nuevo_precio_total
                );

                if ($actualizado) {
                    return $existing['id_detalle']; // Retornar el ID del detalle existente
                } else {
                    throw new Exception("No se pudo actualizar la cantidad del producto existente");
                }
            }

            // Insertar nuevo ítem si no existe
            $sql = "INSERT INTO detalle_carrito 
                    (id_carrito, id_producto, cantidad, precio_total) 
                    VALUES (?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $id_carrito,
                $id_producto,
                $cantidad,
                $precio_unitario * $cantidad // calcular precio total
            ]);

            if ($result) {
                return $this->db->lastInsertId();
            } else {
                throw new Exception("No se pudo insertar el ítem en el carrito");
            }
        } catch (PDOException $e) {
            throw new Exception("Error al agregar el producto al carrito: " . $e->getMessage());
        }
    }

    // actualiza la cantidad de un producto en el carrito al momento de agregar mas de un producto
    public function updateQuantity($id_detalle, $nueva_cantidad, $nuevo_precio_total = null)
    {
        try {
            if ($nuevo_precio_total !== null) {
                $sql = "UPDATE detalle_carrito 
                        SET cantidad = ?, precio_total = ?
                        WHERE id_detalle = ?";
                $params = [$nueva_cantidad, $nuevo_precio_total, $id_detalle];
            } else {
                $sql = "UPDATE detalle_carrito 
                        SET cantidad = ?
                        WHERE id_detalle = ?";
                $params = [$nueva_cantidad, $id_detalle];
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            // Verificar si se actualizó alguna fila
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                throw new Exception("No se encontró el detalle del carrito con ID: " . $id_detalle);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la cantidad del producto: " . $e->getMessage());
        }
    }

    // Elimina un producto del carrito
    public function delete($id_detalle)
    {
        try {
            $sql = "DELETE FROM detalle_carrito WHERE id_detalle = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id_detalle]);
        } catch (PDOException $e) {
            error_log("Error deleting cart item: " . $e->getMessage());
            return false;
        }
    }


    // Obtiene todos los productos del carrito
    public function getItems($id_usuario)
    {
        try {
            $sql = "
            SELECT dc.*,
                   p.id_pro AS id_producto,
                   p.nombre AS nombre_producto,
                   p.imagen AS imagen_producto,
                   p.stock AS stock_producto,
                   p.precio AS precio_producto
            FROM detalle_carrito dc
            INNER JOIN producto p ON dc.id_producto = p.id_pro
            INNER JOIN carrito c ON dc.id_carrito = c.id_carrito
            WHERE c.id_usuario = :id_usuario
        ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los productos del carrito: " . $e->getMessage());
        }
    }

    // obtener cuantos productos hay en el carrito
    public function getProductsCount($id_usuario)
    {
        try {

            $sql = "SELECT COUNT(dc.id_detalle) as count
            FROM detalle_carrito as dc
            INNER JOIN carrito as c ON dc.id_carrito = c.id_carrito
            WHERE c.id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_usuario]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el conteo del carrito: " . $e->getMessage());
        }
    }

    public function getTotal($id_usuario)
    {
        try {
            $sql = "SELECT SUM(dc.precio_total) as total
            FROM detalle_carrito as dc
            INNER JOIN carrito as c ON dc.id_carrito = c.id_carrito
            WHERE c.id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_usuario]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el total del carrito: " . $e->getMessage());
        }
    }

    // eliminar productos sin stock
    public function removeOutOfStockItems($id_usuario)
    {
        try {
            $sql = "
            DELETE dc FROM detalle_carrito dc
            INNER JOIN carrito c ON dc.id_carrito = c.id_carrito
            INNER JOIN producto p ON dc.id_producto = p.id_pro
            WHERE c.id_usuario = :id_usuario AND p.stock < dc.cantidad
        ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount(); // Devuelve la cantidad de filas eliminadas
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar productos sin stock: " . $e->getMessage());
        }
    }

    // verificar si el carrito esta vacio
    public function isEmpty($id_usuario)
    {
        try {
            $sql = "SELECT COUNT(*) as count
            FROM detalle_carrito as dc
            INNER JOIN carrito as c ON dc.id_carrito = c.id_carrito
            WHERE c.id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_usuario]);
            return $stmt->fetchColumn() === 0;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar si el carrito esta vacio: " . $e->getMessage());
        }
    }

    /**
     * Agregar producto al carrito (usado por el builder)
     */
    public function agregarProducto($id_usuario, $id_producto, $cantidad = 1)
    {
        try {
            // Obtener o crear carrito del usuario
            require_once __DIR__ . '/CarritoModel.php';
            $carritoModel = new CarritoModel();
            $carrito = $carritoModel->getOrCreateCart($id_usuario);
            
            if (!$carrito) {
                throw new Exception("No se pudo crear el carrito");
            }
            
            // Obtener precio del producto
            $sqlPrecio = "SELECT precio FROM producto WHERE id_pro = ?";
            $stmtPrecio = $this->db->prepare($sqlPrecio);
            $stmtPrecio->execute([$id_producto]);
            $precio = $stmtPrecio->fetchColumn();
            
            if (!$precio) {
                throw new Exception("Producto no encontrado");
            }
            
            // Usar el método createDetalle existente
            return $this->createDetalle($carrito['id_carrito'], $id_producto, $cantidad, $precio);
            
        } catch (Exception $e) {
            throw new Exception("Error al agregar producto: " . $e->getMessage());
        }
    }
}

```

### app/models/VentaModel.php

```php
<?php

class VentaModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    //------------------------------------------------------------------------------------------------------------------

    // paso 1
    function insertarVenta($idUsuario, $idDireccion, $total, $metodoPagoId, $tipoEntrega, $idSucursal)
    {
        try {
            $sql = "INSERT INTO venta (id_usuario, id_direccion, total, metodo_pago_id, tipo_entrega, id_sucursal) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $idUsuario,
                $idDireccion,
                $total,
                $metodoPagoId,
                $tipoEntrega,
                $idSucursal
            ]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Error al insertar venta: ' . $e->getMessage());
        }
    }

    // paso 2
    public function insertarDetalle($idVenta, $carrito)
    {
        try {
            $sql = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            foreach ($carrito as $item) {
                $stmt->execute([
                    $idVenta,
                    $item['id_producto'],
                    $item['cantidad'],
                    $item['precio_producto']
                ]);
            }
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al insertar detalle: ' . $e->getMessage());
        }
    }

    public function guardarDireccion(
        $idUsuario,
        $departamento,
        $provincia,
        $distrito,
        $calle,
        $numero,
        $piso,
        $referencia
    ) {
        try {
            $sql = "INSERT INTO direccion_entrega (id_usuario, departamento, provincia, distrito, calle, numero, piso, referencia) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $idUsuario,
                $departamento,
                $provincia,
                $distrito,
                $calle,
                $numero,
                $piso,
                $referencia
            ]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Error al guardar direccion: ' . $e->getMessage());
        }
    }

    public function guardarPago($idVenta, $numero_tarjeta, $numero_telefono, $idTransaccion = null)
    {
        try {
            $sql = "INSERT INTO pago (id_venta, numero_tarjeta, numero_telefono, id_transaccion) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $idVenta,
                $numero_tarjeta ?? null,
                $numero_telefono ?? null,
                $idTransaccion ?? null
            ]);
        } catch (Exception $e) {
            throw new Exception('Error al guardar pago: ' . $e->getMessage());
        }
    }

    public function getByUsuario($usuarioId) {
        $sql = "SELECT * FROM venta WHERE id_usuario = :id_usuario ORDER BY fecha_venta DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_usuario' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public function getDetalleById($id_venta, $usuarioId) {
        $sql = "SELECT v.*, dv.*, 
                       p.nombre AS producto_nombre, p.imagen,
                       u.nombre AS cliente_nombre, u.apellidos AS cliente_apellidos, u.dni, u.telefono, u.correo AS cliente_correo,
                       d.departamento, d.provincia, d.distrito, d.calle, d.numero, d.piso, d.referencia,
                       s.nombre AS sucursal_nombre, s.direccion AS sucursal_direccion
                FROM venta v
                INNER JOIN detalle_venta dv ON v.id_ven = dv.id_venta
                INNER JOIN producto p ON dv.id_producto = p.id_pro
                INNER JOIN usuario u ON v.id_usuario = u.id_us
                LEFT JOIN direccion_entrega d ON v.id_direccion = d.id
                LEFT JOIN sucursal s ON v.id_sucursal = s.id_sucur
                WHERE v.id_ven = :id_venta AND v.id_usuario = :usuarioId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_venta' => $id_venta, 'usuarioId' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public function updateDireccionVenta($idVenta, $idDireccion)
    {
        try {
            $sql = "UPDATE venta SET id_direccion = ? WHERE id_ven = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$idDireccion, $idVenta]);
        } catch (Exception $e) {
            throw new Exception('Error al actualizar la dirección de la venta: ' . $e->getMessage());
        }
    }
}
```

### app/models/AdminVentasModel.php

```php
<?php

class AdminVentasModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ✅ Obtener todas las ventas (con búsqueda opcional por nombre de cliente)
    public function getAll($filtroCliente = null)
    {
        $sql = "SELECT 
                v.id_ven AS id,
                v.total,
                CONCAT(u.nombre, ' ', u.apellidos) AS cliente,
                v.fecha_venta AS fecha,
                v.estado AS estado,
                m.nombre AS metodo_pago,
                s.nombre AS sucursal
            FROM venta v
            INNER JOIN usuario u ON v.id_usuario = u.id_us
            LEFT JOIN metodo_pago m ON v.metodo_pago_id = m.id_met
            LEFT JOIN sucursal s ON v.id_sucursal = s.id_sucur";

        $params = [];

        if ($filtroCliente) {
            $sql .= " WHERE CONCAT(u.nombre, ' ', u.apellidos) LIKE ?";
            $params[] = "%$filtroCliente%";
        }

        $sql .= " ORDER BY v.fecha_venta DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ✅ Obtener detalle por venta
    public function getDetalle($id)
    {
        $sql = "SELECT dv.id,
                dv.cantidad,
                dv.precio_unitario,
                v.total,
                p.nombre AS producto_nombre
                FROM detalle_venta dv
                INNER JOIN producto p ON dv.id_producto = p.id_pro
                INNER JOIN venta v ON dv.id_venta = v.id_ven
                WHERE dv.id_venta = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getVentasPorFecha($tipo, $anio, $mes, $dia)
    {
        $where = "";
        $params = [];

        if ($tipo === 'diario') {
            $where = "DATE(v.fecha_venta) = ?";
            $params[] = $dia;
        } elseif ($tipo === 'mensual') {
            $where = "MONTH(v.fecha_venta) = ? AND YEAR(v.fecha_venta) = ?";

            // Separar año y mes
            list($anio, $mes) = explode('-', $mes);
            $params[] = $mes;
            $params[] = $anio;
        } elseif ($tipo === 'anual') {
            $where = "YEAR(v.fecha_venta) = ?";
            $params[] = $anio;
        }

        $sql = "SELECT 
                v.id_ven AS id,
                v.estado,
                CONCAT(u.nombre, ' ', u.apellidos) AS cliente,
                v.fecha_venta AS fecha,
                m.nombre AS metodo_pago,
                v.total,
                s.nombre AS sucursal
            FROM venta v
            INNER JOIN usuario u ON v.id_usuario = u.id_us
            LEFT JOIN metodo_pago m ON v.metodo_pago_id = m.id_met
            LEFT JOIN sucursal s ON v.id_sucursal = s.id_sucur
            WHERE $where
            ORDER BY v.fecha_venta DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getTotalVentasPorFecha($tipo, $fecha)
    {
        $where = "";
        $params = [];

        if ($tipo === 'diario') {
            $where = "DATE(v.fecha_venta) = ?";
            $params[] = $fecha;
        } elseif ($tipo === 'mensual') {
            $where = "MONTH(v.fecha_venta) = MONTH(?) AND YEAR(v.fecha_venta) = YEAR(?)";
            $params[] = $fecha;
            $params[] = $fecha;
        } elseif ($tipo === 'anual') {
            $where = "YEAR(v.fecha_venta) = ?";
            $params[] = $fecha;
        }

        $sql = "SELECT SUM(v.total) as total
            FROM venta v
            WHERE $where";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }


    public function productoMasVendido($tipo, $fecha)
    {
        $condicion = $this->getCondicionFecha($tipo, $fecha, 'v.fecha_venta');

        $sql = "SELECT p.nombre, SUM(dv.cantidad) AS total
            FROM detalle_venta dv
            JOIN venta v ON dv.id_venta = v.id_ven
            JOIN producto p ON dv.id_producto = p.id_pro
            WHERE $condicion
            GROUP BY p.id_pro
            ORDER BY total DESC
            LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function diaConMasVentas($tipo, $fecha)
    {
        $condicion = $this->getCondicionFecha($tipo, $fecha, 'fecha_venta');

        $sql = "SELECT DATE(fecha_venta) AS fecha, SUM(total) AS total
            FROM venta
            WHERE $condicion
            GROUP BY DATE(fecha_venta)
            ORDER BY total DESC
            LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function datosGrafico($tipo, $fecha)
    {
        $condicion = $this->getCondicionFecha($tipo, $fecha, 'fecha_venta');

        $select = $tipo === 'diario' ? "DAY(fecha_venta)" : ($tipo === 'mensual' ? "DAY(fecha_venta)" : "MONTH(fecha_venta)");

        $label = $tipo === 'diario' ? 'Día' : ($tipo === 'mensual' ? 'Día' : 'Mes');

        $sql = "SELECT $select AS etiqueta, SUM(total) AS total
            FROM venta
            WHERE $condicion
            GROUP BY etiqueta
            ORDER BY etiqueta";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getCondicionFecha($tipo, $fecha, $campo)
    {
        switch ($tipo) {
            case 'diario':
                return "DATE($campo) = '{$fecha}'";
            case 'mensual':
                return "DATE_FORMAT($campo, '%Y-%m') = '{$fecha}'";
            case 'anual':
                return "YEAR($campo) = '{$fecha}'";
            default:
                return "1"; // sin filtro
        }
    }


    //------------------------------------------------------------------------------------------------------------------

    // paso 1
    function insertarVenta($idUsuario, $total, $metodoPagoId, $tipoEntrega, $idSucursal)
    {
        try {
            $sql = "INSERT INTO venta (id_usuario, total, metodo_pago_id, tipo_entrega, id_sucursal) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $idUsuario,
                $total,
                $metodoPagoId,
                $tipoEntrega,
                $idSucursal
            ]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Error al insertar venta: ' . $e->getMessage());
        }
    }

    // paso 2
    public function insertarDetalle($idVenta, $carrito)
    {
        try {
            $sql = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            foreach ($carrito as $item) {
                $stmt->execute([
                    $idVenta,
                    $item['id_producto'],
                    $item['cantidad'],
                    $item['precio_producto']
                ]);
            }
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al insertar detalle: ' . $e->getMessage());
        }
    }

    public function guardarDireccion($idVenta, $departamento, $provincia, $distrito, $calle, $numero, $piso, $referencia)
    {
        try {
            $sql = "INSERT INTO direccion_entrega (id_venta, departamento, provincia, distrito, calle, numero, piso, referencia) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $idVenta,
                $departamento,
                $provincia,
                $distrito,
                $calle,
                $numero,
                $piso,
                $referencia
            ]);
        } catch (Exception $e) {
            throw new Exception('Error al guardar direccion: ' . $e->getMessage());
        }
    }

    public function guardarPago($idVenta, $numero_tarjeta, $numero_telefono)
    {
        try {
            $sql = "INSERT INTO pago (id_venta, numero_tarjeta, numero_telefono) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $idVenta,
                $numero_tarjeta ?? null,
                $numero_telefono ?? null
            ]);
        } catch (Exception $e) {
            throw new Exception('Error al guardar pago: ' . $e->getMessage());
        }
    }

    // Cambiar el estado de una venta
    public function actualizarEstado($id, $estado)
    {
        $stmt = $this->db->prepare("UPDATE venta SET estado = :estado WHERE id_ven = :id");
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // Obtener los datos generales de una venta por ID
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT v.id_ven AS id, v.fecha_venta AS fecha, v.estado, v.total FROM venta v WHERE v.id_ven = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDb()
    {
        return $this->db;
    }
}

```

### app/models/MetodoPagoModel.php

```php
<?php

//include_once __DIR__ . '/../config/database.php';

class MetodoPagoModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtiene todos los metodos de pago
    public function getAll(){
        $sql = "SELECT * FROM metodo_pago";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

### app/models/UsuarioModel.php

```php
<?php

//require_once __DIR__ . '/../config/database.php';

class UsuarioModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener todos los usuarios
    public function getAll($nombre = null)
    {
        try {
            $query = "SELECT usuario.*, rol.nombre as rol_nombre
                      FROM usuario
                      INNER JOIN rol ON usuario.id_rol = rol.id_rol";
            
            // Agregar cláusula WHERE si se proporciona un nombre
            if (!empty($nombre)) {
                $query .= " WHERE usuario.nombre LIKE :nombre";
            }
    
            $query .= " ORDER BY usuario.fecha_registro DESC";
    
            $stmt = $this->db->prepare($query);
    
            // Enlazar parámetro solo si se busca por nombre
            if (!empty($nombre)) {
                $stmt->bindValue(':nombre', '%' . $nombre . '%');
            }
    
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Obtener un usuario por ID
    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuario WHERE id_us = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Obtener un usuario por email
    public function findByEmail($email)
    {
        try {
            $query = "SELECT u.*, r.nombre as rol  FROM usuario as u
                      INNER JOIN rol as r ON u.id_rol = r.id_rol
                      WHERE u.correo = :correo AND u.estado = 'activo'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':correo', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En un entorno de producción, sería mejor loguear el error
            // y no mostrarlo directamente al usuario.
            error_log("Error en findByEmail: " . $e->getMessage());
            return false;
        }
    }

    // Crear nuevo usuario
    public function create($data)
    {
        try {
            $stmt = $this->db->prepare("
            INSERT INTO usuario 
            (nombre, apellidos, correo, contrasena, dni, telefono, id_rol, estado, codigo_verificacion) 
            VALUES 
            (:nombre, :apellidos, :correo, :contrasena, :dni, :telefono, :id_rol, :estado, :codigo_verificacion)
        ");

            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellidos', $data['apellidos']);
            $stmt->bindParam(':correo', $data['correo']);
            $stmt->bindParam(':contrasena', $data['contrasena']);
            $stmt->bindParam(':dni', $data['dni']);
            $stmt->bindParam(':telefono', $data['telefono']);
            $stmt->bindParam(':id_rol', $data['id_rol']);
            $stmt->bindParam(':estado', $data['estado']);
            $stmt->bindParam(':codigo_verificacion', $data['codigo_verificacion']);

            return $stmt->execute();
        } catch (PDOException $e) {
            // Relanzar la excepción para que el controlador la maneje
            throw $e;
        }
    }

    // Actualizar usuario existente
    public function update($id, $data)
    {
        try {
            $stmt = $this->db->prepare("
            UPDATE usuario SET
                nombre = :nombre,
                apellidos = :apellidos,
                correo = :correo,
                telefono = :telefono,
                id_rol = :id_rol,
                estado = :estado,
                codigo_verificacion = :codigo_verificacion
            WHERE id_us = :id
        ");

            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellidos', $data['apellidos']);
            $stmt->bindParam(':correo', $data['correo']);
            $stmt->bindParam(':telefono', $data['telefono']);
            $stmt->bindParam(':id_rol', $data['id_rol']);
            $stmt->bindParam(':estado', $data['estado']);
            $stmt->bindParam(':codigo_verificacion', $data['codigo_verificacion']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Eliminar usuario
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM usuario WHERE id_us = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

```

### app/models/RolesModel.php

```php
<?php

//require_once __DIR__ . '/../config/database.php';

class RolesModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM rol";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cambiar la firma para que sea compatible con Model
    public function create($data)
    {
        return parent::create($data);
    }

    // Método personalizado para crear rol
    public function createRol($nombre, $estado)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO rol (nombre, estado) VALUES (:nombre, :estado)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':estado', $estado);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido crear el rol: " . $e->getMessage();
        }
    }

    // Cambiar la firma para que sea compatible con Model
    public function update($id, $data)
    {
        return parent::update($id, $data);
    }

    // Método personalizado para actualizar rol
    public function updateRol($id, $nombre, $estado)
    {
        try {
            $stmt = $this->db->prepare("UPDATE rol SET nombre = :nombre, estado = :estado WHERE id_rol = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':estado', $estado);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido actualizar el rol: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM rol WHERE id_rol = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido eliminar el rol: " . $e->getMessage();
        }
    }
}
```

### app/models/SucursalModel.php

```php
<?php

//include_once __DIR__ . '/../config/database.php';

class SucursalModel extends Model
{
    private $conn;

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM sucursal";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

### app/models/BuilderModel.php

```php
<?php

class BuilderModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtener todas las categorías del builder por tipo
     */
    public function getCategoriesByType($tipo)
    {
        $sql = "SELECT * FROM builder_category WHERE tipo = :tipo ORDER BY orden ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tipo' => $tipo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener productos por categoría de builder
     */
    public function getProductsByBuilderCategory($categoryId)
    {
        $sql = "SELECT p.*, m.nombre AS marca_nombre, mo.nombre AS modelo_nombre
                FROM producto p
                INNER JOIN builder_product_category bpc ON p.id_pro = bpc.id_producto
                LEFT JOIN marca m ON p.id_marca = m.id_mar
                LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
                WHERE bpc.id_builder_category = :categoryId
                AND p.stock > 0
                ORDER BY p.precio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['categoryId' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una categoría por ID
     */
    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM builder_category WHERE id_cat = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Guardar configuración del usuario
     */
    public function saveConfiguration($userId, $nombre, $tipo, $configuracion, $total)
    {
        try {
            $sql = "INSERT INTO builder_configuration (id_usuario, nombre, tipo, configuracion, total) 
                    VALUES (:userId, :nombre, :tipo, :configuracion, :total)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'userId' => $userId,
                'nombre' => $nombre,
                'tipo' => $tipo,
                'configuracion' => json_encode($configuracion),
                'total' => $total
            ]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Error al guardar configuración: ' . $e->getMessage());
        }
    }

    /**
     * Obtener configuraciones del usuario
     */
    public function getUserConfigurations($userId)
    {
        $sql = "SELECT * FROM builder_configuration 
                WHERE id_usuario = :userId 
                ORDER BY fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener producto por ID
     */
    public function getProductById($id)
    {
        $sql = "SELECT p.*, m.nombre AS marca_nombre, mo.nombre AS modelo_nombre
                FROM producto p
                LEFT JOIN marca m ON p.id_marca = m.id_mar
                LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
                WHERE p.id_pro = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Asociar producto con categoría de builder
     */
    public function assignProductToCategory($productId, $categoryId)
    {
        try {
            $sql = "INSERT IGNORE INTO builder_product_category (id_producto, id_builder_category) 
                    VALUES (:productId, :categoryId)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'productId' => $productId,
                'categoryId' => $categoryId
            ]);
        } catch (Exception $e) {
            throw new Exception('Error al asignar producto: ' . $e->getMessage());
        }
    }
}

```

## 9. app/views (VISTAS - FRONTEND)

### 9.1 layouts

### app/views/layouts/default.php

```php
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset(
                $title
            ) ? $title . ' - NewTec' : 'NewTec' ?></title>
    <link rel="icon" href="/perunet/public/img/EMPRESA/p.png">

    <!-- jQuery (debe ir antes de cualquier script que use $) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Otros meta y estilos globales aquí -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/perunet/public/assets/css/app.css?v=<?= time() ?>">
    <script src="/perunet/public/assets/js/app.js"></script>
    <script src="/perunet/public/js/filtros.js"></script>

    <?php if (isset($extraHead)) echo $extraHead; ?>
</head>

<body class="bg-gray-50 min-h-scree">
    <!-- Header global -->
    <?php include __DIR__ . '/../../components/headerNav.php'; ?>

    <!-- Contenido principal -->
    <main class="min-h-screen">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer global -->
    <?php include __DIR__ . '/../../components/footer.php'; ?>

    <!-- Atención al cliente flotante -->
    <?php include __DIR__ . '/../../components/helpSection.php'; ?>

    <!-- Botón flotante Configurador -->
    <?php include __DIR__ . '/../../components/builder_button.php'; ?>

    <!-- Scripts globales aquí -->
    <?php if (isset($extraScripts)) echo $extraScripts; ?>
    <?php if (isset($_SESSION['usuario']['id_us'])): ?>

        

        <script>
            window.PERUNET_USER_ID = <?= (int)$_SESSION['usuario']['id_us'] ?>;
        </script>
    <?php endif; ?>

    <!-- scripts secciones (nosotros, termino y condiciones-->
    <script src="/perunet/public/js/seccion.js" defer></script>
</body>

</html>
```

### app/views/layouts/admin.php

```php
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
```

### 9.2 public

### app/views/public/index.php

```php
<?php
$title = "Inicio - PeruNet";
// Obtener los IDs reales de las categorías destacadas
$idGamer = null;
$idVideo = null;
$idCableado = null;
if (
    isset(
    $categorias
) && is_array($categorias)
) {
    foreach ($categorias as $cat) {
        $nombre = strtolower(trim($cat['nombre']));
        if (strpos($nombre, 'gamer') !== false)
            $idGamer = $cat['id_cat'];
        if (strpos($nombre, 'videovigilancia') !== false)
            $idVideo = $cat['id_cat'];
        if (strpos($nombre, 'cableado') !== false)
            $idCableado = $cat['id_cat'];
    }
}
?>
<!-- Banner Principal (Slider) Hero -->
<div id="banner-slider"
    class="relative w-screen min-h-[80vh] max-h-[100vh] mx-auto mt-0 rounded-none overflow-hidden shadow-2xl border-b border-gray-200 flex items-center justify-center">
    <!-- Mensaje centrado sobre el banner -->
    <div
        class="absolute z-20 left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center">
        <div class="bg-black/50 px-8 py-6 rounded-lg shadow-xl">
            <h1 class="text-4xl md:text-6xl font-bold text-white text-center drop-shadow-lg">Bienvenido a
                NewTec<br><span class="text-lg md:text-2xl font-light">Tecnología y Soluciones</span></h1>
        </div>
    </div>
    <div class="slider-wrapper w-full h-full">
        <img src="/perunet/public/img/EMPRESA/Banner1.jpg"
            class="slider-img w-full h-[80vh] max-h-[100vh] object-cover object-center hidden transition-all duration-700"
            alt="Banner 1">
        <img src="/perunet/public/img/EMPRESA/Banner2.jpg"
            class="slider-img w-full h-[80vh] max-h-[100vh] object-cover object-center hidden transition-all duration-700"
            alt="Banner 2">
        <img src="/perunet/public/img/EMPRESA/Banner3.jpg"
            class="slider-img w-full h-[80vh] max-h-[100vh] object-cover object-center hidden transition-all duration-700"
            alt="Banner 3">
        <img src="/perunet/public/img/EMPRESA/BannerPromocional.jpg"
            class="slider-img w-full h-[80vh] max-h-[100vh] object-cover object-center hidden transition-all duration-700"
            alt="Banner Promocional">
    </div>
    <!-- Flechas -->
    <button id="prev-banner"
        class="absolute left-8 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-4 shadow-lg z-30 border border-gray-300 text-3xl">
        &#8592;
    </button>
    <button id="next-banner"
        class="absolute right-8 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-4 shadow-lg z-30 border border-gray-300 text-3xl">
        &#8594;
    </button>
    <!-- Indicadores -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-4 z-30">
        <span
            class="dot w-5 h-5 bg-white/90 rounded-full cursor-pointer border-2 border-gray-400 transition-all"></span>
        <span
            class="dot w-5 h-5 bg-white/90 rounded-full cursor-pointer border-2 border-gray-400 transition-all"></span>
        <span
            class="dot w-5 h-5 bg-white/90 rounded-full cursor-pointer border-2 border-gray-400 transition-all"></span>
        <span
            class="dot w-5 h-5 bg-white/90 rounded-full cursor-pointer border-2 border-gray-400 transition-all"></span>
    </div>
</div>

<!-- ================= CATEGORÍAS DESTACADAS ================= -->
<section id="categorias-destacadas" class="w-full max-w-7xl mx-auto py-8 px-2 md:px-0">
    <h2 class="text-2xl md:text-3xl font-bold text-center mb-8">NUESTRAS CATEGORÍAS DESTACADAS</h2>
    <div class="flex flex-wrap justify-center gap-4 md:gap-8 mb-8">
        <!-- Gamer -->
        <button
            class="categoria-btn group flex flex-col items-center bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105 focus:outline-none"
            data-categoria="<?= $idGamer ?>" style="width:200px;">
            <img src="/perunet/public/img/EMPRESA/Categoria2.jpg" alt="Gamer"
                class="w-full h-[180px] max-h-[200px] object-cover rounded-t-lg transition-transform duration-200 group-hover:scale-105 group-focus:scale-105 md:h-[140px] md:max-h-[160px] sm:h-[100px] sm:max-h-[120px]"
                />
            <span class="py-3 text-lg font-semibold group-hover:text-red-600">GAMER</span>
        </button>
        <!-- Videovigilancia -->
        <button
            class="categoria-btn group flex flex-col items-center bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105 focus:outline-none"
            data-categoria="<?= $idVideo ?>" style="width:200px;">
            <img src="/perunet/public/img/EMPRESA/Categoria1.jpg" alt="Videovigilancia"
                class="w-full h-[180px] max-h-[200px] object-cover rounded-t-lg transition-transform duration-200 group-hover:scale-105 group-focus:scale-105 md:h-[140px] md:max-h-[160px] sm:h-[100px] sm:max-h-[120px]"
                />
            <span class="py-3 text-lg font-semibold group-hover:text-red-600">VIDEOVIGILANCIA</span>
        </button>
        <!-- Cableado Estructurado -->
        <button
            class="categoria-btn group flex flex-col items-center bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105 focus:outline-none"
            data-categoria="<?= $idCableado ?>" style="width:200px;">
            <img src="/perunet/public/img/EMPRESA/Categoria3.jpg" alt="Cableado Estructurado"
                class="w-full h-[180px] max-h-[200px] object-cover rounded-t-lg transition-transform duration-200 group-hover:scale-105 group-focus:scale-105 md:h-[140px] md:max-h-[160px] sm:h-[100px] sm:max-h-[120px]"
                />
            <span class="py-3 text-lg font-semibold group-hover:text-red-600">CABLEADO ESTRUCTURADO</span>
        </button>
    </div>
</section>

<!-- ================= CONTENEDOR DE PRODUCTOS DINÁMICOS ================= -->
<section id="productos-section" class="w-full max-w-7xl mx-auto pb-12 px-2 md:px-0">
    <div id="productos" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <!-- Aquí se cargarán los productos por AJAX -->
    </div>
</section>
<!-- ================= SECCIÓN DE AYUDA Y CONTACTO ================= -->
<div
    class="w-full max-w-7xl mx-auto my-8 flex flex-col md:flex-row items-center justify-between bg-white rounded-lg shadow p-6">
    <div>
        <h3 class="text-lg font-semibold mb-1">¿Necesitas ayuda?</h3>
        <p class="text-gray-600">Contáctate con un especialista</p>
    </div>
    <a href="/perunet/contacto"
        class="mt-4 md:mt-0 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-full flex items-center gap-2 transition-colors">
        <span class="fa fa-life-ring"></span> CONTACTO
    </a>
</div>

<!-- ================= ESTILOS RESPONSIVOS ================= -->
<style>
    #categorias-destacadas .categoria-btn {
        min-width: 160px;
        max-width: 220px;
    }

    #categorias-destacadas .categoria-btn img {
        width: 100%;
        height: 140px;
        max-height: 180px;
        object-fit: cover;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        transition: transform 0.18s;
    }

    #categorias-destacadas .categoria-btn:hover img,
    #categorias-destacadas .categoria-btn:focus img {
        transform: scale(1.07);
    }

    @media (max-width: 1024px) {
        #categorias-destacadas .categoria-btn img {
            height: 120px;
            max-height: 140px;
        }
    }

    @media (max-width: 768px) {
        #categorias-destacadas .categoria-btn img {
            height: 100px;
            max-height: 120px;
        }

        #categorias-destacadas .categoria-btn {
            min-width: 90vw;
            max-width: 100vw;
        }
    }

    #productos .producto {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.10);
        padding: 2rem 1.2rem 1.5rem 1.2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: box-shadow 0.25s, transform 0.18s;
        min-height: 390px;
        max-width: 320px;
        margin: 0 auto;
        border: 1.5px solid #f3f4f6;
    }

    #productos .producto:hover {
        box-shadow: 0 8px 32px rgba(239, 68, 68, 0.18);
        transform: translateY(-6px) scale(1.035);
        border-color: #fecaca;
    }

    #productos .img-producto {
        width: 120px;
        height: 120px;
        object-fit: contain;
        background: #f9fafb;
        border-radius: 12px;
        margin-bottom: 1.2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e5e7eb;
    }

    #productos .producto h3 {
        font-size: 1.13rem;
        font-weight: 700;
        color: #22223b;
        margin-bottom: 0.2rem;
        text-align: center;
    }

    #productos .producto p {
        font-size: 1rem;
        color: #6b7280;
        margin-bottom: 0.2rem;
        text-align: center;
    }

    #productos .producto h4 {
        font-size: 0.98rem;
        color: #374151;
        font-weight: 400;
        margin-bottom: 0.7rem;
        text-align: center;
        min-height: 38px;
    }

    #productos .producto .marca {
        color: #ef4444;
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.1rem;
    }

    #productos .producto .precio {
        color: #16a34a;
        font-weight: 700;
        font-size: 1.15rem;
        margin-bottom: 0.2rem;
    }

    #productos .btn-ver-mas {
        margin-top: auto;
        background: #ef4444;
        color: #fff;
        padding: 0.65rem 1.5rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1.08rem;
        text-decoration: none;
        transition: background 0.18s, transform 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 0.5em;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.08);
        border: none;
    }

    #productos .btn-ver-mas:hover {
        background: #b91c1c;
        transform: scale(1.07);
    }

    #productos .btn-ver-mas .icon-search {
        display: inline-block;
        width: 1.1em;
        height: 1.1em;
        margin-right: 0.2em;
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        #productos .producto {
            max-width: 100%;
        }
    }
</style>

<script>
    // Slider JS
    const images = document.querySelectorAll('#banner-slider .slider-img');
    const dots = document.querySelectorAll('#banner-slider .dot');
    let current = 0;
    let interval;

    function showSlide(idx) {
        images.forEach((img, i) => {
            img.classList.toggle('hidden', i !== idx);
        });
        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-blue-500', i === idx);
            dot.classList.toggle('border-blue-500', i === idx);
            dot.classList.toggle('scale-125', i === idx);
        });
        current = idx;
    }

    function nextSlide() {
        showSlide((current + 1) % images.length);
    }

    function prevSlide() {
        showSlide((current - 1 + images.length) % images.length);
    }

    function startAutoSlide() {
        interval = setInterval(nextSlide, 4000);
    }

    function stopAutoSlide() {
        clearInterval(interval);
    }

    document.getElementById('next-banner').onclick = () => {
        stopAutoSlide();
        nextSlide();
        startAutoSlide();
    };
    document.getElementById('prev-banner').onclick = () => {
        stopAutoSlide();
        prevSlide();
        startAutoSlide();
    };
    dots.forEach((dot, i) => {
        dot.onclick = () => {
            stopAutoSlide();
            showSlide(i);
            startAutoSlide();
        };
    });

    // Inicializar
    showSlide(0);
    startAutoSlide();
</script>

<!-- ================= JS PARA CATEGORÍAS DESTACADAS Y AJAX ================= -->
<script>
    // Función AJAX para cargar productos por categoría
    function getCategorias(id_categoria) {
        fetch('/perunet/public/php/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'accion=getCategorias&id_categoria=' + encodeURIComponent(id_categoria)
        })
            .then(response => response.text())
            .then(data => {
                // Actualiza la URL sin recargar la página
                if (history.pushState) {
                    history.pushState(null, '', '/perunet/index');
                }
                // Actualiza la lista de productos
                document.getElementById('productos').innerHTML = data;
            });
    }

    // ================= CATEGORÍAS DESTACADAS =================
    document.addEventListener('DOMContentLoaded', function () {
        const categoriaBtns = document.querySelectorAll('.categoria-btn');
        categoriaBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const id_categoria = btn.getAttribute('data-categoria');
                getCategorias(id_categoria);
            });
        });
        // Cargar productos de la primera categoría por defecto (ejemplo: gamer)
        getCategorias('<?= $idGamer ?>');
    });
</script>
```

### app/views/public/contacto.php

```php
<?php
$title = "Perunet | Contacto";
ob_start();
?>
<div class="w-full max-w-5xl mx-auto py-10 px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 bg-white rounded-xl shadow-lg p-8">
        <!-- Información de contacto -->
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-red-700 mb-2 flex items-center gap-2">
                <i class="fa fa-store text-black"></i> Visita Nuestra Tienda
            </h2>
            <p class="text-gray-700">📍 AV PEDRO RUIZ GALLO NRO. 920 INT. 879 CERCADO DE CHICLAYO</p>
            <p class="text-gray-700">📍 AV PEDRO RUIZ 920 INT. 649</p>
            <h2 class="text-2xl font-bold text-red-700 mt-6 mb-2 flex items-center gap-2">
                <i class="fa fa-phone text-black"></i> Central de telefónica
            </h2>
            <p class="text-gray-700">📞 978997728</p>
            <p class="text-gray-700">📞 959175668</p>
            <p class="text-gray-700">📞 965941380</p>
            <h2 class="text-2xl font-bold text-red-700 mt-6 mb-2 flex items-center gap-2">
                <i class="fa fa-envelope text-black"></i> Correos Corporativos
            </h2>
            <p class="text-gray-700">📧 servicioalcliente@perunet.pe</p>
            <p class="text-gray-700">📧 ventas@perunet.pe</p>
        </div>
        <!-- Formulario de contacto -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa fa-paper-plane text-red-600"></i> Envíanos un mensaje
            </h2>
            <form class="space-y-4">
                <input type="text" placeholder="Nombres y Apellidos" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                <input type="tel" placeholder="Teléfono/celular" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                <input type="email" placeholder="Correo electrónico" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                <textarea placeholder="Mensaje" rows="4" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="captcha" required>
                    <label for="captcha" class="text-gray-600">No soy un robot</label>
                </div>
                <small class="block text-gray-400 mb-2">reCAPTCHA Privacidad • Condiciones</small>
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition">ENVIAR MENSAJE</button>
            </form>
        </div>
    </div>
</div>
<!-- Chatbot y scripts específicos -->
<link rel="stylesheet" href="/perunet/public/css/chatbot.css">
<div id="chatbot-container">
    <div id="chatbot-header">
        <h3>Chatbot de Ayuda</h3>
        <button id="close-chatbot-btn">&times;</button>
    </div>
    <div id="chatbot-body">
        <div class="chatbot-message">
            <p>Bienvenido al chatbot de PeruNet. ¿En qué puedo ayudarte?</p>
        </div>
    </div>
    <div id="chatbot-input">
        <input type="text" id="user-input" placeholder="Escribe tu mensaje...">
        <button id="send-btn">Enviar</button>
    </div>
</div>
<script src="/perunet/public/js/chatbot.js"></script>
<script src="/perunet/public/js/funciones.js" defer></script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>
```

### app/views/public/sedes.php

```php
<?php
$title = "Perunet | Sedes";
ob_start();
?>
<div class="w-full max-w-5xl mx-auto py-10 px-4">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-red-700 mb-2 flex items-center justify-center gap-2">
                <i class="fa fa-map-marker-alt text-black"></i> Localizador de tiendas
            </h2>
            <p class="text-gray-700">Encuentra la tienda PeruNet más cercana</p>
        </div>
        <div class="flex flex-col md:flex-row gap-8 items-start">
            <div class="w-full md:w-1/3 mb-6 md:mb-0">
                <div class="mb-4">
                    <label for="sede-select" class="block text-gray-700 font-semibold mb-2">Selecciona una sede:</label>
                    <select id="sede-select" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="default">Sedes</option>
                        <option value="colibri">Colibrí</option>
                        <option value="leguia">Leguía</option>
                        <option value="pedro-ruiz">Pedro Ruiz</option>
                    </select>
                </div>
            </div>
            <div class="w-full md:w-2/3 h-[400px]">
                <div id="map" class="w-full h-full rounded-lg border border-gray-200"></div>
            </div>
        </div>
    </div>
</div>
<!-- Chatbot y scripts específicos -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="/perunet/public/css/chatbot.css">
<div id="chatbot-container">
    <div id="chatbot-header">
        <h3>Chatbot de Ayuda</h3>
        <button id="close-chatbot-btn">&times;</button>
    </div>
    <div id="chatbot-body">
        <div class="chatbot-message">
            <p>Bienvenido al chatbot de PeruNet. ¿En qué puedo ayudarte?</p>
        </div>
    </div>
    <div id="chatbot-input">
        <input type="text" id="user-input" placeholder="Escribe tu mensaje...">
        <button id="send-btn">Enviar</button>
    </div>
</div>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="/perunet/public/js/sedes.js"></script>
<script src="/perunet/public/js/chatbot.js"></script>
<script src="/perunet/public/js/funciones.js" defer></script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>
```

### 9.3 productos

### app/views/productos/index.php

```php
<?php
$title = "Perunet | " . ($nombreCategoria ?? 'Productos');
$style = "productos";
?>
<!-- Cabecera de categoría -->
<div class="w-full bg-gradient-to-r from-red-600 to-pink-500 py-8 px-4 text-center text-white mb-8">
    <h1 class="text-3xl md:text-4xl font-bold mb-2 capitalize">
        <?= htmlspecialchars($nombreCategoria ?? 'Categoría') ?><?= !empty($nombreSubcategoria) ? ' / ' . htmlspecialchars($nombreSubcategoria) : '' ?>
    </h1>
    <p class="text-lg md:text-xl font-light">
        Descubre nuestra selección de productos
        <?= htmlspecialchars($nombreCategoria ?? '') ?><?= !empty($nombreSubcategoria) ? ' / ' . htmlspecialchars($nombreSubcategoria) : '' ?>
    </p>
</div>

<div class="max-w-7xl mx-auto px-2 md:px-6 flex flex-col md:flex-row gap-6">
    <!-- Filtros laterales -->
    <aside class="w-full md:w-64 bg-white rounded-xl shadow p-6 mb-4 md:mb-0 flex-shrink-0">
        <h2 class="text-xl font-semibold mb-4">Filtrar por</h2>
        <!-- Subcategorías como enlaces -->
        <?php if (!empty($subcategorias)): ?>
            <div class="mb-6">
                <h3 class="text-lg font-bold mb-2">Subcategorías</h3>
                <div class="flex flex-col gap-1">
                    <?php foreach ($subcategorias as $subcategoria): ?>
                        <?php
                        $isActive = isset($nombreSubcategoria) && $nombreSubcategoria === $subcategoria['nombre'];
                        $subcatUrl = '/perunet/productos/' . ProductoDetalleController::slugify($nombreCategoria) . '/' . ProductoDetalleController::slugify($subcategoria['nombre']);
                        ?>
                        <a href="<?= $subcatUrl ?>"
                            class="px-3 py-2 rounded transition-colors text-base <?= $isActive ? 'bg-red-600 text-white font-bold' : 'hover:bg-gray-100 text-gray-800' ?>">
                            <?= htmlspecialchars($subcategoria['nombre']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <!-- Marcas como checkboxes -->
        <div class="mb-6">
            <h3 class="text-lg font-bold mb-2">Marcas</h3>
            <form id="marca-form" class="flex flex-col gap-1">
                <?php
                $marcasUnicas = [];
                if (!empty($productos)) {
                    foreach ($productos as $producto) {
                        if (!empty($producto['marca']) && !in_array($producto['marca'], $marcasUnicas)) {
                            $marcasUnicas[] = $producto['marca'];
                        }
                    }
                    sort($marcasUnicas);
                }
                foreach ($marcasUnicas as $marca): ?>
                    <label class="flex items-center gap-2 text-gray-700">
                        <input type="checkbox" value="<?= htmlspecialchars($marca) ?>"
                            class="marca-checkbox rounded border-gray-300 focus:ring-red-500">
                        <span><?= htmlspecialchars($marca) ?></span>
                    </label>
                <?php endforeach; ?>
            </form>
        </div>
        <!-- Rango de precio -->
        <div class="mb-6">
            <h3 class="text-lg font-bold mb-2">Rango de Precio</h3>
            <div class="flex flex-col gap-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Mínimo: S/. <span id="valor-min">0</span></span>
                    <span>Máximo: S/. <span id="valor-max">1000</span></span>
                </div>
                <input type="range" id="precio-min" name="precio-min" min="0" max="1000" step="1" value="0"
                    class="w-full accent-red-600">
                <input type="range" id="precio-max" name="precio-max" min="0" max="1000" step="1" value="1000"
                    class="w-full accent-red-600">
            </div>
        </div>
        <!-- Acciones de filtro -->
        <div class="flex gap-2 mt-4">
            <button id="aplicar-filtros"
                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg flex items-center justify-center gap-2 transition-colors">
                <i class="fas fa-filter"></i> Aplicar Filtros
            </button>
            <button id="reset-filters"
                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 rounded-lg flex items-center justify-center gap-2 transition-colors">
                <i class="fas fa-sync-alt"></i> Restablecer
            </button>
        </div>
    </aside>

    <!-- Grid de productos -->
    <section id="productos" class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $producto): ?>
                <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
                    <div class="w-full flex justify-center mb-4">
                        <img src="/perunet/public/img/<?= htmlspecialchars($producto['imagen'] ?? 'EMPRESA/p.png') ?>"
                            alt="<?= htmlspecialchars($producto['nombre']) ?>" class="w-28 h-28 object-contain rounded">
                    </div>
                    <h3 class="text-lg font-bold text-center mb-1"><?= htmlspecialchars($producto['nombre']) ?></h3>
                    <p class="text-gray-500 text-sm mb-1"><?= htmlspecialchars($producto['marca'] ?? '') ?></p>
                    <p class="text-red-600 font-bold text-lg mb-1">S/ <?= number_format($producto['precio'], 2) ?></p>
                    <span
                        class="text-green-600 font-semibold mb-2"><?= (int) $producto['stock'] > 0 ? 'En stock' : 'Agotado' ?></span>
                    <a href="/perunet/producto/<?= ProductoDetalleController::slugify($producto['categoria']) ?>/<?= ProductoDetalleController::slugify($producto['subcategoria']) ?>/<?= $producto['id_pro'] ?>"
                        class="mt-auto bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors w-full text-center">
                        Ver más
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-16">
                <h3 class="text-2xl font-bold mb-2">No se encontraron productos</h3>
                <p class="text-gray-600 mb-4">Lo sentimos, no hay productos disponibles en esta categoría en este momento.
                </p>
                <a href="/perunet/"
                    class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">Volver
                    al inicio</a>
            </div>
        <?php endif; ?>
    </section>
</div>

<!-- JS de filtros embebido -->
<script>
    // Actualizar valores de rango de precio
    const minInput = document.getElementById('precio-min');
    const maxInput = document.getElementById('precio-max');
    const minVal = document.getElementById('valor-min');
    const maxVal = document.getElementById('valor-max');
    if (minInput && maxInput && minVal && maxVal) {
        minInput.addEventListener('input', () => { minVal.textContent = minInput.value; });
        maxInput.addEventListener('input', () => { maxVal.textContent = maxInput.value; });
    }
    // Aquí puedes agregar más JS para filtros si lo necesitas
</script>
```

### app/views/productos/detalle.php

```php
<?php
$title = "Perunet | Tienda";
$style = "producto-detalle";
// include(__DIR__ . '/../components/head.php');
?>

<!-- Header -->


<body>

    <!-- Product Section -->
    <div class="producto-container">
        <div class="producto-imagen">
            <!-- Product Images -->
            <img src="/perunet/public/img/<?= htmlspecialchars($producto['imagen'] ?? 'EMPRESA/p.png') ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="img-producto">
        </div>

        <!-- descripcion corta -->
        <div class="producto-info">
            <input type="hidden" id="id_usuario"
                value="<?= isset($_SESSION['usuario']['id']) ? $_SESSION['usuario']['id'] : "" ?>">

            <input type="hidden" id="id_producto" value="<?= $producto['id_pro'] ?>">

            <!-- mostrar categoria y subcategoria -->
            <h2 class="producto-categoria"><?= $producto['categoria']; ?> / <?= $producto['subcategoria']; ?></h2>

            <!-- mostrar nombre y modelo -->
            <h1 class="producto-titulo"><?= $producto['nombre']; ?> - <?= $producto['modelo']; ?></h1>

            <!-- mostrar marca y stock -->
            <div class="etiquetas-container">
                <span class="etiqueta etiqueta-marca">
                    <?= $producto['marca']; ?>
                </span>
                <span id="stock" class="etiqueta etiqueta-stock">
                    <?= $msgStock; ?> Stock
                </span>
            </div>

            <!-- mostrar descripcion -->
            <p class="producto-descripcion"><?= $producto['descripcion']; ?></p>

            <!-- mostrar precio -->
            <p id="precio" class="producto-precio">$<?= number_format($producto['precio'], 2); ?></p>

            <!-- mostrar cantidad -->
            <span class="cantidad-label">Cantidad:</span>
            <div class="cantidad-container">
                <button id="restar-cantidad" class="cantidad-btn restar">-</button>
                <input id="cantidad" type="number" value="<?= ($producto['stock_disponible'] == 0) ? $producto['stock_disponible'] : 1 ?>" min="1" max="<?= $producto['stock_disponible'] ?>" class="cantidad-input">
                <button id="sumar-cantidad" class="cantidad-btn sumar">+</button>
            </div>

            <!-- boton agregar al carrito -->
            <button id="agregar-al-carrito" class="btn-agregar-carrito">
                <span>🛒</span> AGREGAR AL CARRITO
            </button>
        </div>
    </div>



    <script type="module" src="/perunet/public/js/carrito.js"></script>
    <script src="/perunet/public/js/funciones.js" defer></script>
</body>

</html>
```

### app/views/productos/productoSelecionado.php

```php
<?php
$title = "Perunet | " . htmlspecialchars($producto['nombre']);
?>

<div class="max-w-5xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-2 gap-10 bg-white rounded-xl shadow-lg mt-10">
    <!-- Imagen del producto -->
    <div class="flex flex-col items-center justify-center">
        <img src="/perunet/public/img/<?= htmlspecialchars($producto['imagen'] ?? 'EMPRESA/p.png') ?>"
            alt="<?= htmlspecialchars($producto['nombre']) ?>"
            class="w-80 h-80 object-contain rounded-lg shadow mb-4">
    </div>
    <!-- Información del producto -->
    <div class="flex flex-col justify-center">
        <input type="hidden" id="id_usuario" value="<?= isset($_SESSION['usuario']['id_us']) ? $_SESSION['usuario']['id_us'] : '' ?>">
        <input type="hidden" id="id_producto" value="<?= $producto['id_pro'] ?>">
        
        <h1 class="text-3xl font-bold mb-4 text-gray-900">
            <?= htmlspecialchars($producto['nombre']) ?>
        </h1>
        <div class="flex flex-col gap-1 mb-4">
            <span class="text-gray-700">Marca: <span class="font-semibold"><?= htmlspecialchars($producto['marca'] ?? 'N/A') ?></span></span>
            <span class="text-gray-700">Modelo: <span class="font-semibold"><?= htmlspecialchars($producto['modelo'] ?? 'N/A') ?></span></span>
            <span class="text-gray-700">Categoría: <span class="font-semibold"><?= htmlspecialchars($producto['categoria'] ?? 'N/A') ?></span></span>
            <span class="text-gray-700">Subcategoría: <span class="font-semibold"><?= htmlspecialchars($producto['subcategoria'] ?? 'N/A') ?></span></span>
        </div>
        <div class="text-2xl font-bold text-red-600 mb-2" id="precio">S/ <?= number_format($producto['precio'], 2) ?></div>
        <div class="mb-4">
            <span id="stock" class="inline-block px-3 py-1 rounded-full text-sm font-medium <?= ($producto['stock_disponible'] > 0) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                <?= $msgStock ?> stock
            </span>
        </div>
        <div class="flex items-center gap-4 mb-6">
            <span class="text-gray-700 font-semibold">Cantidad:</span>
            <div class="flex items-center border rounded-lg overflow-hidden">
                <button id="restar-cantidad" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-lg">-</button>
                <input id="cantidad" type="text" value="<?= ($producto['stock_disponible'] == 0) ? $producto['stock_disponible'] : 1 ?>" min="1" max="<?= $producto['stock_disponible'] ?>" class="w-16 text-center outline-none">
                <button id="sumar-cantidad" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-lg">+</button>
            </div>
        </div>
        <input type="hidden" id="id_usuario" value="<?= isset($_SESSION['usuario']['id']) ? $_SESSION['usuario']['id'] : '' ?>">
        <input type="hidden" id="id_producto" value="<?= $producto['id_pro'] ?>">
        <button id="btn-agregar-carrito" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2 text-lg transition-colors mt-2 <?= $msgStock == 'Agotado' ? 'opacity-50 cursor-not-allowed' : '' ?>" <?= $msgStock == 'Agotado' ? 'disabled' : '' ?>>
            <i class="fas fa-shopping-cart"></i> Agregar al Carrito
        </button>
        <a href="/perunet" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 rounded-lg flex items-center justify-center gap-2 transition-colors mt-3">
            <i class="fas fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>
</div>

<!-- Scripts para carrito (producto seleccionado) -->
<script type="module" src="/perunet/public/js/carrito.js"></script>
```

### 9.4 auth

### app/views/auth/login.php

```php
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
```

### app/views/auth/registro.php

```php
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

```

### 9.5 ventas

### app/views/ventas/index.php

```php
<?php
$title = "Confirmar Compra";
$style = "venta";
ob_start();
?>
<!-- SDK Mercado Pago -->
<script src="https://sdk.mercadopago.com/js/v2"></script>

<main class="min-h-screen flex flex-col items-center py-12 bg-gray-50">
    <!-- Indicador de Progreso (Stepper) -->
    <div class="w-full max-w-3xl mb-8 px-4">
        <div class="flex items-center justify-between relative">
            <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/2 -z-10"></div>
            <div id="line-progress" class="absolute top-1/2 left-0 h-1 bg-red-600 -translate-y-1/2 -z-10 transition-all duration-500" style="width: 0%;"></div>
            
            <div class="step-item flex flex-col items-center gap-2 active" data-step="1">
                <div class="w-10 h-10 rounded-full border-2 border-red-600 bg-white flex items-center justify-center font-bold text-red-600 step-icon transition-all">1</div>
                <span class="text-sm font-semibold text-gray-700">Datos</span>
            </div>
            <div class="step-item flex flex-col items-center gap-2" data-step="2">
                <div class="w-10 h-10 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center font-bold text-gray-400 step-icon transition-all">2</div>
                <span class="text-sm font-semibold text-gray-400">Entrega/Pago</span>
            </div>
            <div class="step-item flex flex-col items-center gap-2" data-step="3">
                <div class="w-10 h-10 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center font-bold text-gray-400 step-icon transition-all">3</div>
                <span class="text-sm font-semibold text-gray-400">Confirmar</span>
            </div>
        </div>
    </div>

    <!-- PASO 1 -->
    <div id="paso1" class="paso-compra w-full max-w-3xl bg-white rounded-xl shadow-lg p-8 animate-fade-in">
        <h2 class="text-2xl font-bold text-red-700 mb-6 flex items-center gap-2">
            <i class="fa fa-user text-black"></i> Datos del Cliente
        </h2>
        <form id="formCliente" class="space-y-4">
            <input type="hidden" name="id" id="usuario_id" value="<?= htmlspecialchars(isset($usuario['id_us']) ? $usuario['id_us'] : '') ?>">
            <div>
                <label for="usuario_nombre" class="block text-gray-700 font-semibold mb-1">Nombres:</label>
                <input id="usuario_nombre" type="text" name="nombre" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" value="<?= htmlspecialchars(isset($usuario['nombre']) ? $usuario['nombre'] : '') ?>" required>
            </div>
            <div>
                <label for="usuario_apellidos" class="block text-gray-700 font-semibold mb-1">Apellidos:</label>
                <input id="usuario_apellidos" type="text" name="apellidos" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" value="<?= htmlspecialchars(isset($usuario['apellidos']) ? $usuario['apellidos'] : '') ?>" required>
            </div>
            <div>
                <label for="usuario_correo" class="block text-gray-700 font-semibold mb-1">Correo electrónico:</label>
                <input id="usuario_correo" type="email" name="correo" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" value="<?= htmlspecialchars(isset($usuario['correo']) ? $usuario['correo'] : '') ?>" required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="usuario_dni" class="block text-gray-700 font-semibold mb-1">DNI:</label>
                    <input id="usuario_dni" type="text" name="dni" maxlength="8" pattern="\d{8}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" value="<?= htmlspecialchars(isset($usuario['dni']) ? $usuario['dni'] : '') ?>" required>
                </div>
                <div>
                    <label for="usuario_telefono" class="block text-gray-700 font-semibold mb-1">Teléfono:</label>
                    <input id="usuario_telefono" type="tel" name="telefono" maxlength="9" pattern="\d{9}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" value="<?= htmlspecialchars(isset($usuario['telefono']) ? $usuario['telefono'] : '') ?>" required>
                </div>
            </div>
        </form>
        <div class="flex justify-end mt-8">
            <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition" onclick="irPaso(2)">Siguiente</button>
        </div>
    </div>

    <!-- PASO 2 -->
    <div id="paso2" class="paso-compra w-full max-w-3xl bg-white rounded-xl shadow-lg p-8 hidden animate-fade-in">
        <h2 class="text-2xl font-bold text-red-700 mb-6 flex items-center gap-2">
            <i class="fa fa-truck text-black"></i> Tipo de Entrega
        </h2>
        <div class="flex flex-col md:flex-row gap-6 mb-6">
            <button id="domicilio-button" type="button" class="flex-1 bg-red-200 hover:bg-red-100 text-red-700 font-bold py-3 px-6 rounded-lg transition" onclick="seleccionarEntrega('domicilio')">Domicilio</button>
            <button id="tienda-button" type="button" class="flex-1 bg-gray-200 hover:bg-gray-100 text-gray-700 font-bold py-3 px-6 rounded-lg transition" onclick="seleccionarEntrega('tienda')">Recojo en Tienda</button>
        </div>

        <!-- FORMULARIO DE DOMICILIO - JUSTO DEBAJO DE LOS BOTONES -->
        <div id="domicilio-section" class="mb-6 hidden animate-fade-in border-t border-gray-100 pt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                 Dirección de Entrega
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="departamento" class="block text-gray-700 font-semibold mb-1">Departamento:</label>
                    <input id="departamento" type="text" name="departamento" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="provincia" class="block text-gray-700 font-semibold mb-1">Provincia:</label>
                    <input id="provincia" type="text" name="provincia" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="distrito" class="block text-gray-700 font-semibold mb-1">Distrito:</label>
                    <input id="distrito" type="text" name="distrito" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="calle" class="block text-gray-700 font-semibold mb-1">Calle:</label>
                    <input id="calle" type="text" name="calle" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="numero" class="block text-gray-700 font-semibold mb-1">Número:</label>
                    <input id="numero" type="text" name="numero" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="piso" class="block text-gray-700 font-semibold mb-1">Piso:</label>
                    <input id="piso" type="text" name="piso" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="md:col-span-2">
                    <label for="referencia" class="block text-gray-700 font-semibold mb-1">Referencia:</label>
                    <input id="referencia" type="text" name="referencia" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>
        </div>

        <!-- LISTA DE SEDES - JUSTO DEBAJO DE LOS BOTONES -->
        <div id="tienda-section" class="mb-6 hidden animate-fade-in border-t border-gray-100 pt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Sedes disponibles</h3>
            <div class="space-y-2">
                <?php foreach ($sucursales as $s): ?>
                    <label class="flex items-center gap-2 bg-gray-100 rounded-lg p-3 cursor-pointer hover:bg-red-100 transition border border-transparent hover:border-red-200">
                        <input type="radio" name="sucursal" value="<?= $s['id_sucur'] ?>" class="w-4 h-4 text-red-600 focus:ring-red-500">
                        <span class="text-gray-700 text-sm">📍 <?= $s['nombre'] ?> - <?= $s['direccion'] ?> - <?= $s['ciudad'] ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- PAGO SEGURO - AL FINAL DEL PASO -->
        <div class="mb-6 py-6 border-t border-gray-100">
            <h3 class="text-xl font-bold text-red-700 mb-4 flex items-center gap-2">
                <i class="fa fa-shield-alt text-black"></i> Pago Seguro
            </h3>
            <div id="paymentBrick_container" class="bg-gray-50 rounded-xl p-4 min-h-[300px]">
                <!-- El formulario de Mercado Pago aparecerá aquí -->
                <p id="mp_loading_text" class="text-center text-gray-500 py-10 animate-pulse">Cargando pasarela segura...</p>
            </div>
        </div>
        
        <div class="flex justify-start mt-8">
            <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition" onclick="irPaso(1)">Regresar</button>
        </div>
    </div>

    <!-- PASO 3 -->
    <div id="paso3" class="paso-compra w-full max-w-3xl bg-white rounded-xl shadow-lg p-8 hidden flex flex-col items-center animate-fade-in text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
            <i class="fa fa-shopping-bag text-3xl text-green-600"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2">¡Compra en Proceso!</h2>
        <p class="text-gray-600 mb-8">Estamos procesando tu pago de forma segura...</p>
        
        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-green-500 animate-pulse" style="width: 70%"></div>
        </div>
    </div>
</main>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>

<script type="module" src="/perunet/public/js/venta.js?v=13"></script>

```

### app/views/ventas/carrito.php

```php
<?php
$title = "Carrito de Compras";
$style = "carrito";
ob_start();
$id_usuario = $_SESSION['usuario']['id_us'] ?? null;
require_once __DIR__ . '/../../models/DetalleCarrito.php';
$carrito = [];
if ($id_usuario) {
    $detalleCarrito = new DetalleCarrito();
    $carrito = $detalleCarrito->getItems($id_usuario);
}
$precio_total = 0;
if (!empty($carrito)) {
    foreach ($carrito as $item) {
        $precio_total += $item['precio_producto'] * $item['cantidad'];
    }
}
?>
<div class="w-full max-w-6xl flex flex-col md:flex-row gap-8 py-8 px-2 md:px-0 mx-auto">
    <!-- Columna izquierda: productos -->
    <section class="flex-1 bg-white rounded-lg shadow-lg p-6 mb-6 md:mb-0">
        <h2 class="text-2xl font-bold text-red-700 mb-4 flex items-center gap-2">
            <i class="fa fa-shopping-cart text-black text-2xl"></i>
            Tu Carrito
        </h2>
        <div id="carrito-contenido">
            <?php if (empty($carrito)): ?>
                <div class="text-center py-8">
                    <p class="text-gray-500 text-lg">Tu carrito está vacío.</p>
                    <a class="inline-block mt-2 px-4 py-2 bg-red-600 hover:bg-red-70 text-white rounded hover:from-black hover:to-red-700 transition"
                        href="/perunet/">Ver productos</a>
                </div>
            <?php endif; ?>
            <input type="hidden" id="id_usuario" value="<?= $id_usuario ?>">
            <?php if (isset($_SESSION['mensaje'])): ?>
                <input type="hidden" id="mensaje" value="<?= $_SESSION['mensaje'] ?>">
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>
            <div id="carrito-productos" class="space-y-4">
                <?php if ($id_usuario != null): ?>
                    <?php foreach ($carrito as $item): ?>
                        <div class="flex items-center gap-4 bg-gray-100 rounded-lg p-4 shadow">
                            <img src="/perunet/public/img/<?= $item['imagen_producto'] ?>"
                                alt="<?= htmlspecialchars($item['nombre_producto']) ?>"
                                class="w-20 h-20 object-contain rounded border border-gray-300 bg-white">
                            <div class="flex-1">
                                <p class="font-semibold text-black text-lg"><?= $item['nombre_producto'] ?></p>
                                <p class="text-gray-700">Precio: <span
                                        class="text-red-700 font-bold">$<?= $item['precio_producto'] ?></span></p>
                                <p class="text-gray-700">Cantidad: <span
                                        class="font-bold text-black"><?= $item['cantidad'] ?></span></p>
                            </div>
                            <button
                                class="ml-2 px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold shadow transition-colors btn-eliminar"
                                onclick="eliminarProducto(<?= $item['id_detalle'] ?>)">Eliminar</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <!-- Columna derecha: métodos de pago y total -->
    <aside class="w-full md:w-80 bg-white rounded-lg shadow-lg p-6 flex flex-col gap-6">
        <div class="payment-methods">
            <h3 class="text-xl font-bold text-black mb-3">Métodos de Pago</h3>
            <div class="grid grid-cols-2 gap-3 mb-2">
                <?php foreach ($metodos as $m): ?>
                    <?php
                    $nombrePago = $m['nombre'] ?? '';
                    $tipoPago = $m['tipo'] ?? $nombrePago;
                    ?>
                    <div class="flex flex-col items-center payment-item cursor-pointer border border-gray-200 rounded-lg p-2 hover:shadow-lg transition"
                        data-metodo="<?= $m['id_met'] ?>">
                        <img src="/perunet/public/img/EMPRESA/PAGOS/<?= htmlspecialchars(strtoupper($nombrePago)) ?>.png"
                            alt="<?= htmlspecialchars($tipoPago) ?>" class="h-10 mb-1">
                        <p class="text-xs text-black font-semibold"><?= htmlspecialchars(strtoupper($tipoPago)) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="text-xs text-gray-500 text-center">SIGUE COMPRANDO, APROVECHA LAS OFERTAS</p>
        </div>
        <div id="total" class="bg-gray-50 rounded-lg p-4 flex flex-col gap-3">
            <p class="text-lg font-bold text-black">Total: <span class="text-red-700" id="total-price">S/ <?= number_format($precio_total, 2) ?></span></p>
            <button id="vaciar-carrito"
                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold shadow transition-colors">Vaciar
                Carrito</button>
            <a href="/perunet/confirmar/compra"
                class="w-full block text-center px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg font-bold shadow transition-colors">Comprar</a>
        </div>
    </aside>
</div>
<!-- Promociones Especiales -->
<section class="w-full max-w-6xl mt-10 mx-auto">
    <h2 class="text-xl font-bold text-black mb-4">PROMOCIONES ESPECIALES</h2>
    <div class="flex flex-col md:flex-row gap-6">
        <div class="flex-1 bg-white rounded-lg shadow p-4 flex items-center gap-4">
            <img src="/perunet/public/img/EMPRESA/Categoria2.jpg" alt="Oferta en Cámaras IP"
                class="w-32 h-32 object-cover rounded">
            <div>
                <h3 class="font-bold text-red-700">Oferta Especial en Cámaras IP</h3>
                <p class="text-gray-700">Compra ahora y obtén un descuento del 20% en cámaras de seguridad IP.</p>
                <a href="/perunet/productos/CamarasIP"
                    class="inline-block mt-2 px-3 py-1 bg-red-600 hover:bg-red-700 to-black text-white rounded hover:from-black hover:to-red-700 transition btn-promotion">Ver
                    Promoción</a>
            </div>
        </div>
    </div>
</section>
<!-- Reseñas de Clientes -->
<section class="w-full max-w-6xl mt-10 mx-auto">
    <h2 class="text-xl font-bold text-black mb-4">Lo que nuestros clientes dicen</h2>
    <div class="flex flex-col md:flex-row gap-6">
        <div class="flex-1 bg-white rounded-lg shadow p-4">
            <p class="text-gray-700">"La página es muy fácil de usar, encontré todo lo que buscaba en minutos. ¡Me
                encanta su diseño y lo rápido que cargan las secciones!"</p>
            <p class="text-right text-sm text-black mt-2">- Estrella Flores</p>
        </div>
        <div class="flex-1 bg-white rounded-lg shadow p-4">
            <p class="text-gray-700">"Excelente servicio. Realicé mi pedido sin problemas, y los productos llegaron
                justo a tiempo. ¡Sin duda volveré a comprar aquí!"</p>
            <p class="text-right text-sm text-black mt-2">- Thalia Burga</p>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>

<!-- Scripts para carrito Lista -->
<script type="module" src="/perunet/public/js/carritoList.js"></script>
```

### 9.6 usuarios

### app/views/usuarios/perfil.php

```php
<?php
$title = "Mi Perfil";
$style = "perfil";
ob_start();
?>
<div class="min-h-screen flex flex-col items-center bg-gray-50 py-8">
    <div class="w-full max-w-4xl flex flex-col md:flex-row gap-8 mb-8">
        <!-- Tarjeta de perfil -->
        <div class="flex-1 bg-white rounded-xl shadow-lg p-8 flex flex-col items-center">
            <img src="https://ui-avatars.com/perunet/api/perunet/?name=<?= urlencode($usuario['nombre'].' '.$usuario['apellidos']) ?>&background=dc2626&color=fff&size=128" alt="Avatar" class="w-32 h-32 rounded-full mb-4 border-4 border-red-100 shadow">
            <h2 class="text-2xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($usuario['nombre'].' '.$usuario['apellidos']) ?></h2>
            <p class="text-gray-600 mb-2"><i class="fa fa-envelope mr-1"></i> <?= htmlspecialchars($usuario['correo']) ?></p>
            <p class="text-gray-600 mb-2"><i class="fa fa-phone mr-1"></i> <?= htmlspecialchars($usuario['telefono']) ?></p>
            <p class="text-gray-600 mb-2"><i class="fa fa-id-card mr-1"></i> DNI: <?= htmlspecialchars($usuario['dni']) ?></p>
            <button type="button" id="editarPerfil" class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">Editar perfil</button>
        </div>
        <!-- Fin tarjeta de perfil -->
    </div>
    <div class="w-full max-w-4xl bg-white rounded-xl shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-black mb-6 flex items-center gap-2">
            <i class="fa fa-chart-bar text-red-700"></i> Resumen de Compras
        </h2>
        <canvas id="graficoCompras" height="100"></canvas>
    </div>
    <div class="w-full max-w-4xl bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-black mb-6 flex items-center gap-2">
            <i class="fa fa-shopping-bag text-red-700"></i> Historial de Compras
        </h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-left">
                        <th class="py-2 px-4"># Pedido</th>
                        <th class="py-2 px-4">Fecha</th>
                        <th class="py-2 px-4">Total</th>
                        <th class="py-2 px-4">Estado</th>
                        <th class="py-2 px-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compras as $compra): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-4 font-semibold">#<?= $compra['id_ven'] ?></td>
                            <td class="py-2 px-4"><?= date('d/m/Y', strtotime($compra['fecha_venta'])) ?></td>
                            <td class="py-2 px-4 text-red-700 font-bold">S/. <?= number_format($compra['total'], 2) ?></td>
                            <td class="py-2 px-4">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                                    <?php
                                    switch($compra['estado']) {
                                        case 'pendiente': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'preparando': echo 'bg-orange-100 text-orange-800'; break;
                                        case 'enviado': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'entregado': echo 'bg-green-100 text-green-800'; break;
                                        case 'cancelado': echo 'bg-red-100 text-red-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>
                                "><?= ucfirst($compra['estado']) ?></span>
                            </td>
                            <td class="py-2 px-4 flex gap-2">
                                <?php if (!empty($compra['id_ven'])): ?>
                                    <a href="/perunet/usuarios/perunet/compra/perunet/<?= $compra['id_ven'] ?>" class="text-blue-600 hover:underline font-semibold">Ver Detalle</a>
                                    <a href="/perunet/usuarios/perunet/tracking/perunet/<?= $compra['id_ven'] ?>" class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold px-3 py-1 rounded transition">Ver Seguimiento</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal de edición de perfil (HTML y JS puro) -->
<div id="modalPerfil" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 relative animate-fade-in">
    <button id="cerrarModalPerfil" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl font-bold">&times;</button>
    <h2 class="text-3xl font-extrabold text-gray-900 mb-6 text-center">Editar Perfil</h2>
    <form id="formEditarPerfil" class="space-y-5">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold mb-1">Nombres:</label>
          <input type="text" name="nombre" id="edit_nombre" class="w-full rounded-lg border border-gray-300 focus:border-red-600 focus:ring-1 focus:ring-red-400 px-4 py-2 shadow-sm transition" required>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">Apellidos:</label>
          <input type="text" name="apellidos" id="edit_apellidos" class="w-full rounded-lg border border-gray-300 focus:border-red-600 focus:ring-1 focus:ring-red-400 px-4 py-2 shadow-sm transition" required>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold mb-1">Correo electrónico:</label>
          <input type="email" name="correo" id="edit_correo" class="w-full rounded-lg border border-gray-300 focus:border-red-600 focus:ring-1 focus:ring-red-400 px-4 py-2 shadow-sm transition" required>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">Teléfono:</label>
          <input type="tel" name="telefono" id="edit_telefono" maxlength="9" pattern="\d{9}" class="w-full rounded-lg border border-gray-300 focus:border-red-600 focus:ring-1 focus:ring-red-400 px-4 py-2 shadow-sm transition" required>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold mb-1">DNI:</label>
          <input type="text" name="dni" id="edit_dni" maxlength="8" pattern="\d{8}" class="w-full rounded-lg border border-gray-300 focus:border-red-600 focus:ring-1 focus:ring-red-400 px-4 py-2 shadow-sm transition" required>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">Nueva Contraseña:</label>
          <input type="password" name="password" id="edit_password" class="w-full rounded-lg border border-gray-300 focus:border-red-600 focus:ring-1 focus:ring-red-400 px-4 py-2 shadow-sm transition" placeholder="••••••••">
        </div>
      </div>
      <div class="flex justify-center gap-4 mt-8">
        <button type="submit" class="bg-[#dc2626] hover:bg-red-700 text-white font-bold px-8 py-2 rounded-lg shadow transition">Guardar Cambios</button>
        <button type="button" id="cancelarModalPerfil" class="bg-gray-200 text-gray-700 font-bold px-8 py-2 rounded-lg hover:bg-gray-300 transition">Cancelar</button>
      </div>
    </form>
  </div>
</div>
<style>
@keyframes fade-in { from { opacity: 0; transform: scale(0.97);} to { opacity: 1; transform: scale(1);} }
.animate-fade-in { animation: fade-in 0.2s; }
</style>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>
<script>
// Mostrar modal al hacer clic en "Editar perfil"
document.getElementById('editarPerfil').addEventListener('click', function() {
  const modal = document.getElementById('modalPerfil');
  modal.classList.remove('hidden');
  // Rellenar los campos con los valores actuales
  document.getElementById('edit_nombre').value = formPerfil.nombre.value;
  document.getElementById('edit_apellidos').value = formPerfil.apellidos.value;
  document.getElementById('edit_correo').value = formPerfil.correo.value;
  document.getElementById('edit_telefono').value = formPerfil.telefono.value;
  document.getElementById('edit_dni').value = formPerfil.dni.value;
  document.getElementById('edit_password').value = '';
});
// Cerrar modal
function cerrarModalPerfil() {
  document.getElementById('modalPerfil').classList.add('hidden');
}
document.getElementById('cerrarModalPerfil').onclick = cerrarModalPerfil;
document.getElementById('cancelarModalPerfil').onclick = cerrarModalPerfil;
// Cerrar al hacer clic fuera del modal
window.addEventListener('mousedown', function(e) {
  const modal = document.getElementById('modalPerfil');
  const box = modal.querySelector('form');
  if (!modal.classList.contains('hidden') && !box.contains(e.target) && !e.target.closest('#editarPerfil')) {
    cerrarModalPerfil();
  }
});
// Enviar formulario por AJAX
const formEditarPerfil = document.getElementById('formEditarPerfil');
formEditarPerfil.onsubmit = function(e) {
  e.preventDefault();
  const data = new FormData(formEditarPerfil);
  fetch('/perunet/public/php/usuario_actualizar.php', {
    method: 'POST',
    body: data
  })
  .then(r => r.json())
  .then(res => {
    if (res.status === 'success') {
      // Actualizar los campos del perfil en la página
      formPerfil.nombre.value = data.get('nombre');
      formPerfil.apellidos.value = data.get('apellidos');
      formPerfil.correo.value = data.get('correo');
      formPerfil.telefono.value = data.get('telefono');
      formPerfil.dni.value = data.get('dni');
      cerrarModalPerfil();
      alert('Perfil actualizado correctamente.');
    } else if (res.status === 'info') {
      alert(res.message);
    } else {
      alert('Error: ' + res.message);
    }
  });
};

// Gráfico de compras (igual que antes)
const compras = <?php echo json_encode($compras); ?>;
const ctx = document.getElementById('graficoCompras').getContext('2d');
const labels = compras.map(c => new Date(c.fecha_venta).toLocaleDateString());
const data = compras.map(c => parseFloat(c.total));
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Total de compras (S/.)',
            data: data,
            backgroundColor: 'rgba(220, 38, 38, 0.7)',
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Compras realizadas' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
```

### app/views/usuarios/detalle_compra.php

```php
<?php
$title = "Detalle de Compra";
ob_start();
?>
<style>
@media print {
    body * { visibility: hidden !important; }
    .comprobante-print, .comprobante-print * { visibility: visible !important; }
    .comprobante-print { position: absolute; left: 0; top: 0; width: 100vw; background: white; box-shadow: none; }
    .no-print { display: none !important; }
}
</style>
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8 mt-8 comprobante-print">
    <h2 class="text-2xl font-bold text-red-700 mb-6 flex items-center gap-2">
        <i class="fa fa-file-invoice"></i> Comprobante de Compra
    </h2>
    <div class="mb-4">
        <strong>Pedido #<?= htmlspecialchars($detalle[0]['id_ven']) ?></strong><br>
        Fecha: <?= date('d/m/Y', strtotime($detalle[0]['fecha_venta'])) ?><br>
        Estado: <?= ucfirst($detalle[0]['estado']) ?>
    </div>
    <table class="min-w-full bg-white border border-gray-200 rounded-lg mb-4">
        <thead>
            <tr class="bg-gray-100 text-gray-700 text-left">
                <th class="py-2 px-4">Producto</th>
                <th class="py-2 px-4">Cantidad</th>
                <th class="py-2 px-4">Precio</th>
                <th class="py-2 px-4">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalle as $item): ?>
            <tr>
                <td class="py-2 px-4"><?= htmlspecialchars($item['producto_nombre']) ?></td>
                <td class="py-2 px-4"><?= $item['cantidad'] ?></td>
                <td class="py-2 px-4">S/ <?= number_format($item['precio_unitario'], 2) ?></td>
                <td class="py-2 px-4">S/ <?= number_format($item['cantidad'] * $item['precio_unitario'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-right font-bold text-lg mb-4">
        Total: S/ <?= number_format($detalle[0]['total'], 2) ?>
    </div>
    <button onclick="window.print()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition no-print">Imprimir</button>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?> 
```

### app/views/usuarios/tracking.php

```php
<?php
$title = "Seguimiento de Pedido #" . $venta['id_ven'];
$style = "perfil"; // Reutilizamos estilos si es necesario
ob_start();

// Definir los estados y su orden
$estados = [
    'pendiente' => ['label' => 'Pedido Recibido', 'icon' => 'fa-clipboard-list'],
    'preparando' => ['label' => 'Preparando', 'icon' => 'fa-box-open'],
    'enviado' => ['label' => 'En Camino', 'icon' => 'fa-shipping-fast'],
    'entregado' => ['label' => 'Entregado', 'icon' => 'fa-check-circle']
];

$estadoActual = strtolower($venta['estado']);
$estadoIndex = array_search($estadoActual, array_keys($estados));
if ($estadoIndex === false) $estadoIndex = -1; // Cancelado u otro
?>

<div class="min-h-screen flex flex-col items-center bg-gray-50 py-8">
    <div class="w-full max-w-4xl bg-white rounded-xl shadow-lg p-8 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fa fa-map-marker-alt text-red-600 mr-2"></i> Seguimiento de Pedido #<?= $venta['id_ven'] ?>
            </h2>
            <a href="/perunet/perfil" class="text-gray-600 hover:text-red-600 font-semibold transition">
                <i class="fa fa-arrow-left mr-1"></i> Volver
            </a>
        </div>

        <!-- Timeline -->
        <div class="relative w-full py-10 px-4">
            <!-- Barra de progreso -->
            <div class="absolute top-1/perunet/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/perunet/2 z-0"></div>
            <div class="absolute top-1/perunet/2 left-0 h-1 bg-red-600 -translate-y-1/perunet/2 z-0 transition-all duration-1000" 
                 style="width: <?= ($estadoIndex >= 0) ? (($estadoIndex /perunet/ (count($estados) - 1)) * 100) : 0 ?>%;"></div>

            <div class="relative z-10 flex justify-between w-full">
                <?php 
                $i = 0;
                foreach ($estados as $key => $info): 
                    $active = $i <= $estadoIndex;
                    $current = $i === $estadoIndex;
                ?>
                    <div class="flex flex-col items-center group">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center border-4 transition-all duration-500
                            <?= $active ? 'bg-red-600 border-red-600 text-white' : 'bg-white border-gray-300 text-gray-400' ?>
                            <?= $current ? 'ring-4 ring-red-200 scale-110' : '' ?>
                        ">
                            <i class="fa <?= $info['icon'] ?> text-lg"></i>
                        </div>
                        <p class="mt-4 font-bold text-sm <?= $active ? 'text-red-700' : 'text-gray-400' ?>">
                            <?= $info['label'] ?>
                        </p>
                    </div>
                <?php 
                $i++;
                endforeach; 
                ?>
            </div>
        </div>

        <?php if ($estadoActual === 'cancelado'): ?>
            <div class="mt-8 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg text-center font-bold">
                <i class="fa fa-times-circle mr-2"></i> Este pedido ha sido cancelado.
            </div>
        <?php endif; ?>

        <!-- Detalles del Pedido -->
        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Detalles de Entrega</h3>
                <p class="text-gray-600 mb-2"><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></p>
                <p class="text-gray-600 mb-2"><strong>Dirección:</strong> <?= htmlspecialchars($venta['direccion'] ?? 'Dirección registrada') ?></p> <!-- Asumiendo que traemos la dirección -->
                <p class="text-gray-600 mb-2"><strong>Método de Pago:</strong> <?= htmlspecialchars($venta['metodo_pago'] ?? 'Tarjeta') ?></p>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Resumen de Compra</h3>
                <ul class="space-y-2">
                    <?php foreach ($detalle as $item): ?>
                        <li class="flex justify-between text-gray-600">
                            <span><?= $item['cantidad'] ?>x <?= htmlspecialchars($item['producto_nombre']) ?></span>
                            <span class="font-semibold">S/. <?= number_format($item['cantidad'] * $item['precio_unitario'], 2) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="mt-4 pt-4 border-t flex justify-between text-xl font-bold text-red-700">
                    <span>Total</span>
                    <span>S/. <?= number_format($venta['total'], 2) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>

```

### 9.7 builder

### app/views/builder/index.php

```php
<?php
$title = "Configurador PC y Setup Gamer";
$style = "builder";
ob_start();
?>

<div class="min-h-screen bg-white py-12">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-extrabold text-gray-900 mb-4">
                <i class="fa fa-cogs text-red-600 mr-3"></i>
                Configurador Gamer
            </h1>
            <p class="text-gray-600 text-lg">Arma tu PC o Setup perfecto paso a paso</p>
        </div>

        <!-- Mode Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- PC Builder Card -->
            <a href="/perunet/builder/perunet/pc?step=1" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800 p-8 hover:scale-105 transition-all duration-300 shadow-2xl">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
                <div class="relative z-10 text-center">
                    <div class="mb-6">
                        <i class="fa fa-desktop text-white text-7xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-4">Armador de PC</h2>
                    <p class="text-blue-100 mb-6">Selecciona cada componente para tu PC gamer personalizada</p>
                    <ul class="text-left text-blue-100 space-y-2 mb-6">
                        <li><i class="fa fa-check-circle text-green-400 mr-2"></i> Procesador</li>
                        <li><i class="fa fa-check-circle text-green-400 mr-2"></i> Tarjeta Gráfica</li>
                        <li><i class="fa fa-check-circle text-green-400 mr-2"></i> RAM, Storage y más</li>
                    </ul>
                    <div class="inline-block bg-white text-blue-700 font-bold px-6 py-3 rounded-full group-hover:bg-blue-100 transition">
                        Comenzar <i class="fa fa-arrow-right ml-2"></i>
                    </div>
                </div>
            </a>

            <!-- Setup Builder Card -->
            <a href="/perunet/builder/perunet/setup?step=1" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-600 to-red-800 p-8 hover:scale-105 transition-all duration-300 shadow-2xl">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
                <div class="relative z-10 text-center">
                    <div class="mb-6">
                        <i class="fa fa-gamepad text-white text-7xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-4">Armador de Setup</h2>
                    <p class="text-red-100 mb-6">Completa tu estación gamer con los mejores periféricos</p>
                    <ul class="text-left text-red-100 space-y-2 mb-6">
                        <li><i class="fa fa-check-circle text-green-400 mr-2"></i> Monitor</li>
                        <li><i class="fa fa-check-circle text-green-400 mr-2"></i> Mouse y Teclado</li>
                        <li><i class="fa fa-check-circle text-green-400 mr-2"></i> Silla, Audífonos y más</li>
                    </ul>
                    <div class="inline-block bg-white text-red-700 font-bold px-6 py-3 rounded-full group-hover:bg-red-100 transition">
                        Comenzar <i class="fa fa-arrow-right ml-2"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Features -->
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center hover:shadow-lg transition">
                <i class="fa fa-bolt text-yellow-500 text-4xl mb-4"></i>
                <h3 class="text-gray-900 font-bold text-xl mb-2">Rápido y Fácil</h3>
                <p class="text-gray-600">Selecciona componentes en pocos pasos</p>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center hover:shadow-lg transition">
                <i class="fa fa-dollar-sign text-green-500 text-4xl mb-4"></i>
                <h3 class="text-gray-900 font-bold text-xl mb-2">Precio en Tiempo Real</h3>
                <p class="text-gray-600">Ve el total actualizado al instante</p>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center hover:shadow-lg transition">
                <i class="fa fa-shopping-cart text-blue-500 text-4xl mb-4"></i>
                <h3 class="text-gray-900 font-bold text-xl mb-2">Agrega al Carrito</h3>
                <p class="text-gray-600">Compra todo junto con un click</p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>

```

### app/views/builder/pc_builder.php

```php
<?php
$title = "Armador de PC Gamer";
$style = "builder";
ob_start();

$totalSteps = count($categories);
$selectedProducts = isset($_SESSION['builder_pc']) ? $_SESSION['builder_pc'] : [];
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 pb-32">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
                    <i class="fa fa-desktop text-blue-600 mr-3"></i>
                    Armador de PC
                </h1>
                <p class="text-gray-600">Paso <?= $currentStep ?> de <?= $totalSteps ?>: <?= $currentCategory['nombre'] ?? '' ?></p>
            </div>
            <a href="/perunet/builder" class="bg-white border-2 border-gray-300 hover:border-red-600 text-gray-700 hover:text-red-600 px-6 py-3 rounded-lg transition">
                <i class="fa fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between mb-2">
                <?php foreach ($categories as $index => $cat): ?>
                    <div class="flex-1 text-center">
                        <div class="text-xs text-gray-400 mb-1"><?= $cat['nombre'] ?></div>
                        <div class="h-2 bg-gray-700 rounded-full mx-1 overflow-hidden">
                            <div class="h-full bg-blue-500 transition-all duration-300" style="width: <?= ($index + 1) <= $currentStep ? '100%' : '0%' ?>"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Category Info -->
        <?php if ($currentCategory): ?>
            <div class="bg-white border-2 border-blue-200 rounded-xl p-6 mb-4 shadow-sm">
                <div class="flex items-center">
                    <i class="fa <?= $currentCategory['icono'] ?> text-blue-600 text-4xl mr-4"></i>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900"><?= $currentCategory['nombre'] ?></h2>
                        <p class="text-gray-600"><?= $currentCategory['descripcion'] ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Navigation Buttons (Top) -->
        <div class="flex justify-between mb-8">
            <?php if ($currentStep > 1): ?>
                <a href="/perunet/builder/pc?step=<?= $currentStep - 1 ?>" class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-bold transition">
                    <i class="fa fa-arrow-left mr-2"></i> Anterior
                </a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>

            <?php if ($currentStep < $totalSteps): ?>
                <a href="/perunet/builder/pc?step=<?= $currentStep + 1 ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold transition">
                    Siguiente <i class="fa fa-arrow-right ml-2"></i>
                </a>
            <?php endif; ?>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden hover:shadow-xl hover:border-blue-400 transition-all duration-300 product-card" data-product-id="<?= $product['id_pro'] ?>" data-product-name="<?= htmlspecialchars($product['nombre']) ?>" data-product-price="<?= $product['precio'] ?>" data-product-image="<?= htmlspecialchars($product['imagen']) ?>">
                        <div class="relative">
                            <img src="/perunet/public/img/<?= htmlspecialchars($product['imagen']) ?>" alt="<?= htmlspecialchars($product['nombre']) ?>" class="w-full h-48 object-cover">
                            <div class="absolute top-2 right-2 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                S/. <?= number_format($product['precio'], 2) ?>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-gray-900 font-bold text-lg mb-2 line-clamp-2"><?= htmlspecialchars($product['nombre']) ?></h3>
                            <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars($product['marca_nombre'] ?? '') ?></p>
                            <p class="text-gray-500 text-xs mb-4 line-clamp-2"><?= htmlspecialchars($product['descripcion'] ?? '') ?></p>
                            <button class="select-product-btn w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition">
                                <i class="fa fa-check-circle mr-2"></i> Seleccionar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12">
                    <i class="fa fa-box-open text-gray-600 text-6xl mb-4"></i>
                    <p class="text-gray-400 text-xl">No hay productos disponibles en esta categoría</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Floating Summary Panel -->
<div id="builder-summary" class="fixed bottom-4 right-4 bg-gray-900 border-2 border-blue-500 rounded-xl shadow-2xl p-6 w-96 max-h-[80vh] overflow-y-auto z-50">
    <h3 class="text-white font-bold text-xl mb-4 flex items-center justify-between">
        <span><i class="fa fa-list-alt text-blue-500 mr-2"></i> Tu Configuración</span>
        <button id="toggle-summary" class="text-gray-400 hover:text-white">
            <i class="fa fa-minus"></i>
        </button>
    </h3>
    <div id="summary-content">
        <div id="selected-products" class="space-y-3 mb-4">
            <!-- Products will be added here dynamically -->
        </div>
        <div class="border-t border-gray-700 pt-4">
            <div class="flex justify-between text-white text-xl font-bold mb-4">
                <span>Total:</span>
                <span id="total-price">S/. 0.00</span>
            </div>
            <form id="add-to-cart-form" action="/perunet/builder/add-to-cart" method="POST">
                <input type="hidden" name="products" id="products-input" value="[]">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition">
                    <i class="fa fa-shopping-cart mr-2"></i> Agregar al Carrito
                </button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>

<script>
// Builder functionality with localStorage persistence
const STORAGE_KEY = 'builder_pc_products';
let selectedProducts = {};
let totalPrice = 0;

// Load saved products from localStorage on page load
function loadSavedProducts() {
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            selectedProducts = JSON.parse(saved);
            updateSummary();
            
            // Highlight selected product in current category
            const currentCategoryId = <?= $currentCategory['id_cat'] ?? 0 ?>;
            if (selectedProducts[currentCategoryId]) {
                document.querySelectorAll('.product-card').forEach(card => {
                    if (card.dataset.productId === selectedProducts[currentCategoryId].id) {
                        card.classList.add('ring-4', 'ring-blue-500');
                    }
                });
            }
        }
    } catch (e) {
        console.error('Error loading saved products:', e);
    }
}

// Save products to localStorage
function saveProducts() {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(selectedProducts));
    } catch (e) {
        console.error('Error saving products:', e);
    }
}

// Select product
document.querySelectorAll('.select-product-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const card = this.closest('.product-card');
        const productId = card.dataset.productId;
        const productName = card.dataset.productName;
        const productPrice = parseFloat(card.dataset.productPrice);
        const productImage = card.dataset.productImage;
        const categoryId = <?= $currentCategory['id_cat'] ?? 0 ?>;
        const categoryName = '<?= $currentCategory['nombre'] ?? '' ?>';

        // Remove previous selection from this category
        if (selectedProducts[categoryId]) {
            totalPrice -= selectedProducts[categoryId].price * (selectedProducts[categoryId].quantity || 1);
        }

        // Add new selection
        selectedProducts[categoryId] = {
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            category: categoryName,
            quantity: selectedProducts[categoryId]?.quantity || 1 // Preserve quantity if exists
        };

        totalPrice += productPrice * (selectedProducts[categoryId].quantity || 1);

        // Save to localStorage
        saveProducts();

        // Update UI
        updateSummary();
        
        // Visual feedback
        document.querySelectorAll('.product-card').forEach(c => c.classList.remove('ring-4', 'ring-blue-500'));
        card.classList.add('ring-4', 'ring-blue-500');
    });
});

function updateSummary() {
    const container = document.getElementById('selected-products');
    const productsArray = Object.values(selectedProducts);
    
    container.innerHTML = productsArray.map((p, index) => `
        <div class="bg-gray-800 rounded-lg p-3" data-product-key="${Object.keys(selectedProducts).find(key => selectedProducts[key] === p)}">
            <div class="flex items-center gap-3 mb-2">
                <img src="/perunet/public/img/${p.image}" class="w-12 h-12 object-cover rounded" onerror="this.src='/perunet/public/img/EMPRESA/p.png'">
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-semibold truncate">${p.category}</p>
                    <p class="text-gray-400 text-xs truncate">${p.name}</p>
                </div>
                <button onclick="removeProduct('${Object.keys(selectedProducts).find(key => selectedProducts[key] === p)}')" class="text-red-400 hover:text-red-300">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <label class="text-gray-400 text-xs">Cant:</label>
                    <input type="number" min="1" max="10" value="${p.quantity || 1}" 
                           class="quantity-input w-16 bg-gray-700 text-white text-center rounded px-2 py-1 text-sm"
                           onchange="updateQuantity('${Object.keys(selectedProducts).find(key => selectedProducts[key] === p)}', this.value)">
                </div>
                <span class="text-blue-400 font-bold text-sm whitespace-nowrap">S/. ${(p.price * (p.quantity || 1)).toFixed(2)}</span>
            </div>
        </div>
    `).join('');

    // Calculate total with quantities
    let total = 0;
    productsArray.forEach(p => {
        total += p.price * (p.quantity || 1);
    });

    document.getElementById('total-price').textContent = `S/. ${total.toFixed(2)}`;
    
    // Send products with quantities
    const productsData = productsArray.map(p => ({
        id: p.id,
        quantity: p.quantity || 1
    }));
    document.getElementById('products-input').value = JSON.stringify(productsData);
}

// Update quantity
function updateQuantity(categoryId, quantity) {
    if (selectedProducts[categoryId]) {
        selectedProducts[categoryId].quantity = parseInt(quantity) || 1;
        saveProducts(); // Save to localStorage
        updateSummary();
    }
}

// Remove product
function removeProduct(categoryId) {
    if (selectedProducts[categoryId]) {
        delete selectedProducts[categoryId];
        saveProducts(); // Save to localStorage
        updateSummary();
        
        // Remove visual selection
        document.querySelectorAll('.product-card').forEach(card => {
            if (card.dataset.productId === selectedProducts[categoryId]?.id) {
                card.classList.remove('ring-4', 'ring-blue-500');
            }
        });
    }
}

// Toggle summary panel
document.getElementById('toggle-summary').addEventListener('click', function() {
    const content = document.getElementById('summary-content');
    const icon = this.querySelector('i');
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.className = 'fa fa-minus';
    } else {
        content.style.display = 'none';
        icon.className = 'fa fa-plus';
    }
});

// Clear localStorage when adding to cart
document.getElementById('add-to-cart-form').addEventListener('submit', function() {
    localStorage.removeItem(STORAGE_KEY);
});

// Load saved products on page load
loadSavedProducts();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>

```

### app/views/builder/setup_builder.php

```php
<?php
$title = "Armador de Setup Gamer";
$style = "builder";
ob_start();

$totalSteps = count($categories);
$selectedProducts = isset($_SESSION['builder_setup']) ? $_SESSION['builder_setup'] : [];
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 pb-32">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
                    <i class="fa fa-gamepad text-red-600 mr-3"></i>
                    Armador de Setup
                </h1>
                <p class="text-gray-600">Paso <?= $currentStep ?> de <?= $totalSteps ?>: <?= $currentCategory['nombre'] ?? '' ?></p>
            </div>
            <a href="/perunet/builder" class="bg-white border-2 border-gray-300 hover:border-red-600 text-gray-700 hover:text-red-600 px-6 py-3 rounded-lg transition">
                <i class="fa fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between mb-2">
                <?php foreach ($categories as $index => $cat): ?>
                    <div class="flex-1 text-center">
                        <div class="text-xs text-gray-400 mb-1"><?= $cat['nombre'] ?></div>
                        <div class="h-2 bg-gray-700 rounded-full mx-1 overflow-hidden">
                            <div class="h-full bg-red-500 transition-all duration-300" style="width: <?= ($index + 1) <= $currentStep ? '100%' : '0%' ?>"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Category Info -->
        <?php if ($currentCategory): ?>
            <div class="bg-white border-2 border-red-200 rounded-xl p-6 mb-4 shadow-sm">
                <div class="flex items-center">
                    <i class="fa <?= $currentCategory['icono'] ?> text-red-600 text-4xl mr-4"></i>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900"><?= $currentCategory['nombre'] ?></h2>
                        <p class="text-gray-600"><?= $currentCategory['descripcion'] ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Navigation Buttons (Top) -->
        <div class="flex justify-between mb-8">
            <?php if ($currentStep > 1): ?>
                <a href="/perunet/builder/setup?step=<?= $currentStep - 1 ?>" class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-bold transition">
                    <i class="fa fa-arrow-left mr-2"></i> Anterior
                </a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>

            <?php if ($currentStep < $totalSteps): ?>
                <a href="/perunet/builder/setup?step=<?= $currentStep + 1 ?>" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold transition">
                    Siguiente <i class="fa fa-arrow-right ml-2"></i>
                </a>
            <?php endif; ?>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden hover:shadow-xl hover:border-red-400 transition-all duration-300 product-card" data-product-id="<?= $product['id_pro'] ?>" data-product-name="<?= htmlspecialchars($product['nombre']) ?>" data-product-price="<?= $product['precio'] ?>" data-product-image="<?= htmlspecialchars($product['imagen']) ?>">
                        <div class="relative">
                            <img src="/perunet/public/img/<?= htmlspecialchars($product['imagen']) ?>" alt="<?= htmlspecialchars($product['nombre']) ?>" class="w-full h-48 object-cover">
                            <div class="absolute top-2 right-2 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                S/. <?= number_format($product['precio'], 2) ?>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-gray-900 font-bold text-lg mb-2 line-clamp-2"><?= htmlspecialchars($product['nombre']) ?></h3>
                            <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars($product['marca_nombre'] ?? '') ?></p>
                            <p class="text-gray-500 text-xs mb-4 line-clamp-2"><?= htmlspecialchars($product['descripcion'] ?? '') ?></p>
                            <button class="select-product-btn w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition">
                                <i class="fa fa-check-circle mr-2"></i> Seleccionar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12">
                    <i class="fa fa-box-open text-gray-600 text-6xl mb-4"></i>
                    <p class="text-gray-400 text-xl">No hay productos disponibles en esta categoría</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Floating Summary Panel -->
<div id="builder-summary" class="fixed bottom-4 right-4 bg-gray-900 border-2 border-red-500 rounded-xl shadow-2xl p-6 w-96 max-h-[80vh] overflow-y-auto z-50">
    <h3 class="text-white font-bold text-xl mb-4 flex items-center justify-between">
        <span><i class="fa fa-list-alt text-red-500 mr-2"></i> Tu Setup</span>
        <button id="toggle-summary" class="text-gray-400 hover:text-white">
            <i class="fa fa-minus"></i>
        </button>
    </h3>
    <div id="summary-content">
        <div id="selected-products" class="space-y-3 mb-4">
            <!-- Products will be added here dynamically -->
        </div>
        <div class="border-t border-gray-700 pt-4">
            <div class="flex justify-between text-white text-xl font-bold mb-4">
                <span>Total:</span>
                <span id="total-price">S/. 0.00</span>
            </div>
            <form id="add-to-cart-form" action="/perunet/builder/add-to-cart" method="POST">
                <input type="hidden" name="products" id="products-input" value="[]">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition">
                    <i class="fa fa-shopping-cart mr-2"></i> Agregar al Carrito
                </button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>

<script>
// Builder functionality with localStorage persistence
const STORAGE_KEY = 'builder_setup_products';
let selectedProducts = {};
let totalPrice = 0;

// Load saved products from localStorage on page load
function loadSavedProducts() {
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            selectedProducts = JSON.parse(saved);
            updateSummary();
            
            // Highlight selected product in current category
            const currentCategoryId = <?= $currentCategory['id_cat'] ?? 0 ?>;
            if (selectedProducts[currentCategoryId]) {
                document.querySelectorAll('.product-card').forEach(card => {
                    if (card.dataset.productId === selectedProducts[currentCategoryId].id) {
                        card.classList.add('ring-4', 'ring-red-500');
                    }
                });
            }
        }
    } catch (e) {
        console.error('Error loading saved products:', e);
    }
}

// Save products to localStorage
function saveProducts() {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(selectedProducts));
    } catch (e) {
        console.error('Error saving products:', e);
    }
}

// Select product
document.querySelectorAll('.select-product-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const card = this.closest('.product-card');
        const productId = card.dataset.productId;
        const productName = card.dataset.productName;
        const productPrice = parseFloat(card.dataset.productPrice);
        const productImage = card.dataset.productImage;
        const categoryId = <?= $currentCategory['id_cat'] ?? 0 ?>;
        const categoryName = '<?= $currentCategory['nombre'] ?? '' ?>';

        // Remove previous selection from this category
        if (selectedProducts[categoryId]) {
            totalPrice -= selectedProducts[categoryId].price * (selectedProducts[categoryId].quantity || 1);
        }

        // Add new selection
        selectedProducts[categoryId] = {
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            category: categoryName,
            quantity: selectedProducts[categoryId]?.quantity || 1 // Preserve quantity if exists
        };

        totalPrice += productPrice * (selectedProducts[categoryId].quantity || 1);

        // Save to localStorage
        saveProducts();

        // Update UI
        updateSummary();
        
        // Visual feedback
        document.querySelectorAll('.product-card').forEach(c => c.classList.remove('ring-4', 'ring-red-500'));
        card.classList.add('ring-4', 'ring-red-500');
    });
});

function updateSummary() {
    const container = document.getElementById('selected-products');
    const productsArray = Object.values(selectedProducts);
    
    container.innerHTML = productsArray.map((p, index) => `
        <div class="bg-gray-800 rounded-lg p-3" data-product-key="${Object.keys(selectedProducts).find(key => selectedProducts[key] === p)}">
            <div class="flex items-center gap-3 mb-2">
                <img src="/perunet/public/img/${p.image}" class="w-12 h-12 object-cover rounded" onerror="this.src='/perunet/public/img/EMPRESA/p.png'">
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-semibold truncate">${p.category}</p>
                    <p class="text-gray-400 text-xs truncate">${p.name}</p>
                </div>
                <button onclick="removeProduct('${Object.keys(selectedProducts).find(key => selectedProducts[key] === p)}')" class="text-red-400 hover:text-red-300">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <label class="text-gray-400 text-xs">Cant:</label>
                    <input type="number" min="1" max="10" value="${p.quantity || 1}" 
                           class="quantity-input w-16 bg-gray-700 text-white text-center rounded px-2 py-1 text-sm"
                           onchange="updateQuantity('${Object.keys(selectedProducts).find(key => selectedProducts[key] === p)}', this.value)">
                </div>
                <span class="text-red-400 font-bold text-sm whitespace-nowrap">S/. ${(p.price * (p.quantity || 1)).toFixed(2)}</span>
            </div>
        </div>
    `).join('');

    // Calculate total with quantities
    let total = 0;
    productsArray.forEach(p => {
        total += p.price * (p.quantity || 1);
    });

    document.getElementById('total-price').textContent = `S/. ${total.toFixed(2)}`;
    
    // Send products with quantities
    const productsData = productsArray.map(p => ({
        id: p.id,
        quantity: p.quantity || 1
    }));
    document.getElementById('products-input').value = JSON.stringify(productsData);
}

// Update quantity
function updateQuantity(categoryId, quantity) {
    if (selectedProducts[categoryId]) {
        selectedProducts[categoryId].quantity = parseInt(quantity) || 1;
        saveProducts(); // Save to localStorage
        updateSummary();
    }
}

// Remove product
function removeProduct(categoryId) {
    if (selectedProducts[categoryId]) {
        delete selectedProducts[categoryId];
        saveProducts(); // Save to localStorage
        updateSummary();
        
        // Remove visual selection
        document.querySelectorAll('.product-card').forEach(card => {
            if (card.dataset.productId === selectedProducts[categoryId]?.id) {
                card.classList.remove('ring-4', 'ring-red-500');
            }
        });
    }
}

// Toggle summary panel
document.getElementById('toggle-summary').addEventListener('click', function() {
    const content = document.getElementById('summary-content');
    const icon = this.querySelector('i');
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.className = 'fa fa-minus';
    } else {
        content.style.display = 'none';
        icon.className = 'fa fa-plus';
    }
});

// Clear localStorage when adding to cart
document.getElementById('add-to-cart-form').addEventListener('submit', function() {
    localStorage.removeItem(STORAGE_KEY);
});

// Load saved products on page load
loadSavedProducts();
</script>

```

### 9.8 errors

### app/views/errors/404.php

```php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada - <?= APP_NAME ?></title>
    <script src="<?= TAILWIND_CDN ?>"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="mb-8">
            <i class="fas fa-exclamation-triangle text-8xl text-yellow-500 mb-4"></i>
            <h1 class="text-6xl font-bold text-gray-900 mb-4">404</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Página no encontrada</h2>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                Lo sentimos, la página que buscas no existe o ha sido movida.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="<?= APP_URL ?>" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-home mr-2"></i>
                Volver al inicio
            </a>
            
            <div class="text-sm text-gray-500">
                <p>O puedes intentar con:</p>
                <div class="mt-2 space-x-4">
                    <a href="<?= APP_URL ?>/productos" class="text-blue-600 hover:text-blue-800">Productos</a>
                    <a href="<?= APP_URL ?>/contacto" class="text-blue-600 hover:text-blue-800">Contacto</a>
                    <a href="<?= APP_URL ?>/sedes" class="text-blue-600 hover:text-blue-800">Sedes</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 
```

### app/views/errors/500.php

```php
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
```

## 10. app/views/admin (PANEL DE ADMINISTRACIÓN)

### app/views/admin/dashboard.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Dashboard - Panel de Administración";
include __DIR__ . '/../../components/adminHead.php';
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../components/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../components/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64">
        <!-- Header del Dashboard -->
        <!-- <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-700 mb-2"></h1>
            <p class="text-gray-600">aaaaa</p>
        </div> -->

        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total de Productos -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Productos</p>
                        <p class="text-3xl font-bold text-blue-600"><?= $totalProductos ?? 0 ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600 font-medium">+12%</span>
                    <span class="text-sm text-gray-500">vs mes anterior</span>
                </div>
            </div>

            <!-- Total de Ventas -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Ventas</p>
                        <p class="text-3xl font-bold text-green-600"><?= $totalVentas ?? 0 ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600 font-medium">+8%</span>
                    <span class="text-sm text-gray-500">vs mes anterior</span>
                </div>
            </div>

            <!-- Total de Usuarios -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Usuarios</p>
                        <p class="text-3xl font-bold text-purple-600"><?= $totalUsuarios ?? 0 ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600 font-medium">+5%</span>
                    <span class="text-sm text-gray-500">vs mes anterior</span>
                </div>
            </div>

            <!-- Ingresos Totales -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Ingresos Totales</p>
                        <p class="text-3xl font-bold text-orange-600">S/ <?= number_format($ingresosTotales ?? 0, 2) ?></p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600 font-medium">+15%</span>
                    <span class="text-sm text-gray-500">vs mes anterior</span>
                </div>
            </div>
        </div>

        <!-- Gráficos y Tablas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Gráfico de Ventas -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Ventas del Mes</h3>
                <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                    <canvas id="grafico-ventas-mes" width="400" height="220"></canvas>
                </div>
                <script>
                const ventasPorDia = <?= json_encode($ventasPorDia ?? [
                    ['fecha' => '2025-07-01', 'total' => 120],
                    ['fecha' => '2025-07-02', 'total' => 80],
                    ['fecha' => '2025-07-03', 'total' => 150],
                    ['fecha' => '2025-07-04', 'total' => 60],
                    ['fecha' => '2025-07-05', 'total' => 200],
                ]) ?>;
                const labels = ventasPorDia.map(v => v.fecha.slice(8,10) + '/' + v.fecha.slice(5,7));
                const data = ventasPorDia.map(v => v.total);
                const ctx = document.getElementById('grafico-ventas-mes').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Ventas S/',
                            data: data,
                            backgroundColor: 'rgba(239,68,68,0.6)',
                            borderColor: 'rgba(239,68,68,1)',
                            borderWidth: 1,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
                </script>
            </div>

            <!-- Productos Más Vendidos -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Productos Más Vendidos</h3>
                <div class="space-y-3">
                    <?php if (!empty($productosMasVendidos)): ?>
                        <?php foreach ($productosMasVendidos as $index => $producto): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-500">#<?= $index + 1 ?></span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700"><?= htmlspecialchars($producto['nombre']) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($producto['marca'] ?? '') ?></p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-blue-600"><?= $producto['cantidad_vendida'] ?? 0 ?> vendidos</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p class="text-gray-500">No hay datos disponibles</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Últimas Actividades -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Últimas Ventas Mejorado -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Últimas Ventas</h3>
                <div class="space-y-3">
                    <?php if (!empty($ultimasVentas)): ?>
                        <?php foreach (array_slice($ultimasVentas, 0, 5) as $venta): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-bold text-gray-700"><?= htmlspecialchars($venta['cliente'] ?? 'Cliente') ?></p>
                                    <p class="text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($venta['fecha'] ?? 'now')) ?></p>
                                </div>
                                <div class="text-right">
                                    <?php if (($venta['total'] ?? 0) > 0): ?>
                                        <p class="text-sm font-bold text-green-600">S/ <?= number_format($venta['total'], 2) ?></p>
                                    <?php else: ?>
                                        <span class="inline-block px-2 py-1 text-xs rounded bg-gray-200 text-gray-500">Sin monto</span>
                                    <?php endif; ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        <?= ($venta['estado'] ?? '') === 'entregado' ? 'bg-green-100 text-green-800' :
                                            (($venta['estado'] ?? '') === 'cancelado' ? 'bg-red-100 text-red-800' :
                                            (($venta['estado'] ?? '') === 'enviado' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) ?>">
                                        <?= ucfirst($venta['estado'] ?? 'pendiente') ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-400">No hay ventas recientes</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Productos con Stock Bajo Mejorado -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Productos con Stock Bajo</h3>
                <div class="space-y-3">
                    <?php if (!empty($productosStockBajo)): ?>
                        <?php foreach ($productosStockBajo as $producto): ?>
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100">
                                <div class="flex items-center space-x-3">
                                    <img src="/perunet/public/img/<?= htmlspecialchars($producto['imagen'] ?? 'EMPRESA/p.png') ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="w-10 h-10 object-contain rounded border border-gray-200">
                                    <div>
                                        <p class="text-sm font-bold text-gray-700"><?= htmlspecialchars($producto['nombre']) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($producto['marca'] ?? '') ?></p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-red-600"><?= $producto['stock'] ?? 0 ?> unidades</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-8">
                            <svg class="w-16 h-16 text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-lg text-gray-500 font-semibold">¡Todo el stock está bien!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
```

### 10.1 config

### app/views/admin/config/categorias.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php include __DIR__ . '/../../../components/adminHead.php'; ?>

<body class="bg-gray-50 min-h-screen">
    <!-- incluir navBar -->
    <?php
    $title = 'Panel de Categorias';
    include __DIR__ . '/../../../components/adminNavBar.php';
    ?>

    <!-- incluir menu -->
    <?php include __DIR__ . '/../../../components/adminMenuNav.php'; ?>
    <!-- contenido principal -->
    <main class="p-4 md:ml-64 mt-13 min-h-screen bg-gray-50">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row-reverse md:items-center md:justify-between gap-4 mb-3">
                <button id="openModalBtn" class="bg-green-100 text-green-700 font-semibold rounded-full px-6 py-2 hover:bg-green-200 transition">
                    + Añadir Categoría
                </button>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nombre</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="tableBody">
                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as $categoria): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($categoria['id_cat']) ?></td>
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($categoria['nombre']) ?></td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <button class="bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition" onclick="editarCategoria('<?= htmlspecialchars(json_encode($categoria)) ?>')">Editar</button>
                                        <button class="bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition" onclick="eliminarCategoria('<?= htmlspecialchars($categoria['id_cat']) ?>')">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center py-6 text-gray-400">No hay categorías registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <!-- ------------------ -->

    <!-- Modal (oculto por defecto) -->
    <div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4">
            <!-- Botón cerrar -->
            <button id="closeModalX" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            <h2 class="text-xl font-semibold text-gray-700 mb-4" id="modalTitle">Agregar nueva Categoría</h2>
            <form class="space-y-4">
                <!-- para almacenar el id cuando se edita -->
                <input type="hidden" id="categoria_id_form">
                <div>
                    <label for="nombre_form" class="block text-sm font-medium text-gray-600 mb-1">Nombre</label>
                    <input type="text" id="nombre_form" name="nombre_form" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" required>
                </div>
                <div class="flex gap-2 pt-4">
                    <button id="submitForm" type="button" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-6 py-2 hover:bg-blue-200 transition flex-1">
                        Guardar
                    </button>
                    <button id="closeModalBtn" type="button" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition flex-1">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script type="module" src="../../public/js/categorias.js"></script>
</body>

</html>
```

### app/views/admin/config/subcategorias.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Administrar Subcategorías";
include __DIR__ . '/../../../components/adminHead.php';
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64 mt-13 min-h-screen bg-gray-50">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row-reverse md:items-center md:justify-between gap-4 mb-3">
                <button id="openModalBtn" class="bg-yellow-100 text-yellow-700 font-semibold rounded-full px-6 py-2 hover:bg-yellow-200 transition">
                    + Añadir Subcategoría
                </button>
            </div>

            <!-- Mensaje de confirmación -->
            <?php if (isset($_GET['mensaje'])): ?>
                <div class="mb-6 p-4 rounded-xl border border-green-200 bg-green-50 text-green-800">
                    <?php
                    switch ($_GET['mensaje']) {
                        case 'guardado':
                            echo "✅ Subcategoría creada correctamente.";
                            break;
                        case 'actualizado':
                            echo "✏️ Subcategoría actualizada correctamente.";
                            break;
                        case 'eliminado':
                            echo "🗑️ Subcategoría eliminada correctamente.";
                            break;
                        default:
                            echo "Ocurrió un error.";
                            break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Tabla de Subcategorías -->
            <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nombre</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Categoría</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="tableBody">
                        <?php if (!empty($subCategorias)): ?>
                            <?php foreach ($subCategorias as $subCategoria): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($subCategoria['id']) ?></td>
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($subCategoria['nombre']) ?></td>
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($subCategoria['categoria']) ?></td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <button class="bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition" onclick="editarSubCategoria(<?= htmlspecialchars(json_encode($subCategoria)) ?>)">Editar</button>
                                        <button class="bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition" onclick="eliminarSubCategoria(<?= htmlspecialchars($subCategoria['id']) ?>)">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-6 text-gray-400">No hay subcategorías registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4">
            <!-- Botón cerrar -->
            <button id="closeModalX" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>

            <h2 class="text-xl font-semibold text-gray-700 mb-4" id="modalTitle">Agregar nueva Subcategoría</h2>
            <form class="space-y-4">
                <!-- para almacenar el id cuando se edita -->
                <input type="hidden" id="subCategoria_id_form">

                <div>
                    <label for="nombre_form" class="block text-sm font-medium text-gray-600 mb-1">Nombre</label>
                    <input type="text" id="nombre_form" name="nombre_form" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" required>
                </div>

                <div>
                    <label for="categoria_id_form" class="block text-sm font-medium text-gray-600 mb-1">Categoría</label>
                    <div id="categoria_select_container">
                        <select id="categoria_id_form" name="categoria_id_form" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" required>
                            <option value="">-- Selecciona una categoría --</option>
                            echo($categorias);
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id_cat'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2 pt-4">
                    <button id="submitForm" type="button" class="bg-yellow-100 text-yellow-700 font-semibold rounded-full px-6 py-2 hover:bg-yellow-200 transition flex-1">
                        Guardar
                    </button>
                    <button id="closeModalBtn" type="button" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition flex-1">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script type="module" src="../../public/js/subCategorias.js"></script>
</body>

</html>
```

### app/views/admin/config/marcas.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Administrar Marcas";
include __DIR__ . '/../../../components/adminHead.php';
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64 mt-13 min-h-screen bg-gray-50">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row-reverse md:items-center md:justify-between gap-4 mb-3">
                <button id="openModalBtn" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-6 py-2 hover:bg-blue-200 transition">
                    + Añadir Marca
                </button>
            </div>

            <!-- Mensaje de confirmación -->
            <?php if (isset($_GET['mensaje'])): ?>
                <div class="mb-6 p-4 rounded-xl border border-green-200 bg-green-50 text-green-800">
                    <?php
                    switch ($_GET['mensaje']) {
                        case 'guardado':
                            echo "✅ Marca creada correctamente.";
                            break;
                        case 'actualizado':
                            echo "✏️ Marca actualizada correctamente.";
                            break;
                        case 'eliminado':
                            echo "🗑️ Marca eliminada correctamente.";
                            break;
                        default:
                            echo "Ocurrió un error.";
                            break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Tabla de Marcas -->
            <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nombre</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="tableBody">
                        <?php if (!empty($marcas)): ?>
                            <?php foreach ($marcas as $marca): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($marca['id_mar']) ?></td>
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($marca['nombre']) ?></td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <button class="bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition" onclick="editarMarca(<?= htmlspecialchars(json_encode($marca)) ?>)">Editar</button>
                                        <button class="bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition" onclick="eliminarMarca(<?= htmlspecialchars($marca['id_mar']) ?>)">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center py-6 text-gray-400">No hay marcas registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4">
            <!-- Botón cerrar -->
            <button id="closeModalX" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>

            <h2 class="text-xl font-semibold text-gray-700 mb-4" id="modalTitle">Agregar nueva Marca</h2>
            <form class="space-y-4">
                <!-- para almacenar el id cuando se edita -->
                <input type="hidden" id="marca_id_form">

                <div>
                    <label for="nombre_form" class="block text-sm font-medium text-gray-600 mb-1">Nombre</label>
                    <input type="text" id="nombre_form" name="nombre_form" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" required>
                </div>

                <div class="flex gap-2 pt-4">
                    <button id="submitForm" type="button" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-6 py-2 hover:bg-blue-200 transition flex-1">
                        Guardar
                    </button>
                    <button id="closeModalBtn" type="button" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition flex-1">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script type="module" src="../../public/js/marcas.js"></script>
</body>

</html>
```

### app/views/admin/config/modelos.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Administrar Modelos";
include __DIR__ . '/../../../components/adminHead.php';?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/adminMenuNav.php'; ?>
    <main class="p-4 md:ml-64 mt-3 min-h-screen bg-gray-50 ">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row-reverse md:items-center md:justify-between gap-4 mb-3">
                <button id="openModalBtn" class="bg-cyan-100 text-cyan-700 font-semibold rounded-full px-6 py-2 hover:bg-cyan-200 transition">
                    + Añadir Modelo
                </button>
            </div>

            <!-- Mensaje de confirmación -->
            <?php if (isset($_GET['mensaje'])): ?>
                <div class="mb-6 p-4 rounded-xl border border-green-200 bg-green-50 text-green-800">
                    <?php
                    switch ($_GET['mensaje']) {
                        case 'guardado':
                            echo "✅ Modelo creado correctamente.";
                            break;
                        case 'actualizado':
                            echo "✏️ Modelo actualizado correctamente.";
                            break;
                        case 'eliminado':
                            echo "🗑️ Modelo eliminado correctamente.";
                            break;
                        default:
                            echo "Ocurrió un error.";
                            break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Tabla de Modelos -->
            <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nombre</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Marca</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="tableBody">
                        <?php if (!empty($modelos)): ?>
                            <?php foreach ($modelos as $modelo): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($modelo['id_mod']) ?></td>
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($modelo['nombre']) ?></td>
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($modelo['marca']) ?></td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <button class="bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition" onclick="editarModelo(<?= htmlspecialchars(json_encode($modelo)) ?>)">Editar</button>
                                        <button class="bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition" onclick="eliminarModelo(<?= htmlspecialchars($modelo['id_mod']) ?>)">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-6 text-gray-400">No hay modelos registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4">
            <!-- Botón cerrar -->
            <button id="closeModalX" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>

            <h2 class="text-xl font-semibold text-gray-700 mb-4" id="modalTitle">Agregar nuevo Modelo</h2>
            <form class="space-y-4">
                <!-- para almacenar el id cuando se edita -->
                <input type="hidden" id="modelo_id_form">

                <div>
                    <label for="nombre_form" class="block text-sm font-medium text-gray-600 mb-1">Nombre</label>
                    <input type="text" id="nombre_form" name="nombre_form" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" required>
                </div>

                <div>
                    <label for="marca_id_form" class="block text-sm font-medium text-gray-700">Marca</label>
                    <select id="marca_id_form" name="marca_id_form" class="
                    mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border" required>
                        <option value="">-- Selecciona una marca --</option>
                        <?php foreach ($marcas as $marca): ?>
                            <option value="<?= $marca['id_mar'] ?>"><?= htmlspecialchars($marca['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex gap-2 pt-4">
                    <button id="submitForm" type="button" class="bg-cyan-100 text-cyan-700 font-semibold rounded-full px-6 py-2 hover:bg-cyan-200 transition flex-1">
                        Guardar
                    </button>
                    <button id="closeModalBtn" type="button" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition flex-1">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script type="module" src="../../public/js/modelos.js"></script>
</body>

</html>
```

### app/views/admin/config/roles.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Administrar Roles";
include __DIR__ . '/../../../components/adminHead.php';
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64 mt-3 min-h-screen bg-gray-50">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row-reverse md:items-center md:justify-between gap-4 mb-3">
                <button id="openModalBtn" class="bg-slate-100 text-slate-700 font-semibold rounded-full px-6 py-2 hover:bg-slate-200 transition">
                    + Añadir Rol
                </button>
            </div>

            <!-- Mensaje de confirmación -->
            <?php if (isset($_GET['mensaje'])): ?>
                <div class="mb-6 p-4 rounded-xl border border-green-200 bg-green-50 text-green-800">
                    <?php
                    switch ($_GET['mensaje']) {
                        case 'guardado':
                            echo "✅ Rol creado correctamente.";
                            break;
                        case 'actualizado':
                            echo "✏️ Rol actualizado correctamente.";
                            break;
                        case 'eliminado':
                            echo "🗑️ Rol eliminado correctamente.";
                            break;
                        default:
                            echo "Ocurrió un error.";
                            break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Tabla de Roles -->
            <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nombre</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Estado</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Fecha de Creación</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Fecha de Modificación</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="tableBody">
                        <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $rol): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($rol['id_rol']) ?></td>
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($rol['nombre']) ?></td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full <?= htmlspecialchars($rol['estado']) === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                            <?= ucfirst(htmlspecialchars($rol['estado'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-700"><?= (!empty($rol['create_at'])) ? date('d/m/Y H:i', strtotime($rol['create_at'])) : '---' ?></td>
                                    <td class="px-4 py-2 text-gray-700"><?= (!empty($rol['update_at'])) ? date('d/m/Y H:i', strtotime($rol['update_at'])) : '---' ?></td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <button class="bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition" onclick='editarRol(`<?= htmlspecialchars(json_encode($rol)) ?>`)'>Editar</button>
                                        <button class="bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition" onclick="eliminarRol(`<?= htmlspecialchars($rol['id_rol']) ?>`)">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-6 text-gray-400">No hay roles registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4">
            <!-- Botón cerrar -->
            <button id="closeModalX" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>

            <h2 class="text-xl font-semibold text-gray-700 mb-4" id="modalTitle">Agregar nuevo Rol</h2>
            <form class="space-y-4">
                <!-- para almacenar el id cuando se edita -->
                <input type="hidden" id="rol_id_form">

                <div>
                    <label for="nombre_form" class="block text-sm font-medium text-gray-600 mb-1">Nombre</label>
                    <input type="text" id="nombre_form" name="nombre_form" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" required>
                </div>

                <div>
                    <label for="estado_form" class="block text-sm font-medium text-gray-600 mb-1">Estado</label>
                    <select id="estado_form" name="estado_form" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" required>
                        <option value="">-- Selecciona un estado --</option>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?= $estado ?>"><?= ucfirst(htmlspecialchars($estado)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex gap-2 pt-4">
                    <button id="submitForm" type="button" class="bg-slate-100 text-slate-700 font-semibold rounded-full px-6 py-2 hover:bg-slate-200 transition flex-1">
                        Guardar
                    </button>
                    <button id="closeModalBtn" type="button" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition flex-1">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script type="module" src="../../public/js/roles.js"></script>
</body>

</html>
```

### 10.2 productos

### app/views/admin/productos/index.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Administrar Productos";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-700">Administrar Productos</h1>
            <form class="flex gap-2 w-full md:w-auto" method="get">
                <input type="text" name="buscar" placeholder="Buscar" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" class="rounded-full border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700 w-full md:w-64">
                <button type="submit" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-4 py-2 hover:bg-blue-200 transition">Buscar</button>
                <button onclick="window.location.href='/perunet/admin/perunet/productos/perunet/crear'" type="button" class="bg-green-100 text-green-700 font-semibold rounded-full px-4 py-2 hover:bg-green-200 transition whitespace-nowrap">+ Añadir Producto</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Precio</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Stock</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Marca</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Modelo</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Imagen</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($productos)): ?>
                        <?php foreach ($productos as $producto): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($producto['id_pro']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($producto['nombre']) ?></td>
                                <td class="px-4 py-2 text-gray-700">S/. <?= htmlspecialchars(number_format($producto['precio'], 2)) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($producto['stock']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($producto['marca'] ?? $producto['marca_nombre'] ?? '') ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($producto['modelo'] ?? $producto['modelo_nombre'] ?? '') ?></td>
                                <td class="px-4 py-2">
                                    <img src="/perunet/public/img/<?= htmlspecialchars($producto['imagen'] ?? 'EMPRESA/p.png') ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="w-20 h-20 object-contain rounded-lg border border-gray-200 bg-gray-50 mx-auto" />
                                </td>
                                <td class="px-4 py-2 flex gap-2">
                                    <button onclick="window.location.href='/perunet/admin/productos/editar/<?= $producto['id_pro'] ?>'" class="mt-7 bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition">Editar</button>
                                    <a href="/perunet/admin/productos/eliminar/<?= $producto['id_pro'] ?>" class=" mt-7 bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition" onclick="return confirm('¿Seguro que deseas eliminar este producto?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-400">No hay productos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Paginación -->
    <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
        <div class="p-4 md:ml-64">
            <div class="max-w-5xl mx-auto">
                <div class="bg-white rounded-xl shadow-md p-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <!-- Información de resultados -->
                        <div class="text-sm text-gray-600">
                            Mostrando <?= (($pagination['currentPage'] - 1) * $pagination['perPage']) + 1 ?>
                            a <?= min($pagination['currentPage'] * $pagination['perPage'], $pagination['totalProductos']) ?>
                            de <?= $pagination['totalProductos'] ?> productos
                        </div>

                        <!-- Navegación de páginas -->
                        <div class="flex items-center gap-2">
                            <!-- Botón Anterior -->
                            <?php if ($pagination['hasPrevPage']): ?>
                                <a href="?page=<?= $pagination['prevPage'] ?><?= !empty($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : '' ?>"
                                    class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition">
                                    ← Anterior
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-2 text-sm font-medium text-gray-300 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                    ← Anterior
                                </span>
                            <?php endif; ?>

                            <!-- Números de página -->
                            <div class="flex items-center gap-1">
                                <?php
                                $startPage = max(1, $pagination['currentPage'] - 2);
                                $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);

                                // Mostrar primera página si no está en el rango
                                if ($startPage > 1): ?>
                                    <a href="?page=1<?= !empty($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : '' ?>"
                                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition">
                                        1
                                    </a>
                                    <?php if ($startPage > 2): ?>
                                        <span class="px-2 py-2 text-sm text-gray-400">...</span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <?php if ($i == $pagination['currentPage']): ?>
                                        <span class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg">
                                            <?= $i ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="?page=<?= $i ?><?= !empty($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : '' ?>"
                                            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition">
                                            <?= $i ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <!-- Mostrar última página si no está en el rango -->
                                <?php if ($endPage < $pagination['totalPages']): ?>
                                    <?php if ($endPage < $pagination['totalPages'] - 1): ?>
                                        <span class="px-2 py-2 text-sm text-gray-400">...</span>
                                    <?php endif; ?>
                                    <a href="?page=<?= $pagination['totalPages'] ?><?= !empty($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : '' ?>"
                                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition">
                                        <?= $pagination['totalPages'] ?>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Botón Siguiente -->
                            <?php if ($pagination['hasNextPage']): ?>
                                <a href="?page=<?= $pagination['nextPage'] ?><?= !empty($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : '' ?>"
                                    class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition">
                                    Siguiente →
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-2 text-sm font-medium text-gray-300 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                    Siguiente →
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Modal para añadir/perunet/editar producto -->
    <div id="modalProducto" class="fixed inset-0 bg-black/perunet/60 flex items-center justify-center hidden z-50 transition-all duration-300 p-4">
        <div class="bg-gradient-to-br from-white via-blue-50 to-blue-100 rounded-3xl shadow-xl p-8 w-full max-w-2xl relative border border-blue-100 animate-fadeIn max-h-[90vh] overflow-y-auto custom-scrollbar" style="box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);">
            <button id="closeModalProducto" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 text-2xl font-bold transition">&times;</button>
            <div class="flex flex-col items-center mb-6">
                <div class="bg-blue-100 text-blue-600 rounded-full p-3 mb-2 shadow-sm">
                    <svg xmlns='http://www.w3.org/perunet/2000/perunet/svg' class='h-8 w-8' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' /perunet/>
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-900 text-center tracking-tight" id="modalTitleProducto">Nuevo Producto</h2>
            </div>
            <form method="post" action="/perunet/admin/perunet/productos/perunet/guardar" class="space-y-5" id="productoForm" enctype="multipart/perunet/form-data">
                <input type="hidden" name="id" id="producto_id_form">

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Nombre</label>
                    <input type="text" name="nombre" id="nombre_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Descripción</label>
                    <textarea name="descripcion" id="descripcion_form" rows="3" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400 resize-none"></textarea>
                </div>

                <div class="flex gap-3">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Precio</label>
                        <input type="number" name="precio" id="precio_form" step="0.01" min="0" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Stock</label>
                        <input type="number" name="stock" id="stock_form" min="0" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Marca</label>
                        <select name="id_marca" id="id_marca_form" class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition" required>
                            <option value="">-- Selecciona una marca --</option>
                            <?php if (isset($marcas)): foreach ($marcas as $marca): ?>
                                    <option value="<?= $marca['id_mar'] ?>"><?= htmlspecialchars($marca['nombre']) ?></option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Modelo</label>
                        <select name="id_modelo" id="id_modelo_form" class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition" required>
                            <option value="">-- Selecciona un modelo --</option>
                            <?php if (isset($modelos)): foreach ($modelos as $modelo): ?>
                                    <option value="<?= $modelo['id_mod'] ?>"><?= htmlspecialchars($modelo['nombre']) ?></option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Subcategoría</label>
                        <select name="id_subcategoria" id="id_subcategoria_form" class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition" required>
                            <option value="">-- Selecciona una subcategoría --</option>
                            <?php if (isset($subcategorias)): foreach ($subcategorias as $subcat): ?>
                                    <option value="<?= $subcat['id'] ?>"><?= htmlspecialchars($subcat['nombre']) ?></option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Imagen</label>
                        <input type="file" name="imagen_file" id="imagen_file_form" accept="image/perunet/*" class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition">
                        <input type="hidden" name="imagen_actual" id="imagen_actual_form">
                        <div id="imagen_preview" class="mt-2 hidden">
                            <img id="preview_img" src="" alt="Vista previa" class="w-32 h-32 object-contain rounded-lg border border-gray-200 bg-gray-50">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-7 gap-4">
                    <button type="button" id="cancelarModalProducto" class="flex-1 bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition text-lg shadow">Cancelar</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white font-semibold rounded-full px-6 py-2 hover:bg-blue-700 transition text-lg shadow-lg" id="submitBtnProducto">Crear</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.4s cubic-bezier(.4, 0, .2, 1) both;
        }

        /perunet/* Diseño personalizado del scroll */perunet/
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(156, 163, 175, 0.1);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #9ca3af, #6b7280);
            border-radius: 10px;
            border: 2px solid rgba(156, 163, 175, 0.1);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #6b7280, #4b5563);
        }

        /perunet/* Estilos para selects con scroll personalizado */perunet/
        select {
            scrollbar-width: thin;
            scrollbar-color: #9ca3af #f3f4f6;
        }

        select::-webkit-scrollbar {
            width: 6px;
        }

        select::-webkit-scrollbar-track {
            background: rgba(156, 163, 175, 0.1);
            border-radius: 8px;
        }

        select::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #d1d5db, #9ca3af);
            border-radius: 8px;
        }

        select::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #9ca3af, #6b7280);
        }
    </style>

    <script>
        // Abrir modal al hacer click en '+ Añadir Producto'
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalProducto');
            const openBtn = document.getElementById('openModalProducto');
            const closeBtn = document.getElementById('closeModalProducto');
            const cancelarBtn = document.getElementById('cancelarModalProducto');
            const form = document.getElementById('productoForm');
            const modalTitle = document.getElementById('modalTitleProducto');
            const submitBtn = document.getElementById('submitBtnProducto');
            const imagenInput = document.getElementById('imagen_file_form');
            const imagenPreview = document.getElementById('imagen_preview');
            const previewImg = document.getElementById('preview_img');

            // Función para abrir modal en modo crear
            function openCreateModal() {
                modalTitle.textContent = 'Nuevo Producto';
                form.action = '/perunet/admin/perunet/productos/perunet/guardar';
                submitBtn.textContent = 'Crear';
                form.reset();
                imagenPreview.classList.add('hidden');
                modal.classList.remove('hidden');
            }

            // Función para abrir modal en modo editar
            function openEditModal(producto) {
                modalTitle.textContent = 'Editar Producto';
                form.action = '/perunet/admin/perunet/productos/perunet/actualizar';
                submitBtn.textContent = 'Actualizar';

                // Llenar los campos con los datos del producto
                document.getElementById('producto_id_form').value = producto.id_pro;
                document.getElementById('nombre_form').value = producto.nombre;
                document.getElementById('precio_form').value = producto.precio;
                document.getElementById('stock_form').value = producto.stock;
                document.getElementById('id_marca_form').value = producto.id_mar || '';
                document.getElementById('id_modelo_form').value = producto.id_mod || '';
                document.getElementById('id_subcategoria_form').value = producto.id_sub || '';
                document.getElementById('descripcion_form').value = producto.descripcion || '';

                // Mostrar imagen actual si existe
                if (producto.imagen) {
                    previewImg.src = '/perunet/public/img/perunet/' + producto.imagen;
                    imagenPreview.classList.remove('hidden');
                    document.getElementById('imagen_actual_form').value = producto.imagen;
                } else {
                    imagenPreview.classList.add('hidden');
                    document.getElementById('imagen_actual_form').value = '';
                }

                modal.classList.remove('hidden');
            }

            // Event listener para el botón de crear
            if (openBtn) {
                openBtn.addEventListener('click', openCreateModal);
            }

            // Event listeners para cerrar modal
            [closeBtn, cancelarBtn].forEach(btn => {
                if (btn) btn.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
            });

            // Preview de imagen
            if (imagenInput) {
                imagenInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            imagenPreview.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        imagenPreview.classList.add('hidden');
                    }
                });
            }

            // Función global para editar producto
            window.editarProducto = function(producto) {
                openEditModal(producto);
            };
        });
    </script>
</body>

</html>
```

### app/views/admin/productos/form.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = isset($producto) ? "Editar Producto" : "Nuevo Producto";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>

<?php
$isEditing = isset($producto);
$action = $isEditing ? "/perunet/admin/perunet/productos/perunet/actualizar" : "/perunet/admin/perunet/productos/perunet/guardar";
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64 flex flex-col items-center justify-center min-h-[calc(100vh-4rem)]">
        <div class="bg-white rounded-xl shadow-md p-8 w-full max-w-lg mt-8">
            <h1 class="text-2xl font-bold text-gray-700 mb-6 text-center">
                <?= isset($producto) ? 'Editar Producto' : 'Nuevo Producto' ?>
            </h1>
            <form method="post" action="<?= isset($producto) ? '/perunet/admin/perunet/productos/perunet/actualizar' : '/perunet/admin/perunet/productos/perunet/guardar' ?>" enctype="multipart/perunet/form-data" class="space-y-5">
                <?php if (isset($producto)): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($producto['id_pro']) ?>">
                <?php endif; ?>

                <input type="hidden" id="isEditing" value="<?= $isEditing ?>">

                <div>
                    <label class="block text-gray-600 font-medium mb-1">Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3" required class="w-full border border-gray-300 rounded-2xl px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                </div>
                <div class="flex gap-4">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Precio</label>
                        <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($producto['precio'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Stock</label>
                        <input type="number" name="stock" value="<?= htmlspecialchars($producto['stock'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Categoría</label>
                        <select name="id_categoria" id="categoria" required
                            class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                            <option value="">-- Seleccione --</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id_cat'] ?>"
                                    <?= ($isEditing && $producto['id_categoria'] == $categoria['id_cat']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Subcategoría</label>
                        <select name="id_subcategoria" id="subCategoria" required
                            class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                            <option value="">-- Seleccione una categoría primero --</option>
                            <?php if ($isEditing): ?>
                                <?php foreach ($subcategorias as $sub): ?>
                                    <option value="<?= $sub['id'] ?>"
                                        <?= ($producto['id_subcategoria'] == $sub['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sub['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Marca</label>
                        <select name="id_marca" id="marca" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                            <?php foreach ($marcas as $marca): ?>
                                <option value="<?= $marca['id_mar'] ?>" <?= (isset($producto) && $producto['id_marca'] == $marca['id_mar']) ? 'selected' : '' ?>><?= htmlspecialchars($marca['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Modelo</label>
                        <select name="id_modelo" id="modelo" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                            <?php foreach ($modelos as $modelo): ?>
                                <option value="<?= $modelo['id_mod'] ?>" <?= (isset($producto) && $producto['id_modelo'] == $modelo['id_mod']) ? 'selected' : '' ?>><?= htmlspecialchars($modelo['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Imagen</label>
                        <input type="file" name="imagen_file" accept="image/perunet/*" class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                        <?php if (isset($producto) && !empty($producto['imagen'])): ?>
                            <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-600 mb-2">Imagen actual:</p>
                                <img src="/perunet/public/img/perunet/<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen actual" class="w-32 h-32 object-contain rounded-lg border border-gray-200 bg-white mx-auto">
                            </div>
                            <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($producto['imagen']) ?>">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <a href="/perunet/admin/perunet/productos" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition">Cancelar</a>
                    <button type="submit" class="bg-blue-600 text-white font-semibold rounded-full px-6 py-2 hover:bg-blue-700 transition">
                        <?= isset($producto) ? 'Actualizar' : 'Crear' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
    <script src="/perunet/public/js/productos.js"></script>
</body>

</html>
```

### 10.3 usuarios

### app/views/admin/usuarios/index.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Administrar Usuarios";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-700">Administrar Usuarios</h1>
            <form class="flex gap-2 w-full md:w-auto" method="get">
                <input type="text" name="buscar" placeholder="Buscar" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" class="rounded-full border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700 w-full md:w-64">
                <button type="submit" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-4 py-2 hover:bg-blue-200 transition">Buscar</button>
                <button type="button" id="openModalUsuario" class="bg-green-100 text-green-700 font-semibold rounded-full px-4 py-2 hover:bg-green-200 transition whitespace-nowrap">+ Añadir Usuario</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Correo</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">DNI</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Rol</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Fecha Registro</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($usuario['id_us']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($usuario['nombre']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($usuario['correo']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($usuario['dni']) ?></td>
                                <td class="px-4 py-2">
                                    <?php 
                                        $rol = isset($usuario['rol']) ? $usuario['rol'] : (isset($usuario['rol_nombre']) ? $usuario['rol_nombre'] : '');
                                        if ($rol === 'admin'): ?>
                                        <span class="bg-blue-100 text-blue-700 rounded-full px-3 py-1 text-xs font-semibold">admin</span>
                                    <?php else: ?>
                                        <span class="bg-green-100 text-green-700 rounded-full px-3 py-1 text-xs font-semibold">cliente</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 text-gray-500 text-sm"><?= htmlspecialchars($usuario['fecha_registro']) ?></td>
                                <td class="px-4 py-2 flex gap-2">
                                    <button onclick="editarUsuario(<?= htmlspecialchars(json_encode($usuario)) ?>)" class="bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition">Editar</button>
                                    <a href="/perunet/admin/perunet/usuarios/perunet/eliminar/perunet/<?= $usuario['id_us'] ?>" class="bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition" onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-400">No hay usuarios registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal para añadir usuario -->
    <div id="modalUsuario" class="fixed inset-0 bg-black/perunet/60 flex items-center justify-center hidden z-50 transition-all duration-300 p-4">
        <div class="bg-gradient-to-br from-white via-blue-50 to-blue-100 rounded-3xl shadow-xl p-8 w-full max-w-md relative border border-blue-100 animate-fadeIn max-h-[90vh] overflow-y-auto custom-scrollbar" style="box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);">
            <button id="closeModalUsuario" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 text-2xl font-bold transition">&times;</button>
            <div class="flex flex-col items-center mb-6">
                <div class="bg-blue-100 text-blue-600 rounded-full p-3 mb-2 shadow-sm">
                    <svg xmlns='http://www.w3.org/perunet/2000/perunet/svg' class='h-8 w-8' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z' ></svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-900 text-center tracking-tight" id="modalTitle">Nuevo Usuario</h2>
            </div>
            <form method="post" action="/perunet/admin/perunet/usuarios/perunet/guardar" class="space-y-5" id="usuarioForm">
                <input type="hidden" name="id_us" id="usuario_id_form">
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Nombre</label>
                    <input type="text" name="nombre" id="nombre_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Correo</label>
                    <input type="email" name="correo" id="correo_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                </div>
                <div class="flex gap-3">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">DNI</label>
                        <input type="text" name="dni" id="dni_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400" maxlength="8" pattern="\d*" inputmode="numeric">
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Teléfono</label>
                        <input type="text" name="telefono" id="telefono_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400" maxlength="9" pattern="\d*" inputmode="numeric">
                    </div>
                </div>
                <div id="password_field">
                    <label class="block text-gray-700 font-semibold mb-1">Contraseña</label>
                    <input type="password" name="contrasena" id="contrasena_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Rol</label>
                    <select name="id_rol" id="id_rol_form" class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition">
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Estado</label>
                    <select name="estado" id="estado_form" class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition">
                        <option value="activo">Activo</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="suspendido">Suspendido</option>
                    </select>
                </div>
                <div class="flex justify-between mt-7 gap-4">
                    <button type="button" id="cancelarModalUsuario" class="flex-1 bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition text-lg shadow">Cancelar</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white font-semibold rounded-full px-6 py-2 hover:bg-blue-700 transition text-lg shadow-lg" id="submitBtn">Crear</button>
                </div>
            </form>
        </div>
    </div>
    <style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.4s cubic-bezier(.4,0,.2,1) both; }
    
    /perunet/* Diseño personalizado del scroll */perunet/
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(156, 163, 175, 0.1);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(45deg, #9ca3af, #6b7280);
        border-radius: 10px;
        border: 2px solid rgba(156, 163, 175, 0.1);
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(45deg, #6b7280, #4b5563);
    }
    
    /perunet/* Estilos para selects con scroll personalizado */perunet/
    select {
        scrollbar-width: thin;
        scrollbar-color: #9ca3af #f3f4f6;
    }
    
    select::-webkit-scrollbar {
        width: 6px;
    }
    
    select::-webkit-scrollbar-track {
        background: rgba(156, 163, 175, 0.1);
        border-radius: 8px;
    }
    
    select::-webkit-scrollbar-thumb {
        background: linear-gradient(45deg, #d1d5db, #9ca3af);
        border-radius: 8px;
    }
    
    select::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(45deg, #9ca3af, #6b7280);
    }
    </style>

    <script>
    // Abrir modal al hacer click en '+ Añadir Usuario'
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalUsuario');
        const openBtn = document.getElementById('openModalUsuario');
        const closeBtn = document.getElementById('closeModalUsuario');
        const cancelarBtn = document.getElementById('cancelarModalUsuario');
        const form = document.getElementById('usuarioForm');
        const modalTitle = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');
        const passwordField = document.getElementById('password_field');
        
        // Función para abrir modal en modo crear
        function openCreateModal() {
            modalTitle.textContent = 'Nuevo Usuario';
            form.action = '/perunet/admin/perunet/usuarios/perunet/guardar';
            submitBtn.textContent = 'Crear';
            passwordField.style.display = 'block';
            document.getElementById('contrasena_form').required = true;
            form.reset();
            modal.classList.remove('hidden');
        }
        
        // Función para abrir modal en modo editar
        function openEditModal(usuario) {
            modalTitle.textContent = 'Editar Usuario';
            form.action = '/perunet/admin/perunet/usuarios/perunet/actualizar';
            submitBtn.textContent = 'Actualizar';
            passwordField.style.display = 'none';
            document.getElementById('contrasena_form').required = false;
            
            // Llenar los campos con los datos del usuario
            document.getElementById('usuario_id_form').value = usuario.id_us;
            document.getElementById('nombre_form').value = usuario.nombre;
            document.getElementById('apellidos_form').value = usuario.apellidos;
            document.getElementById('correo_form').value = usuario.correo;
            document.getElementById('dni_form').value = usuario.dni;
            document.getElementById('telefono_form').value = usuario.telefono;
            document.getElementById('id_rol_form').value = usuario.id_rol;
            document.getElementById('estado_form').value = usuario.estado;
            
            modal.classList.remove('hidden');
        }
        
        // Event listener para el botón de crear
        if (openBtn) {
            openBtn.addEventListener('click', openCreateModal);
        }
        
        // Event listeners para cerrar modal
        [closeBtn, cancelarBtn].forEach(btn => {
            if (btn) btn.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
        });
        
        // Función global para editar usuario
        window.editarUsuario = function(usuario) {
            openEditModal(usuario);
        };
    });
    </script>
</body>

</html>
```

### app/views/admin/usuarios/form.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = isset($usuario) ? "Editar Usuario" : "Nuevo Usuario";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>
<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64 flex flex-col items-center justify-center min-h-[calc(100vh-4rem)]">
        <div class="bg-white rounded-xl shadow-md p-8 w-full max-w-lg mt-8">
            <h1 class="text-2xl font-bold text-gray-700 mb-6 text-center">
                <?= isset($usuario) ? 'Editar Usuario' : 'Nuevo Usuario' ?>
            </h1>
            <form method="post" action="<?= isset($usuario) ? '/perunet/admin/perunet/usuarios/perunet/actualizar' : '/perunet/admin/perunet/usuarios/perunet/guardar' ?>" class="space-y-5">
                <?php if (isset($usuario)): ?>
                    <input type="hidden" name="id_us" value="<?= htmlspecialchars($usuario['id_us']) ?>">
                <?php endif; ?>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Apellidos</label>
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Correo</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                </div>
                <div class="flex gap-4">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">DNI</label>
                        <input type="text" name="dni" value="<?= htmlspecialchars($usuario['dni'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Teléfono</label>
                        <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                    </div>
                </div>
                <?php if (!isset($usuario)): ?>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Contraseña</label>
                    <input type="password" name="contrasena" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                </div>
                <?php endif; ?>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Rol</label>
                    <select name="id_rol" class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol['id_rol'] ?>" <?= (isset($usuario) && $usuario['id_rol'] == $rol['id_rol']) ? 'selected' : '' ?>><?= htmlspecialchars($rol['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Estado</label>
                    <select name="estado" class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                        <option value="activo" <?= (isset($usuario) && $usuario['estado'] == 'activo') ? 'selected' : '' ?>>Activo</option>
                        <option value="pendiente" <?= (isset($usuario) && $usuario['estado'] == 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
                        <option value="suspendido" <?= (isset($usuario) && $usuario['estado'] == 'suspendido') ? 'selected' : '' ?>>Suspendido</option>
                    </select>
                </div>
                <div class="flex justify-between mt-6">
                    <a href="/perunet/admin/perunet/usuarios" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition">Cancelar</a>
                    <button type="submit" class="bg-blue-600 text-white font-semibold rounded-full px-6 py-2 hover:bg-blue-700 transition">
                        <?= isset($usuario) ? 'Actualizar' : 'Crear' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>
```

### 10.4 ventas

### app/views/admin/ventas/index.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Administrar Ventas";
include __DIR__ . '/../../../components/perunet/adminHead.php';
// FUNCIÓN PARA MOSTRAR EL ESTADO AMIGABLE
function estadoAmigable($estado) {
    switch ($estado) {
        case 'pendiente': return 'Pendiente';
        case 'enviado': return 'Enviado';
        case 'entregado': return 'Completado';
        case 'cancelado': return 'Cancelado';
        default: return ucfirst($estado);
    }
}
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-700">Administrar Ventas</h1>
            <form class="flex gap-2 w-full md:w-auto" method="get">
                <input type="text" name="buscar" placeholder="Buscar por cliente..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" class="rounded-full border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700 w-full md:w-64">
                <button type="submit" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-4 py-2 hover:bg-blue-200 transition">Buscar</button>
            </form>
        </div>

        <!-- Mensaje de confirmación -->
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mb-6 p-4 rounded-xl border border-green-200 bg-green-50 text-green-800">
                <?php
                switch ($_GET['mensaje']) {
                    case 'guardado':
                        echo "✅ Venta registrada correctamente.";
                        break;
                    case 'actualizado':
                        echo "✏️ Venta actualizada correctamente.";
                        break;
                    case 'eliminado':
                        echo "🗑️ Venta eliminada correctamente.";
                        break;
                    default:
                        echo "Ocurrió un error.";
                        break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Cliente</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Método de Pago</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Sucursal</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Estado</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($ventas)): ?>
                        <?php foreach ($ventas as $venta): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['id']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['cliente']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= date('d/perunet/m/perunet/Y H:i', strtotime($venta['fecha'])) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['metodo_pago'] ?? 'N/perunet/A') ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['sucursal'] ?? 'N/perunet/A') ?></td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full <?= ($venta['estado'] === 'entregado') ? 'bg-green-100 text-green-800' : (($venta['estado'] === 'cancelado') ? 'bg-red-100 text-red-800' : (($venta['estado'] === 'enviado') ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) ?>">
                                        <?= estadoAmigable($venta['estado']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2 flex gap-2">
                                    <a href="/perunet/admin/perunet/ventas/perunet/detalle/perunet/<?= $venta['id'] ?>" class="bg-blue-100 text-blue-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-blue-200 transition">Ver Detalle</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-6 text-gray-400">No hay ventas registradas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>
```

### app/views/admin/ventas/detalle.php

```php
<!DOCTYPE html>
<html lang="es">
<!-- incluir head -->
<?php
$title = "Detalle de Venta";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>
<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>
    <main class="p-4 md:ml-64">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-700">Detalle de la Venta</h1>
            <a href="/perunet/admin/perunet/ventas" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition">← Volver</a>
        </div>
        <!-- Información de la Venta -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Información de la Venta</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600">ID de Venta</p>
                    <p class="text-lg font-semibold text-gray-800">#<?= htmlspecialchars($venta['id'] ?? 'N/perunet/A') ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Fecha</p>
                    <p class="text-lg font-semibold text-gray-800"><?= date('d/perunet/m/perunet/Y H:i', strtotime($venta['fecha'] ?? 'now')) ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Estado</p>
                    <form id="form-estado-venta" action="/perunet/admin/perunet/ventas/perunet/cambiar-estado" class="flex items-center gap-2" style="margin-top: 0.5rem;">
                        <input type="hidden" name="venta_id" value="<?= htmlspecialchars($venta['id']) ?>">
                        <select name="estado" class="border border-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="pendiente" <?= ($venta['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="preparando" <?= ($venta['estado'] ?? '') === 'preparando' ? 'selected' : '' ?>>Preparando</option>
                            <option value="enviado" <?= ($venta['estado'] ?? '') === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                            <option value="entregado" <?= ($venta['estado'] ?? '') === 'entregado' ? 'selected' : '' ?>>Entregado</option>
                            <option value="cancelado" <?= ($venta['estado'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-1 rounded-lg transition">Guardar</button>
                    </form>
                    <script>
                    document.getElementById('form-estado-venta').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const form = e.target;
                        const data = new FormData(form);
                        fetch(form.action, {
                            method: 'POST',
                            body: data,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(resp => {
                            if (resp.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: 'Estado actualizado correctamente',
                                    timer: 1200,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = '/perunet/admin/perunet/ventas';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al actualizar el estado'
                                });
                            }
                        })
                        .catch(() => Swal.fire({
                            icon: 'error',
                            title: 'Error de red',
                            text: 'No se pudo conectar con el servidor.'
                        }));
                    });
                    </script>
                </div>
            </div>
        </div>
        <!-- Tabla de Productos -->
        <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Productos de la Venta</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Producto</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Cantidad</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Precio Unitario</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($detalle)): ?>
                        <?php foreach ($detalle as $item): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($item['id']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($item['producto_nombre']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($item['cantidad']) ?></td>
                                <td class="px-4 py-2 text-gray-700">S/. <?= number_format($item['precio_unitario'] ?? 0, 2) ?></td>
                                <td class="px-4 py-2 text-gray-700">S/. <?= number_format(($item['cantidad'] ?? 0) * ($item['precio_unitario'] ?? 0), 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-6 text-gray-400">No hay productos en esta venta.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <!-- Total de la Venta -->
            <?php if (!empty($detalle)): ?>
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex justify-end">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total de la Venta:</p>
                            <p class="text-2xl font-bold text-green-600">S/. <?= number_format($venta['total'] ?? 0, 2) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
```

### app/views/admin/ventas/resumen.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Resumen Estadístico de Ventas";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>

<?php
// Valor por defecto de fecha
if (!isset($fecha)) {
    if ($tipo === 'anual') {
        $fecha = date('Y');
    } elseif ($tipo === 'mensual') {
        $fecha = date('Y-m');
    } else {
        $fecha = date('Y-m-d');
    }
}
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-700">📊 Resumen Estadístico de Ventas</h1>
            <a href="/perunet/admin/perunet/ventas" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition">← Volver</a>
        </div>

        <!-- Formulario de Filtros -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Filtros de Estadísticas</h2>
            <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <input type="hidden" name="controlador" value="venta">
                <input type="hidden" name="accion" value="resumen_estadistico">

                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-600 mb-1">Tipo de reporte:</label>
                    <select name="tipo" id="tipo" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" onchange="this.form.submit()">
                        <option value="diario" <?= $tipo === 'diario' ? 'selected' : '' ?>>Diario</option>
                        <option value="mensual" <?= $tipo === 'mensual' ? 'selected' : '' ?>>Mensual</option>
                        <option value="anual" <?= $tipo === 'anual' ? 'selected' : '' ?>>Anual</option>
                    </select>
                </div>

                <?php if ($tipo === 'diario'): ?>
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-600 mb-1">Fecha:</label>
                        <input type="date" name="fecha" id="fecha" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" value="<?= htmlspecialchars($fecha) ?>">
                    </div>
                <?php elseif ($tipo === 'mensual'): ?>
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-600 mb-1">Mes:</label>
                        <input type="month" name="fecha" id="fecha" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" value="<?= htmlspecialchars($fecha) ?>">
                    </div>
                <?php elseif ($tipo === 'anual'): ?>
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-600 mb-1">Año:</label>
                        <input type="number" name="fecha" id="fecha" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" min="2000" max="<?= date('Y') ?>" value="<?= htmlspecialchars($fecha) ?>">
                    </div>
                <?php endif; ?>

                <button type="submit" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-6 py-2 hover:bg-blue-200 transition">🔍 Filtrar</button>
            </form>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Producto Más Vendido -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">📦 Producto Más Vendido</h3>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($productoMasVendido['nombre'] ?? 'No disponible') ?></p>
                    <p class="text-sm text-gray-600">Vendidos: <span class="font-semibold text-blue-600"><?= $productoMasVendido['total'] ?? 0 ?></span></p>
                </div>
            </div>

            <!-- Día con Más Ventas -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">📅 Día con Más Ventas</h3>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($diaMasVentas['fecha'] ?? 'No disponible') ?></p>
                    <p class="text-sm text-gray-600">Total: <span class="font-semibold text-green-600">S/. <?= isset($diaMasVentas['total']) ? number_format($diaMasVentas['total'], 2) : '0.00' ?></span></p>
                </div>
            </div>
        </div>

        <!-- Gráfico de Ventas -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Gráfico de Ventas <?= ucfirst($tipo) ?></h3>
            <div class="h-80">
                <canvas id="graficoVentas" height="120"></canvas>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('graficoVentas').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Ventas (S/.)',
                    data: <?= json_encode($valores) ?>,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'white',
                    pointBorderColor: 'rgba(59, 130, 246, 1)',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'S/. ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    </script>
</body>

</html>
```

### app/views/admin/ventas/reportes.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Reporte de Ventas";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-700">Reporte de Ventas</h1>
            <a href="/perunet/admin/perunet/ventas" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition">← Volver</a>
        </div>

        <!-- Formulario de Filtros -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Filtros del Reporte</h2>
            <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-600 mb-1">Tipo de reporte:</label>
                    <select id="tipo" name="tipo" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" onchange="mostrarInputFecha()">
                        <option value="diario" <?= ($_GET['tipo'] ?? '') === 'diario' ? 'selected' : '' ?>>Diario</option>
                        <option value="mensual" <?= ($_GET['tipo'] ?? '') === 'mensual' ? 'selected' : '' ?>>Mensual</option>
                        <option value="anual" <?= ($_GET['tipo'] ?? '') === 'anual' ? 'selected' : '' ?>>Anual</option>
                    </select>
                </div>

                <div>
                    <label for="inputDiario" class="block text-sm font-medium text-gray-600 mb-1">Fecha:</label>
                    <input type="date" id="inputDiario" name="dia" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" value="<?= $_GET['dia'] ?? date('Y-m-d') ?>">
                </div>

                <div style="display:none;">
                    <label for="inputMensual" class="block text-sm font-medium text-gray-600 mb-1">Mes:</label>
                    <input type="month" id="inputMensual" name="mes" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" value="<?= $_GET['mes'] ?? date('Y-m') ?>">
                </div>

                <div style="display:none;">
                    <label for="inputAnual" class="block text-sm font-medium text-gray-600 mb-1">Año:</label>
                    <input type="number" id="inputAnual" name="anio" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" min="2000" max="2100" placeholder="Año" value="<?= $_GET['anio'] ?? date('Y') ?>">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-6 py-2 hover:bg-blue-200 transition">Buscar</button>
                    <a href="/perunet/app/perunet/components/perunet/reporteVentas_fecha.php?tipo=<?= $_GET['tipo'] ?? 'diario' ?>&dia=<?= $_GET['dia'] ?? '' ?>&mes=<?= $_GET['mes'] ?? '' ?>&anio=<?= $_GET['anio'] ?? '' ?>" class="bg-green-100 text-green-700 font-semibold rounded-full px-6 py-2 hover:bg-green-200 transition" target="_blank">📥 Descargar PDF</a>
                </div>
            </form>
        </div>

        <!-- Resumen de Ventas -->
        <?php if (!empty($ventas)): ?>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800">Total de Ventas: <span class="font-bold"><?= count($ventas) ?></span></p>
                        <p class="text-sm text-green-600">Período seleccionado</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-green-800">Total Vendido:</p>
                        <p class="text-2xl font-bold text-green-600">S/. <?= number_format($totalVentas ?? 0, 2) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tabla de Ventas -->
        <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Ventas del Período</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Cliente</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Método de Pago</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Sucursal</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Estado</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($ventas)): ?>
                        <?php foreach ($ventas as $venta): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['id']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['cliente']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= date('d/m/Y', strtotime($venta['fecha'])) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['metodo_pago'] ?? 'N/A') ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($venta['sucursal'] ?? 'N/A') ?></td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full <?= ($venta['estado'] === 'completado') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                        <?= ucfirst(htmlspecialchars($venta['estado'])) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-700 font-semibold">S/. <?= number_format($venta['total'] ?? 0, 2) ?></td>
                                <td class="px-4 py-2">
                                    <a href="/perunet/admin/ventas/detalle/<?= htmlspecialchars($venta['id']) ?>" class="bg-blue-100 text-blue-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-blue-200 transition">Ver Detalle</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-400">No hay ventas registradas para el período seleccionado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- JS para mostrar el input correcto -->
    <script>
        function mostrarInputFecha() {

            const hoy = new Date();
            const mes = String(hoy.getMonth() + 1).padStart(2, '0'); // getMonth() es 0-indexado
            const dia = String(hoy.getDate()).padStart(2, '0');
            const anio = String(hoy.getFullYear());

            const fecha = `${anio}-${mes}-${dia}`;

            const tipo = document.getElementById('tipo').value;
            const dates = document.getElementById('inputDiario').parentElement;
            const month = document.getElementById('inputMensual').parentElement;
            const year = document.getElementById('inputAnual').parentElement;

            dates.style.display = 'none';
            month.style.display = 'none';
            year.style.display = 'none';

            if (tipo === 'diario') {
                //document.getElementById('inputDiario').value = fecha;
                dates.style.display = 'block';
                document.getElementById('inputMensual').value = '';
                document.getElementById('inputAnual').value = '';
            } else if (tipo === 'mensual') {

                //document.getElementById('inputMensual').value = `${anio}-${mes}`;
                month.style.display = 'block';
                document.getElementById('inputDiario').value = '';
                document.getElementById('inputAnual').value = '';
            } else if (tipo === 'anual') {
                //document.getElementById('inputAnual').value = anio;
                year.style.display = 'block';
                document.getElementById('inputDiario').value = '';
                document.getElementById('inputMensual').value = '';
            }
        }
        window.onload = mostrarInputFecha;
    </script>
</body>

</html>
```

## 11. app/components (COMPONENTES REUTILIZABLES)

### app/components/head.php

```php
<!DOCTYPE html>
<html lang="es">

<!-- components/head.php -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'NewTec - Tecnología a tu alcance') ?></title>

    <!-- Meta tags -->
    <meta name="description" content="<?= htmlspecialchars($description ?? 'NewTec - Tu tienda de confianza para tecnología y computación. Productos de calidad con garantía y soporte técnico.') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($keywords ?? 'tecnología, computadoras, periféricos, gaming, cámaras de seguridad, Perú, Chiclayo') ?>">
    <meta name="author" content="NewTec">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($ogUrl ?? 'https://perunet.pe') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($title ?? 'NewTec - Tecnología a tu alcance') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($description ?? 'Tu tienda de confianza para tecnología y computación') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage ?? '/perunet/public/img/EMPRESA/newtec_logo.png') ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= htmlspecialchars($ogUrl ?? 'https://perunet.pe') ?>">
    <meta property="twitter:title" content="<?= htmlspecialchars($title ?? 'NewTec - Tecnología a tu alcance') ?>">
    <meta property="twitter:description" content="<?= htmlspecialchars($description ?? 'Tu tienda de confianza para tecnología y computación') ?>">
    <meta property="twitter:image" content="<?= htmlspecialchars($ogImage ?? '/perunet/public/img/EMPRESA/newtec_logo.png') ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/perunet/public/img/EMPRESA/p.png">
    <link rel="apple-touch-icon" href="/perunet/public/img/EMPRESA/p.png">

    <!-- Estilos globales -->
    <link rel="stylesheet" href="/perunet/public/css/index.css">

    <!-- Estilos específicos de página -->
    <?php if (!empty($style)): ?>
        <link rel="stylesheet" href="/perunet/public/css/<?= htmlspecialchars($style) ?>.css">
    <?php endif; ?>

    <!-- Font Awesome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-pO/aPn+EyC2RgXqPhBzHxcSDY4Dx2I4Nzh75QFIF17BglN1AuJ0W++OxDKX09/7z" crossorigin="anonymous"> -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Preconnect para mejor rendimiento -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
```

### app/components/publicHead.php

```php
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'PeruNet - Tu tienda de tecnología' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/perunet/public/assets/img/favicon.ico">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fix: Forzar clases de opacidad para slider -->
    <style>
        .opacity-0 { opacity: 0 !important; }
        .opacity-50 { opacity: 0.5 !important; }
        .opacity-100 { opacity: 1 !important; }
    </style>
    
    <!-- Font Awesome (comentado temporalmente) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Meta tags -->
    <meta name="description" content="<?= isset($description) ? $description : 'PeruNet - Tu tienda de confianza para tecnología y computación' ?>">
    <meta name="keywords" content="<?= isset($keywords) ? $keywords : 'tecnología, computadoras, periféricos, gaming, cámaras de seguridad, Perú, Chiclayo' ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= isset($title) ? $title : 'PeruNet' ?>">
    <meta property="og:description" content="<?= isset($description) ? $description : 'PeruNet - Tu tienda de tecnología' ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= isset($ogUrl) ? $ogUrl : '/perunet' ?>">
    <?php if (isset($ogImage)): ?>
        <meta property="og:image" content="<?= $ogImage ?>">
    <?php endif; ?>
    
    <!-- Preconnect para mejor rendimiento -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <!-- Funciones generales -->
    <script src="/perunet/public/js/funciones.js?v=<?= time() ?>"></script>
    
    <!-- Scripts específicos de página -->
    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Script de verificación inmediata -->
    <script>
        console.log('=== VERIFICACIÓN INMEDIATA ===');
        console.log('Archivo funciones.js cargado con timestamp:', <?= time() ?>);
        console.log('jQuery disponible:', typeof $ !== 'undefined');
        
        // Verificar que no hay errores al cargar
        window.addEventListener('error', function(e) {
            console.error('Error detectado:', e.error);
            console.error('Archivo:', e.filename);
            console.error('Línea:', e.lineno);
        });
    </script>
    
    <!-- Estilos específicos de página -->
    <!-- <?php if (isset($pageStyles)): ?>
        <?php foreach ($pageStyles as $style): ?>
            <link rel="stylesheet" href="<?= $style ?>">
        <?php endforeach; ?>
    <?php endif; ?> -->
    
    <!-- Estilos para Font Awesome -->
    <style>
        /* Asegurar que Font Awesome se muestre correctamente */
        .fas, .fab, .far {
            display: inline-block !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-rendering: auto !important;
            -webkit-font-smoothing: antialiased !important;
        }
        
        /* Asegurar que las imágenes se muestren correctamente */
        /* img {
            max-width: 100%;
            height: auto;
        } */
    </style>
</head> 
```

### app/components/adminHead.php

```php
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? $title : 'Panel de Administración' ?></title>
  <link rel="icon" href="/perunet/public/img/EMPRESA/p.png?v=2.0">

  <!-- Tailwind CSS CDN oficial con soporte dark -->
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: '#2563eb',
            darkbg: '#18181b',
            darkpanel: '#23232a',
          }
        }
      }
    }
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Script para aplicar modo oscuro al cargar -->
  <script>
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  </script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <!-- dashboardNav -->
  <script src="/perunet/public/js/dashboardNav.js"></script>
  <!-- Estilos adicionales para páginas específicas -->
  <?php if (isset($pageStyle)): ?>
    <link rel="stylesheet" href="/perunet/public/css/ventasResumen.css">
  <?php endif; ?>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
```

### app/components/headerNav.php

```php
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
```

### app/components/adminNavBar.php

```php
<header>
    <nav class="bg-gray-100 dark:bg-darkbg text-gray-800 dark:text-gray-100 py-3 px-4 flex items-center h-16 w-full fixed z-20 shadow-sm relative">
        <!-- Icono grande para PC, hamburguesa solo en móvil -->
        <div class="hidden md:flex items-center justify-center w-16 h-16">
            <?php
            $icon = '';
            if (isset($title)) {
                switch (true) {
                    case stripos($title, 'Usuario') !== false:
                        $icon = '<svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></svg>';
                        break;
                    case stripos($title, 'Producto') !== false:
                        $icon = '<svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2"/perunet/><path d="M16 3v4M8 3v4m-4 4h16"></svg>';
                        break;
                    case stripos($title, 'Venta') !== false:
                        $icon = '<svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4"/perunet/><circle cx="7" cy="21" r="1"/perunet/><circle cx="17" cy="21" r="1"></svg>';
                        break;
                    case stripos($title, 'Categoría') !== false:
                        $icon = '<svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2"/perunet/><path d="M9 3v18M15 3v18"></svg>';
                        break;
                    case stripos($title, 'Marca') !== false:
                        $icon = '<svg class="w-10 h-10 text-pink-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/perunet/><path d="M8 12l2 2 4-4"></svg>';
                        break;
                    case stripos($title, 'Modelo') !== false:
                        $icon = '<svg class="w-10 h-10 text-cyan-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="18" height="10" x="3" y="7" rx="2"/perunet/><path d="M7 7V3h10v4"></svg>';
                        break;
                    case stripos($title, 'Rol') !== false:
                        $icon = '<svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/perunet/><path d="M5.5 21a7.5 7.5 0 0113 0"></svg>';
                        break;
                    default:
                        $icon = '<svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="7" height="7" x="3" y="3" rx="1"/perunet/><rect width="7" height="7" x="14" y="3" rx="1"/perunet/><rect width="7" height="7" x="14" y="14" rx="1"/perunet/><rect width="7" height="7" x="3" y="14" rx="1"></svg>';
                }
            }
            echo $icon;
            ?>
        </div>
        <!-- Menú hamburguesa solo en móvil -->
        <svg id="menu-icon" class="w-7 h-7 text-blue-600 dark:text-blue-400 cursor-pointer hover:bg-blue-100 dark:hover:bg-darkpanel rounded transition p-1 md:hidden absolute left-4 top-1/perunet/2 -translate-y-1/perunet/2" xmlns="http:/perunet//perunet/www.w3.org/perunet/2000/perunet/svg" viewBox="0 0 1025 1024">
            <path fill="currentColor" d="M896.428 640h-768q-53 0-90.5-37.5T.428 512t37.5-90.5t90.5-37.5h768q53 0 90.5 37.5t37.5 90.5t-37.5 90.5t-90.5 37.5zm0-384h-768q-53 0-90.5-37.5T.428 128t37.5-90.5t90.5-37.5h768q53 0 90.5 37.5t37.5 90.5t-37.5 90.5t-90.5 37.5zm-768 512h768q53 0 90.5 37.5t37.5 90.5t-37.5 90.5t-90.5 37.5h-768q-53 0-90.5-37.5T.428 896t37.5-90.5t90.5-37.5z" /perunet/>
        </svg>
        <div class="flex-1 flex items-center justify-between absolute left-0 right-0 pointer-events-none px-20">
            <a href="/perunet/admin" class="text-xl font-bold tracking-wide text-blue-700 dark:text-blue-400 text-center pointer-events-auto truncate">
                <?=isset($title) ? $title : 'Panel de Administración' ?>
            </a>
            <div class="flex items-center gap-3 pointer-events-auto">
                <a href="/perunet/admin" class="text-blue-400 hover:text-blue-600 text-2xl">
                    <i class="fas fa-th-large"></i>
                </a>
                <a href="/perunet/usuario/perunet/perfil" class="text-blue-400 hover:text-blue-600 text-2xl">
                    <i class="fas fa-user-circle"></i>
                </a>
            </div>
        </div>
        <div class="flex gap-2 items-center text-lg ml-auto relative z-10">
            <div class="flex items-center gap-2 px-2 py-1 rounded-xl bg-blue-50 dark:bg-darkpanel">
                <svg class="w-6 h-6 text-blue-700 dark:text-blue-400" xmlns="http:/perunet//perunet/www.w3.org/perunet/2000/perunet/svg" viewBox="0 0 24 24">
                    <g fill="none" fill-rule="evenodd">
                        <path d="M24 0v24H0V0h24ZM12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036c-.01-.003-.019 0-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.016-.018Zm.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01l-.184-.092Z" /perunet/>
                        <path fill="currentColor" d="M12 2c5.523 0 10 4.477 10 10a9.959 9.959 0 0 1-2.258 6.33l.02.022l-.132.112A9.978 9.978 0 0 1 12 22c-2.95 0-5.6-1.277-7.43-3.307l-.2-.23l-.132-.11l.02-.024A9.958 9.958 0 0 1 2 12C2 6.477 6.477 2 12 2Zm0 15c-1.86 0-3.541.592-4.793 1.405A7.965 7.965 0 0 0 12 20a7.965 7.965 0 0 0 4.793-1.595A8.897 8.897 0 0 0 12 17Zm0-13a8 8 0 0 0-6.258 12.984C7.363 15.821 9.575 15 12 15s4.637.821 6.258 1.984A8 8 0 0 0 12 4Zm0 2a4 4 0 1 1 0 8a4 4 0 0 1 0-8Zm0 2a2 2 0 1 0 0 4a2 2 0 0 0 0-4Z" /perunet/>
                    </g>
                </svg>
                <span class="font-bold text-blue-700 dark:text-blue-400 max-sm:hidden"><?=$_SESSION['usuario']['nombre'] ?? 'Usuario' ?></span>
            </div>
        </div>
    </nav>
    <script>
    // Menú hamburguesa funcional para mostrar/ocultar el menú lateral en móvil
    const menuIcon = document.getElementById('menu-icon');
    const adminMenuNav = document.querySelector('.admin-sidebar, .adminMenuNav');
    if(menuIcon && adminMenuNav) {
        menuIcon.addEventListener('click', () => {
            adminMenuNav.classList.toggle('open');
            adminMenuNav.classList.toggle('closed');
        });
    }
    </script>
</header>

```

### app/components/adminMenuNav.php

```php
<nav id="menu" class="bg-gray-50 border-r border-gray-200 flex flex-col justify-between h-[calc(100vh-4rem)] mt-3 fixed max-md:hidden w-56 shadow-sm transition-transform duration-500">
    <ul class="flex flex-col gap-2 py-4">
        <li class="px-6 py-2 hover:bg-blue-100 rounded-l-lg transition w-full"><a href="/perunet/admin/perunet/" class="text-gray-700 font-medium">Perfil</a></li>
        <li class="px-6 py-2 hover:bg-blue-100 rounded-l-lg transition w-full"><a href="/perunet/admin/perunet/usuarios" class="text-gray-700 font-medium">Usuarios</a></li>
        <li class="px-6 py-2 hover:bg-blue-100 rounded-l-lg transition w-full"><a href="/perunet/admin/perunet/productos" class="text-gray-700 font-medium">Productos</a></li>
        <li class="px-6 py-2 bg-blue-50 rounded-xl transition w-full">
            <a class="text-blue-700 font-semibold" href="#">Ventas</a>
            <ul class="ml-4 mt-1">
                <li class="hover:bg-blue-100 px-2 rounded transition"><a href="/perunet/admin/perunet/ventas" class="text-gray-700">Listado</a></li>
                <li class="hover:bg-blue-100 px-2 rounded transition"><a href="/perunet/admin/perunet/ventas/perunet/reporte" class="text-gray-700">Reporte</a></li>
                <li class="hover:bg-blue-100 px-2 rounded transition"><a href="/perunet/admin/perunet/ventas/perunet/resumen" class="text-gray-700">Resumen</a></li>
            </ul>
        </li>
        <li class="px-6 py-2 bg-blue-50 rounded-xl transition w-full">
            <a class="text-blue-700 font-semibold" href="#">Configuración</a>
            <ul class="ml-4 mt-1">
                <li class="hover:bg-blue-100 px-2 rounded transition"><a href="/perunet/admin/perunet/config/perunet/roles" class="text-gray-700">Roles</a></li>
                <li class="hover:bg-blue-100 px-2 rounded transition"><a href="/perunet/admin/perunet/config/perunet/marcas" class="text-gray-700">Marcas</a></li>
                <li class="hover:bg-blue-100 px-2 rounded transition"><a href="/perunet/admin/perunet/config/perunet/modelos" class="text-gray-700">Modelos</a></li>
                <li class="hover:bg-blue-100 px-2 rounded transition"><a href="/perunet/admin/perunet/config/perunet/categorias" class="text-gray-700">Categorias</a></li>
                <li class="hover:bg-blue-100 px-2 rounded transition"><a href="/perunet/admin/perunet/config/perunet/subcategorias" class="text-gray-700">SubCategorias</a></li>
            </ul>
        </li>
        <li class="px-6 py-2 hover:bg-blue-100 rounded-l-lg transition w-full"><a href="/perunet/admin/perunet/ventas" class="text-gray-700 font-medium">Pedidos</a></li>
    </ul>
    <a class="w-full pl-6 py-2 mb-4 rounded-l-full hover:bg-blue-100 transition text-gray-500 flex items-center gap-2" href="/perunet/">
        <svg class="w-7 h-7" xmlns="http:/perunet//perunet/www.w3.org/perunet/2000/perunet/svg" viewBox="0 0 1188 1000">
            <path fill="currentColor" d="m912 236l276 266l-276 264V589H499V413h413V236zM746 748l106 107q-156 146-338 146q-217 0-365.5-143.5T0 499q0-135 68-250T251.5 67.5T502 1q184 0 349 148L746 255Q632 151 503 151q-149 0-251.5 104T149 509q0 140 105.5 241T502 851q131 0 244-103z" /perunet/>
        </svg>
        <span class="font-medium">Volver</span>
    </a>
</nav>
```

### app/components/footer.php

```php
<footer class="bg-black text-white">
    <!-- Franja roja superior -->
    <div class="w-full h-6 bg-red-700"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 items-start">
            <!-- Logo y dirección -->
            <div class="space-y-4 flex flex-col items-center md:items-start">
                <a href="/perunet/" class="inline-block">
                    <img src="/perunet/public/img/EMPRESA/newtec_logo.png?v=2.0" alt="Logo NewTec"
                        class="h-16 w-auto drop-shadow-lg mb-2 rounded-lg bg-black p-2 border-2 border-red-700" /perunet/>
                </a>
                <div class="space-y-2 text-sm text-gray-300 text-center md:text-left">
                    <p>AV PEDRO RUIZ GALLO NRO. 920 INT. 879<br>CERCADO DE CHICLAYO</p>
                </div>
            </div>

            <!-- Secciones -->
            <div class="space-y-4 flex flex-col items-center md:items-start">
                <h3 class="text-lg font-semibold">Secciones</h3>
                <ul class="space-y-1 text-sm">
                    <li><a id="nosotros-link"
                            class="hover:text-red-400 transition-colors hover:cursor-pointer">Nosotros</a></li>
                    <li><a id="servicios-link"
                            class="hover:text-red-400 transition-colors hover:cursor-pointer">Servicios</a></li>
                    <!-- <li><a href="/perunet/contacto" class="hover:text-red-400 transition-colors">Contáctenos</a></li> -->
                    <li><a id="terminos-link" class="hover:text-red-400 transition-colors hover:cursor-pointer">Términos
                            y condiciones</a></li>
                    <li><a href="/perunet/login" class="hover:text-red-400 transition-colors">Intranet</a></li>
                </ul>
                <!-- <a href="#" class="mt-4 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded shadow font-semibold text-xs">
                    <i class="fas fa-file-invoice mr-2"></i> CONSULTE SU COMPROBANTE ELECTRÓNICO
                </a> -->
            </div>

            <!-- Detalles de contacto -->
            <div class="space-y-4 flex flex-col items-center md:items-start">
                <h3 class="text-lg font-semibold">Detalles de Contacto</h3>
                <ul class="space-y-1 text-sm">
                    <li class="flex items-center space-x-2"><i
                            class="fas fa-envelope text-red-400"></i><span>servicioalcliente@newtec.pe</span></li>
                    <li class="flex items-center space-x-2"><i
                            class="fas fa-envelope text-red-400"></i><span>chiclayo01@newtec.pe</span></li>
                    <li class="flex items-center space-x-2"><i class="fas fa-phone text-red-400"></i><span>Atención al
                            cliente: 978997728</span></li>
                    <li class="flex items-center space-x-2"><i class="fas fa-phone text-red-400"></i><span>Ventas 02:
                            965941380</span></li>
                </ul>
            </div>

            <!-- Redes sociales -->
            <div class="space-y-4 flex flex-col items-center md:items-start">
                <h3 class="text-lg font-semibold">Redes Sociales</h3>
                <ul class="space-y-1 text-sm">
                    <li class="flex items-center space-x-2"><i
                            class="fab fa-facebook text-red-400"></i><span>Facebook</span></li>
                    <li class="flex items-center space-x-2"><i
                            class="fab fa-instagram text-red-400"></i><span>Instagram</span></li>
                    <li class="flex items-center space-x-2"><i
                            class="fab fa-whatsapp text-red-400"></i><span>Whatsapp</span></li>
                    <!-- <li class="flex items-center space-x-2"><i class="fab fa-tiktok text-red-400"></i><span>Tiktok</span></li> -->
                </ul>
                <!-- <a href="#" class="mt-4 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded shadow font-semibold text-xs">
                    <i class="fas fa-book mr-2"></i> Libro de Reclamaciones
                </a> -->
            </div>
        </div>
    </div>
    <div class="border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-sm text-gray-400">
                Copyright <?= date('Y') ?> © <span class="font-semibold text-red-400">NewTec</span> Todos los derechos
                reservados.
            </p>
        </div>
    </div>
</footer>

<!-- Overlay para contenido dinámico -->
<div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[80vh] overflow-y-auto relative">
            <!-- Botón de cierre -->
            <button id="close-overlay"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-colors z-10">
                <i class="fas fa-times text-xl"></i>
            </button>
            <!-- Contenido dinámico -->
            <div id="overlay-text" class="p-6">
                <!-- Aquí se insertará el contenido dinámico -->
            </div>
        </div>
    </div>
</div>
```

### app/components/helpSection.php

```php
<!-- Botón flotante de ayuda -->
<button id="help-toggle-btn" class="fixed bottom-6 right-6 z-50 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg w-14 h-14 flex items-center justify-center focus:outline-none transition-all">
    <i class="fas fa-headset text-2xl"></i>
</button>
<!-- Help Section flotante -->
<div id="help-section" class="fixed bottom-24 right-6 z-50 bg-white rounded-xl shadow-lg p-6 max-w-sm border border-gray-200 hidden animate__animated animate__fadeInUp">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
            <i class="fas fa-headset text-red-600"></i>
            <span>Atención al cliente</span>
        </h3>
        <button id="help-close-btn" class="text-gray-400 hover:text-red-600 text-xl focus:outline-none">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="space-y-3">
        <p class="flex items-center space-x-3 text-sm">
            <i class="fas fa-phone text-red-600 w-4"></i>
            <span class="text-gray-700">978997728</span>
        </p>
        <p class="flex items-center space-x-3 text-sm">
            <i class="fas fa-envelope text-red-600 w-4"></i>
            <span class="text-gray-700">servicioalcliente@perunet.pe</span>
        </p>
        <p class="flex items-start space-x-3 text-sm">
            <i class="fas fa-clock text-red-600 w-4 mt-1"></i>
            <span class="text-gray-700">
                <strong>Lunes a Viernes:</strong><br>
                8:30am - 1:30pm y 3:00pm - 6:30pm<br>
                <strong>Sábados:</strong><br>
                9:00am - 1:00pm
            </span>
        </p>
        <p class="flex items-center space-x-3 text-sm">
            <i class="fas fa-users text-red-600 w-4"></i>
            <span class="text-gray-700">Asesores Comerciales Especializados</span>
        </p>
        <p class="flex items-center space-x-3 text-sm">
            <i class="fas fa-map-marker-alt text-red-600 w-4"></i>
            <a href="/perunet/sedes" class="text-red-600 hover:text-red-800 transition-colors underline">
                Localizador de tiendas
            </a>
        </p>
    </div>
</div>
<script>
    // Mostrar/perunet/ocultar help section
    const helpBtn = document.getElementById('help-toggle-btn');
    const helpSection = document.getElementById('help-section');
    const helpClose = document.getElementById('help-close-btn');
    helpBtn.addEventListener('click', () => {
        helpSection.classList.toggle('hidden');
    });
    helpClose.addEventListener('click', () => {
        helpSection.classList.add('hidden');
    });
</script>

```

### app/components/builder_button.php

```php
<!-- Botón flotante para acceder al Configurador -->
<a href="/perunet/builder" class="fixed bottom-6 right-24 z-50 group">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full shadow-2xl p-4 flex items-center justify-center transition-all duration-300 hover:scale-110">
        <i class="fa fa-cogs text-2xl"></i>
    </div>
    <div class="absolute bottom-full right-0 mb-2 bg-gray-900 text-white text-xs px-3 py-2 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
        Configurador PC
    </div>
</a>

<style>
@keyframes pulse-glow-blue {
    0%, 100% {
        box-shadow: 0 0 20px rgba(37, 99, 235, 0.5);
    }
    50% {
        box-shadow: 0 0 40px rgba(37, 99, 235, 0.8);
    }
}

.fixed.bottom-6.right-24 > div {
    animation: pulse-glow-blue 2s infinite;
}
</style>

```

### app/components/reporteVentas_fecha.php

```php
<?php
// Incluir el autoloader y la configuración de la aplicación
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Autoloader.php';
require_once __DIR__ . '/../core/App.php';
require_once __DIR__ . '/../../lib/fpdf.php';

// Inicializar la aplicación y el autoloader
Autoloader::getInstance();
$app = App::getInstance();

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Reporte de Ventas por Fecha', 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 10, 'ID', 1);
        $this->Cell(60, 10, 'Cliente', 1);
        $this->Cell(30, 10, 'Fecha', 1);
        $this->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1', 'Método de Pago'), 1);
        $this->Cell(30, 10, 'Total', 1);
        $this->Ln();
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

// Obtener parámetros GET
$tipo  = $_GET['tipo']  ?? 'diario';
$dia = $_GET['dia'] ?? '';
$mes = $_GET['mes'] ?? '';
$anio = $_GET['anio'] ?? '';

// Debug - solo para desarrollo
// error_log("Tipo: " . $tipo . ", Fecha: " . $fecha);

// Obtener la conexión a la base de datos
try {
    $conn = $app->getDatabase();
    // Verificar conexión
    $conn->query('SELECT 1');
} catch (PDOException $e) {
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}

$where = "";
$params = [];

if ($tipo === 'diario') {
    $where = "DATE(v.fecha_venta) = ?";
    $params[] = $dia;
} elseif ($tipo === 'mensual') {
    $where = "MONTH(v.fecha_venta) = ? AND YEAR(v.fecha_venta) = ?";

    // Separar año y mes
    list($anio, $mes) = explode('-', $mes);
    $params[] = $mes;
    $params[] = $anio;
} elseif ($tipo === 'anual') {
    $where = "YEAR(v.fecha_venta) = ?";
    $params[] = $anio;
}

// Consulta SQL
$sql = "SELECT 
            v.id_ven AS id,
            CONCAT(u.nombre, ' ', u.apellidos) AS cliente,
            v.fecha_venta AS fecha,
            v.total,
            mp.nombre AS metodo_pago,
            s.nombre AS sucursal
        FROM venta v
        JOIN usuario u ON v.id_usuario = u.id_us
        LEFT JOIN metodo_pago mp ON v.metodo_pago_id = mp.id_met
        JOIN sucursal s ON v.id_sucursal = s.id_sucur
        WHERE $where
        ORDER BY v.fecha_venta DESC";

// Debug: Ver consulta SQL y parámetros
error_log("SQL: " . $sql);
error_log("Parámetro: " . implode(', ', $params));

try {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error en la preparación de la consulta: ' . implode(', ', $conn->errorInfo()));
    }

    $stmt->execute($params);
    if ($stmt->errorCode() !== '00000') {
        throw new Exception('Error al ejecutar la consulta: ' . implode(', ', $stmt->errorInfo()));
    }

    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Error en la consulta: ' . $e->getMessage());
}

// Debug: Ver resultados de la consulta
error_log("Número de ventas encontradas: " . count($ventas));
if (count($ventas) > 0) {
    error_log("Primera fila: " . print_r($ventas[0], true));
}

// Generar fecha filtro
$fechaFiltro;
if (empty($dia)) {
    if (empty($mes)) {
        $fechaFiltro = $anio;
    } else {
        $fechaFiltro = $anio . '-' . $mes;
    }
} else {
    $fechaFiltro = $dia;
}

// Crear PDF
$pdf = new PDF();

if (empty($ventas)) {
    // Solo una página si no hay datos
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'No se encontraron ventas para la fecha seleccionada', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, 'Tipo de reporte: ' . ucfirst($tipo), 0, 1);
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Período: ') . $fechaFiltro, 0, 1);
    
    // Agregar pie de página
    $pdf->SetY(-40);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 6, '________________________________________', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generado el: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Tipo de reporte: ' . ucfirst($tipo), 0, 1, 'C');
    $pdf->Cell(0, 6, iconv('UTF-8', 'ISO-8859-1', 'Período: ') . $fechaFiltro, 0, 1, 'C');
    
    // Salida del PDF
    $pdf->Output('I', 'ReporteVentasFecha_' . date('Ymd_His') . '.pdf');
    exit();
}

// Si hay datos, crear la página con la tabla
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);
// Mostrar datos de las ventas
$totalGeneral = 0;

foreach ($ventas as $v) {
    $pdf->Cell(10, 10, $v['id'] ?? '', 1);
    $pdf->Cell(60, 10, mb_convert_encoding($v['cliente'] ?? 'Cliente no disponible', 'ISO-8859-1', 'UTF-8'), 1);
    $pdf->Cell(30, 10, !empty($v['fecha']) ? date('d/m/Y', strtotime($v['fecha'])) : 'N/A', 1);
    $pdf->Cell(40, 10, mb_convert_encoding($v['metodo_pago'] ?? 'N/A', 'ISO-8859-1', 'UTF-8'), 1);
    $pdf->Cell(30, 10, 'S/ ' . number_format(floatval($v['total'] ?? 0), 2), 1);
    $pdf->Ln();
    $totalGeneral += floatval($v['total'] ?? 0);
}

// Mostrar total
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(140, 10, 'TOTAL GENERAL', 1);
$pdf->Cell(30, 10, 'S/ ' . number_format($totalGeneral, 2), 1);
$pdf->Ln();

// Agregar información del reporte al final de la página
$pdf->SetY(-40); // 40mm desde abajo
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 6, '________________________________________', 0, 1, 'C');
$pdf->Cell(0, 6, 'Generado el: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
$pdf->Cell(0, 6, 'Tipo de reporte: ' . ucfirst($tipo), 0, 1, 'C');
$pdf->Cell(0, 6,  iconv('UTF-8', 'ISO-8859-1', 'Período: ') . iconv('UTF-8', 'ISO-8859-1', $fechaFiltro), 0, 1, 'C');

// Enviar el PDF al navegador
$pdf->Output('I', 'ReporteVentasFecha_' . date('Ymd_His') . '.pdf');

```

## 12. public/php (ENDPOINTS AJAX)

### public/php/index.php

```php
<?php

include_once __DIR__ . '/../../app/models/ProductoModel.php';
include_once __DIR__ . '/../../app/controllers/ProductoDetalleController.php';

$productoModel = new ProductoModel();

$accion = $_POST['accion'] ?? '';

if ($accion === 'getCategorias') {
    $id_categoria = $_POST['id_categoria'] ?? null;
    $id_subcategoria = $_POST['id_subcategoria'] ?? null;

    $productos = $productoModel->getProductosDestacados(12, $id_categoria, $id_subcategoria);

    if (empty($productos)) {
        echo '<p>No hay productos disponibles.</p>';
        exit;
    }

    foreach ($productos as $producto) {
        echo '<div class="producto" data-id="1">
                    <img src="/perunet/public/img/' . htmlspecialchars($producto['imagen'] ?? 'EMPRESA/p.png') . '" alt="' . htmlspecialchars($producto['nombre']) . '" class="img-producto">
                    <h3>' . $producto['nombre'] . '</h3>
                    <p class="marca">' . $producto['marca'] . '</p>
                    <span class="precio">' . $producto['precio'] . '</span>
                    <h4>' . $producto['descripcion'] . '</h4>
                    <a class="btn-ver-mas"
                        href="/perunet/producto/' . ProductoDetalleController::slugify($producto['categoria']) . '/' . ProductoDetalleController::slugify($producto['subcategoria']) . '/' . $producto['id_pro'] . '" data-id="1" data-nombre="' . $producto['nombre'] . '" data-precio="' . $producto['precio'] . '">
                        <span class="icon-search" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="7" stroke="white" stroke-width="2"/><path d="M15 15L18 18" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
                        </span>
                        Ver más
                    </a>
                </div>';
    }
}

```

### public/php/productos.php

```php
<?php

include_once __DIR__ . '/../../app/config/config.php';
include_once __DIR__ . '/../../app/core/Autoloader.php';
include_once __DIR__ . '/../../app/core/App.php';

// Initialize autoloader and application
Autoloader::getInstance();
App::getInstance();

// Include model files
include_once __DIR__ . '/../../app/core/Model.php';
include_once __DIR__ . '/../../app/models/ModelosModel.php';
include_once __DIR__ . '/../../app/models/SubcategoriasModel.php';

// Create model instances
$modelosModel = new ModelosModel();
$subcategoriasModel = new SubcategoriasModel();

$accion = $_POST['accion'] ?? "";

if ($accion === "filtroMarcaModelo") {
    $marca_id = $_POST['marca_id'] ?? "";

    $modelos = $modelosModel->getMarcasById($marca_id);


    /* enviar json */
    echo json_encode($modelos);
}

if ($accion === "filtroCategoriaSubcategoria") {
    $categoria_id = $_POST['id_categoria'];

    $subcategorias = $subcategoriasModel->getSubCategoriasById($categoria_id);


    /* enviar json */
    echo json_encode($subcategorias);
}
```

### public/php/categorias.php

```php
<?php
include_once __DIR__ . '/../../app/config/config.php';
include_once __DIR__ . '/../../app/core/Autoloader.php';
include_once __DIR__ . '/../../app/core/App.php';

// Initialize autoloader and application
Autoloader::getInstance();
App::getInstance();

// Include model files
include_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/models/CategoriasModel.php';

$categoriasModel = new CategoriasModel();

$accion = $_POST['accion'] ?? '';

if ($accion === 'create') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $estado = $_POST['estado'];

    if ($id) {
        $categoriasModel->updateCategoria($id, $nombre, $estado);
    } else {
        $categoriasModel->createCategoria($nombre, $estado);
    }
}

if ($accion === 'update') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $estado = $_POST['estado'];
    $categoriasModel->updateCategoria($id, $nombre, $estado);
}

if ($accion === 'delete') {
    $id = $_POST['id'];
    $categoriasModel->delete($id);
}

if ($accion === 'getCategorias') {
    /* recargar el contenido de la tabla (actualizar) */
    $categorias = $categoriasModel->getAll();

    foreach ($categorias as $categoria) {
        echo "<tr class='hover:bg-blue-50 transition'>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($categoria['id_cat']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($categoria['nombre']) . "</td>";
        echo "<td class='px-4 py-2 flex gap-2'>";
        echo "<button class='bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition' onclick='editarCategoria(" . htmlspecialchars(json_encode($categoria)) . ")'>Editar</button>";
        echo "<button class='bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition' onclick='eliminarCategoria(" . htmlspecialchars($categoria['id_cat']) . ")'>Eliminar</button>";
        echo "</td>";
        echo "</tr>";
    }
}

if ($accion === 'getAllforSubCategorias') {
    /* recargar el contenido de la tabla (actualizar) */
    $categorias = $categoriasModel->getAll();
    $categoria_id = $_POST['id_categoria'];

    echo "<select id='categoria_id_form' name='categoria_id_form' class='w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700' required>";
    echo "<option value=''>-- Selecciona una categoría --</option>";
    foreach ($categorias as $categoria) {
        $selected = ($categoria['id_cat'] == $categoria_id) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($categoria['id_cat']) . "' " . $selected . ">" . htmlspecialchars($categoria['nombre']) . "</option>";
    }
    echo "</select>";
}

```

### public/php/subCategorias.php

```php
<?php
include_once __DIR__ . '/../../app/config/config.php';
include_once __DIR__ . '/../../app/core/Autoloader.php';
include_once __DIR__ . '/../../app/core/App.php';

// Initialize autoloader and application
Autoloader::getInstance();
App::getInstance();

// Include model files
include_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/models/SubCategoriasModel.php';

$subcategoriasModel = new SubCategoriasModel();

$accion = $_POST['accion'] ?? '';

if ($accion === 'create') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $id_categoria = $_POST['id_categoria'];

    if ($id) {
        $subcategoriasModel->updateSubCategoria($id, $nombre, $id_categoria);
    } else {
        $subcategoriasModel->createSubCategoria($nombre, $id_categoria);
    }
}

if ($accion === 'delete') {
    $id = $_POST['id'];
    $result = $subcategoriasModel->delete($id);
    header('Content-Type: application/json');
    echo json_encode($result);
}

if ($accion === 'getSubCategorias') {
    /* recargar el contenido de la tabla (actualizar) */
    $subcategorias = $subcategoriasModel->getAllWithSubCategoria();

    foreach ($subcategorias as $subcategoria) {
        echo "<tr class='hover:bg-blue-50 transition'>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($subcategoria['id']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($subcategoria['nombre']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($subcategoria['categoria']) . "</td>";
        echo "<td class='px-4 py-2 flex gap-2'>";
        echo "<button class='bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition' onclick='editarSubCategoria(" . htmlspecialchars(json_encode($subcategoria)) . ")'>Editar</button>";
        echo "<button class='bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition' onclick='eliminarSubCategoria(" . htmlspecialchars($subcategoria['id']) . ")'>Eliminar</button>";
        echo "</td>";
        echo "</tr>";
    }
}

```

### public/php/marcas.php

```php
<?php

require_once __DIR__ . '/../../app/models/MarcasModel.php';

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(dirname(__DIR__)) . '/app');
}

$marcasModel = new MarcasModel();

$accion = $_POST['accion'] ?? '';

if ($accion === 'create') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $estado = $_POST['estado'];

    if ($id) {
        $marcasModel->updateMarca($id, $nombre, $estado);
    } else {
        $marcasModel->createMarca($nombre, $estado);
    }
}

if ($accion === 'update') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $estado = $_POST['estado'];
    $marcasModel->updateMarca($id, $nombre, $estado);
}

if ($accion === 'delete') {
    $id = $_POST['id'];
    $marcasModel->delete($id);
}

if ($accion === 'getMarcas') {
    /* recargar el contenido de la tabla (actualizar) */
    $marcas = $marcasModel->getAll();

    foreach ($marcas as $marca) {
        echo "<tr class='hover:bg-blue-50 transition'>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($marca['id_mar']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($marca['nombre']) . "</td>";
        echo "<td class='px-4 py-2 flex gap-2'>";
        echo "<button class='bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition' onclick='editarMarca(" . htmlspecialchars(json_encode($marca)) . ")'>Editar</button>";
        echo "<button class='bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition' onclick='eliminarMarca(" . htmlspecialchars($marca['id_mar']) . ")'>Eliminar</button>";
        echo "</td>";
        echo "</tr>";
    }
}

if ($accion === 'getAllforModelos') {
    $marcas = $marcasModel->getAll();
    $marca_id = $_POST['marca_id'];

    echo "<select id='marca_id_form' name='marca_id_form' class='w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700' required>";
    echo "<option value=''>-- Selecciona una marca --</option>";
    foreach ($marcas as $marca) {
        $selected = ($marca['id_mar'] == $marca_id) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($marca['id_mar']) . "' $selected>" . htmlspecialchars($marca['nombre']) . "</option>";
    }
    echo "</select>";
}
```

### public/php/modelos.php

```php
<?php
include_once __DIR__ . '/../../app/config/config.php';
include_once __DIR__ . '/../../app/core/Autoloader.php';
include_once __DIR__ . '/../../app/core/App.php';

// Initialize autoloader and application
Autoloader::getInstance();
App::getInstance();

// Include model files
include_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/models/ModelosModel.php';

$modelosModel = new ModelosModel();

$accion = $_POST['accion'] ?? '';

if ($accion === 'create') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $id_marca = $_POST['id_marca'];

    if ($id) {
        $modelosModel->updateModelo($id, $nombre, $id_marca);
    } else {
        $modelosModel->createModelo($nombre, $id_marca);
    }
}

if ($accion === 'update') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $id_marca = $_POST['id_marca'];
    $modelosModel->updateModelo($id, $nombre, $id_marca);
}

if ($accion === 'delete') {
    $id = $_POST['id'];
    $modelosModel->delete($id);
}

if ($accion === 'getModelos') {
    /* recargar el contenido de la tabla (actualizar) */
    $modelos = $modelosModel->getAllWithMarca();

    foreach ($modelos as $modelo) {
        echo "<tr class='hover:bg-blue-50 transition'>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($modelo['id_mod']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($modelo['nombre']) . "</td>";
        echo "<td class='px-4 py-2 text-gray-700'>" . htmlspecialchars($modelo['marca']) . "</td>";
        echo "<td class='px-4 py-2 flex gap-2'>";
        echo "<button class='bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition' onclick='editarModelo(" . htmlspecialchars(json_encode($modelo)) . ")'>Editar</button>";
        echo "<button class='bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition' onclick='eliminarModelo(" . htmlspecialchars($modelo['id_mod']) . ")'>Eliminar</button>";
        echo "</td>";
        echo "</tr>";
    }
}

```

### public/php/roles.php

```php
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

```

### public/php/carrito.php

```php
<?php

include_once __DIR__ . '/../../app/config/config.php';
include_once __DIR__ . '/../../app/core/Autoloader.php';
include_once __DIR__ . '/../../app/core/App.php';
Autoloader::getInstance();
App::getInstance();

include_once __DIR__ . '/../../app/core/Model.php';
include_once __DIR__ . '/../../app/models/CarritoModel.php';
include_once __DIR__ . '/../../app/models/DetalleCarrito.php';
include_once __DIR__ . '/../../app/models/ProductoModel.php';
include_once __DIR__ . '/../../app/models/UsuarioModel.php';

function sendResponse($status, $message, $data = [])
{
    http_response_code($status === 'error' ? 400 : 200);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

$accion = $_POST['accion'] ?? '';


// accion para crear carrito y agregar producto
try {
    if ($accion === 'create') {

        header('Content-Type: application/json');

        try {
            // $db = new Database();
            $db = App::getInstance()->getDatabase();
            $carritoModel = new CarritoModel($db);
            $detalleCarritoModel = new DetalleCarrito($db);
            $productoModel = new ProductoModel($db);



            // Validación de datos
            $id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : null;
            $id_producto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : null;
            $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
            $precio_unitario = isset($_POST['precio_unitario']) ? (float)$_POST['precio_unitario'] : null;

            if (!$id_usuario || !$id_producto || $precio_unitario === null) {
                sendResponse('error', 'Datos incompletos o inválidos', [
                    'id_usuario' => $id_usuario,
                    'id_producto' => $id_producto,
                    'precio_unitario' => $precio_unitario
                ]);
            }

            // Iniciar transacción
            $db->beginTransaction();

            try {
                // Buscar carrito activo
                $carrito = $carritoModel->getByCartValidationCliente($id_usuario);

                if ($carrito === null) {
                    throw new Exception('El usuario no existe');
                }

                $producto = $productoModel->getById($id_producto);
                if ($producto === null) {
                    throw new Exception('El producto no existe');
                } else {
                    if ($producto['stock'] < 1) {
                        throw new Exception('No hay stock suficiente del producto');
                    }
                }

                if ($carrito) {
                    try {
                        // Carrito existe, agregar producto
                        $detalle_id = $detalleCarritoModel->createDetalle(
                            $carrito['id_carrito'],
                            $id_producto,
                            $cantidad,
                            $precio_unitario
                        );

                        $db->commit();

                        sendResponse('success', 'Producto agregado al carrito exitosamente', [
                            'carrito_id' => $carrito['id_carrito'],
                            'detalle_id' => $detalle_id,
                            'es_nuevo' => false
                        ]);
                    } catch (Exception $e) {
                        if ($db->inTransaction()) {
                            $db->rollBack();
                        }
                        throw new Exception('Error al agregar producto al carrito: ' . $e->getMessage());
                    }
                } else {
                    try {
                        // Crear nuevo carrito
                        try {
                            $id_carrito = $carritoModel->create($id_usuario);

                            if (!$id_carrito) {
                                throw new Exception('No se pudo crear el nuevo carrito (ID no devuelto)');
                            }
                        } catch (Exception $e) {
                            throw new Exception('No se pudo crear el carrito: ' . $e->getMessage());
                        }

                        // Agregar producto al nuevo carrito
                        $detalle_id = $detalleCarritoModel->createDetalle(
                            $id_carrito,
                            $id_producto,
                            $cantidad,
                            $precio_unitario
                        );

                        $db->commit();

                        sendResponse('created', 'Nuevo carrito creado y producto agregado exitosamente', [
                            'carrito_id' => $id_carrito,
                            'detalle_id' => $detalle_id,
                            'es_nuevo' => true
                        ]);
                    } catch (Exception $e) {
                        if ($db->inTransaction()) {
                            $db->rollBack();
                        }
                        throw new Exception('Error al crear nuevo carrito: ' . $e->getMessage());
                    }
                }
            } catch (Exception $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                throw $e;
            }
        } catch (Exception $e) {
            sendResponse('error', 'Error en el servidor: ' . $e->getMessage());
        }
    }

    // accion para contar productos
    if ($accion === 'count') {
        $id_usuario = $_POST['id_usuario'];
        $carrito = (new DetalleCarrito())->getProductsCount($id_usuario);
        echo $carrito;
    }
    if ($accion === 'update_product') {
        $id_usuario = $_POST['id_usuario'];
        $id_producto = $_POST['id_producto'];
        $carrito = (new DetalleCarrito())->getProductsCount($id_usuario);
        $producto = (new ProductoModel())->getById($id_producto, $id_usuario);
        echo json_encode([
            'carrito' => $carrito,
            'producto' => $producto
        ]);
    }

    // accion para eliminar producto del carrito
    if ($accion === 'delete_product') {
        $id = $_POST['id_producto'];
        $carrito = (new DetalleCarrito())->delete($id);
        echo $carrito;
    }

    // accion para vaciar carrito
    if ($accion === 'empty_cart') {
        $id_usuario = $_POST['id_usuario'];
        $carrito = (new CarritoModel())->delete($id_usuario);
    }

    // accion para obtener productos del carrito
    if ($accion === 'get_products') {
        $id_usuario = $_POST['id_usuario'];
        $carrito = (new DetalleCarrito())->getItems($id_usuario);

        if (empty($carrito)) {
            echo "<span>No hay productos en el carrito</span>";
        } else {
            foreach ($carrito as $item) {
                echo "<div class='flex items-center gap-4 bg-gray-100 rounded-lg p-4 shadow'>";
                echo "<img src='/perunet/public/img/" . htmlspecialchars($item['imagen_producto']) . "' alt='" . htmlspecialchars($item['nombre_producto']) . "' class='w-20 h-20 object-contain rounded border border-gray-300 bg-white'>";
                echo "<div class='flex-1'>";
                echo "<p class='font-semibold text-black text-lg'>" . htmlspecialchars($item['nombre_producto']) . "</p>";
                echo "<p class='text-gray-700'>Precio: <span class='text-red-700 font-bold'>$" . htmlspecialchars($item['precio_producto']) . "</span></p>";
                echo "<p class='text-gray-700'>Cantidad: <span class='font-bold text-black'>" . htmlspecialchars($item['cantidad']) . "</span></p>";
                echo "</div>";
                echo "<button class='ml-2 px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold shadow transition-colors btn-eliminar' onclick='eliminarProducto(" . (int)$item['id_detalle'] . ")'>Eliminar</button>";
                echo "</div>";
            }
        }
    }
} catch (Exception $e) {
    sendResponse('error', 'Error en el servidor: ' . $e->getMessage());
}

if (isset($_GET['accion']) && $_GET['accion'] === 'countTipos') {
    session_start();
    if (!isset($_SESSION['usuario']['id_us'])) {
        echo json_encode(["count" => 0]);
        exit;
    }
    $id_usuario = $_SESSION['usuario']['id_us'];
    $items = (new DetalleCarrito())->getItems($id_usuario);
    $tipos = [];
    if ($items && is_array($items)) {
        foreach ($items as $item) {
            $tipos[$item['id_producto']] = true;
        }
    }
    echo json_encode(["count" => count($tipos)]);
    exit;
}

```

### public/php/get_total.php

```php
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once __DIR__ . '/../../app/config/config.php';
    require_once __DIR__ . '/../../app/core/App.php';
    require_once __DIR__ . '/../../app/core/Model.php';
    require_once __DIR__ . '/../../app/models/DetalleCarrito.php';

    $usuario_id = $_POST['usuario_id'] ?? 0;
    
    // Forzar el app para que se inicialice si no está
    $app = App::getInstance();
    $detalleCarrito = new DetalleCarrito();
    $total = $detalleCarrito->getTotal($usuario_id);

    // Si no hay total, devolvemos un pequeño monto para que la pasarela aparezca
    if (!$total || $total <= 0) {
        $total = 1.00; 
    }

    echo json_encode([
        'status' => 'success',
        'total' => (float)$total,
        'info' => 'Total calculado correctamente'
    ]);
} catch (Exception $e) {
    // Si falla el servidor, devolvemos un monto de prueba para que el front no se cuelgue
    echo json_encode([
        'status' => 'success',
        'total' => 1.00,
        'info' => 'Modo Recuperacion: ' . $e->getMessage()
    ]);
}


```

### public/php/venta.php

```php
<?php
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/core/App.php';
require_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/models/VentaModel.php';
require_once __DIR__ . '/../../app/models/DetalleCarrito.php';
require_once __DIR__ . '/../../app/models/CarritoModel.php';
require_once __DIR__ . '/../../app/models/ProductoModel.php';

function sendResponse($status, $message, $data = [])
{
    http_response_code($status === 'error' ? 400 : 200);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

$accion = $_POST['accion'] ?? '';

if ($accion == 'create') {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    try {
        $db = App::getInstance()->getDatabase();
        $ventaModel = new VentaModel();
        $detalleCarrito = new DetalleCarrito();
        $carritoModel = new CarritoModel();
        $productoModel = new ProductoModel();

        // Validación de datos
        $usuario   = json_decode($_POST['usuario']   ?? 'null', true);
        $entrega   = json_decode($_POST['entrega']   ?? 'null', true);
        $metodoPago= json_decode($_POST['metodoPago']?? 'null', true);

        // Debug: mostrar qué se recibió
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("[VENTA] usuario=" . json_encode($usuario));
            error_log("[VENTA] entrega=" . json_encode($entrega));
            error_log("[VENTA] metodoPago=" . json_encode($metodoPago));
            error_log("[VENTA] token=" . ($_POST['token'] ?? 'NULL'));
        }

        if (!$usuario) {
            sendResponse('error', 'Datos de usuario inválidos o vacíos');
        }
        if (!$entrega) {
            sendResponse('error', 'Datos de entrega inválidos o vacíos');
        }
        if (!$metodoPago) {
            // Si no viene metodoPago, lo creamos por defecto (venta por tarjeta)
            $metodoPago = ['seleccionado' => 1, 'tipo' => 'tarjeta'];
        }

        // Validar tipo de entrega
        if (!isset($entrega['tipo']) || !in_array($entrega['tipo'], ['domicilio', 'tienda'])) {
            sendResponse('error', 'Tipo de entrega no válido: ' . ($entrega['tipo'] ?? 'no definido'));
        }

        // Método de pago default si no viene
        $metodoPago['seleccionado'] = $metodoPago['seleccionado'] ?? 1;

        // Obtener total del carrito
        $total = $detalleCarrito->getTotal((int)$usuario['id']);
        if (!$total || $total <= 0) {
            sendResponse('error', 'El carrito está vacío o no se pudo obtener el total');
        }

        // 1. PROCESAR PAGO CON MERCADO PAGO
        $accessToken = "TEST-2111198746304150-033019-5a041b9a6bacc7bc553b34b27baf215f-3303201883";
        $payment_data = [
            'token'              => $_POST['token'] ?? null,
            'issuer_id'          => $_POST['issuer_id'] ?? null,
            'payment_method_id'  => $_POST['payment_method_id'] ?? null,
            'transaction_amount' => (float)($_POST['transaction_amount'] ?? 0),
            'installments'       => (int)($_POST['installments'] ?? 1),
            'payer'              => json_decode($_POST['payer'] ?? '{}', true)
        ];

        // MODO PRUEBA LOCAL: En localhost con HTTP, MP no puede crear tokens (requiere HTTPS).
        // Cuando DEBUG_MODE=true y el token es 'PRUEBA_LOCAL', simulamos el pago aprobado
        // para poder probar el flujo completo de la orden. NUNCA usar en producción.
        $esModoPruebaLocal = defined('DEBUG_MODE') && DEBUG_MODE && $payment_data['token'] === 'PRUEBA_LOCAL';

        if ($esModoPruebaLocal) {
            // Simular pago aprobado localmente
            $idPagoMP = 'LOCAL_TEST_' . date('YmdHis') . '_' . rand(1000, 9999);
        } else {
            // Flujo real de Mercado Pago
            if (!$payment_data['token']) {
                sendResponse('error', 'Token de pago no recibido. Completa los datos de tarjeta correctamente.');
            }

            $ch = curl_init("https://api.mercadopago.com/v1/payments");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $accessToken",
                "Content-Type: application/json",
                "X-Idempotency-Key: " . uniqid()
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment_data));
            // SSL bypass para XAMPP local - NO usar en producción
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response  = curl_exec($ch);
            $curlError = curl_error($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($curlError) {
                sendResponse('error', 'Error de conexión con Mercado Pago: ' . $curlError);
            }

            $result = json_decode($response, true);

            if ($httpCode !== 200 && $httpCode !== 201) {
                $msg = $result['message'] ?? 'Error al procesar el pago con Mercado Pago';
                sendResponse('error', $msg, $result);
            }

            if ($result['status'] !== 'approved') {
                $status_detail = $result['status_detail'] ?? 'desconocido';
                sendResponse('error', "Pago no aprobado. Estado: {$result['status']} ({$status_detail})");
            }

            $idPagoMP = $result['id'];
        }

        // 2. INICIAR TRANSACCIÓN LOCAL
        $db->beginTransaction();

        try {

            // 1. Registrar venta principal (sin dirección al principio)
            $idVenta = $ventaModel->insertarVenta(
                $usuario['id'],
                null, // id_direccion se actualizará después
                $total,
                $metodoPago['seleccionado'],
                $entrega['tipo'],
                $entrega['tipo'] === 'tienda' ? $entrega['sucursal']['id'] : null
            );

            if (!$idVenta) {
                throw new Exception('No se pudo crear la venta (ID no devuelto)');
            }

            // 2. Registrar dirección de entrega (si aplica) y asociarla a la venta
            if ($entrega['tipo'] === 'domicilio') {
                $idDireccion = $ventaModel->guardarDireccion(
                    $usuario['id'], // <-- Este debe ser el id_us de la tabla usuario
                    $entrega['domicilio']['departamento'] ?? '',
                    $entrega['domicilio']['provincia'] ?? '',
                    $entrega['domicilio']['distrito'] ?? '',
                    $entrega['domicilio']['calle'] ?? '',
                    $entrega['domicilio']['numero'] ?? '',
                    $entrega['domicilio']['piso'] ?? '',
                    $entrega['domicilio']['referencia'] ?? ''
                );
                
                // 3. Actualizar la venta con el ID de la dirección
                if ($idDireccion) {
                    $ventaModel->updateDireccionVenta($idVenta, $idDireccion);
                }
            }

            // Registrar detalles de la venta
            $detalles_Carrito = $detalleCarrito->getItems($usuario['id']);

            // comprobar si hay carrito
            if (!$detalles_Carrito) {
                throw new Exception('No se ha creado el carrito');
            }

            // insertar y comprobar si hay detalles
            if (!$ventaModel->insertarDetalle($idVenta, $detalles_Carrito)) {
                throw new Exception('Error al registrar los detalles de la venta');
            }

            // actualizar stock productos del carrito
            foreach ($detalles_Carrito as $detalle) {
                $productoModel->actualizarStock($detalle['id_producto'], $detalle['stock_producto'] - $detalle['cantidad']);
            }

            // Registrar método de pago con el ID de Mercado Pago
            $ventaModel->guardarPago(
                $idVenta,
                null, // No guardamos tarjeta por seguridad
                $metodoPago['celular']['numero'] ?? null,
                $idPagoMP // Guardamos el ID de transacción de Mercado Pago
            );

            // Limpiar carrito
            $carritoModel->delete($usuario['id']);

            // Confirmar transacción
            $db->commit();

            // Respuesta exitosa
            sendResponse('success', 'Venta registrada exitosamente', [
                'idVenta' => $idVenta,
                'total' => $total
            ]);
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $db->rollBack();
            throw $e;
        }
    } catch (Exception $e) {
        sendResponse('error', $e->getMessage());
    }
}
```

### public/php/usuario_actualizar.php

```php
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
```

