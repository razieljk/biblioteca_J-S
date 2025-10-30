<?php
session_start();
require_once '../model/MYSQL.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/registro.php");
    exit();
}

$nombre   = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$tipo     = 'usuario'; 

if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
    header("Location: ../views/registro.php?error=1");
    exit();
}

$mysql = new MYSQL();
$mysql->conectar();
$conn = $mysql->getConexion();

$sql_check = "SELECT id FROM usuarios WHERE email = ? LIMIT 1";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check && $result_check->num_rows > 0) {
    $stmt_check->close();
    $mysql->desconectar();
    header("Location: ../views/registro.php?error=2"); 
    exit();
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nombre, apellido, email, contrasena, tipo) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $nombre, $apellido, $email, $hashed_password, $tipo);
$stmt->execute();

$stmt->close();
$mysql->desconectar();

header("Location: ../views/login.php?registro=1");
exit();
?>
