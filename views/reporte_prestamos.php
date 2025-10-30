<?php
require_once '../model/MYSQL.php';

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

if (!$result) {
    die('<h3>❌ Error en la consulta SQL:</h3><pre>' . $conn->error . '</pre>');
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte de Préstamos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{background:#f8f9fa}</style>
</head>
<body>
<div class="container py-4">
  <h2 class="mb-3">Reporte de Préstamos</h2>

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
        <a href="export_prestamos_excel.php" class="btn btn-success"> Exportar a Excel </a>
        <a href="../dashboard.php" class="btn btn-secondary">⬅ Volver al Dashboard</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
<?php
$db->desconectar();
