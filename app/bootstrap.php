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
