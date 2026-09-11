<?php

//require_once __DIR__ . '/../config/database.php';

class RolesModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM rol";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cambiar la firma para que sea compatible con Model
    public function create($data)
    {
        return parent::create($data);
    }

    // Método personalizado para crear rol
    public function createRol($nombre, $estado)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO rol (nombre, estado) VALUES (:nombre, :estado)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':estado', $estado);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido crear el rol: " . $e->getMessage();
        }
    }

    // Cambiar la firma para que sea compatible con Model
    public function update($id, $data)
    {
        return parent::update($id, $data);
    }

    // Método personalizado para actualizar rol
    public function updateRol($id, $nombre, $estado)
    {
        try {
            $stmt = $this->db->prepare("UPDATE rol SET nombre = :nombre, estado = :estado WHERE id_rol = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':estado', $estado);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido actualizar el rol: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM rol WHERE id_rol = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido eliminar el rol: " . $e->getMessage();
        }
    }
}