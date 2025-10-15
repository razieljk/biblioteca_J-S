<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: ../dashboard.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow p-4" style="width: 350px;">
        <h3 class="text-center mb-3">Inicio de Sesión</h3>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center">
                <?php
                    if ($_GET['error'] == 1) {
                        echo "Correo o contraseña incorrecta.";
                    }
                ?>
            </div>
        <?php endif; ?>

        <form method="post" action="../controller/login_p.php">
            <div class="mb-3 text-start">
                <label for="email" class="form-label">Correo electrónico</label>
                <input 
                    type="email" 
                    class="form-control" 
                    id="email" 
                    name="email" 
                    placeholder="Ingrese su correo" 
                    required
                >
            </div>

            <div class="mb-3 text-start">
                <label for="password" class="form-label">Contraseña</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    placeholder="Ingrese su contraseña" 
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                Iniciar sesión
            </button>

            <div class="text-center">
                <p class="mb-0">¿No tienes una cuenta?</p>
                <a href="registro.php" class="text-decoration-none fw-bold text-primary">
                    Regístrate aquí
                </a>
            </div>
        </form>
    </div>

    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
</body>
</html>
