<?php
$mensaje = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 1:
            $mensaje = 'Por favor completa todos los campos.';
            break;
        case 2:
            $mensaje = 'El correo ya está registrado.';
            break;
        case 3:
            $mensaje = 'No se permiten caracteres especiales en el nombre o apellido.';
            break;
    }
}

if (isset($_GET['registro']) && $_GET['registro'] == 1) {
    $mensaje = '✅ Usuario registrado correctamente.';
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
      <div class="alert alert-warning text-center">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="../controller/registro_p.php">
      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" placeholder="Ingresa tu nombre" required>

      <label for="apellido">Apellido:</label>
      <input type="text" id="apellido" name="apellido" placeholder="Ingresa tu apellido" required>

      <label for="email">Correo electrónico:</label>
      <input type="email" id="email" name="email" placeholder="Ejemplo@gmail.com" required>

      <label for="password">Contraseña:</label>
      <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>

      <button type="submit">Registrar</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="./login.php">Inicia sesión</a></p>
  </div>

</body>
</html>
