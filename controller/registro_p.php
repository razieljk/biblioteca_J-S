<?php
require_once __DIR__ . '/../model/MYSQL.php';

$mysql = new MYSQL();
$mysql->conectar();
$conn = $mysql->getConexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['contrasena']);

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    if (!$stmt) die("Error en SELECT: " . $conn->error);
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $mysql->desconectar();
        header("Location: ../views/registro.php?error=email_existe");
        exit();
    }
    $stmt->close();

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $tipo = 'cliente';

    $stmt = $conn->prepare("
        INSERT INTO usuarios (nombre, apellido, email, contrasena, tipo)
        VALUES (?, ?, ?, ?, ?)
    ");
    if (!$stmt) die("Error en INSERT: " . $conn->error);

    $stmt->bind_param("sssss", $nombre, $apellido, $email, $hash, $tipo);

    if ($stmt->execute()) {
        $stmt->close();
        $mysql->desconectar();
        header("Location: ../views/login.php?registro=ok");
        exit();
    } else {
        die("Error al insertar: " . $stmt->error);
    }
}
?>
