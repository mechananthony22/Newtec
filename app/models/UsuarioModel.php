<?php

//require_once __DIR__ . '/../config/database.php';

class UsuarioModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener todos los usuarios
    public function getAll($nombre = null)
    {
        try {
            $query = "SELECT usuario.*, rol.nombre as rol_nombre
                      FROM usuario
                      INNER JOIN rol ON usuario.id_rol = rol.id_rol";
            
            // Agregar cláusula WHERE si se proporciona un nombre
            if (!empty($nombre)) {
                $query .= " WHERE usuario.nombre LIKE :nombre";
            }
    
            $query .= " ORDER BY usuario.fecha_registro DESC";
    
            $stmt = $this->db->prepare($query);
    
            // Enlazar parámetro solo si se busca por nombre
            if (!empty($nombre)) {
                $stmt->bindValue(':nombre', '%' . $nombre . '%');
            }
    
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Obtener un usuario por ID
    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuario WHERE id_us = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Obtener un usuario por email
    public function findByEmail($email)
    {
        try {
            $query = "SELECT u.*, r.nombre as rol  FROM usuario as u
                      INNER JOIN rol as r ON u.id_rol = r.id_rol
                      WHERE u.correo = :correo AND u.estado = 'activo'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':correo', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En un entorno de producción, sería mejor loguear el error
            // y no mostrarlo directamente al usuario.
            error_log("Error en findByEmail: " . $e->getMessage());
            return false;
        }
    }

    // Crear nuevo usuario
    public function create($data)
    {
        try {
            $stmt = $this->db->prepare("
            INSERT INTO usuario 
            (nombre, apellidos, correo, contrasena, dni, telefono, id_rol, estado, codigo_verificacion) 
            VALUES 
            (:nombre, :apellidos, :correo, :contrasena, :dni, :telefono, :id_rol, :estado, :codigo_verificacion)
        ");

            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellidos', $data['apellidos']);
            $stmt->bindParam(':correo', $data['correo']);
            $stmt->bindParam(':contrasena', $data['contrasena']);
            $stmt->bindParam(':dni', $data['dni']);
            $stmt->bindParam(':telefono', $data['telefono']);
            $stmt->bindParam(':id_rol', $data['id_rol']);
            $stmt->bindParam(':estado', $data['estado']);
            $stmt->bindParam(':codigo_verificacion', $data['codigo_verificacion']);

            return $stmt->execute();
        } catch (PDOException $e) {
            // Relanzar la excepción para que el controlador la maneje
            throw $e;
        }
    }

    // Actualizar usuario existente
    public function update($id, $data)
    {
        try {
            $stmt = $this->db->prepare("
            UPDATE usuario SET
                nombre = :nombre,
                apellidos = :apellidos,
                correo = :correo,
                telefono = :telefono,
                id_rol = :id_rol,
                estado = :estado,
                codigo_verificacion = :codigo_verificacion
            WHERE id_us = :id
        ");

            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellidos', $data['apellidos']);
            $stmt->bindParam(':correo', $data['correo']);
            $stmt->bindParam(':telefono', $data['telefono']);
            $stmt->bindParam(':id_rol', $data['id_rol']);
            $stmt->bindParam(':estado', $data['estado']);
            $stmt->bindParam(':codigo_verificacion', $data['codigo_verificacion']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Eliminar usuario
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM usuario WHERE id_us = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
