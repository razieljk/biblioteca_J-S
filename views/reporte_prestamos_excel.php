<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once '../model/MYSQL.php';
$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$sql = "SELECT 
            u.nombre AS usuario,
            l.titulo AS libro,
            p.fecha_prestamo,
            p.fecha_devolucion,
            p.dias,
            p.estado
        FROM prestamo p
        JOIN usuarios u ON p.id_usuario = u.id
        JOIN libro l ON p.id_libro = l.id
        ORDER BY p.fecha_prestamo DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Préstamos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="container">

    <?php if ($result && $result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Usuario</th>
                    <th>Libro</th>
                    <th>Fecha Préstamo</th>
                    <th>Fecha Devolución</th>
                    <th>Días</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['usuario']) ?></td>
                    <td><?= htmlspecialchars($row['libro']) ?></td>
                    <td><?= htmlspecialchars($row['fecha_prestamo']) ?></td>
                    <td><?= htmlspecialchars($row['fecha_devolucion']) ?></td>
                    <td><?= htmlspecialchars($row['dias']) ?></td>
                    <td><?= htmlspecialchars($row['estado']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3 text-center">
        <a href="export_prestamos_excel.php" class="btn btn-success px-4">📥 Exportar a Excel</a>
        <a href="../dashboard.php" class="btn btn-secondary ms-2">⬅ Volver al Dashboard</a>
    </div>

    <?php else: ?>
        <div class="alert alert-warning text-center">No hay préstamos registrados.</div>
    <?php endif; ?>
</div>

</body>
</html>

<?php $db->desconectar(); ?>
