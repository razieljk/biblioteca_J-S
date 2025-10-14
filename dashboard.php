<?php

session_start();

require_once __DIR__ . '/model/conexion.php';

$customFields = [
    ['name' => 'autor', 'label' => 'Autor', 'type' => 'text'],
    ['name' => 'genero', 'label' => 'Género', 'type' => 'text'],
    ['name' => 'anio', 'label' => 'Año', 'type' => 'number'],
];

$books = [
    ['id'=>1,'titulo'=>'Cien Años de Soledad','autor'=>'Gabriel García Márquez','genero'=>'Realismo','anio'=>1967,'descripcion'=>'La obra maestra del realismo mágico.','precio'=>45000,'imagen_url'=>'https://via.placeholder.com/300x180?text=Cien+Años'],
    ['id'=>2,'titulo'=>'El Principito','autor'=>'Antoine de Saint-Exupéry','genero'=>'Infantil','anio'=>1943,'descripcion'=>'Un libro para todas las edades sobre la amistad.','precio'=>22000,'imagen_url'=>'https://via.placeholder.com/300x180?text=Principito'],
    ['id'=>3,'titulo'=>'1984','autor'=>'George Orwell','genero'=>'Ciencia Ficción','anio'=>1949,'descripcion'=>'Distopía clásica sobre vigilancia y poder.','precio'=>32000,'imagen_url'=>'https://via.placeholder.com/300x180?text=1984'],
    ['id'=>4,'titulo'=>'Don Quijote de la Mancha','autor'=>'Miguel de Cervantes','genero'=>'Clásico','anio'=>1605,'descripcion'=>'La historia del caballero andante que confunde molinos con gigantes.','precio'=>38000,'imagen_url'=>'https://via.placeholder.com/300x180?text=Quijote'],
    ['id'=>5,'titulo'=>'La Sombra del Viento','autor'=>'Carlos Ruiz Zafón','genero'=>'Misterio','anio'=>2001,'descripcion'=>'Novela sobre libros, secretos y Barcelona.','precio'=>36000,'imagen_url'=>'https://via.placeholder.com/300x180?text=Sombra+del+Viento'],
    ['id'=>6,'titulo'=>'El Alquimista','autor'=>'Paulo Coelho','genero'=>'Fantasía','anio'=>1988,'descripcion'=>'Búsqueda personal y destino.','precio'=>28000,'imagen_url'=>'https://via.placeholder.com/300x180?text=Alquimista'],
    ['id'=>7,'titulo'=>'Rayuela','autor'=>'Julio Cortázar','genero'=>'Experimental','anio'=>1963,'descripcion'=>'Una novela que invita a leerla en varios órdenes.','precio'=>33000,'imagen_url'=>'https://via.placeholder.com/300x180?text=Rayuela'],
    ['id'=>8,'titulo'=>'Crónica de una muerte anunciada','autor'=>'Gabriel García Márquez','genero'=>'Novela','anio'=>1981,'descripcion'=>'Un misterio narrado con inevitabilidad.','precio'=>24000,'imagen_url'=>'https://via.placeholder.com/300x180?text=Crónica'],
    ['id'=>9,'titulo'=>'Ficciones','autor'=>'Jorge Luis Borges','genero'=>'Cuentos','anio'=>1944,'descripcion'=>'Colección de relatos cortos y laberínticos.','precio'=>30000,'imagen_url'=>'https://via.placeholder.com/300x180?text=Ficciones'],
    ['id'=>10,'titulo'=>'La Iliada','autor'=>'Homero','genero'=>'Épica','anio'=>-750,'descripcion'=>'Poema épico de la guerra de Troya.','precio'=>41000,'imagen_url'=>'https://via.placeholder.com/300x180?text=Iliada'],
];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filters = [];
foreach ($customFields as $f) {
    $val = isset($_GET[$f['name']]) ? trim($_GET[$f['name']]) : '';
    if ($val !== '') $filters[$f['name']] = $val;
}

