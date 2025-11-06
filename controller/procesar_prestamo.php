<?php
session_start();
require_once __DIR__ . '/../model/MYSQL.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'iniciar sesion']);
    exit;
}

$idUsuario = $_SESSION['usuario_id'];
$idLibro = $_POST['id_libro'] ?? '';
$dias = $_POST['dias'] ?? null; 

if (!$idLibro) {
    echo json_encode(['success' => false, 'message' => 'libro no valido']);
    exit;
}

if (!$dias || !is_numeric($dias)) {
    echo json_encode(['success' => false, 'message' => 'selecionar dias validos']);
    exit;
}

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$sqlActivos = "SELECT COUNT(*) AS total 
               FROM prestamo 
               WHERE id_usuario = ? AND estado IN ('pendiente', 'activo')";
$stmt = $conn->prepare($sqlActivos);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($result['total'] >= 2) {
    echo json_encode(['success' => false, 'message' => 'ya cuentas con 2 prestamos activos']);
    $db->desconectar();
    exit;
}

$sqlPendiente = "SELECT id 
                 FROM prestamo 
                 WHERE id_usuario = ? AND id_libro = ? AND estado = 'pendiente'";
$stmt = $conn->prepare($sqlPendiente);
$stmt->bind_param("ii", $idUsuario, $idLibro);
$stmt->execute();
$yaPendiente = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($yaPendiente) {
    echo json_encode(['success' => false, 'message' => 'ya cuentas con una solicitud pendiente']);
    $db->desconectar();
    exit;
}

$sqlInsert = "INSERT INTO prestamo (id_usuario, id_libro, fecha_solicitud, dias, estado) 
              VALUES (?, ?, NOW(), ?, 'pendiente')";
$stmt = $conn->prepare($sqlInsert);
$stmt->bind_param("iii", $idUsuario, $idLibro, $dias);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'solicitud de prestamo enviada']);
} else {
    echo json_encode(['success' => false, 'message' => 'error al registrar la solicitud']);
}

$stmt->close();
$db->desconectar();
?>
