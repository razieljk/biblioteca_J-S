<?php
session_start();
if (isset($_SESSION['empleado_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - ServiPlus</title>
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >
    <style>
        body {
            background-color: #008000;
        }
        .login-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            margin-bottom: 30px;
            font-size: 1.8rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-container text-center">
        <h1>Login Empleado</h1>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php
                    if ($_GET['error'] == 1) {
                        echo "Documento o contraseña incorrecta.";
                    } elseif ($_GET['error'] == 2) {
                        echo "Empleado inactivo, no puede ingresar.";
                    }
                ?>
            </div>
        <?php endif; ?>

        <form method="post" action="../controlador/login_p.php">
            <div class="mb-3 text-start">
                <label for="documento" class="form-label">Documento</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="documento" 
                    name="documento" 
                    placeholder="Ingrese su documento" 
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
            <button type="submit" class="btn btn-primary w-100">
                Iniciar sesion
            </button>
        </form>
    </div>

    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
</body>
</html>
