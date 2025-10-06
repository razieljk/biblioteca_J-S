<?php
session_start();
require_once '../modelo/MYSQL.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/login.php");
    exit();
}

$documento = trim($_POST['documento'] ?? '');
$password  = $_POST['password'] ?? '';

if (empty($documento) || empty($password)) {
    header("Location: ../views/login.php?error=1");
    exit();
}

$mysql = new MYSQL();
$mysql->conectar();

$query = "
    SELECT e.*, c.nombre AS cargo_nombre 
    FROM empleados e
    LEFT JOIN cargos c ON e.cargo_id = c.id
    WHERE e.documento='$documento' 
    LIMIT 1
";

$resultado = $mysql->consulta($query);

if ($resultado && $empleado = $resultado->fetch_assoc()) {
    if ($empleado['estado'] !== 'Activo') {
        $mysql->desconectar();
        header("Location: ../views/login.php?error=2");
        exit();
    }

    if (password_verify($password, $empleado['password'])) {
        $_SESSION['empleado_id']   = $empleado['id'];
        $_SESSION['nombre']        = $empleado['nombre_completo'];
        $_SESSION['correo']        = $empleado['correo'];
        $_SESSION['cargo_id']      = $empleado['cargo_id'];
        $_SESSION['cargo_nombre']  = $empleado['cargo_nombre'];

        $mysql->desconectar();
        header("Location: ../views/index.php");
        exit();
    }
}

$mysql->desconectar();
header("Location: ../views/login.php?error=1");
exit();
