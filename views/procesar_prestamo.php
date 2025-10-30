<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../model/MYSQL.php';

try {
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception('No has iniciado sesión.');
    }

    $id_usuario = $_SESSION['usuario_id'];
    $id_libro = $_POST['id_libro'] ?? null;
    $dias = (int)($_POST['dias'] ?? 0);

    if (!$id_libro || !$dias) {
        throw new Exception('⚠️ Faltan datos (libro o días).');
    }

    $db = new MYSQL();
    $db->conectar();
    $conn = $db->getConexion();

    // ✅ Verificar disponibilidad
    $stmt = $conn->prepare("SELECT cantidad FROM libro WHERE id = ?");
    $stmt->bind_param("i", $id_libro);
    $stmt->execute();
    $res = $stmt->get_result();
    $libro = $res->fetch_assoc();
    $stmt->close();

    if (!$libro || $libro['cantidad'] <= 0) {
        throw new Exception("El libro no está disponible.");
    }

    // ✅ Calcular fechas
    date_default_timezone_set('America/Bogota');
    $fecha_prestamo = date('Y-m-d');
    $fecha_devolucion = date('Y-m-d', strtotime("+$dias days"));
    $estado = 'pendiente'; // Estado inicial del préstamo

    // ✅ Insertar préstamo
    $stmt = $conn->prepare("
        INSERT INTO prestamo (id_usuario, id_libro, fecha_prestamo, fecha_devolucion, estado, dias)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        throw new Exception("Error preparando INSERT: " . $conn->error);
    }
    $stmt->bind_param("iisssi", $id_usuario, $id_libro, $fecha_prestamo, $fecha_devolucion, $estado, $dias);
    $stmt->execute();
    $stmt->close();

    // ✅ Actualizar cantidad en inventario
    $stmt = $conn->prepare("UPDATE libro SET cantidad = cantidad - 1 WHERE id = ?");
    $stmt->bind_param("i", $id_libro);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => "✅ Préstamo registrado correctamente.<br>📅 Fecha préstamo: <b>$fecha_prestamo</b><br>📆 Fecha devolución: <b>$fecha_devolucion</b>"
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '⚠️ ' . $e->getMessage()]);
}
?>
