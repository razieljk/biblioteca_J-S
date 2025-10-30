<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../model/MYSQL.php';
session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido.');
    }

    if (!isset($_SESSION['usuario_id']) || strtolower($_SESSION['tipo'] ?? '') !== 'administrador') {
        throw new Exception('permisos insuficientes');
    }

    $id_prestamo = intval($_POST['id_prestamo'] ?? 0);
    $accion = $_POST['accion'] ?? '';

    if ($id_prestamo <= 0 || $accion === '') {
        throw new Exception('datos invalidos');
    }

    $db = new MYSQL();
    $db->conectar();
    $conn = $db->getConexion();

    $stmt = $conn->prepare("SELECT id_libro, estado FROM prestamo WHERE id = ?");
    $stmt->bind_param("i", $id_prestamo);
    $stmt->execute();
    $prestamo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$prestamo) {
        throw new Exception('Préstamo no encontrado.');
    }

    $id_libro = $prestamo['id_libro'];
    $mensaje = '';

    switch ($accion) {
        case 'aceptar':
            $stmt = $conn->prepare("
                UPDATE prestamo 
                SET estado = 'activo',
                    fecha_prestamo = NOW(),
                    fecha_devolucion = DATE_ADD(NOW(), INTERVAL 7 DAY)
                WHERE id = ?
            ");
            $stmt->bind_param("i", $id_prestamo);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE libro SET cantidad = cantidad - 1 WHERE id = ?");
            $stmt->bind_param("i", $id_libro);
            $stmt->execute();
            $stmt->close();

            $mensaje = 'prestamo aprobado correctamente.';
            break;

        case 'rechazar':
 
            $stmt = $conn->prepare("UPDATE prestamo SET estado = 'rechazado' WHERE id = ?");
            $stmt->bind_param("i", $id_prestamo);
            $stmt->execute();
            $stmt->close();

            $mensaje = 'prestamo rechazado';
            break;

        case 'devolver':

            $stmt = $conn->prepare("UPDATE prestamo SET estado = 'devuelto' WHERE id = ?");
            $stmt->bind_param("i", $id_prestamo);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE libro SET cantidad = cantidad + 1 WHERE id = ?");
            $stmt->bind_param("i", $id_libro);
            $stmt->execute();
            $stmt->close();

            $mensaje = 'Libro devuelto';
            break;

        default:
            throw new Exception('accion invalida');
    }

    $db->desconectar();

    echo json_encode(['success' => true, 'message' => $mensaje]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '⚠️ ' . $e->getMessage()]);
}
?>
