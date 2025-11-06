<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once '../model/MYSQL.php';
require_once '../libs/fpdf186/fpdf.php';

$tipo = $_GET['tipo'] ?? '';
if (!in_array($tipo, ['disponibles', 'prestados'])) {
    die("Tipo de reporte no válido");
}

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

if ($tipo === 'disponibles') {
    $titulo = "Reporte de Libros Disponibles";
    $sql = "SELECT titulo, autor, categoria, cantidad FROM libro WHERE cantidad > 0";
} else {
    $titulo = "Reporte de Libros Prestados";
    $sql = "SELECT l.titulo, l.autor, COUNT(p.id) AS veces_prestado
            FROM prestamos p
            JOIN libro l ON p.libro_id = l.id
            WHERE p.estado = 'prestado'
            GROUP BY l.id";
}

$result = $conn->query($sql);

// Título principal
$pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
$pdf->Ln(8);

// Encabezados de tabla
$pdf->SetFont('Arial', 'B', 12);

if ($tipo === 'disponibles') {
    $pdf->Cell(80, 8, utf8_decode('Título'), 1);
    $pdf->Cell(50, 8, utf8_decode('Autor'), 1);
    $pdf->Cell(40, 8, utf8_decode('Categoría'), 1);
    $pdf->Cell(20, 8, utf8_decode('Cant.'), 1);
    $pdf->Ln();

    // Contenido
    $pdf->SetFont('Arial', '', 11);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(80, 8, utf8_decode($row['titulo']), 1);
        $pdf->Cell(50, 8, utf8_decode($row['autor']), 1);
        $pdf->Cell(40, 8, utf8_decode($row['categoria']), 1);
        $pdf->Cell(20, 8, $row['cantidad'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(90, 8, utf8_decode('Título'), 1);
    $pdf->Cell(70, 8, utf8_decode('Autor'), 1);
    $pdf->Cell(30, 8, utf8_decode('Veces Prestado'), 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 11);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(90, 8, utf8_decode($row['titulo']), 1);
        $pdf->Cell(70, 8, utf8_decode($row['autor']), 1);
        $pdf->Cell(30, 8, $row['veces_prestado'], 1);
        $pdf->Ln();
    }
}

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 8, utf8_decode('Generado el: ') . date('d/m/Y H:i:s'), 0, 1, 'R');

$pdf->Output();

$db->desconectar();
?>
