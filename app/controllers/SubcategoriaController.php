<?php

class SubcategoriaController
{
    private $model;

    public function __construct()
    {
        $this->model = new SubcategoriasModel();
    }

    public function index()
    {
        $subcategorias = $this->model->getAll();
        require_once __DIR__ . '/../views/subcategoria_list.php'; // Solo si deseas listar
    }
}
