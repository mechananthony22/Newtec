<?php

//include_once __DIR__ . '/../config/database.php';

class SucursalModel extends Model
{
    private $conn;

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM sucursal";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}