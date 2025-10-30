<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../model/MYSQL.php';

try {
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception('Debes iniciar sesión.');
    }

    $id_usuario = $_SESSION['usuario_id'];
    $id_libro = $_POST['id_libro'] ?? null;
    $dias = $_POST['dias'] ?? null; 

    if (!$id_libro) {
        throw new Exception('No se recibió el ID del libro.');
    }

    if (!$dias || !is_numeric($dias)) {
        throw new Exception('Debes seleccionar una cantidad de días válida.');
    }

    $db = new MYSQL();
    $db->conectar();
    $conn = $db->getConexion();

    $stmt = $conn->prepare("SELECT id FROM reserva WHERE id_usuario = ? AND id_libro = ? AND estado = 'pendiente'");
    $stmt->bind_param("ii", $id_usuario, $id_libro);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        throw new Exception('Ya tienes una reserva pendiente para este libro.');
    }
    $stmt->close();

    $fecha = date('Y-m-d');
    $estado = 'pendiente';

    $stmt = $conn->prepare("INSERT INTO reserva (id_usuario, id_libro, fecha_reserva, dias, estado) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisis", $id_usuario, $id_libro, $fecha, $dias, $estado);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Reserva registrada correctamente.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

