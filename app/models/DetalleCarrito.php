<?php

//require_once __DIR__ . '/../config/database.php';

class DetalleCarrito extends Model
{
    protected $table = 'detalle_carrito';
    // Obtiene un producto del carrito
    public function getByCartAndProduct($id_carrito, $id_producto)
    {
        try {
            // obtener datos de detalle_carrito y precio del producto
            $sql = "SELECT dc.*, p.precio
                    FROM detalle_carrito dc
                    INNER JOIN producto p ON dc.id_producto = p.id_pro
                    WHERE dc.id_carrito = ? AND dc.id_producto = ? 
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_carrito, $id_producto]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el ítem del carrito: " . $e->getMessage());
            return false;
        }
    }

    // Cambiar la firma para que sea compatible con Model
    public function create($data)
    {
        return parent::create($data);
    }

    // Método personalizado para crear detalle de carrito
    public function createDetalle($id_carrito, $id_producto, $cantidad, $precio_unitario)
    {
        try {
            // Verificar si el producto ya existe en el carrito
            $existing = $this->getByCartAndProduct($id_carrito, $id_producto);

            if ($existing) {
                // Si el producto ya existe, actualizar la cantidad
                $nueva_cantidad = $existing['cantidad'] + $cantidad;
                $nuevo_precio_total = $existing['precio'] * $nueva_cantidad;
                $actualizado = $this->updateQuantity(
                    $existing['id_detalle'],
                    $nueva_cantidad,
                    $nuevo_precio_total
                );

                if ($actualizado) {
                    return $existing['id_detalle']; // Retornar el ID del detalle existente
                } else {
                    throw new Exception("No se pudo actualizar la cantidad del producto existente");
                }
            }

            // Insertar nuevo ítem si no existe
            $sql = "INSERT INTO detalle_carrito 
                    (id_carrito, id_producto, cantidad, precio_total) 
                    VALUES (?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $id_carrito,
                $id_producto,
                $cantidad,
                $precio_unitario * $cantidad // calcular precio total
            ]);

            if ($result) {
                return $this->db->lastInsertId();
            } else {
                throw new Exception("No se pudo insertar el ítem en el carrito");
            }
        } catch (PDOException $e) {
            throw new Exception("Error al agregar el producto al carrito: " . $e->getMessage());
        }
    }

    // actualiza la cantidad de un producto en el carrito al momento de agregar mas de un producto
    public function updateQuantity($id_detalle, $nueva_cantidad, $nuevo_precio_total = null)
    {
        try {
            if ($nuevo_precio_total !== null) {
                $sql = "UPDATE detalle_carrito 
                        SET cantidad = ?, precio_total = ?
                        WHERE id_detalle = ?";
                $params = [$nueva_cantidad, $nuevo_precio_total, $id_detalle];
            } else {
                $sql = "UPDATE detalle_carrito 
                        SET cantidad = ?
                        WHERE id_detalle = ?";
                $params = [$nueva_cantidad, $id_detalle];
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            // Verificar si se actualizó alguna fila
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                throw new Exception("No se encontró el detalle del carrito con ID: " . $id_detalle);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la cantidad del producto: " . $e->getMessage());
        }
    }

    // Elimina un producto del carrito
    public function delete($id_detalle)
    {
        try {
            $sql = "DELETE FROM detalle_carrito WHERE id_detalle = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id_detalle]);
        } catch (PDOException $e) {
            error_log("Error deleting cart item: " . $e->getMessage());
            return false;
        }
    }


    // Obtiene todos los productos del carrito
    public function getItems($id_usuario)
    {
        try {
            $sql = "
            SELECT dc.*,
                   p.id_pro AS id_producto,
                   p.nombre AS nombre_producto,
                   p.imagen AS imagen_producto,
                   p.stock AS stock_producto,
                   p.precio AS precio_producto
            FROM detalle_carrito dc
            INNER JOIN producto p ON dc.id_producto = p.id_pro
            INNER JOIN carrito c ON dc.id_carrito = c.id_carrito
            WHERE c.id_usuario = :id_usuario
        ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los productos del carrito: " . $e->getMessage());
        }
    }

    // obtener cuantos productos hay en el carrito
    public function getProductsCount($id_usuario)
    {
        try {

            $sql = "SELECT COUNT(dc.id_detalle) as count
            FROM detalle_carrito as dc
            INNER JOIN carrito as c ON dc.id_carrito = c.id_carrito
            WHERE c.id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_usuario]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el conteo del carrito: " . $e->getMessage());
        }
    }

    public function getTotal($id_usuario)
    {
        try {
            $sql = "SELECT SUM(dc.precio_total) as total
            FROM detalle_carrito as dc
            INNER JOIN carrito as c ON dc.id_carrito = c.id_carrito
            WHERE c.id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_usuario]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el total del carrito: " . $e->getMessage());
        }
    }

    // eliminar productos sin stock
    public function removeOutOfStockItems($id_usuario)
    {
        try {
            $sql = "
            DELETE dc FROM detalle_carrito dc
            INNER JOIN carrito c ON dc.id_carrito = c.id_carrito
            INNER JOIN producto p ON dc.id_producto = p.id_pro
            WHERE c.id_usuario = :id_usuario AND p.stock < dc.cantidad
        ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount(); // Devuelve la cantidad de filas eliminadas
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar productos sin stock: " . $e->getMessage());
        }
    }

    // verificar si el carrito esta vacio
    public function isEmpty($id_usuario)
    {
        try {
            $sql = "SELECT COUNT(*) as count
            FROM detalle_carrito as dc
            INNER JOIN carrito as c ON dc.id_carrito = c.id_carrito
            WHERE c.id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_usuario]);
            return $stmt->fetchColumn() === 0;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar si el carrito esta vacio: " . $e->getMessage());
        }
    }

    /**
     * Agregar producto al carrito (usado por el builder)
     */
    public function agregarProducto($id_usuario, $id_producto, $cantidad = 1)
    {
        try {
            // Obtener o crear carrito del usuario
            require_once __DIR__ . '/CarritoModel.php';
            $carritoModel = new CarritoModel();
            $carrito = $carritoModel->getOrCreateCart($id_usuario);
            
            if (!$carrito) {
                throw new Exception("No se pudo crear el carrito");
            }
            
            // Obtener precio del producto
            $sqlPrecio = "SELECT precio FROM producto WHERE id_pro = ?";
            $stmtPrecio = $this->db->prepare($sqlPrecio);
            $stmtPrecio->execute([$id_producto]);
            $precio = $stmtPrecio->fetchColumn();
            
            if (!$precio) {
                throw new Exception("Producto no encontrado");
            }
            
            // Usar el método createDetalle existente
            return $this->createDetalle($carrito['id_carrito'], $id_producto, $cantidad, $precio);
            
        } catch (Exception $e) {
            throw new Exception("Error al agregar producto: " . $e->getMessage());
        }
    }
}
