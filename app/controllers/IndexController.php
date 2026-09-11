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
