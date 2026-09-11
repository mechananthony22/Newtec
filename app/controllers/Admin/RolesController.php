<?php

namespace Admin;

use RolesModel;

class RolesController {
    private $rolesModel;

    public function __construct() {
        $this->rolesModel = new RolesModel();
    }
    
    public function index() {
        $roles = $this->rolesModel->getAll();
        $estados = ['activo', 'suspendido'];
        require_once __DIR__ . '/../../views/admin/config/roles.php';
    }
}