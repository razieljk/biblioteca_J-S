<?php
require_once __DIR__ . '/../model/MYSQL.php';
$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $tipo = trim($_POST['tipo']);

    $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: agregar_usuario.php?error=email");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, contrasena, tipo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $apellido, $email, $contrasena, $tipo);

    if ($stmt->execute()) {
        header("Location: usuarios.php?success=agregado");
        exit;
    } else {
        header("Location: agregar_usuario.php?error=insert");
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
  <title>Agregar Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { background-color: #f5f7fa; font-family: 'Inter', sans-serif; }
    .form-container { max-width: 500px; margin: 60px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .titulo { color: #1e6091; font-weight: 600; text-align: center; }
  </style>
</head>
<body>
<div class="form-container">
  <h3 class="titulo mb-4">Agregar usuario</h3>
  <form method="post" action="">
    <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre" required>
    <input type="text" name="apellido" class="form-control mb-2" placeholder="Apellido" required>
    <input type="email" name="email" class="form-control mb-2" placeholder="Correo" required>
    <input type="password" name="contrasena" class="form-control mb-2" placeholder="Contraseña" required>
    <select name="tipo" class="form-select mb-3" required>
      <option value="" disabled selected>Selecciona tipo</option>
      <option value="cliente">cliente</option>
      <option value="administrador">administrador</option>
    </select>
    <button type="submit" class="btn btn-primary w-100">Guardar</button>
    <a href="usuarios.php" class="btn btn-outline-secondary w-100 mt-2">Volver</a>
  </form>
</div>
<script src="../assets/js/usuarios.js"></script>
</body>
</html>
