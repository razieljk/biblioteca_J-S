<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../model/MYSQL.php';

try {
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception('iniciar sesión para reservar un libro');
    }

    $id_usuario = $_SESSION['usuario_id'];
    $id_libro = $_POST['id_libro'] ?? null;
    $dias = $_POST['dias'] ?? null;

    if (!$id_libro || !$dias) {
        throw new Exception('Faltan datos para procesar la reserva.');
    }

    $db = new MYSQL();
    $db->conectar();
    $conn = $db->getConexion();

    $stmt = $conn->prepare("SELECT cantidad FROM libro WHERE id = ?");
    $stmt->bind_param("i", $id_libro);
    $stmt->execute();
    $res_libro = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$res_libro) {
        throw new Exception('El libro no existe');
    }

    if ($res_libro['cantidad'] <= 0) {
        throw new Exception('no disponible para reservar');
    }

    
    $stmt = $conn->prepare("
        SELECT id 
        FROM reserva 
        WHERE id_usuario = ? 
        AND id_libro = ? 
        AND estado IN ('pendiente', 'aprobada')
    ");
    $stmt->bind_param("ii", $id_usuario, $id_libro);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        throw new Exception('ya cuanta con una reserva pendiente o activa');
    }
    $stmt->close();

    $fecha_reserva = date('Y-m-d');
    $estado = 'pendiente';

    $stmt = $conn->prepare("
        INSERT INTO reserva (id_usuario, id_libro, fecha_reserva, dias, estado)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iisis", $id_usuario, $id_libro, $fecha_reserva, $dias, $estado);
    $stmt->execute();
    $stmt->close();

    $db->desconectar();

    echo json_encode([
        'success' => true,
        'message' => 'reserva realizada. aprobacion pendiente'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => '⚠️ ' . $e->getMessage()
    ]);
}
?>
