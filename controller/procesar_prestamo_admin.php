<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../model/MYSQL.php';
require __DIR__ . '/../vendor/autoload.php'; // Importante: PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido.');
    }

    if (!isset($_SESSION['usuario_id']) || strtolower($_SESSION['tipo'] ?? '') !== 'administrador') {
        throw new Exception('Permisos insuficientes.');
    }

    $id_prestamo = intval($_POST['id_prestamo'] ?? 0);
    $accion = $_POST['accion'] ?? '';

    if ($id_prestamo <= 0 || $accion === '') {
        throw new Exception('Datos inválidos.');
    }

    $db = new MYSQL();
    $db->conectar();
    $conn = $db->getConexion();

    // Obtener información del préstamo y usuario
    $stmt = $conn->prepare("
        SELECT p.id_libro, p.estado, u.email, u.nombre, l.titulo
        FROM prestamo p
        INNER JOIN usuarios u ON p.id_usuario = u.id
        INNER JOIN libro l ON p.id_libro = l.id
        WHERE p.id = ?
    ");
    $stmt->bind_param("i", $id_prestamo);
    $stmt->execute();
    $prestamo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$prestamo) {
        throw new Exception('No se encontró el préstamo.');
    }

    $id_libro = $prestamo['id_libro'];
    $correoUsuario = $prestamo['email'];
    $nombreUsuario = $prestamo['nombre'];
    $tituloLibro = $prestamo['titulo'];
    $mensaje = '';
    $asuntoCorreo = '';
    $contenidoCorreo = '';

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

            $mensaje = 'Prestamo aprobado';
            $asuntoCorreo = " Tu prestamo fue aprobado - Biblioteca YJ";
            $contenidoCorreo = "
                <h2 style='color:#2c3e50;'>¡Hola, $nombreUsuario!</h2>
                <p>Tu solicitud de préstamo del libro <b>\"$tituloLibro\"</b> ha sido <b>aprobada ✅</b>.</p>
                <p>Puedes pasar por la biblioteca a recogerlo. Recuerda devolverlo en un plazo de 7 días.</p>
                <hr>
                <p style='font-size:12px;color:#777;'>Biblioteca YJ - Sistema de gestión de préstamos.</p>
            ";
            break;

        case 'rechazar':
            $stmt = $conn->prepare("UPDATE prestamo SET estado = 'rechazado' WHERE id = ?");
            $stmt->bind_param("i", $id_prestamo);
            $stmt->execute();
            $stmt->close();

            $mensaje = 'Prestamo rechazado';
            $asuntoCorreo = " Tu solicitud de prestamo fue rechazada - Biblioteca YJ";
            $contenidoCorreo = "
                <h2 style='color:#c0392b;'>Hola, $nombreUsuario</h2>
                <p>Lamentamos informarte que tu solicitud de préstamo para el libro <b>\"$tituloLibro\"</b> ha sido <b>rechazada</b>.</p>
                <p>Por favor, contacta con la biblioteca para más información.</p>
                <hr>
                <p style='font-size:12px;color:#777;'>Biblioteca YJ - Sistema de gestión de préstamos.</p>
            ";
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
            $asuntoCorreo = " Devolucion registrada - Biblioteca YJ";
            $contenidoCorreo = "
                <h2>Gracias, $nombreUsuario</h2>
                <p>Hemos registrado la devolución del libro <b>\"$tituloLibro\"</b>. ¡Esperamos verte pronto de nuevo! 😊</p>
                <hr>
                <p style='font-size:12px;color:#777;'>Biblioteca YJ - Sistema de gestión de préstamos.</p>
            ";
            break;

        default:
            throw new Exception('Acción inválida.');
    }

    // Enviar correo con PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bibliotecayj@gmail.com';
        $mail->Password = 'cbghdrjejfnkktao'; // Contraseña de aplicación
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('bibliotecayj@gmail.com', 'Biblioteca YJ');
        $mail->addAddress($correoUsuario, $nombreUsuario);
        $mail->isHTML(true);
        $mail->Subject = $asuntoCorreo;
        $mail->Body = $contenidoCorreo;

        $mail->send();
        $mensaje .= ' y correo enviado al usuario.';
    } catch (Exception $e) {
        $mensaje .= " (Estado actualizado, pero error al enviar correo: {$mail->ErrorInfo})";
    }

    $db->desconectar();

    echo json_encode(['success' => true, 'message' => $mensaje]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '⚠️ ' . $e->getMessage()]);
}
?>
