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