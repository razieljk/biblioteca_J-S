<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../model/MYSQL.php';
$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$sqlHistorial = "SELECT 
                    u.nombre AS usuario,
                    l.titulo AS libro,
                    p.fecha_prestamo,
                    p.fecha_devolucion,
                    p.dias,
                    p.estado
                FROM prestamo p
                JOIN usuarios u ON p.id_usuario = u.id
                JOIN libro l ON p.id_libro = l.id
                ORDER BY p.fecha_prestamo DESC";

$resultHistorial = $conn->query($sqlHistorial);

$db->desconectar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial de Préstamos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
.card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
.table th { background-color: #0d6efd; color: white; }
</style>
</head>
<body class="p-4">

<div class="container">
  <div class="card p-4">
    <h4 class="text-center text-primary fw-bold mb-3">📚 Historial de Préstamos</h4>

    <?php if ($resultHistorial && $resultHistorial->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead>
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
            <?php while ($row = $resultHistorial->fetch_assoc()): ?>
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
      </div>

      <div class="text-center mt-3">
        <a href="export_prestamos_excel.php" class="btn btn-success px-4">📄 Descargar Excel</a>
        <a href="../dashboard.php" class="btn btn-secondary ms-2">⬅ Volver al Dashboard</a>
      </div>
    <?php else: ?>
      <div class="alert alert-warning text-center">No se encontraron datos para el historial.</div>
    <?php endif; ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
