<?php
require_once __DIR__ . '/../model/MYSQL.php';
$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $stmt->close();
        $db->desconectar();
        header("Location: usuarios.php?success=eliminado");
        exit;
    } else {
        $stmt->close();
        $db->desconectar();
        header("Location: usuarios.php?error=delete");
        exit;
    }
}

$db->desconectar();
header("Location: usuarios.php?error=delete");
exit;
?>
