<?php

class BuilderModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtener todas las categorías del builder por tipo
     */
    public function getCategoriesByType($tipo)
    {
        $sql = "SELECT * FROM builder_category WHERE tipo = :tipo ORDER BY orden ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tipo' => $tipo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener productos por categoría de builder
     */
    public function getProductsByBuilderCategory($categoryId)
    {
        $sql = "SELECT p.*, m.nombre AS marca_nombre, mo.nombre AS modelo_nombre
                FROM producto p
                INNER JOIN builder_product_category bpc ON p.id_pro = bpc.id_producto
                LEFT JOIN marca m ON p.id_marca = m.id_mar
                LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
                WHERE bpc.id_builder_category = :categoryId
                AND p.stock > 0
                ORDER BY p.precio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['categoryId' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una categoría por ID
     */
    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM builder_category WHERE id_cat = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Guardar configuración del usuario
     */
    public function saveConfiguration($userId, $nombre, $tipo, $configuracion, $total)
    {
        try {
            $sql = "INSERT INTO builder_configuration (id_usuario, nombre, tipo, configuracion, total) 
                    VALUES (:userId, :nombre, :tipo, :configuracion, :total)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'userId' => $userId,
                'nombre' => $nombre,
                'tipo' => $tipo,
                'configuracion' => json_encode($configuracion),
                'total' => $total
            ]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Error al guardar configuración: ' . $e->getMessage());
        }
    }

    /**
     * Obtener configuraciones del usuario
     */
    public function getUserConfigurations($userId)
    {
        $sql = "SELECT * FROM builder_configuration 
                WHERE id_usuario = :userId 
                ORDER BY fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener producto por ID
     */
    public function getProductById($id)
    {
        $sql = "SELECT p.*, m.nombre AS marca_nombre, mo.nombre AS modelo_nombre
                FROM producto p
                LEFT JOIN marca m ON p.id_marca = m.id_mar
                LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
                WHERE p.id_pro = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Asociar producto con categoría de builder
     */
    public function assignProductToCategory($productId, $categoryId)
    {
        try {
            $sql = "INSERT IGNORE INTO builder_product_category (id_producto, id_builder_category) 
                    VALUES (:productId, :categoryId)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'productId' => $productId,
                'categoryId' => $categoryId
            ]);
        } catch (Exception $e) {
            throw new Exception('Error al asignar producto: ' . $e->getMessage());
        }
    }
}
