<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

try {
  $stmt = $pdo->prepare("SELECT * FROM membresia");
  $stmt->execute();
  $membresias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  error_log("Error al obtener membresías: " . $e->getMessage());
  $membresias = [];
}
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
  <nav class="container-fluid header-top">
    <div class="nav-shaps-2"></div>
    <div class="container d-flex align-items-center">
      <div class="d-flex align-items-center h-100">
        <a href="#" class="navbar-brand" style="height: 125px;">
          <h1 class="text-primary mb-0">
            <img src="/GestiFit/public/img/logo_gestifit_cuadrado-nofondo.png" class="img-fluid" width="70"
              height="70" />
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
                <div class="pe-0">
                  <a href="mailto:info@manicomiogym.com" class="text-muted small"><i class="fa fa-clock text-primary me-2"></i>Lunes - Sabado: 8.00 am-7.00 pm</a>
                </div>
              </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
              <div class="d-flex justify-content-end">
                <div class="d-flex pe-3">
                  <a class="btn p-0 text-primary me-3" href="https://www.instagram.com/elmanicomiogym"><i class="fab fa-instagram"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="nav-bar px-0 py-lg-0" style="height: 80px;">
          <nav class="navbar navbar-expand-lg navbar-light d-flex justify-content-lg-end">
            <a href="#" class="navbar-brand-2">
              <h1 class="text-primary mb-0"><i class="fas fa-hand-rock me-2"></i>GestiFit</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
              <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
              <div class="navbar-nav mx-0 mx-lg-auto">
                <a href="/GestiFit/public/admin/admin.php" class="nav-item nav-link fa fa-home active"> Principal</a>
                <a href="/GestiFit/public/admin/adminClientes.php" class="nav-item nav-link fa fa-users"> Clientes</a>
                <a href="/GestiFit/public/admin/adminMembresias.php" class="nav-item nav-link fa fa-user-tie"> Membresias</a>
                <a href="/GestiFit/public/admin/adminInstructores.php" class="nav-item nav-link fa fa-dumbbell"> Instructores</a>
                <a href="/GestiFit/public/admin/adminClases.php" class="nav-item nav-link fa fa-id-card"> Clases</a>
                <div class="nav-btn ps-3">
                  <a href="login.html" class="btn btn-primary py-2 px-4 ms-0 ms-lg-3"> <span>Cerrar sesión</span></a>
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
                  <td>
                    <button type="button" class="btn btn-sm btn-secondary btnEditarMembresia"
                      data-id="<?= $m['idMembresia'] ?>"
                      data-nombre="<?= htmlspecialchars($m['nombre']) ?>"
                      data-costo="<?= htmlspecialchars($m['costo']) ?>"
                      data-duracion="<?= htmlspecialchars($m['duracionMeses']) ?>">
                      Editar
                    </button>
                    <form action="/GestiFit/public/php/eliminarMembresia.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar esta membresía?');">
                      <input type="hidden" name="idMembresia" value="<?= htmlspecialchars($m['idMembresia']) ?>">
                      <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center">No hay membresías registradas.</td>
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
      <form action="/GestiFit/public/php/agregarMembresia.php" method="POST" class="modal-content">
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
      <form action="/GestiFit/public/php/editarMembresia.php" method="POST" class="modal-content">
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

          modal.show();
        });
      });
    });
  </script>

</body>

</html>