<?php

namespace Admin;

use ModelosModel;
use MarcasModel;

class ModelosController
{
    private $model;
    private $marcaModel;

    public function __construct()
    {
        $this->model = new ModelosModel();
        $this->marcaModel = new MarcasModel();
    }

    public function index()
    {
        $marcas = $this->marcaModel->getAll();
        $modelos = $this->model->getAllWithMarca();
        require_once __DIR__ . '/../../views/admin/config/modelos.php'; 
    }
}
