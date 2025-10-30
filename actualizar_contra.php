<?php
require_once __DIR__ . '/model/MYSQL.php';

$email = 'juan@gmail.com';   
$plain = '12345';             

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$hash = password_hash($plain, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE email = ?");
$stmt->bind_param("ss", $hash, $email);
if ($stmt->execute()) {
    echo "OK: contraseña actualizada para $email\n";
    echo "Hash guardado: $hash\n";
} else {
    echo "ERROR: " . $stmt->error . "\n";
}
$stmt->close();
$db->desconectar();
