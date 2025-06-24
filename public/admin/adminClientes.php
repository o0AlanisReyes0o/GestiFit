<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

$nombreFiltro = $_GET['nombre'] ?? '';
$membresiaFiltro = $_GET['membresia'] ?? '';

try {
  $sql = "SELECT 
            u.idUsuario,
            u.nombre,
            u.apellidoPaterno,
            u.apellidoMaterno,
            u.email,
            u.telefono,
            m.idMembresia,               -- Aquí agregado
            m.nombre AS membresia,
            um.fechaInicio AS fecha_inicio,
            um.fechaFin AS fecha_fin
          FROM usuario u
          LEFT JOIN usuariomembresia um ON u.idUsuario = um.idUsuario
          LEFT JOIN membresia m ON um.idMembresia = m.idMembresia
          WHERE u.tipo = 'cliente'";

  $params = [];

  if (!empty($nombreFiltro)) {
    $sql .= " AND CONCAT(u.nombre, ' ', u.apellidoPaterno, ' ', u.apellidoMaterno) LIKE :nombre";
    $params[':nombre'] = '%' . $nombreFiltro . '%';
  }

  if (!empty($membresiaFiltro)) {
    $sql .= " AND m.nombre LIKE :membresia";
    $params[':membresia'] = '%' . $membresiaFiltro . '%';
  }

  $sql .= " ORDER BY u.idUsuario";

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  error_log("Error al obtener usuarios: " . $e->getMessage());
  $usuarios = [];
}

