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