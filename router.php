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
