<?php
require_once __DIR__ . '/../model/MYSQL.php';
session_start();

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $isbm = trim($_POST['isbm'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $disponibilidad = trim($_POST['disponibilidad'] ?? '');
    $cantidad = (int)($_POST['cantidad'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $imagen = '';

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = basename($_FILES['imagen']['name']);
        $rutaDestino = __DIR__ . '/../uploads/' . $nombreArchivo;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $imagen = 'uploads/' . $nombreArchivo;
        }
    }

    if ($titulo && $autor && $isbm && $categoria && $disponibilidad && $cantidad > 0) {
        $sql = "INSERT INTO libro (titulo, autor, ISBM, categoria, disponibilidad, cantidad, descripcion, imagen)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssdss", $titulo, $autor, $isbm, $categoria, $disponibilidad, $cantidad, $descripcion, $imagen);

        if ($stmt->execute()) {
            header("Location: agregar_libro.php?success=1");
            exit;
        } else {
            header("Location: agregar_libro.php?error=1");
            exit;
        }
    } else {
        header("Location: agregar_libro.php?error=2");
        exit;
    }
}

if (isset($_GET['success'])) {
    $mensaje = "Libro agregado";
    $tipo = "success";
} elseif (isset($_GET['error'])) {
    $errorCode = $_GET['error'];
    if ($errorCode == 1) {
        $mensaje = "no se pudo agregar";
    } else {
        $mensaje = "altan campos por rellenar";
    }
    $tipo = "error";
}

$db->desconectar();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Agregar Libro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Agregar nuevo libro</h3>
    <div class="d-flex gap-2">
      <a href="../dashboard.php" class="btn btn-outline-secondary">Volver al Dashboard</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Autor</label>
            <input type="text" name="autor" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">ISBN</label>
            <input type="text" name="isbm" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Categoría</label>
            <input type="text" name="categoria" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Disponibilidad</label>
            <select name="disponibilidad" class="form-select" required>
              <option value="">Selecciona...</option>
              <option value="disponible">Disponible</option>
              <option value="no disponible">No disponible</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" class="form-control" min="1" required>
          </div>
          <div class="col-md-12">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3"></textarea>
          </div>
          <div class="col-md-12">
            <label class="form-label">Imagen</label>
            <input type="file" name="imagen" class="form-control" accept="image/*">
          </div>
        </div>

        <div class="mt-4 text-end">
          <button type="submit" class="btn btn-primary">Guardar libro</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const mensaje = <?= json_encode($mensaje) ?>;
  const tipo = <?= json_encode($tipo) ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/agregar_libro.js"></script>
</body>
</html>
