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
