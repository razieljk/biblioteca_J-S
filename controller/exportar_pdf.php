<?php
require_once '../model/MYSQL.php';
require_once '../libs/fpdf/fpdf.php';

$tipo = $_GET['tipo'] ?? 'disponibles';

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

if ($tipo === 'prestados') {
    $titulo = "Reporte de Libros Prestados";
    $sql = "SELECT l.titulo, l.autor, u.nombre AS usuario, p.fecha_prestamo, p.fecha_devolucion
            FROM prestamos p
            JOIN libro l ON p.libro_id = l.id
            JOIN usuario u ON p.usuario_id = u.id
            WHERE p.estado = 'prestado'";
} else {
    $titulo = "Reporte de Libros Disponibles";
    $sql = "SELECT titulo, autor, categoria, cantidad FROM libro WHERE cantidad > 0";
}

$result = $conn->query($sql);

// Crear PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$pdf->Cell(0, 10, utf8_decode("Biblioteca Virtual"), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, utf8_decode("Generado el: " . date('d/m/Y H:i')), 0, 1, 'R');
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(52, 152, 219); // azul
$pdf->SetTextColor(255, 255, 255);

if ($tipo === 'prestados') {
    $headers = ['TÍTULO', 'AUTOR', 'USUARIO', 'FECHA PRÉSTAMO', 'FECHA DEVOLUCIÓN'];
    $widths = [80, 50, 60, 40, 40];
} else {
    $headers = ['TÍTULO', 'AUTOR', 'CATEGORÍA', 'CANTIDAD'];
    $widths = [100, 60, 60, 40];
}

foreach ($headers as $i => $header) {
    $pdf->Cell($widths[$i], 10, utf8_decode($header), 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $i = 0;
        foreach ($row as $valor) {
            $pdf->Cell($widths[$i], 8, utf8_decode($valor), 1);
            $i++;
        }
        $pdf->Ln();
    }
} else {
    $pdf->Cell(array_sum($widths), 10, utf8_decode("No hay registros"), 1, 0, 'C');
}
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 9);
$pdf->Cell(0, 10, utf8_decode("Biblioteca Virtual © " . date('Y')), 0, 0, 'C');

$pdf->Output("I", "$titulo.pdf");
