<?php

require_once __DIR__ . '/../core/Model.php';

class CategoriasModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM categoria";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createCategoria($nombre)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO categoria (nombre) VALUES (:nombre)");
            $stmt->bindParam(':nombre', $nombre);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido crear la categoria: " . $e->getMessage();
        }
    }

    public function updateCategoria($id, $nombre)
    {
        try {
            $stmt = $this->db->prepare("UPDATE categoria SET nombre = :nombre WHERE id_cat = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido actualizar la categoria: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM categoria WHERE id_cat = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido eliminar la marca: " . $e->getMessage();
        }
    }

    public function getBySlug($slug)
    {
        // Obtener todas las categorías y buscar la que coincida con el slug
        $categorias = $this->getAll();
        foreach ($categorias as $categoria) {
            if (function_exists('slugify')) {
                if (slugify($categoria['nombre']) === $slug) {
                    return $categoria;
                }
            } else {
                // Fallback simple si no existe slugify
                if (strtolower(str_replace(' ', '-', $categoria['nombre'])) === $slug) {
                    return $categoria;
                }
            }
        }
        return null;
    }
}
