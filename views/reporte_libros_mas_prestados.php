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

$sql_historial = "SELECT 
            u.nombre AS usuario,
            l.titulo AS libro,
            p.fecha_prestamo,
            p.fecha_devolucion,
            p.estado,
            p.dias
        FROM prestamo p
        JOIN usuarios u ON p.id_usuario = u.id
        JOIN libro l ON p.id_libro = l.id
        ORDER BY p.fecha_prestamo DESC";
$result_historial = $conn->query($sql_historial);
$historial = [];
if($result_historial && $result_historial->num_rows > 0){
    while($row = $result_historial->fetch_assoc()){
        $historial[] = $row;
    }
}

$sql_libros = "SELECT 
            l.titulo AS libro,
            l.autor,
            COUNT(p.id) AS total_prestamos
        FROM prestamo p
        JOIN libro l ON p.id_libro = l.id
        WHERE p.estado IN ('activo','devuelto')
        GROUP BY l.id
        ORDER BY total_prestamos DESC";
$result_libros = $conn->query($sql_libros);
$libros = [];
if($result_libros && $result_libros->num_rows > 0){
    while($row = $result_libros->fetch_assoc()){
        $libros[] = $row;
    }
}

$db->desconectar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Préstamos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f8f9fa;font-family:Inter,sans-serif;}
.card{border-radius:15px;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
.nav-tabs .nav-link.active{background-color:#0d6efd;color:white !important;}
</style>
</head>
<body class="p-4">
<div class="container">
    <div class="card p-4">
        <h4 class="text-center text-primary fw-bold mb-3">📊 Reporte de Préstamos</h4>

      

        <div class="tab-content">
            <div class="tab-pane fade show active" id="historial" role="tabpanel" aria-labelledby="historial-tab">
                <?php if(!empty($historial)): ?>
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
                                <?php foreach($historial as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['usuario']) ?></td>
                                    <td><?= htmlspecialchars($row['libro']) ?></td>
                                    <td><?= htmlspecialchars($row['fecha_prestamo']) ?></td>
                                    <td><?= htmlspecialchars($row['fecha_devolucion']) ?></td>
                                    <td><?= htmlspecialchars($row['dias']) ?></td>
                                    <td><?= htmlspecialchars($row['estado']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="export_historial_pdf.php" class="btn btn-danger">📄 Exportar a PDF</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">No se encontraron datos para el historial de préstamos.</div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="masprestados" role="tabpanel" aria-labelledby="masprestados-tab">
                <?php if(!empty($libros)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>Total de Préstamos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach($libros as $row): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($row['libro']) ?></td>
                                    <td><?= htmlspecialchars($row['autor']) ?></td>
                                    <td><?= htmlspecialchars($row['total_prestamos']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="export_libros_mas_prestados_pdf.php" class="btn btn-danger">📄 Exportar a PDF</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">No se encontraron datos de libros más prestados.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-3 text-center">
            <a href="../dashboard.php" class="btn btn-secondary">⬅ Volver al Dashboard</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
