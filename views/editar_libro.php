<?php
require_once __DIR__ . '/../model/MYSQL.php';
session_start();

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$id = $_GET['id'] ?? null;
$libros = [];
$libroSeleccionado = null;

$sql = "SELECT * FROM libro";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $libros[] = $row;
}

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM libro WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $libroSeleccionado = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $titulo = trim($_POST['titulo']);
    $autor = trim($_POST['autor']);
    $isbm = trim($_POST['isbm']);
    $categoria = trim($_POST['categoria']);
    $disponibilidad = trim($_POST['disponibilidad']);
    $cantidad = (int)$_POST['cantidad'];
    $descripcion = trim($_POST['descripcion']);
    $imagen = '';

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = basename($_FILES['imagen']['name']);
        $rutaDestino = __DIR__ . '/../uploads/' . $nombreArchivo;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $imagen = 'uploads/' . $nombreArchivo;
        }
    }

    $sqlUpdate = "UPDATE libro SET titulo=?, autor=?, isbm=?, categoria=?, disponibilidad=?, cantidad=?, descripcion=?";
    if ($imagen) $sqlUpdate .= ", imagen=?";
    $sqlUpdate .= " WHERE id=?";

    $stmt = $conn->prepare($sqlUpdate);
    if ($imagen) {
        $stmt->bind_param("sssssdssi", $titulo, $autor, $isbm, $categoria, $disponibilidad, $cantidad, $descripcion, $imagen, $id);
    } else {
        $stmt->bind_param("sssssdsi", $titulo, $autor, $isbm, $categoria, $disponibilidad, $cantidad, $descripcion, $id);
    }

    if ($stmt->execute()) {
        header("Location: editar_libro.php?success=1&id=$id");
        exit;
    } else {
        header("Location: editar_libro.php?error=1&id=$id");
        exit;
    }
}

$db->desconectar();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Libro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { background-color: #f5f7fa; font-family: 'Inter', sans-serif; }
    .form-container { max-width: 700px; margin: 40px auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .titulo { color: #1e6091; font-weight: 600; text-align: center; margin-bottom: 25px; }
  </style>
</head>
<body>

<div class="container">
  <div class="form-container">
    <h3 class="titulo">Editar libro</h3>

    <form method="GET" action="">
      <div class="mb-3">
        <label class="form-label">Seleccionar libro</label>
        <div class="input-group">
          <select name="id" class="form-select" required onchange="this.form.submit()">
            <option value="">-- Selecciona un libro --</option>
            <?php foreach ($libros as $l): ?>
              <option value="<?= $l['id'] ?>" <?= $id == $l['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($l['titulo']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </form>

    <?php if ($libroSeleccionado): ?>
    <form method="POST" enctype="multipart/form-data" id="editarLibroForm">
      <input type="hidden" name="id" value="<?= $libroSeleccionado['id'] ?>">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Título</label>
          <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($libroSeleccionado['titulo']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Autor</label>
          <input type="text" name="autor" class="form-control" value="<?= htmlspecialchars($libroSeleccionado['autor']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">ISBN</label>
          <input type="text" name="isbm" class="form-control" value="<?= htmlspecialchars($libroSeleccionado['isbm']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Categoría</label>
          <input type="text" name="categoria" class="form-control" value="<?= htmlspecialchars($libroSeleccionado['categoria']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Disponibilidad</label>
          <select name="disponibilidad" class="form-select" required>
            <option value="disponible" <?= $libroSeleccionado['disponibilidad'] === 'disponible' ? 'selected' : '' ?>>disponible</option>
            <option value="no disponible" <?= $libroSeleccionado['disponibilidad'] === 'no disponible' ? 'selected' : '' ?>>no disponible</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Cantidad</label>
          <input type="number" name="cantidad" class="form-control" value="<?= htmlspecialchars($libroSeleccionado['cantidad']) ?>" min="1" required>
        </div>
        <div class="col-md-12">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($libroSeleccionado['descripcion']) ?></textarea>
        </div>
        <div class="col-md-12">
          <label class="form-label">Imagen actual</label><br>
          <?php if ($libroSeleccionado['imagen']): ?>
            <img src="../<?= htmlspecialchars($libroSeleccionado['imagen']) ?>" alt="Portada" class="img-thumbnail mb-2" style="max-height: 120px;">
          <?php else: ?>
            <p class="text-muted">Sin imagen</p>
          <?php endif; ?>
          <input type="file" name="imagen" class="form-control" accept="image/*">
        </div>
      </div>

      <div class="mt-4 text-end">
        <button type="submit" class="btn btn-warning">Guardar cambios</button>
        <a href="../dashboard.php" class="btn btn-outline-secondary">Volver al Dashboard</a>
      </div>
    </form>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/editar_libro.js"></script>
<script>
  const success = "<?= $_GET['success'] ?? '' ?>";
  const error = "<?= $_GET['error'] ?? '' ?>";
</script>
</body>
</html>
