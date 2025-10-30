<?php
session_start();
require_once '../model/MYSQL.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/login.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: ../views/login.php?error=1");
    exit();
}

$mysql = new MYSQL();
$mysql->conectar();
$conn = $mysql->getConexion();

$sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $usuario = $resultado->fetch_assoc()) {
    if (password_verify($password, $usuario['contrasena'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre']     = $usuario['nombre'] . " " . $usuario['apellido'];
        $_SESSION['email']      = $usuario['email'];
        $_SESSION['tipo']       = $usuario['tipo'];

        $stmt->close();
        $mysql->desconectar();
        header("Location: ../dashboard.php");
        exit();
    } else {
        $stmt->close();
        $mysql->desconectar();
        header("Location: ../views/login.php?error=1");
        exit();
    }
} else {
    $stmt->close();
    $mysql->desconectar();
    header("Location: ../views/login.php?error=1");
    exit();
}
?>
