<?php

namespace Admin;

use MarcasModel;

class MarcasController {
    private $marcaModel;

    public function __construct() {
        $this->marcaModel = new MarcasModel();
    }
    
    public function index() {
        $marcas = $this->marcaModel->getAll();
        require_once __DIR__ . '/../../views/admin/config/marcas.php';
    }
}