<?php
require_once '../model/MYSQL.php';
require_once '../libs/fpdf186/fpdf.php';

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$sql = "SELECT 
            u.nombre AS usuario,
            l.titulo AS libro,
            p.fecha_prestamo,
            p.fecha_devolucion,
            p.estado,
            p.dias
        FROM prestamo p
        JOIN usuarios u ON p.id_usuario = u.id
        JOIN libro l ON p.id_libro = l.id
        ORDER BY p.fecha_prestamo DESC";


$result = $conn->query($sql);

// Validación de errores SQL
if (!$result) {
    die('<h3 style="color:red;text-align:center;">❌ Error en la consulta SQL:</h3><pre>' . $conn->error . '</pre>');
}

if ($result->num_rows === 0) {
    die('<h3 style="text-align:center;color:orange;">⚠ No hay registros de préstamos para mostrar.</h3>');
}

if (isset($_GET['export']) && $_GET['export'] === 'pdf') {

    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, 'Reporte de Prestamos', 0, 1, 'C');
            $this->Ln(5);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(200, 220, 255);

    $pdf->Cell(40, 10, 'Usuario', 1, 0, 'C', true);
    $pdf->Cell(50, 10, 'Libro', 1, 0, 'C', true);
    $pdf->Cell(35, 10, utf8_decode('F. Préstamo'), 1, 0, 'C', true);
    $pdf->Cell(35, 10, utf8_decode('F. Devolución'), 1, 0, 'C', true);
    $pdf->Cell(20, 10, 'Días', 1, 0, 'C', true);
    $pdf->Cell(20, 10, 'Estado', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 10);

    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40, 8, utf8_decode($row['usuario']), 1);
        $pdf->Cell(50, 8, utf8_decode($row['libro']), 1);
        $pdf->Cell(35, 8, $row['fecha_prestamo'], 1);
        $pdf->Cell(35, 8, $row['fecha_devolucion'], 1);
        $pdf->Cell(20, 8, $row['dias'], 1, 0, 'C');
        $pdf->Cell(20, 8, utf8_decode($row['estado']), 1, 1, 'C');
    }

    $pdf->Output('D', 'reporte_prestamos.pdf');
    exit;
}

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte de Préstamos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8f9fa; }
  </style>
</head>
<body>
<div class="container py-4">
  <h2 class="mb-3"> Reporte de Préstamos</h2>

  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="card-title mb-3">Historial de Préstamos</h5>

      <table class="table table-striped table-bordered">
        <thead class="table-primary">
          <tr>
            <th>Usuario</th>
            <th>Libro</th>
            <th>Fecha Préstamo</th>
            <th>Fecha Devolución</th>
            <th>Días</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['usuario']) ?></td>
              <td><?= htmlspecialchars($row['libro']) ?></td>
              <td><?= htmlspecialchars($row['fecha_prestamo']) ?></td>
              <td><?= htmlspecialchars($row['fecha_devolucion']) ?></td>
              <td><?= htmlspecialchars($row['dias']) ?></td>
              <td><?= htmlspecialchars($row['estado']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <div class="mt-3">
        <a href="?export=pdf" class="btn btn-danger">📄 Exportar a PDF</a>
        <a href="../dashboard.php" class="btn btn-secondary">⬅ Volver al Dashboard</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
<?php
$db->desconectar();
?>
