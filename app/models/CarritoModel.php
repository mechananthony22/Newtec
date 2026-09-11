<?php

//require_once __DIR__ . '/../config/database.php';
//require_once __DIR__ . '/../models/UsuarioModel.php';

class CarritoModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {

        try {
            $sql = "SELECT * FROM carrito";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los carritos: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        try {
            $sql = "SELECT * FROM carrito WHERE id_carrito = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el carrito: " . $e->getMessage());
        }
    }

    public function getByCartValidationCliente($id_usuario)
    {
        try {
            $userExist = (new UsuarioModel())->getById($id_usuario);
            // Primero verificar si el usuario existe
            if (!$userExist) {
                return null;
            }

            // Buscar carrito activo de usuario
            $sql = "SELECT * FROM carrito WHERE id_usuario = ? AND estado = 'activo' LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_usuario]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener validación de usuario con carrito: " . $e->getMessage());
        }
    }

    public function create($id_usuario)
    {
        try {
            // Verificar primero que el usuario existe
            $sqlCheck = "SELECT id_us FROM usuario WHERE id_us = ?";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([$id_usuario]);

            if (!$stmtCheck->fetch()) {
                throw new Exception("El usuario con ID $id_usuario no existe");
            }

            // Crear el carrito
            $sql = "INSERT INTO carrito (id_usuario, estado, fecha_creacion) 
                    VALUES (?, 'activo', NOW())";

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$id_usuario]);

            if ($result) {
                $id_carrito = $this->db->lastInsertId();
                error_log("Carrito creado exitosamente con ID: " . $id_carrito);
                return $id_carrito;
            } else {
                $error = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la consulta: " . json_encode($error));
            }
        } catch (PDOException $e) {
            error_log("Error en CarritoModel::create - " . $e->getMessage());
            throw new Exception("Error al crear el carrito: " . $e->getMessage());
        }
    }


    // Cambiar la firma para que sea compatible con Model
    public function update($id, $data)
    {
        return parent::update($id, $data);
    }

    // Método personalizado para actualizar carrito
    public function updateCarrito($id, $id_usuario, $estado)
    {
        try {
            $sql = "UPDATE carrito SET id_usuario = ?, estado = ? WHERE id_carrito = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $id_usuario,
                $estado,
                $id
            ]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el carrito: " . $e->getMessage());
        }
    }

    public function delete($id_usuario)
    {
        try {
            $sql = "DELETE FROM carrito
                    WHERE carrito.id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_usuario]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el carrito: " . $e->getMessage());
        }
    }

    // Devuelve la cantidad de carritos (puedes ajustar para contar productos de un usuario si es necesario)
    public function getCount($id_usuario = null)
    {
        if ($id_usuario !== null) {
            $sql = "SELECT COUNT(*) as count FROM carrito WHERE id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_usuario]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM carrito";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Obtener o crear carrito activo del usuario
     */
    public function getOrCreateCart($id_usuario)
    {
        try {
            // Buscar carrito activo existente
            $carrito = $this->getByCartValidationCliente($id_usuario);
            
            if ($carrito) {
                return $carrito;
            }
            
            // Si no existe, crear uno nuevo
            $id_carrito = $this->create($id_usuario);
            
            if ($id_carrito) {
                return $this->getById($id_carrito);
            }
            
            throw new Exception("No se pudo crear el carrito");
            
        } catch (Exception $e) {
            throw new Exception("Error al obtener o crear carrito: " . $e->getMessage());
        }
    }
}
