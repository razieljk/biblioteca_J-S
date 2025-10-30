<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../model/MYSQL.php';

try {
    $tipoUsuario = strtolower($_SESSION['tipo'] ?? '');

    if ($tipoUsuario !== 'admin' && $tipoUsuario !== 'administrador') {
        throw new Exception('No tienes permisos para realizar esta acción.');
    }

    $id_reserva = $_POST['id_reserva'] ?? null;
    $nuevo_estado = $_POST['nuevo_estado'] ?? null;

    if (!$id_reserva || !$nuevo_estado) {
        throw new Exception('Faltan datos necesarios.');
    }

    $db = new MYSQL();
    $db->conectar();
    $conn = $db->getConexion();

    $stmt = $conn->prepare("SELECT * FROM reserva WHERE id = ?");
    $stmt->bind_param("i", $id_reserva);
    $stmt->execute();
    $res = $stmt->get_result();
    $reserva = $res->fetch_assoc();
    $stmt->close();

    if (!$reserva) {
        throw new Exception('No se encontró la reserva.');
    }

    if ($reserva['estado'] !== 'pendiente') {
        throw new Exception('Esta reserva ya fue procesada.');
    }

    if ($nuevo_estado === 'aprobada') {
        $id_libro = $reserva['id_libro'];
        $id_usuario = $reserva['id_usuario'];
        $dias = $reserva['dias'] ?? 7;

        $stmt = $conn->prepare("SELECT cantidad FROM libro WHERE id = ?");
        $stmt->bind_param("i", $id_libro);
        $stmt->execute();
        $res_libro = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$res_libro || $res_libro['cantidad'] <= 0) {
            throw new Exception('No hay ejemplares disponibles para préstamo.');
        }

        $fecha_prestamo = date('Y-m-d');
        $fecha_devolucion = date('Y-m-d', strtotime("+$dias days"));

        $stmt = $conn->prepare("
            INSERT INTO prestamo (id_usuario, id_libro, id_reserva, fecha_prestamo, fecha_devolucion, estado)
            VALUES (?, ?, ?, ?, ?, 'activo')
        ");
        $stmt->bind_param("iiiss", $id_usuario, $id_libro, $id_reserva, $fecha_prestamo, $fecha_devolucion);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE libro SET cantidad = cantidad - 1 WHERE id = ?");
        $stmt->bind_param("i", $id_libro);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $conn->prepare("UPDATE reserva SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_estado, $id_reserva);
    $stmt->execute();
    $stmt->close();

    $db->desconectar();

    echo json_encode([
        'success' => true,
        'message' => "Reserva {$nuevo_estado} correctamente."
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => '⚠️ ' . $e->getMessage()
    ]);
}
?>
