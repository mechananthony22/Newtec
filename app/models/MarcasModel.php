<?php

require_once __DIR__ . '/../core/Model.php';

class MarcasModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM marca";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createMarca($nombre)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO marca (nombre) VALUES (:nombre)");
            $stmt->bindParam(':nombre', $nombre);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido crear la marca: " . $e->getMessage();
        }
    }

    public function updateMarca($id, $nombre)
    {
        try {
            $stmt = $this->db->prepare("UPDATE marca SET nombre = :nombre WHERE id_mar = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido actualizar la marca: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM marca WHERE id_mar = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido eliminar la marca: " . $e->getMessage();
        }
    }
}
