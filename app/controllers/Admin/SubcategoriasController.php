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
