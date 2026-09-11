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