<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: views/login.php");
    exit();
}

require_once __DIR__ . '/model/MYSQL.php';

$tipoUsuario = $_SESSION['tipo'] ?? 'cliente';
$section = $_GET['section'] ?? '';

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard - Biblioteca Virtual</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Inter', Arial, Helvetica, sans-serif; background: #f6f8fb; color: #222; }
.sidebar { min-height: 100vh; background-color: #0d6efd; color: #fff; }
.sidebar a { color: #fff; text-decoration: none; display: block; padding: .75rem 1rem; border-radius: .375rem; }
.sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.15); }
.submenu a { color: #cfd8ff !important; font-size: 0.9rem; padding-left: 2rem; }
.submenu a:hover { background-color: rgba(255,255,255,0.1); }
.book-card { border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 6px 18px rgba(15,23,42,0.06); transition: transform .18s ease, box-shadow .18s ease; background: #fff; }
.book-card:hover { transform: translateY(-6px); box-shadow: 0 12px 28px rgba(15,23,42,0.09); }
.book-card img { height: 180px; width: 100%; object-fit: cover; }
.card-profile { background: #fff; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.15); padding: 30px; max-width: 800px; margin: 30px auto; }
</style>
</head>
<body>

<nav class="navbar navbar-light bg-white border-bottom sticky-top">
  <div class="container-fluid">
    <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">☰</button>
    <a class="navbar-brand fw-bold ms-2" href="#">Biblioteca</a>
    <a href="views/logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
  </div>
</nav>

<div class="offcanvas offcanvas-start sidebar text-white" tabindex="-1" id="sidebar">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title">Menú</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body p-0">
    <nav class="nav flex-column p-3">

      <?php if ($tipoUsuario !== 'cliente'): ?>
        <a href="views/usuarios.php">Administrar usuarios</a>

        <div class="mt-2">
          <a class="d-flex justify-content-between align-items-center text-white text-decoration-none"
             data-bs-toggle="collapse" href="#submenuLibros" role="button" aria-expanded="false">
            <span>Administrar libros</span><span class="small">▼</span>
          </a>
          <div class="collapse submenu mt-1" id="submenuLibros">
            <a href="views/agregar_libro.php" class="d-block py-1">Añadir libro</a>
            <a href="views/editar_libro.php" class="d-block py-1">Editar libro</a>
            <a href="views/eliminar_libro.php" class="d-block py-1">Eliminar libro</a>
          </div>
        </div>

        <div class="mt-2">
          <a class="d-flex justify-content-between align-items-center text-white text-decoration-none"
             data-bs-toggle="collapse" href="#submenuReservas" role="button" aria-expanded="false">
            <span>Reservas</span><span class="small">▼</span>
          </a>
          <div class="collapse submenu mt-1" id="submenuReservas">
            <a href="views/admin_reservas.php" class="d-block py-1">Ver reservas</a>
          </div>
        </div>

        <div class="mt-2">
          <a class="d-flex justify-content-between align-items-center text-white text-decoration-none"
             data-bs-toggle="collapse" href="#submenuPrestamos" role="button" aria-expanded="false">
            <span>Préstamos</span><span class="small">▼</span>
          </a>
          <div class="collapse submenu mt-1" id="submenuPrestamos">
            <a href="views/admin_prestamos.php" class="d-block py-1">Ver préstamos</a>
          </div>
        </div>

        <!-- MENÚ REPORTES -->
        <div class="mt-2">
          <a class="d-flex justify-content-between align-items-center text-white text-decoration-none"
             data-bs-toggle="collapse" href="#submenuReportes" role="button" aria-expanded="false">
            <span> Reportes</span><span class="small">▼</span>
          </a>
          <div class="collapse submenu mt-1" id="submenuReportes">
      <!-- Submenú Excel -->
<a class="d-flex justify-content-between align-items-center text-white text-decoration-none ps-3"
   data-bs-toggle="collapse" href="#submenuExcel" role="button" aria-expanded="false">
  <span> Excel</span><span class="small">▼</span>
</a>
<div class="collapse submenu mt-1 ps-3" id="submenuExcel">
  <a href="views/reporte_inventario_excel.php" class="d-block py-1">Inventario</a>
  <a href="views/reporte_prestamos_excel.php" class="d-block py-1">Préstamos</a>
</div>


            <!-- Submenú PDF -->
            <a class="d-flex justify-content-between align-items-center text-white text-decoration-none ps-3 mt-2"
               data-bs-toggle="collapse" href="#submenuPDF" role="button" aria-expanded="false">
              <span> PDF</span><span class="small">▼</span>
            </a>
            <div class="collapse submenu mt-1 ps-3" id="submenuPDF">
              <!-- PDF Inventario -->
              <a class="d-flex justify-content-between align-items-center text-white text-decoration-none ps-3"
                 data-bs-toggle="collapse" href="#submenuPDFInventario" role="button" aria-expanded="false">
                <span>Inventario</span><span class="small">▼</span>
              </a>
              <div class="collapse submenu mt-1 ps-3" id="submenuPDFInventario">
                <a href="views/reporte_inventario_pdf.php?tipo=disponibles" class="d-block py-1">Libros disponibles</a>
                <a href="views/reporte_inventario_pdf.php?tipo=prestados" class="d-block py-1">Libros prestados</a>
              </div>

              <!-- PDF Préstamos -->
              <a class="d-flex justify-content-between align-items-center text-white text-decoration-none ps-3 mt-2"
                 data-bs-toggle="collapse" href="#submenuPDFPrestamos" role="button" aria-expanded="false">
                <span>Préstamos</span><span class="small">▼</span>
              </a>
              <div class="collapse submenu mt-1 ps-3" id="submenuPDFPrestamos">
                <a href="views/reporte_prestamos_pdf.php" class="d-block py-1">Historial de préstamos</a>
                <a href="views/reporte_libros_mas_prestados.php" class="d-block py-1">Libros más prestados</a>
              </div>
            </div>

          </div>
        </div>

      <?php else: ?>
        <a href="dashboard.php" class="<?= (!isset($_GET['section'])) ? 'active' : '' ?>">Inicio</a>
        <a href="dashboard.php?section=perfil" class="<?= (isset($_GET['section']) && $_GET['section'] === 'perfil') ? 'active' : '' ?>">Perfil</a>
        <a href="views/admin_reservas.php">Mis reservas</a>
        <a href="views/admin_prestamos.php">Mis préstamos</a>
      <?php endif; ?>

    </nav>
  </div>
</div>

<div class="container-fluid p-4">

<?php
// ---------- CONTENIDO DINÁMICO SEGÚN SECCIÓN ----------
if ($tipoUsuario==='cliente' && $section==='perfil') {
    // PERFIL USUARIO
    $usuario_id = $_SESSION['usuario_id'];
    $message = '';
    if ($_SERVER['REQUEST_METHOD']==='POST'){
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if($nombre && $apellido && $email){
            $stmtCheck = $conn->prepare("SELECT id FROM usuarios WHERE email=? AND id<>?");
            $stmtCheck->bind_param("si",$email,$usuario_id);
            $stmtCheck->execute();
            $resCheck = $stmtCheck->get_result();
            if($resCheck->num_rows>0){ $message="⚠️ Este correo ya está en uso."; }
            else{
                if($password!==''){
                    $passHash = password_hash($password,PASSWORD_DEFAULT);
                    $stmt=$conn->prepare("UPDATE usuarios SET nombre=?,apellido=?,email=?,contrasena=? WHERE id=?");
                    $stmt->bind_param("ssssi",$nombre,$apellido,$email,$passHash,$usuario_id);
                }else{
                    $stmt=$conn->prepare("UPDATE usuarios SET nombre=?,apellido=?,email=? WHERE id=?");
                    $stmt->bind_param("sssi",$nombre,$apellido,$email,$usuario_id);
                }
                if($stmt->execute()){ $message='Perfil actualizado'; $_SESSION['nombre']=$nombre.' '.$apellido; $_SESSION['email']=$email; }
                else{ $message='No se pudo actualizar el perfil'; }
                $stmt->close();
            }
            $stmtCheck->close();
        }else{ $message='Todos los campos son obligatorios.'; }
    }

    $stmt=$conn->prepare("SELECT nombre,apellido,email FROM usuarios WHERE id=?");
    $stmt->bind_param("i",$usuario_id);
    $stmt->execute();
    $res=$stmt->get_result();
    $usuario=$res->fetch_assoc();
    $stmt->close();
    ?>

    <div class="card-profile">
        <h4 class="mb-3">Mi Perfil</h4>
        <?php if($message): ?><div class="alert alert-info"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <form method="post">
            <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Apellido</label><input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($usuario['apellido']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Correo electrónico</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Nueva contraseña (opcional)</label><input type="password" name="password" class="form-control" placeholder="Dejar vacío para no cambiar"></div>
            <div class="d-flex justify-content-end gap-2">
                <a href="dashboard.php" class="btn btn-outline-secondary">← Volver al Dashboard</a>
                <button type="submit" class="btn btn-primary">Actualizar perfil</button>
            </div>
        </form>
    </div>

<?php
} elseif (in_array($section, ['reporte_inventario_excel','reporte_prestamos_excel','reporte_inventario_pdf_disponibles','reporte_inventario_pdf_prestados','reporte_historial_prestamos_pdf','reporte_libros_mas_prestados_pdf'])) {
    // REPORTES
    echo "<h4>Sección de Reportes: <strong>".htmlspecialchars($section)."</strong></h4>";
    echo "<p>Aquí se mostraría el contenido correspondiente a este reporte.</p>";

} else {
    // LIBROS DISPONIBLES (SECCIÓN POR DEFECTO)
    $sql = "SELECT id,titulo,autor,categoria,descripcion,imagen,cantidad,disponibilidad FROM libro";
    $res=$conn->query($sql);
    $books=[];
    if($res && $res->num_rows>0){ while($r=$res->fetch_assoc()) $books[]=$r; }

    $search = $_GET['search'] ?? '';
    $categoriaFiltro = $_GET['categoria'] ?? '';
    $filtered = array_filter($books,function($b) use($search,$categoriaFiltro){
        $ok=true;
        if($search!==''){
            $q=mb_strtolower($search);
            $texto=mb_strtolower($b['titulo'].' '.$b['autor'].' '.$b['descripcion'].' '.$b['categoria']);
            if(mb_strpos($texto,$q)===false) $ok=false;
        }
        if($categoriaFiltro!==''){
            if(mb_strtolower($b['categoria'])!==mb_strtolower($categoriaFiltro)) $ok=false;
        }
        return $ok;
    });
    $page=max(1,(int)($_GET['page']??1));
    $perPage=8;
    $totalRows=count($filtered);
    $totalPages=max(1,(int)ceil($totalRows/$perPage));
    $offset=($page-1)*$perPage;
    $visible=array_slice($filtered,$offset,$perPage);
    ?>

    <h4>Libros disponibles <small class="text-muted">(<?= $totalRows ?> encontrados)</small></h4>
    <form method="get" action="dashboard.php" class="row g-3 align-items-end mt-2 mb-4">
        <div class="col-md-4 col-lg-3"><label class="form-label">Categoría</label><input type="text" name="categoria" class="form-control" placeholder="Ej: Terror, Romance..." value="<?= htmlspecialchars($categoriaFiltro) ?>"></div>
        <div class="col-md-5 col-lg-4"><label class="form-label">Buscar</label><input type="text" name="search" class="form-control" placeholder="Título, autor o descripción..." value="<?= htmlspecialchars($search) ?>"></div>
        <div class="col-md-3 col-lg-2"><button type="submit" class="btn btn-primary w-100">Filtrar</button></div>
    </form>

    <?php if(empty($visible)): ?>
        <div class="alert alert-warning mt-4">No se encontraron libros con los filtros aplicados.</div>
    <?php else: ?>
        <div class="row g-3 mt-3">
            <?php foreach($visible as $book): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card book-card h-100">
                    <img src="<?= htmlspecialchars($book['imagen'] ?? 'https://via.placeholder.com/300x180?text=Sin+Imagen') ?>" alt="Portada">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title text-primary fw-bold text-center bg-light py-2 rounded shadow-sm"><?= htmlspecialchars($book['titulo']) ?></h6>
                        <p class="small mb-2"><strong>Autor:</strong> <?= htmlspecialchars($book['autor']) ?></p>
                        <p class="small mb-2"><strong>Categoría:</strong> <?= htmlspecialchars($book['categoria']) ?></p>
                        <p class="small text-muted flex-grow-1"><?= htmlspecialchars(mb_strimwidth($book['descripcion'],0,100,'...')) ?></p>
                        <div class="mt-3 text-end"><a href="views/info_libro.php?id=<?= urlencode($book['id']) ?>" class="btn btn-outline-primary rounded-pill px-3">Más información</a></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php
}
$db->desconectar();
?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
