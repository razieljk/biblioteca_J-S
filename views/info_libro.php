<?php
session_start();
require_once __DIR__ . '/../model/MYSQL.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$id_libro = $_GET['id'] ?? null;
$libro = null;

if ($id_libro) {
    $stmt = $conn->prepare("SELECT * FROM libro WHERE id = ?");
    $stmt->bind_param("i", $id_libro);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $libro = $resultado->fetch_assoc();
    $stmt->close();
}

$db->desconectar();

if (!$libro) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Libro no encontrado.</div></div>");
}
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($libro['titulo']) ?> - Biblioteca</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: linear-gradient(180deg, #f7f9fc 0%, #eaf1fb 100%);
      font-family: 'Inter', Arial, sans-serif;
      color: #222;
    }
    .book-container { max-width: 900px; margin: 60px auto; }
    .book-card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.08);
      overflow: hidden;
      display: flex;
      flex-wrap: wrap;
    }
    .book-image { flex: 1 1 350px; background-color: #f0f2f5; }
    .book-image img { width: 100%; height: 100%; object-fit: cover; border-radius: 18px 0 0 18px; }
    .book-info { flex: 1 1 500px; padding: 30px; }
    .book-info h2 { font-size: 1.8rem; color: #0d6efd; margin-bottom: 1rem; }
    .book-info p { margin-bottom: .6rem; line-height: 1.5; }
    .book-description {
      background: #f8f9fa; border-radius: 10px; padding: 15px;
      margin-top: 1rem; font-size: 0.95rem; color: #555;
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-back { border-radius: 50px; font-weight: 500; }
    .availability { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 0.9rem; font-weight: 500; color: #fff; }
    .available { background-color: #198754; }
    .unavailable { background-color: #dc3545; }
    .modal-content { border-radius: 18px; box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
    .action-buttons button { margin-right: 8px; }
  </style>
</head>
<body>

<div class="container book-container">
  <a href="../dashboard.php" class="btn btn-outline-secondary btn-back mb-4">← Volver</a>

  <div class="book-card">
    <div class="book-image">
      <img src="../<?= htmlspecialchars($libro['imagen'] ?: 'img-portadas/default.png') ?>" 
           alt="<?= htmlspecialchars($libro['titulo']) ?>">
    </div>
    <div class="book-info">
      <h2><?= htmlspecialchars($libro['titulo']) ?></h2>
      <p><strong>Autor:</strong> <?= htmlspecialchars($libro['autor']) ?></p>
      <p><strong>Categoría:</strong> <?= htmlspecialchars($libro['categoria']) ?></p>
      <p><strong>Cantidad disponible:</strong> <?= htmlspecialchars($libro['cantidad']) ?></p>

      <div class="book-description mt-3">
        <strong>Descripción:</strong><br>
        <?= nl2br(htmlspecialchars($libro['descripcion'])) ?>
      </div>

      <div class="mt-4 action-buttons">
        <?php if ((int)$libro['cantidad'] > 0): ?>
            <button id="btnReserva" class="btn btn-outline-primary rounded-pill px-4">
                Reservar
            </button>

            <button id="btnPrestamo" class="btn btn-primary rounded-pill px-4"
                    data-bs-toggle="modal"
                    data-bs-target="#modalPrestamo"
                    data-id-libro="<?= $libro['id'] ?>">
                    solicitar prestamo
            </button>
        <?php else: ?>
            <button class="btn btn-secondary rounded-pill px-4" disabled>Agotado</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalPrestamo" tabindex="-1" aria-labelledby="modalPrestamoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPrestamoLabel">confirmar prestamo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formPrestamo">
          <input type="hidden" name="id_libro" id="id_libro">
          <div class="mb-3">
            <label for="dias" class="form-label">Duración del prestamo:</label>
            <select class="form-select" name="dias" id="dias" required>
              <option value="">Seleccionar</option>
              <option value="7">7 días</option>
              <option value="20">20 días</option>
              <option value="30">30 días</option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Confirmar prestamo</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const modalPrestamo = document.getElementById('modalPrestamo');
modalPrestamo.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;
  const idLibro = button.getAttribute('data-id-libro');
  document.getElementById('id_libro').value = idLibro;
});

document.getElementById("formPrestamo").addEventListener("submit", async function(e) {
  e.preventDefault();
  const formData = new FormData(this);

  try {
    const response = await fetch("../controller/procesar_prestamo.php", {
      method: "POST",
      body: formData
    });

    const data = await response.json();

    Swal.fire({
      icon: data.success ? "success" : "error",
      title: data.success ? "Solicitud enviada" : "Error",
      text: data.message,
      confirmButtonColor: data.success ? "#0d6efd" : "#dc3545"
    }).then(() => {
      if (data.success) location.reload();
    });

  } catch (err) {
    console.error("Error en fetch:", err);
    Swal.fire({
      icon: "error",
      title: "Error de conexión",
      text: "no se pudo conectar con el servidor."
    });
  }
});

document.getElementById("btnReserva").addEventListener("click", async () => {
  const idLibro = <?= $libro['id'] ?>;

  const { value: dias } = await Swal.fire({
    title: "Reservar libro",
    html: `
      <label for="dias_reserva" class="form-label">Selecciona los días de reserva:</label>
      <select id="dias_reserva" class="swal2-select" required>
        <option value="">Seleccionar...</option>
        <option value="7">7 días</option>
        <option value="20">20 días</option>
        <option value="30">30 días</option>
      </select>
    `,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: "Reservar",
    cancelButtonText: "Cancelar",
    preConfirm: () => {
      const dias = document.getElementById("dias_reserva").value;
      if (!dias) {
        Swal.showValidationMessage("seleccionar dias valida");
      }
      return dias;
    }
  });

  if (!dias) return;

  const formData = new FormData();
  formData.append("id_libro", idLibro);
  formData.append("dias", dias);

  try {
    const response = await fetch("../controller/procesar_reserva.php", {
      method: "POST",
      body: formData
    });

    const data = await response.json();

    Swal.fire({
      icon: data.success ? "success" : "error",
      title: data.success ? "Reserva realizada" : "Error",
      text: data.message,
      confirmButtonColor: data.success ? "#0d6efd" : "#dc3545"
    }).then(() => {
      if (data.success) location.reload();
    });

  } catch (error) {
    console.error("Error al procesar reserva:", error);
    Swal.fire({
      icon: "error",
      title: "Error de conexión",
      text: "no se pudo conectar con el servidor."
    });
  }
});
</script>
</body>
</html>
