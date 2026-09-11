<?php

//require_once __DIR__ . '/../config/database.php';

class SubcategoriasModel extends Model
{
    private $conn;

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM subcategoria";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithSubCategoria()
    {
        try {
            $query = "SELECT mo.id, mo.nombre, ma.id_cat, ma.nombre as categoria 
            FROM subcategoria as mo 
            JOIN categoria as ma ON mo.id_categoria = ma.id_cat";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener las subcategorias: " . $e->getMessage();
        }
    }

    public function getSubCategoriasById($id)
    {
        try {
            $query = "SELECT * FROM subcategoria WHERE id_categoria = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener las subcategorias: " . $e->getMessage();
        }
    }

    // Cambiar la firma para que sea compatible con Model
    public function create($data)
    {
        // Puedes usar el método padre si quieres la funcionalidad base
        return parent::create($data);
    }

    // Método personalizado para crear subcategoría
    public function createSubcategoria($nombre, $id_categoria)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO subcategoria (nombre, id_categoria) VALUES (:nombre, :id_categoria)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id_categoria', $id_categoria);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido crear la subcategoria: " . $e->getMessage();
        }
    }

    public function update($id, $data)
    {
        return parent::update($id, $data);
    }

    // Método personalizado para actualizar subcategoría
    public function updateSubcategoria($id, $nombre, $id_categoria)
    {
        try {
            $stmt = $this->db->prepare("UPDATE subcategoria SET nombre = :nombre, id_categoria = :id_categoria WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id_categoria', $id_categoria);
            return $stmt->execute();
        } catch (Exception $e) {
            return "No se ha podido actualizar la subcategoria: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM subcategoria WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ["success" => true, "message" => "Subcategoria eliminada correctamente"];
        } catch (Exception $e) {
            return ["success" => false, "message" => "No se ha podido eliminar la subcategoria: " . $e->getMessage()];
        }
    }

    /**
     * Obtiene todas las categorías con sus subcategorías para el menú de navegación
     * @return array Estructura de categorías con sus subcategorías
     */
    public function getCategoriesWithSubcategories()
    {
        try {
            // Usamos una sola consulta con JOIN para obtener todo en una sola llamada
            $query = "SELECT 
                        c.id_cat as id, 
                        c.nombre as categoria_nombre,
                        s.id as subcategoria_id,
                        s.nombre as subcategoria_nombre
                      FROM categoria c
                      LEFT JOIN subcategoria s ON c.id_cat = s.id_categoria
                      ORDER BY c.nombre, s.nombre";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $result = [];
            $currentCategory = null;
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Si es una nueva categoría o la primera iteración
                if ($currentCategory === null || $currentCategory['id'] != $row['id']) {
                    if ($currentCategory !== null) {
                        $result[] = $currentCategory;
                    }
                    $currentCategory = [
                        'id' => $row['id'],
                        'nombre' => $row['categoria_nombre'],
                        'subcategorias' => []
                    ];
                }
                
                // Agregar subcategoría si existe
                if ($row['subcategoria_id'] !== null) {
                    $currentCategory['subcategorias'][] = [
                        'id' => $row['subcategoria_id'],
                        'nombre' => $row['subcategoria_nombre']
                    ];
                }
            }
            
            // Agregar la última categoría procesada
            if ($currentCategory !== null) {
                $result[] = $currentCategory;
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Error al obtener categorías con subcategorías: " . $e->getMessage());
            return [];
        }
    }

    public function getBySlug($slug, $id_categoria = null)
    {
        $sql = "SELECT * FROM subcategoria WHERE REPLACE(LOWER(nombre), ' ', '-') = :slug";
        if ($id_categoria !== null) {
            $sql .= " AND id_categoria = :id_categoria";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        if ($id_categoria !== null) {
            $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener subcategorías por id de categoría
    public function getByCategory($id_categoria)
    {
        $sql = "SELECT * FROM subcategoria WHERE id_categoria = :id_categoria";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
