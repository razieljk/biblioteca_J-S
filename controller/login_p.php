<?php
session_start();
require_once __DIR__ . '/../model/MYSQL.php';

$mysql = new MYSQL();
$mysql->conectar();
$conn = $mysql->getConexion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/login.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    header("Location: ../views/login.php?error=1");
    exit();
}

$stmt = $conn->prepare("SELECT id, nombre, contrasena, tipo FROM usuarios WHERE email = ? LIMIT 1");
if (!$stmt) {
    die("Error al preparar SELECT: " . $conn->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if (!($row = $res->fetch_assoc())) {
    $stmt->close();
    $mysql->desconectar();
    header("Location: ../views/login.php?error=1");
    exit();
}
$stmt->close();

$storedHash = $row['contrasena'];
$userId     = (int)$row['id'];
$userName   = $row['nombre'] ?? '';
$userTipo   = $row['tipo'] ?? 'cliente';

if (preg_match('/^[a-f0-9]{32}$/i', $storedHash)) {
    if (md5($password) === $storedHash) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $up = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
        if ($up) {
            $up->bind_param("si", $newHash, $userId);
            $up->execute();
            $up->close();
        }
        $_SESSION['usuario_id'] = $userId;
        $_SESSION['usuario_nombre'] = $userName;
        $_SESSION['usuario_tipo'] = $userTipo;
        $mysql->desconectar();
        header("Location: ../dashboard.php");
        exit();
    } else {
        $mysql->desconectar();
        header("Location: ../views/login.php?error=1");
        exit();
    }
}

if (password_verify($password, $storedHash)) {
    if (password_needs_rehash($storedHash, PASSWORD_DEFAULT)) {
        $rehash = password_hash($password, PASSWORD_DEFAULT);
        $up = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
        if ($up) {
            $up->bind_param("si", $rehash, $userId);
            $up->execute();
            $up->close();
        }
    }

    $_SESSION['usuario_id'] = $userId;
    $_SESSION['usuario_nombre'] = $userName;
    $_SESSION['usuario_tipo'] = $userTipo;
    $mysql->desconectar();
    header("Location: ../dashboard.php");
    exit();
} else {
    $mysql->desconectar();
    header("Location: ../views/login.php?error=1");
    exit();
}
