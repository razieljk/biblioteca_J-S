<?php
session_start();
require_once __DIR__ . '/model/conexion.php';

// 🔹 Consulta los libros desde la base de datos
$sql = "SELECT id, titulo, autor, categoria, descripcion, imagen FROM libro";
$result = $conn->query($sql);

$books = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

$search = $_GET['search'] ?? '';
$categoriaFiltro = $_GET['categoria'] ?? '';

// 🔹 Filtrado
$filtered = array_filter($books, function ($b) use ($search, $categoriaFiltro) {
    $ok = true;

    if ($search !== '') {
        $q = mb_strtolower($search);
        $texto = mb_strtolower($b['titulo'] . ' ' . $b['autor'] . ' ' . $b['descripcion'] . ' ' . $b['categoria']);
        if (mb_strpos($texto, $q) === false) $ok = false;
    }

    if ($categoriaFiltro !== '') {
        if (mb_strtolower($b['categoria']) !== mb_strtolower($categoriaFiltro)) $ok = false;
    }

    return $ok;
});

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 8;
$totalRows = count($filtered);
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$offset = ($page - 1) * $perPage;
$visible = array_slice($filtered, $offset, $perPage);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Mis Libros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', Arial, Helvetica, sans-serif;
      background: #f6f8fb;
      color: #222;
    }
    .sidebar {
      min-height: 100vh;
      background-color: #0d6efd;
      color: #fff;
    }
    .sidebar a {
      color: #fff;
      text-decoration: none;
      display: block;
      padding: .75rem 1rem;
      border-radius: .375rem;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: rgba(255,255,255,0.15);
    }
    .book-card {
      border: none;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 6px 18px rgba(15,23,42,0.06);
      transition: transform .18s ease, box-shadow .18s ease;
      background: #fff;
    }
    .book-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 28px rgba(15,23,42,0.09);
    }
    .book-card img {
      height: 180px;
      width: 100%;
      object-fit: cover;
    }
  </style>
</head>
<body>

<!-- 🔹 Navbar superior -->
<nav class="navbar navbar-light bg-white border-bottom sticky-top">
  <div class="container-fluid">
    <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
      ☰
    </button>
    <a class="navbar-brand fw-bold ms-2" href="#">Biblioteca</a>
    <a href="#" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
  </div>
</nav>

<!-- 🔹 Sidebar (oculta incluso en pantallas grandes) -->
<div class="offcanvas offcanvas-start sidebar text-white" tabindex="-1" id="sidebar">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title">Menú</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body p-0">
    <nav class="nav flex-column p-3">
      <a href="#" class="active">Libros</a>
      <a href="#">Usuarios</a>
      <a href="#">Añadir libro</a>
      <a href="#">Préstamos</a>
    </nav>
  </div>
</div>

<!-- 🔹 Contenido principal -->
<div class="container-fluid p-4">
  <h4>Libros disponibles <small class="text-muted">(<?= $totalRows ?> encontrados)</small></h4>

  <!-- 🔹 Filtros -->
  <form method="get" action="dashboard.php" class="row g-3 align-items-end mt-2 mb-4">
    <div class="col-md-4 col-lg-3">
      <label class="form-label">Categoría</label>
      <input type="text" name="categoria" class="form-control" placeholder="Ej: Terror, Romance..." value="<?= htmlspecialchars($categoriaFiltro) ?>">
    </div>
    <div class="col-md-5 col-lg-4">
      <label class="form-label">Buscar</label>
      <input type="text" name="search" class="form-control" placeholder="Título, autor o descripción..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-3 col-lg-2">
      <button type="submit" class="btn btn-primary w-100">Filtrar</button>
    </div>
  </form>

  <?php if (empty($visible)): ?>
    <div class="alert alert-warning mt-4">No se encontraron libros con los filtros aplicados.</div>
  <?php else: ?>
    <div class="row g-3 mt-3">
      <?php foreach ($visible as $book): ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <div class="card book-card h-100">
            <img src="<?= htmlspecialchars($book['imagen'] ?? 'https://via.placeholder.com/300x180?text=Sin+Imagen') ?>" alt="Portada">
            <div class="card-body d-flex flex-column">
              <h6 class="card-title text-primary fw-bold text-center bg-light py-2 rounded shadow-sm">
                <?= htmlspecialchars($book['titulo']) ?>
              </h6>
              <p class="small mb-2"><strong>Autor:</strong> <?= htmlspecialchars($book['autor']) ?></p>
              <p class="small mb-2"><strong>Categoría:</strong> <?= htmlspecialchars($book['categoria']) ?></p>
              <p class="small text-muted flex-grow-1"><?= htmlspecialchars(mb_strimwidth($book['descripcion'], 0, 100, '...')) ?></p>
              <div class="mt-3 text-end">
                <a href="#" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold px-3">Ver</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
