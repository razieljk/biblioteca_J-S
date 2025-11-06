<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../model/MYSQL.php';
$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$tipo = $_GET['tipo'] ?? 'disponibles';
$data = [];
$titulo = '';

if ($tipo === 'disponibles') {
    $sql = "SELECT id, titulo, autor, categoria, cantidad, descripcion, imagen
            FROM libro
            WHERE disponibilidad = 'disponible'";
    $titulo = "Libros Disponibles";
} elseif ($tipo === 'prestados') {
    $sql = "SELECT l.titulo, l.autor, u.nombre AS usuario, p.fecha_prestamo, p.fecha_devolucion, p.estado
            FROM prestamo p
            JOIN usuarios u ON p.id_usuario = u.id
            JOIN libro l ON p.id_libro = l.id
            ORDER BY p.fecha_prestamo DESC";
    $titulo = "Libros Prestados";
}

if (!empty($sql)) {
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
}
$db->desconectar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($titulo) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
.card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
.nav-tabs .nav-link.active { background-color: #0d6efd; color: white !important; }
</style>
</head>
<body class="p-4">

<div class="container">
    <div class="card p-4">

        <?php if (!empty($data)): ?>
            <h5 class="text-center text-success mb-3"><?= htmlspecialchars($titulo) ?></h5>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <?php foreach (array_keys($data[0]) as $col): ?>
                                <th><?= strtoupper(htmlspecialchars($col)) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <?php foreach ($row as $valor): ?>
                                    <td><?= htmlspecialchars($valor) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-3">
                <a href="export_inventario_pdf.php?tipo=<?= urlencode($tipo) ?>" class="btn btn-danger px-4">
                    📄 Exportar a PDF
                </a>
                <a href="../dashboard.php" class="btn btn-secondary ms-2">Volver al Dashboard</a>
            </div>

        <?php else: ?>
            <div class="alert alert-warning text-center mt-4">
                No se encontraron datos para este reporte.
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
