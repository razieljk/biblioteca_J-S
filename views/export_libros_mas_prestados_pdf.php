<?php
require_once '../model/MYSQL.php';
require_once '../libs/fpdf186/fpdf.php';

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$sql = "SELECT 
            l.titulo AS libro,
            l.autor,
            COUNT(p.id) AS total_prestamos
        FROM prestamo p
        JOIN libro l ON p.id_libro = l.id
        WHERE p.estado IN ('activo', 'devuelto')
        GROUP BY l.id
        ORDER BY total_prestamos DESC";

$result = $conn->query($sql);

if (!$result) {
    die('Error en la consulta: ' . $conn->error);
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Reporte: Libros mas prestados',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(10,10,'#',1);
$pdf->Cell(80,10,'Libro',1);
$pdf->Cell(60,10,'Autor',1);
$pdf->Cell(40,10,'Total Prestamos',1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);
$i = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(10,10,$i++,1);
    $pdf->Cell(80,10,utf8_decode($row['libro']),1);
    $pdf->Cell(60,10,utf8_decode($row['autor']),1);
    $pdf->Cell(40,10,$row['total_prestamos'],1);
    $pdf->Ln();
}

$pdf->Output('D', 'libros_mas_prestados.pdf');
$db->desconectar();
?>
