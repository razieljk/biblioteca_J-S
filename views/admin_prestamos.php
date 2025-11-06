<?php
session_start();
require_once __DIR__ . '/../model/MYSQL.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$tipoUsuario = strtolower($_SESSION['tipo'] ?? 'cliente');
$idUsuario = $_SESSION['usuario_id'];

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

if ($tipoUsuario === 'administrador') {
    $query = "
        SELECT 
            p.id AS id_prestamo,
            u.nombre AS usuario,
            l.titulo AS libro,
            p.fecha_solicitud,
            p.dias,
            p.estado
        FROM prestamo p
        INNER JOIN usuarios u ON p.id_usuario = u.id
        INNER JOIN libro l ON p.id_libro = l.id
        ORDER BY p.fecha_solicitud DESC
    ";
} else {
    $query = "
        SELECT 
            p.id AS id_prestamo,
            u.nombre AS usuario,
            l.titulo AS libro,
            p.fecha_solicitud,
            p.dias,
            p.estado
        FROM prestamo p
        INNER JOIN usuarios u ON p.id_usuario = u.id
        INNER JOIN libro l ON p.id_libro = l.id
        WHERE p.id_usuario = $idUsuario
        ORDER BY p.fecha_solicitud DESC
    ";
}

$result = $conn->query($query);
$db->desconectar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Préstamos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #f5f7fb; }
        table { border-radius: 8px; overflow: hidden; }
        .badge { font-size: 0.9em; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">administración de préstamos</h3>
        <a href="../dashboard.php" class="btn btn-outline-secondary">
            ← Volver al Dashboard
        </a>
    </div>

    <div class="table-responsive shadow">
        <table class="table table-striped table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Libro</th>
                    <th>Fecha Solicitud</th>
                    <th>Días</th>
                    <th>Estado</th>
                    <?php if ($tipoUsuario === 'administrador'): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($fila = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['libro']) ?></td>
                            <td><?= htmlspecialchars($fila['fecha_solicitud']) ?></td>
                            <td><?= htmlspecialchars($fila['dias'] ?? '-') ?></td>
                            <td>
                                <?php
                                $estado = $fila['estado'];
                                $badge = [
                                    'pendiente' => 'warning text-dark',
                                    'activo' => 'primary',
                                    'devuelto' => 'success',
                                    'rechazado' => 'danger'
                                ][$estado] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst($estado) ?></span>
                            </td>

                            <?php if ($tipoUsuario === 'administrador'): ?>
                                <td>
                                    <?php if ($estado === 'pendiente'): ?>
                                        <button class="btn btn-sm btn-success" onclick="cambiarEstado(<?= $fila['id_prestamo'] ?>, 'aceptar')">
                                            aceptar
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="cambiarEstado(<?= $fila['id_prestamo'] ?>, 'rechazar')">
                                            rechazar
                                        </button>
                                    <?php elseif ($estado === 'activo'): ?>
                                        <button class="btn btn-sm btn-primary" onclick="cambiarEstado(<?= $fila['id_prestamo'] ?>, 'devolver')">
                                            marcar devuelto
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">completado</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $tipoUsuario === 'administrador' ? 6 : 5 ?>" class="text-muted py-4">
                            sin prestamos registrados
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($tipoUsuario === 'administrador'): ?>
<script>
function cambiarEstado(id, accion) {
    const mensajes = {
        aceptar: {titulo: "aceptar prestamo", texto: "el libro será prestado al cliente"},
        rechazar: {titulo: "rechazar prestamo", texto: "el prestamo sera cancelado"},
        devolver: {titulo: "marcar como devuelto", texto: "el libro regresa al inventario"}
    };

    Swal.fire({
        title: mensajes[accion].titulo,
        text: mensajes[accion].texto,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'confirmar',
        cancelButtonText: 'cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('../controller/procesar_prestamo_admin.php', {
                method: 'POST',
                body: new URLSearchParams({ id_prestamo: id, accion })
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? 'exito' : 'Error',
                    text: data.message
                }).then(() => location.reload());
            })
            .catch(() => {
                Swal.fire('Error', 'problema con el servidor.', 'error');
            });
        }
    });
}
</script>
<?php endif; ?>

</body>
</html>
