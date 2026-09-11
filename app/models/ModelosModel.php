
<?php

// require_once __DIR__ . '/../config/database.php';

class ModelosModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM modelo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithMarca()
    {
        try {
            $query = "SELECT mo.id_mod, mo.nombre, ma.id_mar, ma.nombre as marca 
            FROM modelo as mo 
            JOIN marca as ma ON mo.id_marca = ma.id_mar";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener los modelos: " . $e->getMessage();
        }
    }

    public function getMarcasById($id_marca)
    {
        try {
            $query = "SELECT * FROM modelo WHERE id_marca = :id_marca";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_marca', $id_marca, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener los modelos: " . $e->getMessage();
        }
    }

    public function createModelo($nombre, $id_marca)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO modelo (nombre, id_marca) VALUES (:nombre, :id_marca)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id_marca', $id_marca);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido crear el modelo: " . $e->getMessage();
        }
    }

    public function updateModelo($id, $nombre, $id_marca)
    {
        try {
            $stmt = $this->db->prepare("UPDATE modelo SET nombre = :nombre, id_marca = :id_marca WHERE id_mod = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id_marca', $id_marca);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido actualizar el modelo: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM modelo WHERE id_mod = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido eliminar el modelo: " . $e->getMessage();
        }
    }
}