// Obtener membresías activas
$stmtM = $pdo->prepare("SELECT idMembresia, nombre FROM Membresia");
$stmtM->execute();
$membresias = $stmtM->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <title>GestiFit - Admin Clientes</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta content="" name="keywords" />
  <meta content="" name="description" />

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
  <!-- Spinner Start -->
  <div id="spinner"
    class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
  <!-- Spinner End -->

  <!-- Navbar & Hero Start -->
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
                  <a href="mailto:info@manicomiogym.com" class="text-muted small"><i
                      class="fa fa-clock text-primary me-2"></i>Lunes - Sabado: 8.00 am-7.00 pm</a>
                </div>
              </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
              <div class="d-flex justify-content-end">
                <div class="d-flex pe-3">
                  <a class="btn p-0 text-primary me-3"
                    href="https://www.instagram.com/elmanicomiogym?igsh=MXB3eHBkdjFjYXJleQ=="><i
                      class="fab fa-instagram"></i></a>
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
                <a href="/GestiFit/public/admin/admin.php" class="nav-item nav-link">
                  <i class="fa fa-home me-2"></i>Principal
                </a>
                <a href="/GestiFit/public/admin/adminClientes.php" class="nav-item nav-link">
                  <i class="fa fa-users me-2"></i>Clientes</a>
                <a href="/GestiFit/public/admin/adminMembresias.php" class="nav-item nav-link">
                  <i class="fa fa-user-tie me-2"></i>Membresias</a>
                <a href="/GestiFit/public/admin/adminInstructores.php" class="nav-item nav-link">
                  <i class="fa fa-dumbbell me-2"></i>Instructores</a>
                <a href="/GestiFit/public/admin/adminClases.php" class="nav-item nav-link">
                  <i class="fa fa-id-card me-2"></i>Clases</a>
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
  <!-- Navbar & Hero End -->

  <div class="align-items-center" style="margin-top: 50px;">
    <h2 class="mb-4">¿Deseas hacer cambios en Clientes?</h2>
  </div>

  <div class="container-fluid" style="margin-top: 50px;">
    <div class="main-content container py-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary mb-4">Clientes registrados</h3>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarCliente">
          Nuevo Registro
        </button>

      </div>

      <!-- 🔍 Filtros -->
      <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
          <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre completo"
            value="<?= htmlspecialchars($nombreFiltro) ?>">
        </div>
        <div class="col-md-4">
          <input type="text" name="membresia" class="form-control" placeholder="Buscar por membresía"
            value="<?= htmlspecialchars($membresiaFiltro) ?>">
        </div>
        <div class="col-md-4 d-flex">
          <button type="submit" class="btn btn-primary me-2">Filtrar</button>
          <a href="adminClientes.php" class="btn btn-secondary">Limpiar</a>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Membresía</th>
              <th>Inicio</th>
              <th>Fin</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($usuarios)): ?>
              <?php foreach ($usuarios as $u): ?>
                <tr>
                  <td><?= htmlspecialchars($u['idUsuario']) ?></td>
                  <td><?= htmlspecialchars("{$u['nombre']} {$u['apellidoPaterno']} {$u['apellidoMaterno']}") ?></td>
                  <td><?= htmlspecialchars($u['email']) ?></td>
                  <td><?= htmlspecialchars($u['telefono']) ?></td>
                  <td><?= htmlspecialchars($u['membresia'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($u['fecha_inicio'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($u['fecha_fin'] ?? 'N/A') ?></td>
                  <td>
                    <button
                      type="button"
                      class="btn btn-sm btn-secondary btnEditarUsuario"
                      data-id="<?= htmlspecialchars($u['idUsuario']) ?>"
                      data-nombre="<?= htmlspecialchars($u['nombre']) ?>"
                      data-apellido-paterno="<?= htmlspecialchars($u['apellidoPaterno']) ?>"
                      data-apellido-materno="<?= htmlspecialchars($u['apellidoMaterno']) ?>"
                      data-email="<?= htmlspecialchars($u['email']) ?>"
                      data-telefono="<?= htmlspecialchars($u['telefono']) ?>"
                      data-membresiaid="<?= htmlspecialchars($u['idMembresia'] ?? '') ?>">
                      Editar
                    </button>
                    <form method="post" action="/GestiFit/public/php/eliminarUsuario.php" style="display:inline;" onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?');">
                      <input type="hidden" name="idUsuario" value="<?= $u['idUsuario'] ?>">
                      <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="text-center">No hay usuarios registrados.</td>
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
  <!-- Copyright End -->

  <!-- Back to Top -->
  <div class="back-to-top">
    <a href="#"><i class="fa fa-angle-up"></i></a>
  </div>

  <!-- JavaScript Libraries -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/GestiFit/lib/wow/wow.min.js"></script>
  <script src="/GestiFit/lib/easing/easing.min.js"></script>
  <script src="/GestiFit/lib/waypoints/waypoints.min.js"></script>
  <script src="/GestiFit/lib/owlcarousel/owl.carousel.min.js"></script>
  <!-- Script necesario para que se renderice el embed -->
  <script async src="//www.instagram.com/embed.js"></script>

  <!-- Template Javascript -->
  <script src="/GestiFit/public/js/main.js"></script>
  <script src="/GestiFit/public/js/admin.js"></script>

  <!-- Modal Editar Usuario -->
  <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="formEditarUsuario" method="POST" action="/GestiFit/public/php/editarUsuario.php">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body row">
            <input type="hidden" name="idUsuario" id="modal_idUsuario" />

            <div class="mb-3 col-md-6">
              <label for="modal_nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="modal_nombre" name="nombre" required />
            </div>

            <div class="mb-3 col-md-6">
              <label for="modal_apellidoPaterno" class="form-label">Apellido Paterno</label>
              <input type="text" class="form-control" id="modal_apellidoPaterno" name="apellidoPaterno" required />
            </div>

            <div class="mb-3 col-md-6">
              <label for="modal_apellidoMaterno" class="form-label">Apellido Materno</label>
              <input type="text" class="form-control" id="modal_apellidoMaterno" name="apellidoMaterno" />
            </div>

            <div class="mb-3 col-md-6">
              <label for="modal_email" class="form-label">Email</label>
              <input type="email" class="form-control" id="modal_email" name="email" required />
            </div>

            <div class="mb-3 col-md-6">
              <label for="modal_telefono" class="form-label">Teléfono</label>
              <input type="text" class="form-control" id="modal_telefono" name="telefono" />
            </div>

            <div class="mb-3 col-md-6">
              <label for="modal_membresia" class="form-label">Membresía</label>
              <select class="form-control" id="modal_membresia" name="membresia">
                <option value="">-- Sin membresía --</option>
                <?php foreach ($membresias as $m): ?>
                  <option value="<?= htmlspecialchars($m['idMembresia']) ?>"
                    <?= (isset($membresiaActual) && $m['idMembresia'] == $membresiaActual) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($m['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              </select>

            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Agregar Cliente -->

  <div class="modal fade" id="modalAgregarCliente" tabindex="-1" aria-labelledby="modalAgregarClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="formAgregarCliente" method="POST" action="/GestiFit/public/php/agregarCliente.php">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="modalAgregarClienteLabel">Agregar Nuevo Cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body row">
            <?php if (isset($_GET['error'])): ?>
              <div class="alert alert-danger">
                <?php
                switch ($_GET['error']) {
                  case 'usuario':
                    echo "⚠️ El nombre de usuario ya está en uso.";
                    break;
                  case 'email':
                    echo "⚠️ El correo electrónico ya está registrado.";
                    break;
                  default:
                    echo "⚠️ Error desconocido.";
                }
                ?>
              </div>
            <?php endif; ?>

            <div class="mb-3 col-md-6">
              <label for="nombre" class="form-label">Nombre *</label>
              <input type="text" class="form-control" id="nombre" name="nombre" required />
            </div>
            <div class="mb-3 col-md-6">
              <label for="apellidoPaterno" class="form-label">Apellido Paterno *</label>
              <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" required />
            </div>
            <div class="mb-3 col-md-6">
              <label for="apellidoMaterno" class="form-label">Apellido Materno *</label>
              <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" required />
            </div>
            <div class="mb-3 col-md-6">
              <label for="edad" class="form-label">Edad *</label>
              <input type="text" class="form-control" id="edad" name="edad" min="0" max="120" required />
            </div>
            <div class="mb-3 col-md-6">
              <label for="usuario" class="form-label">Usuario *</label>
              <input type="text" class="form-control" id="usuario" name="usuario" required />
            </div>
            <div class="mb-3 col-md-6">
              <label for="contrasena" class="form-label">Contraseña *</label>
              <input type="password" class="form-control" id="contrasena" name="contrasena" required />
            </div>
            <div class="mb-3 col-6">
              <label for="direccion" class="form-label">Dirección *</label>
              <input type="text" class="form-control" id="direccion" name="direccion" rows="2" required></input>
            </div>
            <div class="mb-3 col-md-6">
              <label for="agregar_membresia" class="form-label">Membresía</label>
              <select class="form-control" id="agregar_membresia" name="membresia">
                <option value="">-- Sin membresía --</option>
                <?php foreach ($membresias as $m): ?>
                  <option value="<?= htmlspecialchars($m['idMembresia']) ?>">
                    <?= htmlspecialchars($m['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3 col-md-6">
              <label for="email" class="form-label">Email *</label>
              <input type="email" class="form-control" id="email" name="email" required />
            </div>
            <div class="mb-3 col-md-6">
              <label for="telefono" class="form-label">Teléfono *</label>
              <input type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9+\-\s]{7,15}" required />
              <div class="form-text">Ejemplo: +52 55 1234 5678</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Agregar Cliente</button>
          </div>
        </div>
      </form>
    </div>
  </div>


  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var editarModal = new bootstrap.Modal(document.getElementById('editarUsuarioModal'), {});

      document.querySelectorAll('.btnEditarUsuario').forEach(function(btn) {
        btn.addEventListener('click', function() {
          document.getElementById('modal_idUsuario').value = this.dataset.id;
          document.getElementById('modal_nombre').value = this.dataset.nombre;
          document.getElementById('modal_apellidoPaterno').value = this.dataset.apellidoPaterno;
          document.getElementById('modal_apellidoMaterno').value = this.dataset.apellidoMaterno;
          document.getElementById('modal_email').value = this.dataset.email;
          document.getElementById('modal_telefono').value = this.dataset.telefono;

          // Cargar la membresía seleccionada en el select
          var membresiaSelect = document.getElementById('modal_membresia');
          membresiaSelect.value = this.dataset.membresiaid || '';

          editarModal.show();
        });
      });
    });
  </script>

  <?php if (isset($_GET['error'])): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const modal = new bootstrap.Modal(document.getElementById('modalAgregarCliente'));
        modal.show();
      });
    </script>
  <?php endif; ?>


</body>

</html>