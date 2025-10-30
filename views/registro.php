<?php
require_once __DIR__ . '/../model/MYSQL.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    if ($nombre === '' || $apellido === '' || $email === '' || $contrasena === '') {
        $mensaje = 'Por favor completa todos los campos.';
    } else {
        $db = new MYSQL();
        $db->conectar();
        $conn = $db->getConexion();

        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        if (!$stmt) {
            die("Error en la consulta: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = 'El correo ya está registrado.';
        } else {
            $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);
            $tipo = 'cliente';
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, contrasena, tipo) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("Error en la inserción: " . $conn->error);
            }
            $stmt->bind_param("sssss", $nombre, $apellido, $email, $contrasenaHash, $tipo);

            if ($stmt->execute()) {
                header("Location: login.php?registro=exitoso");
                exit;
            } else {
                $mensaje = 'Error al registrar el usuario.';
            }
        }

        $stmt->close();
        $db->desconectar();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - Biblioteca Virtual</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/registro.css" rel="stylesheet">
</head>
<body>

  <div class="registro-card">
    <h2>Registro de usuario</h2>

    <?php if ($mensaje): ?>
      <div class="alert alert-warning"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="post">
      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" placeholder="Ingresa tu nombre" required>

      <label for="apellido">Apellido:</label>
      <input type="text" id="apellido" name="apellido" placeholder="Ingresa tu apellido" required>

      <label for="email">Correo electrónico:</label>
      <input type="email" id="email" name="email" placeholder="Ejemplo@gmail.com" required>

      <label for="contrasena">Contraseña:</label>
      <input type="password" id="contrasena" name="contrasena" placeholder="Ingresa tu contraseña" required>

      <button type="submit">Registrar</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="./login.php">Inicia sesión</a></p>
  </div>

</body>
</html>
