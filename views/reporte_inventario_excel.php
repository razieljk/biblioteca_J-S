<?php
require_once '../model/MYSQL.php';

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$sqlLibros = "SELECT titulo, autor, categoria, cantidad, disponibilidad 
              FROM libro 
              ORDER BY titulo ASC";
$libros = $conn->query($sqlLibros);
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title> Reporte de Inventario</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f6f8fb; font-family: 'Segoe UI', Arial, sans-serif; }
.container { background: #fff; padding: 25px; margin-top: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
h2 { border-left: 5px solid #0d6efd; padding-left: 10px; }
table { border-collapse: collapse; width: 100%; margin-top: 15px; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
th { background: #007bff; color: white; }
tr:nth-child(even) { background-color: #f2f2f2; }
.btn-export { margin-top: 20px; display: flex; justify-content: center; gap: 10px; }
</style>
</head>
<body>
<div class="container">
  <h2 class="text-center mb-4"> Reporte de Inventario</h2>

  <table>
    <thead>
      <tr>
        <th>Título</th>
        <th>Autor</th>
        <th>Categoría</th>
        <th>Cantidad</th>
        <th>Disponibilidad</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($libros && $libros->num_rows > 0): ?>
        <?php while ($lib = $libros->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($lib['titulo']) ?></td>
          <td><?= htmlspecialchars($lib['autor']) ?></td>
          <td><?= htmlspecialchars($lib['categoria']) ?></td>
          <td><?= htmlspecialchars($lib['cantidad']) ?></td>
          <td><?= htmlspecialchars(ucfirst($lib['disponibilidad'])) ?></td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5" class="text-center text-muted">No hay libros registrados.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="btn-export">
    <form action="export_inventario.php" method="post" target="descarga_excel" style="display:inline;">
      <button type="submit" class="btn btn-success"> Exportar Inventario a Excel</button>
    </form>
    <a href="../dashboard.php" class="btn btn-secondary">⬅ Volver al Dashboard</a>
  </div>

  <iframe name="descarga_excel" style="display:none;"></iframe>
</div>
</body>
</html>
<?php
$db->desconectar();
?>
