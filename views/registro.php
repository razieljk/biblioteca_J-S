<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Sesión</title>
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
    rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

  <div class="card shadow-lg p-4" style="width: 380px; border-radius: 1rem;">
    <h3 class="text-center mb-4 text-primary">Registro de Sesión</h3>

    <form action="../controller/registro_p.php" method="post">
      <div class="mb-3">
        <label for="username" class="form-label">Nombre de usuario</label>
        <input 
          type="text" 
          class="form-control" 
          id="nombre" 
          name="nombre" 
          placeholder="Ingresa tu nombre de usuario" 
          required>
      </div>

       <div class="mb-3">
        <label for="username" class="form-label">Apellido de usuario</label>
        <input 
          type="text" 
          class="form-control" 
          id="apellido" 
          name="apellido" 
          placeholder="Ingresa tu apellido de usuario" 
          required>
      </div>

       <div class="mb-3">
        <label for="username" class="form-label">Email de usuario</label>
        <input 
          type="email" 
          class="form-control" 
          id="email" 
          name="email" 
          placeholder="Ingresa tu email de usuario" 
          pattern="[a-z0-9._%+-]+@gmail\.com$"
          required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input 
          type="password" 
          class="form-control" 
          id="contrasena" 
          name="contrasena" 
          placeholder="Ingresa tu contraseña" 
          required>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2">
        Registrar
      </button>
    </form>

    <div class="text-center mt-3">
      <p class="mb-0">¿Ya tienes una cuenta?</p>
      <a href="./login.php" class="fw-bold text-decoration-none text-primary">
        Inicia sesión aquí
      </a>
    </div>
  </div>

  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
  </script>
</body>
</html>
