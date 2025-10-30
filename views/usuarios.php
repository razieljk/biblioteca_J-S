<?php
require_once __DIR__ . '/../model/MYSQL.php';
$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);
$usuarios = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}
$db->desconectar();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { background-color: #f5f7fa; font-family: 'Inter', sans-serif; }
    .container { max-width: 900px; }
    .table thead { background-color: #184e77; color: #fff; }
    .btn-agregar { background-color: #1e6091; color: #fff; }
    .btn-agregar:hover { background-color: #1a4d73; }
    .btn-dashboard { background-color: #168aad; color: #fff; }
    .btn-dashboard:hover { background-color: #12708c; }
    .titulo { color: #1e6091; font-weight: 600; }
  </style>
</head>
<body>
<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="titulo">Usuarios registrados</h3>
    <div class="d-flex gap-2">
      <a href="agregar_usuario.php" class="btn btn-agregar">Agregar usuario</a>
      <a href="../dashboard.php" class="btn btn-dashboard">Volver al Dashboard</a>
    </div>
  </div>

  <?php if (empty($usuarios)): ?>
    <div class="alert alert-warning">No hay usuarios registrados.</div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="table table-bordered align-middle text-center bg-white shadow-sm">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Apellido</th>
          <th>Email</th>
          <th>Tipo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['id']) ?></td>
          <td><?= htmlspecialchars($u['nombre']) ?></td>
          <td><?= htmlspecialchars($u['apellido']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['tipo']) ?></td>
          <td>
            <a href="editar_usuario.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
            <button class="btn btn-sm btn-outline-danger eliminar-btn" data-id="<?= $u['id'] ?>">Eliminar</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/usuarios.js"></script>
</body>
</html>
