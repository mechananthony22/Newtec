<?php

class VentaModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    //------------------------------------------------------------------------------------------------------------------

    // paso 1
    function insertarVenta($idUsuario, $idDireccion, $total, $metodoPagoId, $tipoEntrega, $idSucursal)
    {
        try {
            $sql = "INSERT INTO venta (id_usuario, id_direccion, total, metodo_pago_id, tipo_entrega, id_sucursal) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $idUsuario,
                $idDireccion,
                $total,
                $metodoPagoId,
                $tipoEntrega,
                $idSucursal
            ]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Error al insertar venta: ' . $e->getMessage());
        }
    }

    // paso 2
    public function insertarDetalle($idVenta, $carrito)
    {
        try {
            $sql = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            foreach ($carrito as $item) {
                $stmt->execute([
                    $idVenta,
                    $item['id_producto'],
                    $item['cantidad'],
                    $item['precio_producto']
                ]);
            }
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al insertar detalle: ' . $e->getMessage());
        }
    }

    public function guardarDireccion(
        $idUsuario,
        $departamento,
        $provincia,
        $distrito,
        $calle,
        $numero,
        $piso,
        $referencia
    ) {
        try {
            $sql = "INSERT INTO direccion_entrega (id_usuario, departamento, provincia, distrito, calle, numero, piso, referencia) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $idUsuario,
                $departamento,
                $provincia,
                $distrito,
                $calle,
                $numero,
                $piso,
                $referencia
            ]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Error al guardar direccion: ' . $e->getMessage());
        }
    }

    public function guardarPago($idVenta, $numero_tarjeta, $numero_telefono, $idTransaccion = null)
    {
        try {
            $sql = "INSERT INTO pago (id_venta, numero_tarjeta, numero_telefono, id_transaccion) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $idVenta,
                $numero_tarjeta ?? null,
                $numero_telefono ?? null,
                $idTransaccion ?? null
            ]);
        } catch (Exception $e) {
            throw new Exception('Error al guardar pago: ' . $e->getMessage());
        }
    }

    public function getByUsuario($usuarioId) {
        $sql = "SELECT * FROM venta WHERE id_usuario = :id_usuario ORDER BY fecha_venta DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_usuario' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public function getDetalleById($id_venta, $usuarioId) {
        $sql = "SELECT v.*, dv.*, 
                       p.nombre AS producto_nombre, p.imagen,
                       u.nombre AS cliente_nombre, u.apellidos AS cliente_apellidos, u.dni, u.telefono, u.correo AS cliente_correo,
                       d.departamento, d.provincia, d.distrito, d.calle, d.numero, d.piso, d.referencia,
                       s.nombre AS sucursal_nombre, s.direccion AS sucursal_direccion
                FROM venta v
                INNER JOIN detalle_venta dv ON v.id_ven = dv.id_venta
                INNER JOIN producto p ON dv.id_producto = p.id_pro
                INNER JOIN usuario u ON v.id_usuario = u.id_us
                LEFT JOIN direccion_entrega d ON v.id_direccion = d.id
                LEFT JOIN sucursal s ON v.id_sucursal = s.id_sucur
                WHERE v.id_ven = :id_venta AND v.id_usuario = :usuarioId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_venta' => $id_venta, 'usuarioId' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public function updateDireccionVenta($idVenta, $idDireccion)
    {
        try {
            $sql = "UPDATE venta SET id_direccion = ? WHERE id_ven = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$idDireccion, $idVenta]);
        } catch (Exception $e) {
            throw new Exception('Error al actualizar la dirección de la venta: ' . $e->getMessage());
        }
    }
}