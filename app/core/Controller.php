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