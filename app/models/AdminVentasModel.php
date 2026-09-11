<?php

class AdminVentasModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ✅ Obtener todas las ventas (con búsqueda opcional por nombre de cliente)
    public function getAll($filtroCliente = null)
    {
        $sql = "SELECT 
                v.id_ven AS id,
                v.total,
                CONCAT(u.nombre, ' ', u.apellidos) AS cliente,
                v.fecha_venta AS fecha,
                v.estado AS estado,
                m.nombre AS metodo_pago,
                s.nombre AS sucursal
            FROM venta v
            INNER JOIN usuario u ON v.id_usuario = u.id_us
            LEFT JOIN metodo_pago m ON v.metodo_pago_id = m.id_met
            LEFT JOIN sucursal s ON v.id_sucursal = s.id_sucur";

        $params = [];

        if ($filtroCliente) {
            $sql .= " WHERE CONCAT(u.nombre, ' ', u.apellidos) LIKE ?";
            $params[] = "%$filtroCliente%";
        }

        $sql .= " ORDER BY v.fecha_venta DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ✅ Obtener detalle por venta
    public function getDetalle($id)
    {
        $sql = "SELECT dv.id,
                dv.cantidad,
                dv.precio_unitario,
                v.total,
                p.nombre AS producto_nombre
                FROM detalle_venta dv
                INNER JOIN producto p ON dv.id_producto = p.id_pro
                INNER JOIN venta v ON dv.id_venta = v.id_ven
                WHERE dv.id_venta = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getVentasPorFecha($tipo, $anio, $mes, $dia)
    {
        $where = "";
        $params = [];

        if ($tipo === 'diario') {
            $where = "DATE(v.fecha_venta) = ?";
            $params[] = $dia;
        } elseif ($tipo === 'mensual') {
            $where = "MONTH(v.fecha_venta) = ? AND YEAR(v.fecha_venta) = ?";

            // Separar año y mes
            list($anio, $mes) = explode('-', $mes);
            $params[] = $mes;
            $params[] = $anio;
        } elseif ($tipo === 'anual') {
            $where = "YEAR(v.fecha_venta) = ?";
            $params[] = $anio;
        }

        $sql = "SELECT 
                v.id_ven AS id,
                v.estado,
                CONCAT(u.nombre, ' ', u.apellidos) AS cliente,
                v.fecha_venta AS fecha,
                m.nombre AS metodo_pago,
                v.total,
                s.nombre AS sucursal
            FROM venta v
            INNER JOIN usuario u ON v.id_usuario = u.id_us
            LEFT JOIN metodo_pago m ON v.metodo_pago_id = m.id_met
            LEFT JOIN sucursal s ON v.id_sucursal = s.id_sucur
            WHERE $where
            ORDER BY v.fecha_venta DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getTotalVentasPorFecha($tipo, $fecha)
    {
        $where = "";
        $params = [];

        if ($tipo === 'diario') {
            $where = "DATE(v.fecha_venta) = ?";
            $params[] = $fecha;
        } elseif ($tipo === 'mensual') {
            $where = "MONTH(v.fecha_venta) = MONTH(?) AND YEAR(v.fecha_venta) = YEAR(?)";
            $params[] = $fecha;
            $params[] = $fecha;
        } elseif ($tipo === 'anual') {
            $where = "YEAR(v.fecha_venta) = ?";
            $params[] = $fecha;
        }

        $sql = "SELECT SUM(v.total) as total
            FROM venta v
            WHERE $where";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }


    public function productoMasVendido($tipo, $fecha)
    {
        $condicion = $this->getCondicionFecha($tipo, $fecha, 'v.fecha_venta');

        $sql = "SELECT p.nombre, SUM(dv.cantidad) AS total
            FROM detalle_venta dv
            JOIN venta v ON dv.id_venta = v.id_ven
            JOIN producto p ON dv.id_producto = p.id_pro
            WHERE $condicion
            GROUP BY p.id_pro
            ORDER BY total DESC
            LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function diaConMasVentas($tipo, $fecha)
    {
        $condicion = $this->getCondicionFecha($tipo, $fecha, 'fecha_venta');

        $sql = "SELECT DATE(fecha_venta) AS fecha, SUM(total) AS total
            FROM venta
            WHERE $condicion
            GROUP BY DATE(fecha_venta)
            ORDER BY total DESC
            LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function datosGrafico($tipo, $fecha)
    {
        $condicion = $this->getCondicionFecha($tipo, $fecha, 'fecha_venta');

        $select = $tipo === 'diario' ? "DAY(fecha_venta)" : ($tipo === 'mensual' ? "DAY(fecha_venta)" : "MONTH(fecha_venta)");

        $label = $tipo === 'diario' ? 'Día' : ($tipo === 'mensual' ? 'Día' : 'Mes');

        $sql = "SELECT $select AS etiqueta, SUM(total) AS total
            FROM venta
            WHERE $condicion
            GROUP BY etiqueta
            ORDER BY etiqueta";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getCondicionFecha($tipo, $fecha, $campo)
    {
        switch ($tipo) {
            case 'diario':
                return "DATE($campo) = '{$fecha}'";
            case 'mensual':
                return "DATE_FORMAT($campo, '%Y-%m') = '{$fecha}'";
            case 'anual':
                return "YEAR($campo) = '{$fecha}'";
            default:
                return "1"; // sin filtro
        }
    }


    //------------------------------------------------------------------------------------------------------------------

    // paso 1
    function insertarVenta($idUsuario, $total, $metodoPagoId, $tipoEntrega, $idSucursal)
    {
        try {
            $sql = "INSERT INTO venta (id_usuario, total, metodo_pago_id, tipo_entrega, id_sucursal) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $idUsuario,
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

    public function guardarDireccion($idVenta, $departamento, $provincia, $distrito, $calle, $numero, $piso, $referencia)
    {
        try {
            $sql = "INSERT INTO direccion_entrega (id_venta, departamento, provincia, distrito, calle, numero, piso, referencia) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $idVenta,
                $departamento,
                $provincia,
                $distrito,
                $calle,
                $numero,
                $piso,
                $referencia
            ]);
        } catch (Exception $e) {
            throw new Exception('Error al guardar direccion: ' . $e->getMessage());
        }
    }

    public function guardarPago($idVenta, $numero_tarjeta, $numero_telefono)
    {
        try {
            $sql = "INSERT INTO pago (id_venta, numero_tarjeta, numero_telefono) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $idVenta,
                $numero_tarjeta ?? null,
                $numero_telefono ?? null
            ]);
        } catch (Exception $e) {
            throw new Exception('Error al guardar pago: ' . $e->getMessage());
        }
    }

    // Cambiar el estado de una venta
    public function actualizarEstado($id, $estado)
    {
        $stmt = $this->db->prepare("UPDATE venta SET estado = :estado WHERE id_ven = :id");
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // Obtener los datos generales de una venta por ID
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT v.id_ven AS id, v.fecha_venta AS fecha, v.estado, v.total FROM venta v WHERE v.id_ven = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDb()
    {
        return $this->db;
    }
}
