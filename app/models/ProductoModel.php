<?php
require_once __DIR__ . '/../core/Model.php';

class ProductoModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener todos los productos con categoría, subcategoría, marca y modelo
    public function getAll()
    {
        $sql = "
            SELECT p.*, 
                   sc.nombre AS subcategoria,
                   c.nombre AS categoria,
                   m.nombre AS marca,
                   mo.nombre AS modelo
            FROM producto p
            LEFT JOIN subcategoria sc ON p.id_subcategoria = sc.id
            LEFT JOIN categoria c ON sc.id_categoria = c.id_cat
            LEFT JOIN marca m ON p.id_marca = m.id_mar
            LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
            ORDER BY p.fecha_creacion DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar productos por nombre, descripción o marca
    public function search($busqueda, $marcas = [], $precioMin = null, $precioMax = null)
    {
        $busquedaTerm = '%' . $busqueda . '%';
        
        $sql = "
            SELECT p.*, 
                   sc.nombre AS subcategoria,
                   c.nombre AS categoria,
                   m.nombre AS marca,
                   mo.nombre AS modelo
            FROM producto p
            LEFT JOIN subcategoria sc ON p.id_subcategoria = sc.id
            LEFT JOIN categoria c ON sc.id_categoria = c.id_cat
            LEFT JOIN marca m ON p.id_marca = m.id_mar
            LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
            WHERE (p.nombre LIKE ? OR p.descripcion LIKE ? OR m.nombre LIKE ?)
        ";
        
        $params = [$busquedaTerm, $busquedaTerm, $busquedaTerm];
        
        if (!empty($marcas) && is_array($marcas) && count($marcas) > 0) {
            $marcasArr = array_values($marcas);
            $placeholders = array_fill(0, count($marcasArr), '?');
            $sql .= " AND m.nombre IN (" . implode(',', $placeholders) . ")";
            $params = array_merge($params, $marcasArr);
        }
        
        if ($precioMin !== null && $precioMin !== '' && is_numeric($precioMin)) {
            $sql .= " AND p.precio >= ?";
            $params[] = (float)$precioMin;
        }
        
        if ($precioMax !== null && $precioMax !== '' && is_numeric($precioMax)) {
            $sql .= " AND p.precio <= ?";
            $params[] = (float)$precioMax;
        }
        
        $sql .= " ORDER BY p.fecha_creacion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todas las marcas únicas de productos
    public function getMarcas()
    {
        $sql = "SELECT DISTINCT m.id_mar, m.nombre 
                FROM marca m
                INNER JOIN producto p ON p.id_marca = m.id_mar
                ORDER BY m.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener productos paginados
    public function getPaginated($page = 1, $perPage = 10, $search = '')
    {
        $offset = ($page - 1) * $perPage;

        $whereClause = '';
        $params = [];

        if (!empty($search)) {
            $whereClause = "WHERE p.nombre LIKE :search1 OR p.descripcion LIKE :search2";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }

        $sql = "
        SELECT p.*, 
               sc.nombre AS subcategoria,
               c.nombre AS categoria,
               m.nombre AS marca,
               mo.nombre AS modelo
        FROM producto p
        LEFT JOIN subcategoria sc ON p.id_subcategoria = sc.id
        LEFT JOIN categoria c ON sc.id_categoria = c.id_cat
        LEFT JOIN marca m ON p.id_marca = m.id_mar
        LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
        {$whereClause}
        ORDER BY p.fecha_creacion DESC
        LIMIT :limit OFFSET :offset
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        if (!empty($search)) {
            $stmt->bindParam(':search1', $params[':search1']);
            $stmt->bindParam(':search2', $params[':search2']);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Obtener total de productos para paginación
    public function getTotalCount($search = '')
    {
        $whereClause = '';
        $params = [];

        if (!empty($search)) {
            $whereClause = "WHERE p.nombre LIKE :search1 OR p.descripcion LIKE :search2";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }

        $sql = "
        SELECT COUNT(*) as total
        FROM producto p
        {$whereClause}
    ";

        $stmt = $this->db->prepare($sql);

        if (!empty($search)) {
            $stmt->bindParam(':search1', $params[':search1']);
            $stmt->bindParam(':search2', $params[':search2']);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }


    // Obtener productos por nombre de categoría
    public function getByCategoria($nombreCategoria)
    {
        $sql = "
            SELECT p.*, 
                   sc.nombre AS subcategoria,
                   c.nombre AS categoria,
                   m.nombre AS marca,
                   mo.nombre AS modelo
            FROM producto p
            LEFT JOIN subcategoria sc ON p.id_subcategoria = sc.id
            LEFT JOIN categoria c ON sc.id_categoria = c.id_cat
            LEFT JOIN marca m ON p.id_marca = m.id_mar
            LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
            WHERE c.nombre = :categoria
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':categoria', $nombreCategoria);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener productos por nombre de subcategoria
    public function getBySubcategoria($nombreCategoria, $nombreSubcategoria)
    {
        try {
            $sql = "
                SELECT p.*, sc.nombre as subcategoria, c.nombre as categoria, mo.nombre as modelo, ma.nombre as marca
                FROM producto as p
                LEFT JOIN subcategoria as sc ON sc.id = p.id_subcategoria
                LEFT JOIN categoria as c ON c.id_cat = sc.id_categoria
                LEFT JOIN modelo as mo ON mo.id_mod = p.id_modelo
                LEFT JOIN marca as ma ON ma.id_mar = p.id_marca
                WHERE REPLACE(LOWER(c.nombre), ' ', '-') = :categoria
                AND REPLACE(LOWER(sc.nombre), ' ', '-') = :subcategoria;
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':categoria', $nombreCategoria, PDO::PARAM_STR);
            $stmt->bindParam(':subcategoria', $nombreSubcategoria, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener productos por subcategoria: " . $e->getMessage());
        }
    }

    // Obtener producto por ID
    public function getById($id, $id_usuario = null)
    {
        try {
            $sql = "
            SELECT 
                p.*, 
                cat.id_cat AS id_categoria,
                sc.nombre AS subcategoria,
                cat.nombre AS categoria,
                m.nombre AS marca,
                mo.nombre AS modelo,
                GREATEST(
                    p.stock - COALESCE(SUM(
                        CASE 
                            WHEN :id_usuario1 IS NOT NULL AND c.id_usuario = :id_usuario2 THEN dc.cantidad
                            ELSE 0
                        END
                    ), 0),
                0) AS stock_disponible
            FROM producto p
            LEFT JOIN subcategoria sc ON p.id_subcategoria = sc.id
            LEFT JOIN categoria cat ON sc.id_categoria = cat.id_cat
            LEFT JOIN marca m ON p.id_marca = m.id_mar
            LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
            LEFT JOIN detalle_carrito dc ON p.id_pro = dc.id_producto
            LEFT JOIN carrito c ON dc.id_carrito = c.id_carrito
            WHERE p.id_pro = :id
            GROUP BY p.id_pro
            LIMIT 1
        ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':id_usuario1', $id_usuario, PDO::PARAM_INT);
            $stmt->bindValue(':id_usuario2', $id_usuario, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el producto: " . $e->getMessage());
        }
    }

    /**
     * Obtener productos por categoría con filtros
     */
    public function getByCategory($id_categoria, $marcas = [], $precioMin = null, $precioMax = null)
    {
        $sql = "SELECT p.*, c.nombre AS categoria, s.nombre AS subcategoria, m.nombre AS marca
                FROM producto p
                JOIN subcategoria s ON p.id_subcategoria = s.id
                JOIN categoria c ON s.id_categoria = c.id_cat
                JOIN marca m ON p.id_marca = m.id_mar
                WHERE c.id_cat = :id_categoria";

        $params = [':id_categoria' => $id_categoria];

        if (!empty($marcas)) {
            $marcas_placeholders = [];
            foreach ($marcas as $key => $marca) {
                $placeholder = ":marca" . $key;
                $marcas_placeholders[] = $placeholder;
                $params[$placeholder] = $marca;
            }
            $sql .= " AND m.nombre IN (" . implode(',', $marcas_placeholders) . ")";
        }

        if ($precioMin !== null) {
            $sql .= " AND p.precio >= :precio_min";
            $params[':precio_min'] = $precioMin;
        }

        if ($precioMax !== null) {
            $sql .= " AND p.precio <= :precio_max";
            $params[':precio_max'] = $precioMax;
        }

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => &$val) {
            // Determinar el tipo de dato para bindParam
            if (is_int($val)) {
                $type = PDO::PARAM_INT;
            } elseif (is_bool($val)) {
                $type = PDO::PARAM_BOOL;
            } elseif (is_null($val)) {
                $type = PDO::PARAM_NULL;
            } else {
                $type = PDO::PARAM_STR;
            }
            $stmt->bindParam($key, $val, $type);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener productos por subcategoría con filtros
     */
    public function getBySubcategory($id_subcategoria, $marcas = [], $precioMin = null, $precioMax = null)
    {
        $sql = "SELECT p.*, c.nombre AS categoria, s.nombre AS subcategoria, m.nombre AS marca
                FROM producto p
                JOIN subcategoria s ON p.id_subcategoria = s.id
                JOIN categoria c ON s.id_categoria = c.id_cat
                JOIN marca m ON p.id_marca = m.id_mar
                WHERE p.id_subcategoria = :id_subcategoria";

        $params = [':id_subcategoria' => $id_subcategoria];

        if (!empty($marcas)) {
            $marcas_placeholders = [];
            foreach ($marcas as $key => $marca) {
                $placeholder = ":marca" . $key;
                $marcas_placeholders[] = $placeholder;
                $params[$placeholder] = $marca;
            }
            $sql .= " AND m.nombre IN (" . implode(',', $marcas_placeholders) . ")";
        }

        if ($precioMin !== null) {
            $sql .= " AND p.precio >= :precio_min";
            $params[':precio_min'] = $precioMin;
        }

        if ($precioMax !== null) {
            $sql .= " AND p.precio <= :precio_max";
            $params[':precio_max'] = $precioMax;
        }

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Crear nuevo producto
    public function create($data)
    {
        $sql = "INSERT INTO producto 
                (nombre, descripcion, precio, stock, imagen, id_subcategoria, id_marca, id_modelo)
                VALUES (:nombre, :descripcion, :precio, :stock, :imagen, :id_subcategoria, :id_marca, :id_modelo)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':precio', $data['precio']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':imagen', $data['imagen']);
        $stmt->bindParam(':id_subcategoria', $data['id_subcategoria']);
        $stmt->bindParam(':id_marca', $data['id_marca']);
        $stmt->bindParam(':id_modelo', $data['id_modelo']);
        return $stmt->execute();
    }

    // Actualizar producto existente
    public function update($id, $data)
    {
        $sql = "UPDATE producto SET 
                    nombre = :nombre,
                    descripcion = :descripcion,
                    precio = :precio,
                    stock = :stock,
                    id_subcategoria = :id_subcategoria,
                    id_marca = :id_marca,
                    id_modelo = :id_modelo
               ";

        /* si la imagen es distinta de null la actualiza */
        if ($data['imagen'] !== null) {
            $sql .= ", imagen = :imagen";
        }

        $sql .= " WHERE id_pro = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':precio', $data['precio']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':id_subcategoria', $data['id_subcategoria']);
        $stmt->bindParam(':id_marca', $data['id_marca']);
        $stmt->bindParam(':id_modelo', $data['id_modelo']);

        /* si la imagen es distinta de null la actualiza */
        if ($data['imagen'] !== null) {
            $stmt->bindParam(':imagen', $data['imagen']);
        }
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Eliminar producto
    public function delete($id)
    {
        $sql = "DELETE FROM producto WHERE id_pro = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // actualizar stock
    public function actualizarStock($id, $stock)
    {
        try {
            $sql = "UPDATE producto SET stock = :stock WHERE id_pro = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el stock del producto: " . $e->getMessage());
        }
    }

    // Obtener productos destacados (últimos productos agregados)
    public function getProductosDestacados($limite = 12, $id_categoria = null, $id_subcategoria = null)
    {
        try {
            $sql = "
            SELECT p.*, 
                   sc.nombre AS subcategoria,
                   c.nombre AS categoria,
                   m.nombre AS marca,
                   mo.nombre AS modelo
            FROM producto p
            LEFT JOIN subcategoria sc ON p.id_subcategoria = sc.id
            LEFT JOIN categoria c ON sc.id_categoria = c.id_cat
            LEFT JOIN marca m ON p.id_marca = m.id_mar
            LEFT JOIN modelo mo ON p.id_modelo = mo.id_mod
            WHERE 1 = 1
        ";

            // Filtros condicionales
            if ($id_categoria !== null) {
                $sql .= " AND c.id_cat = :id_categoria";
            }
            if ($id_subcategoria !== null) {
                $sql .= " AND sc.id = :id_subcategoria";
            }

            $sql .= " ORDER BY p.fecha_creacion DESC LIMIT :limite";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);

            if ($id_categoria !== null) {
                $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
            }
            if ($id_subcategoria !== null) {
                $stmt->bindParam(':id_subcategoria', $id_subcategoria, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener productos destacados: " . $e->getMessage());
        }
    }
}
