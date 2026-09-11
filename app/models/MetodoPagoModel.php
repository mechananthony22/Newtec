<?php

//include_once __DIR__ . '/../config/database.php';

class MetodoPagoModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtiene todos los metodos de pago
    public function getAll(){
        $sql = "SELECT * FROM metodo_pago";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}