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
