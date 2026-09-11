<?php
// Incluir el autoloader y la configuración de la aplicación
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Autoloader.php';
require_once __DIR__ . '/../core/App.php';
require_once __DIR__ . '/../../lib/fpdf.php';

// Inicializar la aplicación y el autoloader
Autoloader::getInstance();
$app = App::getInstance();

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Reporte de Ventas por Fecha', 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 10, 'ID', 1);
        $this->Cell(60, 10, 'Cliente', 1);
        $this->Cell(30, 10, 'Fecha', 1);
        $this->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1', 'Método de Pago'), 1);
        $this->Cell(30, 10, 'Total', 1);
        $this->Ln();
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

// Obtener parámetros GET
$tipo  = $_GET['tipo']  ?? 'diario';
$dia = $_GET['dia'] ?? '';
$mes = $_GET['mes'] ?? '';
$anio = $_GET['anio'] ?? '';

// Debug - solo para desarrollo
// error_log("Tipo: " . $tipo . ", Fecha: " . $fecha);

// Obtener la conexión a la base de datos
try {
    $conn = $app->getDatabase();
    // Verificar conexión
    $conn->query('SELECT 1');
} catch (PDOException $e) {
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}

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

// Consulta SQL
$sql = "SELECT 
            v.id_ven AS id,
            CONCAT(u.nombre, ' ', u.apellidos) AS cliente,
            v.fecha_venta AS fecha,
            v.total,
            mp.nombre AS metodo_pago,
            s.nombre AS sucursal
        FROM venta v
        JOIN usuario u ON v.id_usuario = u.id_us
        LEFT JOIN metodo_pago mp ON v.metodo_pago_id = mp.id_met
        JOIN sucursal s ON v.id_sucursal = s.id_sucur
        WHERE $where
        ORDER BY v.fecha_venta DESC";

// Debug: Ver consulta SQL y parámetros
error_log("SQL: " . $sql);
error_log("Parámetro: " . implode(', ', $params));

try {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error en la preparación de la consulta: ' . implode(', ', $conn->errorInfo()));
    }

    $stmt->execute($params);
    if ($stmt->errorCode() !== '00000') {
        throw new Exception('Error al ejecutar la consulta: ' . implode(', ', $stmt->errorInfo()));
    }

    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Error en la consulta: ' . $e->getMessage());
}

// Debug: Ver resultados de la consulta
error_log("Número de ventas encontradas: " . count($ventas));
if (count($ventas) > 0) {
    error_log("Primera fila: " . print_r($ventas[0], true));
}

// Generar fecha filtro
$fechaFiltro;
if (empty($dia)) {
    if (empty($mes)) {
        $fechaFiltro = $anio;
    } else {
        $fechaFiltro = $anio . '-' . $mes;
    }
} else {
    $fechaFiltro = $dia;
}

// Crear PDF
$pdf = new PDF();

if (empty($ventas)) {
    // Solo una página si no hay datos
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'No se encontraron ventas para la fecha seleccionada', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, 'Tipo de reporte: ' . ucfirst($tipo), 0, 1);
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Período: ') . $fechaFiltro, 0, 1);
    
    // Agregar pie de página
    $pdf->SetY(-40);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 6, '________________________________________', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generado el: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Tipo de reporte: ' . ucfirst($tipo), 0, 1, 'C');
    $pdf->Cell(0, 6, iconv('UTF-8', 'ISO-8859-1', 'Período: ') . $fechaFiltro, 0, 1, 'C');
    
    // Salida del PDF
    $pdf->Output('I', 'ReporteVentasFecha_' . date('Ymd_His') . '.pdf');
    exit();
}

// Si hay datos, crear la página con la tabla
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);
// Mostrar datos de las ventas
$totalGeneral = 0;

foreach ($ventas as $v) {
    $pdf->Cell(10, 10, $v['id'] ?? '', 1);
    $pdf->Cell(60, 10, mb_convert_encoding($v['cliente'] ?? 'Cliente no disponible', 'ISO-8859-1', 'UTF-8'), 1);
    $pdf->Cell(30, 10, !empty($v['fecha']) ? date('d/m/Y', strtotime($v['fecha'])) : 'N/A', 1);
    $pdf->Cell(40, 10, mb_convert_encoding($v['metodo_pago'] ?? 'N/A', 'ISO-8859-1', 'UTF-8'), 1);
    $pdf->Cell(30, 10, 'S/ ' . number_format(floatval($v['total'] ?? 0), 2), 1);
    $pdf->Ln();
    $totalGeneral += floatval($v['total'] ?? 0);
}

// Mostrar total
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(140, 10, 'TOTAL GENERAL', 1);
$pdf->Cell(30, 10, 'S/ ' . number_format($totalGeneral, 2), 1);
$pdf->Ln();

// Agregar información del reporte al final de la página
$pdf->SetY(-40); // 40mm desde abajo
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 6, '________________________________________', 0, 1, 'C');
$pdf->Cell(0, 6, 'Generado el: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
$pdf->Cell(0, 6, 'Tipo de reporte: ' . ucfirst($tipo), 0, 1, 'C');
$pdf->Cell(0, 6,  iconv('UTF-8', 'ISO-8859-1', 'Período: ') . iconv('UTF-8', 'ISO-8859-1', $fechaFiltro), 0, 1, 'C');

// Enviar el PDF al navegador
$pdf->Output('I', 'ReporteVentasFecha_' . date('Ymd_His') . '.pdf');
