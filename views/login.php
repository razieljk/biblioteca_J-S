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
    <title>Inicio de Sesión - Biblioteca Virtual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to bottom right, #f5deb3, #fff8e7);
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url('https://cdn.pixabay.com/photo/2016/03/09/09/17/books-1245690_1280.jpg') center/cover no-repeat;
            opacity: 0.25;
            filter: blur(5px);
            z-index: -1;
        }

        .login-card {
            width: 360px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(0,0,0,0.3);
        }

        .login-icon {
            font-size: 3rem;
            color: #8B4513;
            margin-bottom: 10px;
        }

        h3 {
            font-weight: 600;
            color: #5a3e1b;
        }

        .btn-primary {
            background-color: #8B4513;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #a0522d;
        }

        .form-control:focus {
            border-color: #8B4513;
            box-shadow: 0 0 5px rgba(139,69,19,0.5);
        }

        a.text-success {
            color: #8B4513 !important;
        }

        a.text-success:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card text-center">
        <h3 class="mb-3">Biblioteca</h3>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center py-2">
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
        </form>

        <div class="text-center mt-3">
            <p class="mb-1">¿No tienes cuenta? <a href="registro.php" class="fw-bold text-success text-decoration-none">
                Registrarse
            </a></p>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