$filtered = array_filter($books, function($b) use ($search, $filters) {
    $ok = true;
    if ($search !== '') {
        $q = mb_strtolower($search);
        $hay = mb_strtolower($b['titulo'] . ' ' . $b['autor'] . ' ' . $b['descripcion'] . ' ' . ($b['genero'] ?? ''));
        if (mb_strpos($hay, $q) === false) $ok = false;
    }
    foreach ($filters as $k => $v) {
        $v = mb_strtolower($v);
        if (!isset($b[$k]) || mb_strpos(mb_strtolower((string)$b[$k]), $v) === false) {
            $ok = false;
            break;
        }
    }
    return $ok;
});

$order = $_GET['order'] ?? 'titulo';
usort($filtered, function($a, $b) use ($order) {
    if ($order === 'anio') return $a['anio'] <=> $b['anio'];
    return strcmp(mb_strtolower($a['titulo']), mb_strtolower($b['titulo']));
});

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 8;
$totalRows = count($filtered);
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$offset = ($page - 1) * $perPage;
$visible = array_slice($filtered, $offset, $perPage);

function buildQuery(array $overrides = []) {
    $qs = array_merge($_GET, $overrides);
    return http_build_query($qs);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Diseño de Libros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    :root{ --primary:#0d6efd; --muted:#6c757d; --bg:#f6f8fb; --card-bg:#ffffff; }
    body{ font-family:'Inter',Arial,Helvetica,sans-serif; background:var(--bg); color:#222; }
    .navbar{ background:transparent !important; }
    .container-fluid{ padding-left:1rem; padding-right:1rem; }
    .sidebar{ width:280px; background:var(--card-bg); border-right:1px solid #e9eef6; position:fixed; top:0; bottom:0; left:0; padding-top:70px; overflow-y:auto; }
    @media(min-width:992px){ main.main-content{ margin-left:300px; } }
    .book-card{ border:none; border-radius:12px; overflow:hidden; box-shadow:0 6px 18px rgba(15,23,42,0.06); transition:transform .18s ease, box-shadow .18s ease; background:var(--card-bg); }
    .book-card:hover{ transform:translateY(-6px); box-shadow:0 12px 28px rgba(15,23,42,0.09); }
    .book-card img{ height:180px; width:100%; object-fit:cover; background:linear-gradient(90deg,#f0f2f6,#ffffff); }
    .card-body h6{ font-weight:600; margin-bottom:.25rem; }
    .card-subtitle{ color:var(--muted); font-size:.85rem; }
    .search-input{ border-radius:40px; box-shadow:0 4px 12px rgba(13,110,253,0.06); }
    .btn-primary, .btn-outline-primary{ border-radius:999px; }
    .small-muted{ color:var(--muted); font-size:.85rem; }
    footer.small{ color:var(--muted); text-align:center; padding:2rem 0; }
    .sidebar .p-3{ padding-bottom: 100px; }
    .no-results { padding: 30px; text-align:center; background:#fff; border-radius:12px; box-shadow:0 6px 18px rgba(15,23,42,0.04); }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg border-bottom bg-white">
  <div class="container-fluid">
    <div class="d-flex align-items-center">
      <button class="btn btn-outline-secondary d-lg-none me-2" id="toggleSidebar">☰</button>
      <a class="navbar-brand fw-bold" href="dashboard.php">Mis Libros</a>
    </div>

    <form class="d-flex mx-auto w-50" method="get" action="dashboard.php" style="max-width:720px;">
      <input type="hidden" name="page" value="1">
      <input class="form-control me-2 search-input" type="search" placeholder="Buscar libros, autores, descripciones..." aria-label="Buscar" name="search" value="<?= htmlspecialchars($search) ?>">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </form>

    <div class="d-flex align-items-center">
      <a href="#" class="btn btn-outline-danger">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">

    <aside class="d-none d-lg-block sidebar">
      <div class="p-3">
        <h5>Filtros</h5>
        <p class="text-muted small">Cambia los campos en la parte superior del archivo si necesitas otros filtros.</p>
        <form method="get" action="dashboard.php">
          <input type="hidden" name="page" value="1">
          <div class="mb-3">
            <label class="form-label">Ordenar</label>
            <select name="order" class="form-select">
              <option value="titulo" <?= (($_GET['order'] ?? '')==='titulo') ? 'selected' : '' ?>>Título (A-Z)</option>
              <option value="anio" <?= (($_GET['order'] ?? '')==='anio') ? 'selected' : '' ?>>Año</option>
            </select>
          </div>

          <?php foreach ($customFields as $f): ?>
            <div class="mb-3">
              <label class="form-label"><?= htmlspecialchars($f['label']) ?></label>
              <input type="<?= $f['type'] ?>" name="<?= htmlspecialchars($f['name']) ?>" class="form-control" value="<?= htmlspecialchars($_GET[$f['name']] ?? '') ?>">
            </div>
          <?php endforeach; ?>

          <div class="d-grid gap-2">
            <button class="btn btn-success" type="submit">Aplicar filtros</button>
            <a href="dashboard.php" class="btn btn-secondary">Limpiar</a>
          </div>
        </form>

        <hr>
        <h6>Campos editables</h6>
        <small class="text-muted">Los campos mostrados son solo de ejemplo.</small>
      </div>
    </aside>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasFilters">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title">Filtros</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
        <form method="get" action="dashboard.php">
          <input type="hidden" name="page" value="1">
          <?php foreach ($customFields as $f): ?>
            <div class="mb-3">
              <label class="form-label"><?= htmlspecialchars($f['label']) ?></label>
              <input type="<?= $f['type'] ?>" name="<?= htmlspecialchars($f['name']) ?>" class="form-control" value="<?= htmlspecialchars($_GET[$f['name']] ?? '') ?>">
            </div>
          <?php endforeach; ?>
          <div class="d-grid gap-2">
            <button class="btn btn-success" type="submit">Aplicar filtros</button>
          </div>
        </form>
      </div>
    </div>

    <main class="col p-4 main-content">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Libros disponibles <small class="text-muted">(<?= $totalRows ?> encontrados)</small></h4>
        <div>
          <button class="btn btn-outline-primary d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFilters">Filtros</button>
        </div>
      </div>

      <?php if (empty($visible)): ?>
        <div class="no-results">
          <h5>No se encontraron libros</h5>
          <p class="text-muted">Intenta eliminar filtros o probar otra búsqueda.</p>
        </div>
      <?php else: ?>
        <div class="row g-3">
          <?php foreach ($visible as $book): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
              <div class="card book-card h-100">
                <img src="<?= htmlspecialchars($book['imagen_url']) ?>" class="card-img-top" alt="Portada">
                <div class="card-body d-flex flex-column">
                  <h6 class="card-title"><?= htmlspecialchars($book['titulo']) ?></h6>
                  <p class="card-subtitle small mb-2"><?= htmlspecialchars($book['autor']) ?> — <?= (int)$book['anio'] ?></p>
                  <p class="card-text small flex-grow-1"><?= htmlspecialchars(mb_strimwidth($book['descripcion'], 0, 100, '...')) ?></p>
                  <div class="d-flex justify-content-between align-items-center mt-2">
                    <strong>$<?= number_format($book['precio'], 0, ',', '.') ?></strong>
                    <a href="#" class="btn btn-sm btn-outline-primary">Ver</a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
          <nav class="mt-4" aria-label="Paginación">
            <ul class="pagination">
              <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?<?= buildQuery(['page'=> $page-1]) ?>">Anterior</a>
              </li>
              <?php for ($p=1; $p <= $totalPages; $p++): ?>
                <li class="page-item <?= $p === $page ? 'active' : '' ?>"><a class="page-link" href="?<?= buildQuery(['page'=>$p]) ?>"><?= $p ?></a></li>
              <?php endfor; ?>
              <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?<?= buildQuery(['page'=> $page+1]) ?>">Siguiente</a>
              </li>
            </ul>
          </nav>
        <?php endif; ?>

      <?php endif; ?>
    </main>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('toggleSidebar')?.addEventListener('click', function(){
    var el = document.getElementById('offcanvasFilters');
    if (el) {
      var bs = new bootstrap.Offcanvas(el);
      bs.show();
    }
  });
</script>
</body>
</html>
