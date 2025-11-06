<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../model/MYSQL.php';
require __DIR__ . '/../vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $tipoUsuario = strtolower($_SESSION['tipo'] ?? '');

    if ($tipoUsuario !== 'admin' && $tipoUsuario !== 'administrador') {
        throw new Exception('Sin permisos suficientes');
    }

    $id_reserva = $_POST['id_reserva'] ?? null;
    $nuevo_estado = $_POST['nuevo_estado'] ?? null;

    if (!$id_reserva || !$nuevo_estado) {
        throw new Exception('Faltan datos');
    }

    $db = new MYSQL();
    $db->conectar();
    $conn = $db->getConexion();

    // Obtener información de la reserva, usuario y libro
    $stmt = $conn->prepare("
        SELECT r.id, r.id_libro, r.id_usuario, r.estado, r.dias, 
               u.email, u.nombre, l.titulo
        FROM reserva r
        INNER JOIN usuarios u ON r.id_usuario = u.id
        INNER JOIN libro l ON r.id_libro = l.id
        WHERE r.id = ?
    ");
    $stmt->bind_param("i", $id_reserva);
    $stmt->execute();
    $res = $stmt->get_result();
    $reserva = $res->fetch_assoc();
    $stmt->close();

    if (!$reserva) {
        throw new Exception('No se encontró la reserva');
    }

    if ($reserva['estado'] !== 'pendiente') {
        throw new Exception('Esta reserva ya fue procesada');
    }

    $correoUsuario = $reserva['email'];
    $nombreUsuario = $reserva['nombre'];
    $tituloLibro = $reserva['titulo'];
    $mensaje = '';
    $asuntoCorreo = '';
    $contenidoCorreo = '';

    if ($nuevo_estado === 'aprobada') {
        $id_libro = $reserva['id_libro'];
        $id_usuario = $reserva['id_usuario'];
        $dias = $reserva['dias'] ?? 7;

        // Verificar disponibilidad
        $stmt = $conn->prepare("SELECT cantidad FROM libro WHERE id = ?");
        $stmt->bind_param("i", $id_libro);
        $stmt->execute();
        $res_libro = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$res_libro || $res_libro['cantidad'] <= 0) {
            throw new Exception('No disponible para reservar');
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

        // Descontar libro
        $stmt = $conn->prepare("UPDATE libro SET cantidad = cantidad - 1 WHERE id = ?");
        $stmt->bind_param("i", $id_libro);
        $stmt->execute();
        $stmt->close();

        $estado_final = 'convertida';
        $mensaje = 'Reserva aprobada y convertida a préstamo.';
        $asuntoCorreo = " Tu reserva fue aprobada - Biblioteca YJ";
        $contenidoCorreo = "
            <h2 style='color:#2c3e50;'>¡Hola, $nombreUsuario!</h2>
            <p>Tu reserva del libro <b>\"$tituloLibro\"</b> ha sido <b>aprobada ✅</b> y convertida en préstamo.</p>
            <p>Podrás retirarlo en la biblioteca. Recuerda devolverlo en un plazo de <b>$dias días</b>.</p>
            <hr>
            <p style='font-size:12px;color:#777;'>Biblioteca YJ - Sistema de reservas y préstamos.</p>
        ";
    } else {
        $estado_final = 'rechazada';
        $mensaje = 'Reserva rechazada.';
        $asuntoCorreo = " Tu reserva fue rechazada - Biblioteca YJ";
        $contenidoCorreo = "
            <h2 style='color:#c0392b;'>Hola, $nombreUsuario</h2>
            <p>Lamentamos informarte que tu reserva para el libro <b>\"$tituloLibro\"</b> ha sido <b>rechazada</b>.</p>
            <p>Puedes comunicarte con la biblioteca si deseas más información o intentar nuevamente.</p>
            <hr>
            <p style='font-size:12px;color:#777;'>Biblioteca YJ - Sistema de reservas y préstamos.</p>
        ";
    }

    // Actualizar estado de la reserva
    $stmt = $conn->prepare("UPDATE reserva SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $estado_final, $id_reserva);
    $stmt->execute();
    $stmt->close();

    // Enviar correo con PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bibliotecayj@gmail.com';
        $mail->Password = 'cbghdrjejfnkktao'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('bibliotecayj@gmail.com', 'Biblioteca YJ');
        $mail->addAddress($correoUsuario, $nombreUsuario);
        $mail->isHTML(true);
        $mail->Subject = $asuntoCorreo;
        $mail->Body = $contenidoCorreo;

        $mail->send();
        $mensaje .= ' Se notificó al usuario por correo.';
    } catch (Exception $e) {
        $mensaje .= " (Estado actualizado, pero error al enviar correo: {$mail->ErrorInfo})";
    }

    $db->desconectar();

    echo json_encode([
        'success' => true,
        'message' => $mensaje
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => '⚠️ ' . $e->getMessage()
    ]);
}
?>
