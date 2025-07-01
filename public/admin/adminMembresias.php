<?php
// Usar rutas absolutas para mayor seguridad
require_once __DIR__ . '/../../src/php/conexiondb.php';

// Iniciar sesión antes de cualquier verificación
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['idUsuario']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: /GestiFit/public/index.html");
    exit;
}

// Obtener información del usuario
$userId = $_SESSION['idUsuario'];
$query = "SELECT * FROM usuario WHERE idUsuario = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: /GestiFit/public/login.html");
    exit;
}

// Obtener todas las membresías
$queryMembresias = "SELECT * FROM membresia";
$stmtMembresias = $pdo->query($queryMembresias);
$membresias = $stmtMembresias->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <title>GestiFit - Admin Membresías</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Teko:wght@300..700&display=swap"
    rel="stylesheet" />

  <!-- Icon Font Stylesheet -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="icon" type="image/png" href="/GestiFit/public/img/logo_gestifit_cuadrado-nofondo.png" />

  <!-- Libraries Stylesheet -->
  <link rel="stylesheet" href="/GestiFit/lib/animate/animate.min.css" />
  <link href="/Gestifit/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

  <!-- Customized Bootstrap Stylesheet -->
  <link href="/GestiFit/public/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Template Stylesheet -->
  <link href="/GestiFit/public/css/stylesAdmin.css" rel="stylesheet" />
  <link href="/GestiFit/public/css/style.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<div class="text-white bg-dark text-center py-2 fw-bold">
  <i class="fas fa-user-shield me-2"></i>Modo Administrador
</div>
<!-- Navbar & Hero Start -->
<nav class="container-fluid header-top">
  <div class="nav-shaps-2"></div>
  <div class="container d-flex align-items-center">
    <div class="d-flex align-items-center h-100">
      <a href="#" class="navbar-brand" style="height: 125px;">
        <h1 class="text-primary mb-0">
          <img src="/GestiFit/public/img/logo_gestifit_cuadrado-nofondo.png" class="img-fluid" width="70" height="70">
          GestiFit
        </h1>
      </a>
    </div>
    <div class="w-100 h-100">
      <div class="topbar px-0 py-2 d-none d-lg-block" style="height: 45px;">
        <div class="row gx-0 align-items-center">
          <div class="col-lg-8 text-center text-lg-center mb-lg-0">
            <div class="d-flex flex-wrap">
              <div class="pe-4"></div>
            </div>
          </div>
          <div class="col-lg-4 text-center text-lg-end">
            <div class="d-flex justify-content-end">
              <div class="d-flex pe-3">
                <a class="btn p-0 text-primary me-3"
                  href="https://www.instagram.com/elmanicomiogym?igsh=MXB3eHBkdjFjYXJleQ==">
                  <i class="fab fa-instagram"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="nav-bar px-0 py-lg-0" style="height: 80px;">
        <nav class="navbar navbar-expand-lg navbar-light d-flex justify-content-lg-end">
          <a href="#" class="navbar-brand-2">
            <h1 class="text-primary mb-0">
              <i class="fas fa-hand-rock me-2"></i>GestiFit
            </h1>
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="fa fa-bars"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav mx-0 mx-lg-auto">
              <a href="/GestiFit/public/admin/admin.php" class="nav-item nav-link active">
                <i class="fa fa-home me-2"></i>Principal
              </a>
              <a href="/GestiFit/public/admin/adminClientes.php" class="nav-item nav-link">
                <i class="fa fa-users me-2"></i>Clientes
              </a>
              <a href="/GestiFit/public/admin/adminMembresias.php" class="nav-item nav-link">
                <i class="fa fa-user-tie me-2"></i>Membresías
              </a>
              <a href="/GestiFit/public/admin/adminInstructores.php" class="nav-item nav-link">
                <i class="fa fa-dumbbell me-2"></i>Instructores
              </a>
              <a href="/GestiFit/public/admin/adminClases.php" class="nav-item nav-link">
                <i class="fa fa-id-card me-2"></i>Clases
              </a>
              <a href="/GestiFit/public/admin/adminRutinas.php" class="nav-item nav-link">
                <i class="fas fa-running me-2"></i>Rutinas
              </a>
              <a href="/GestiFit/public/admin/adminAvisos.php" class="nav-item nav-link ">
                <i class="fas fa-bullhorn me-2"></i>Avisos
              </a>

              <div class="nav-btn ps-3">
                <a href="/GestiFit/src/cerrar_sesion.php"
                   class="btn btn-primary py-2 px-4 ms-0 ms-lg-3"
                   onclick="return confirm('¿Seguro que deseas cerrar sesión?')">
                  <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                </a>
              </div>

              <div class="nav-shaps-1"></div>
            </div>
          </div>
        </nav>
      </div>
    </div>
  </div>
