<?php
session_start();
require_once '../model/MYSQL.php';

// Solo permitir método POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/registro.php");
    exit();
}

// Obtener datos del formulario
$nombre   = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$tipo     = 'usuario';

// Validar campos vacíos
if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
    header("Location: ../views/registro.php?error=1");
    exit();
}

// ✅ Validar que nombre y apellido no tengan caracteres especiales
if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $nombre) ||
    !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $apellido)) {
    header("Location: ../views/registro.php?error=3");
    exit();
}

// Conectar a la base de datos
$mysql = new MYSQL();
$mysql->conectar();
$conn = $mysql->getConexion();

// Verificar si el correo ya existe
$sql_check = "SELECT id FROM usuarios WHERE email = ? LIMIT 1";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check && $result_check->num_rows > 0) {
    $stmt_check->close();
    $mysql->desconectar();
    header("Location: ../views/registro.php?error=2"); // correo duplicado
    exit();
}

// Encriptar contraseña
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insertar usuario nuevo
$sql = "INSERT INTO usuarios (nombre, apellido, email, contrasena, tipo) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $nombre, $apellido, $email, $hashed_password, $tipo);
$stmt->execute();

// Cerrar conexión
$stmt->close();
$mysql->desconectar();

// Redirigir con mensaje de éxito
header("Location: ../views/registro.php?registro=1");
exit();
?>
