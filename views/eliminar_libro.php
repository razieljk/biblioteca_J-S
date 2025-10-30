<?php
require_once __DIR__ . '/../model/MYSQL.php';
session_start();

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        $check = $conn->prepare("SELECT id, titulo, imagen FROM libro WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $resultado = $check->get_result();
        $libro = $resultado->fetch_assoc();
        $check->close();

        if ($libro) {
            if (!empty($libro['imagen']) && file_exists(__DIR__ . '/../' . $libro['imagen'])) {
                unlink(__DIR__ . '/../' . $libro['imagen']);
            }

            $stmt = $conn->prepare("DELETE FROM libro WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                header("Location: eliminar_libro.php?success=" . urlencode("El libro \"" . $libro['titulo'] . "\" fue eliminado correctamente."));
                exit;
            } else {
                header("Location: eliminar_libro.php?error=" . urlencode("No se pudo eliminar el libro."));
                exit;
            }
        } else {
            header("Location: eliminar_libro.php?error=" . urlencode("El libro seleccionado no existe."));
            exit;
        }
    } else {
        header("Location: eliminar_libro.php?error=" . urlencode("Debes seleccionar un libro para eliminar."));
        exit;
    }
}

if (isset($_GET['success'])) {
    $mensaje = $_GET['success'];
    $tipo = 'success';
} elseif (isset($_GET['error'])) {
    $mensaje = $_GET['error'];
    $tipo = 'error';
}

$sql = "SELECT id, titulo FROM libro ORDER BY titulo ASC";
$result = $conn->query($sql);
$db->desconectar();
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Eliminar Libro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Eliminar libro</h3>
    <a href="../dashboard.php" class="btn btn-outline-secondary">Volver al Dashboard</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form id="formEliminar" method="POST">
        <div class="mb-3">
          <label class="form-label">Selecciona el libro a eliminar</label>
          <select name="id" class="form-select" required>
            <option value="">-- Selecciona un libro --</option>
            <?php while ($libro = $result->fetch_assoc()): ?>
              <option value="<?= $libro['id'] ?>"><?= htmlspecialchars($libro['titulo']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="mt-4 text-end">
          <button type="submit" class="btn btn-danger">
            Eliminar libro
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const mensaje = <?= json_encode($mensaje) ?>;
  const tipo = <?= json_encode($tipo) ?>;
</script>
<script src="../assets/js/eliminar_libro.js"></script>
</body>
</html>
