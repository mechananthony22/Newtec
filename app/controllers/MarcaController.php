<?php

class MarcaController
{
    private $model;

    public function __construct()
    {
        $this->model = new MarcasModel();
    }

    public function index()
    {
        $marcas = $this->model->getAll();
        require_once __DIR__ . '/../views/marca_list.php'; // Solo si deseas listar
    }
}
