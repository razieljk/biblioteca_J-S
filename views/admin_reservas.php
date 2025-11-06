<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$tipoUsuario = strtolower($_SESSION['tipo'] ?? 'cliente');

require_once __DIR__ . '/../model/MYSQL.php';
$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

if ($tipoUsuario === 'administrador') {
    $sql = "
        SELECT r.id, r.fecha_reserva, r.estado, r.dias,
               u.nombre AS nombre_usuario, u.apellido AS apellido_usuario,
               l.titulo AS titulo_libro
        FROM reserva r
        INNER JOIN usuarios u ON r.id_usuario = u.id
        INNER JOIN libro l ON r.id_libro = l.id
        ORDER BY r.fecha_reserva DESC
    ";
} else {
    $usuarioId = $_SESSION['usuario_id'];
    $sql = "
        SELECT r.id, r.fecha_reserva, r.estado, r.dias,
               u.nombre AS nombre_usuario, u.apellido AS apellido_usuario,
               l.titulo AS titulo_libro
        FROM reserva r
        INNER JOIN usuarios u ON r.id_usuario = u.id
        INNER JOIN libro l ON r.id_libro = l.id
        WHERE r.id_usuario = $usuarioId
        ORDER BY r.fecha_reserva DESC
    ";
}

$reservas = $conn->query($sql);
$db->desconectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Administrar Reservas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body { background: #f5f7fb; }
.table-container { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
.badge-pendiente { background-color: #ffc107; color: #000; }
.badge-aprobada { background-color: #28a745; }
.badge-rechazada { background-color: #dc3545; }
.badge-convertida { background-color: #6c757d; }
</style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>administrar reservas</h4>
        <a href="../dashboard.php" class="btn btn-outline-primary">← Volver al Dashboard</a>
    </div>

    <div class="table-container">
        <table class="table table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Libro</th>
                    <th>Fecha</th>
                    <th>Días</th>
                    <th>Estado</th>
                    <?php if ($tipoUsuario === 'administrador'): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($reservas && $reservas->num_rows > 0): ?>
                    <?php while ($r = $reservas->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['titulo_libro']) ?></td>
                            <td><?= htmlspecialchars($r['fecha_reserva']) ?></td>
                            <td><?= htmlspecialchars($r['dias'] ?? '-') ?></td>
                            <td>
                                <?php
                                    $estado = $r['estado'];
                                    $badge = match($estado) {
                                        'pendiente' => 'badge-pendiente',
                                        'aprobada' => 'badge-aprobada',
                                        'rechazada' => 'badge-rechazada',
                                        'convertida' => 'badge-convertida',
                                        default => 'bg-secondary'
                                    };
                             
                                    $texto_estado = $estado === 'convertida' 
                                        ? 'convertida a prestamo' 
                                        : ucfirst($estado);
                                ?>
                                <span class="badge <?= $badge ?>"><?= htmlspecialchars($texto_estado) ?></span>
                            </td>

                            <?php if ($tipoUsuario === 'administrador'): ?>
                                <td>
                                    <?php if ($estado === 'pendiente'): ?>
                                        <button class="btn btn-success btn-sm me-1" onclick="actualizarEstado(<?= $r['id'] ?>, 'aprobada')">Aprobar</button>
                                        <button class="btn btn-danger btn-sm" onclick="actualizarEstado(<?= $r['id'] ?>, 'rechazada')">Rechazar</button>
                                    <?php else: ?>
                                        <em class="text-muted">Sin acciones</em>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $tipoUsuario === 'administrador' ? 5 : 4 ?>" class="text-center text-muted py-4">No hay reservas registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($tipoUsuario === 'administrador'): ?>
<script>
function actualizarEstado(idReserva, nuevoEstado) {
    Swal.fire({
        title: '¿Confirmar acción?',
        text: '¿marcar la reserva como "' + nuevoEstado + '"?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'confirmar',
        cancelButtonText: 'cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../controller/procesar_reserva_admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_reserva=' + idReserva + '&nuevo_estado=' + nuevoEstado
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    title: data.success ? 'exito' : 'Error',
                    text: data.message,
                    icon: data.success ? 'success' : 'error'
                }).then(() => location.reload());
            })
            .catch(err => {
                Swal.fire('Error', 'problema en el servidor.', 'error');
                console.error(err);
            });
        }
    });
}
</script>
<?php endif; ?>

</body>
</html>
