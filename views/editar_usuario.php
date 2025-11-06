<?php
require_once __DIR__ . '/../model/MYSQL.php';
$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    header("Location: usuarios.php?error=noexist");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $tipo = trim($_POST['tipo']);

    $verificar = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $verificar->bind_param("si", $email, $id);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        header("Location: editar_usuario.php?id=$id&error=correo_existente");
        exit;
    }

    $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, apellido=?, email=?, tipo=? WHERE id=?");
    $stmt->bind_param("ssssi", $nombre, $apellido, $email, $tipo, $id);

    if ($stmt->execute()) {
        header("Location: usuarios.php?success=editado");
        exit;
    } else {
        header("Location: editar_usuario.php?id=$id&error=update");
        exit;
    }
}

$db->desconectar();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f5f7fa; font-family: 'Inter', sans-serif; }
    .form-container { max-width: 500px; margin: 60px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .titulo { color: #1e6091; font-weight: 600; text-align: center; }
  </style>
</head>
<body>
<div class="form-container">
  <h3 class="titulo mb-4">Editar usuario</h3>
  <form method="post" action="">
    <input type="text" name="nombre" class="form-control mb-2" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
    <input type="text" name="apellido" class="form-control mb-2" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
    <input type="email" name="email" class="form-control mb-2" value="<?= htmlspecialchars($usuario['email']) ?>" required>
    <select name="tipo" class="form-select mb-3" required>
      <option value="cliente" <?= $usuario['tipo'] === 'cliente' ? 'selected' : '' ?>>cliente</option>
      <option value="administrador" <?= $usuario['tipo'] === 'administrador' ? 'selected' : '' ?>>administrador</option>
    </select>
    <button type="submit" class="btn btn-primary w-100">Guardar cambios</button>
    <a href="usuarios.php" class="btn btn-outline-secondary w-100 mt-2">Volver</a>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  const pageError = "<?= $_GET['error'] ?? '' ?>";
</script>

<script src="../assets/js/usuarios.js"></script>
</body>
</html>