</nav>


  <div class="align-items-center" style="margin-top: 50px;">
    <h2 class="mb-4">¿Deseas hacer cambios en Membresías?</h2>
  </div>

  <div class="container-fluid" style="margin-top: 50px;">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary mb-4">Membresías Activas</h2>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarMembresia">Añadir Membresía</button>
      </div>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>Nombre</th>
              <th>Costo</th>
              <th>Duración</th>
              <th>Descripción</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($membresias)): ?>
              <?php foreach ($membresias as $m): ?>
                <tr>
                  <td><?= htmlspecialchars($m['nombre']) ?></td>
                  <td>$<?= number_format($m['costo'], 2) ?></td>
                  <td><?= htmlspecialchars($m['duracionMeses']) ?> meses</td>
                  <td><?= htmlspecialchars($m['descripcion']) ?></td>
                  <td>
                    <button type="button" class="btn btn-sm btn-secondary btnEditarMembresia"
                      data-id="<?= $m['idMembresia'] ?>"
                      data-nombre="<?= htmlspecialchars($m['nombre']) ?>"
                      data-costo="<?= htmlspecialchars($m['costo']) ?>"
                      data-duracion="<?= htmlspecialchars($m['duracionMeses']) ?>"
                      data-descripcion="<?= htmlspecialchars($m['descripcion']) ?>"
                      data-beneficios="<?= htmlspecialchars($m['beneficios']) ?>">
                      Editar
                    </button>
                    <form action="/GestiFit/src/php/eliminarMembresia.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar esta membresía?');">
                      <input type="hidden" name="idMembresia" value="<?= htmlspecialchars($m['idMembresia']) ?>">
                      <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center">No hay membresías registradas.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="copyright py-3 text-center text-white bg-dark mt-auto">
    <div class="container">
      <small>&copy; 2025 GestiFit. Todos los derechos reservados.</small>
    </div>
  </div>

  <div class="back-to-top">
    <a href="#"><i class="fa fa-angle-up"></i></a>
  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/GestiFit/lib/wow/wow.min.js"></script>
  <script src="/GestiFit/lib/easing/easing.min.js"></script>
  <script src="/GestiFit/lib/waypoints/waypoints.min.js"></script>
  <script src="/GestiFit/lib/owlcarousel/owl.carousel.min.js"></script>
  <script async src="//www.instagram.com/embed.js"></script>
  <script src="/GestiFit/public/js/bootstrap.bundle.min.js"></script>
  <script src="/GestiFit/public/js/main.js"></script>
  <script src="/GestiFit/public/js/admin.js"></script>

  <!-- Modal Agregar Membresía -->
  <div class="modal fade" id="modalAgregarMembresia" tabindex="-1" aria-labelledby="modalAgregarMembresiaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form action="/GestiFit/src/php/agregarMembresia.php" method="POST" class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="modalAgregarMembresiaLabel">Agregar Nueva Membresía</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body row">
          <div class="mb-3 col-md-6">
            <label for="nombre_nueva" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="nombre_nueva" required>
          </div>
          <div class="mb-3 col-md-6">
            <label for="costo_nuevo" class="form-label">Costo</label>
            <input type="number" step="0.01" class="form-control" name="costo" id="costo_nuevo" required>
          </div>
          <div class="mb-3 col-md-6">
            <label for="duracion_nueva" class="form-label">Duración (meses)</label>
            <input type="number" class="form-control" name="duracionMeses" id="duracion_nueva" required>
          </div>
          <div class="mb-3 col-md-6">
            <label for="descripcion_nueva" class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" id="descripcion_nueva" required></textarea>
          </div>
          <div class="mb-3 col-md-12">
            <label for="beneficios_nuevos" class="form-label">Beneficios (separados por comas)</label>
            <textarea class="form-control" name="beneficios" id="beneficios_nuevos" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Registrar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Editar Membresía -->
  <div class="modal fade" id="modalEditarMembresia" tabindex="-1" aria-labelledby="modalEditarMembresiaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form action="/GestiFit/src/php/editarMembresia.php" method="POST" class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalEditarMembresiaLabel">Editar Membresía</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body row">
          <input type="hidden" name="idMembresia" id="modal_idMembresia">
          <div class="mb-3 col-md-6">
            <label for="modal_nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="modal_nombre" required>
          </div>
          <div class="mb-3 col-md-6">
            <label for="modal_costo" class="form-label">Costo</label>
            <input type="number" step="0.01" class="form-control" name="costo" id="modal_costo" required>
          </div>
          <div class="mb-3 col-md-6">
            <label for="modal_duracion" class="form-label">Duración (meses)</label>
            <input type="number" class="form-control" name="duracionMeses" id="modal_duracion" required>
          </div>
          <div class="mb-3 col-md-6">
            <label for="modal_descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" id="modal_descripcion" required></textarea>
          </div>
          <div class="mb-3 col-md-12">
            <label for="modal_beneficios" class="form-label">Beneficios (separados por comas)</label>
            <textarea class="form-control" name="beneficios" id="modal_beneficios" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modal = new bootstrap.Modal(document.getElementById('modalEditarMembresia'));

      document.querySelectorAll('.btnEditarMembresia').forEach(btn => {
        btn.addEventListener('click', () => {
          document.getElementById('modal_idMembresia').value = btn.dataset.id;
          document.getElementById('modal_nombre').value = btn.dataset.nombre;
          document.getElementById('modal_costo').value = btn.dataset.costo;
          document.getElementById('modal_duracion').value = btn.dataset.duracion;
          document.getElementById('modal_descripcion').value = btn.dataset.descripcion;
          document.getElementById('modal_beneficios').value = btn.dataset.beneficios;

          modal.show();
        });
      });
    });
  </script>

  

</body>
</html>