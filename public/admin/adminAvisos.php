<?php
// Usar rutas absolutas para mayor seguridad
require_once __DIR__ . '/../../src/conexion.php';

// Iniciar sesión antes de cualquier verificación
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['idUsuario'])) {
    header("Location: /GestiFit/public/index.html");
    exit;
}

// Obtener información del usuario
$userId = $_SESSION['idUsuario'];
$query = "SELECT * FROM usuario WHERE idUsuario = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['tipo'] != 'administrador') {
    session_destroy();
    header("Location: /GestiFit/public/login.html");
    exit;
}

// Obtener lista de avisos
$queryAvisos = "SELECT * FROM configuraciones WHERE clave LIKE 'aviso_%'";
$avisos = $pdo->query($queryAvisos)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>GestiFit - Admin Avisos</title>
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
    <link href="/GestiFit/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="/GestiFit/public/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="/GestiFit/public/css/stylesAdmin.css" rel="stylesheet" />
    <link href="/GestiFit/public/css/style.css" rel="stylesheet" />
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
              <a href="/GestiFit/public/admin/admin.php" class="nav-item nav-link">
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
              <a href="/GestiFit/public/admin/adminAvisos.php" class="nav-item nav-link active">
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
<!-- Navbar & Hero End -->

<div class="align-items-center" style="margin-top: 50px;">
    <h2 class="mb-4 text-center mt-5">Administración de Avisos</h2>
</div>

<div class="container-fluid mt-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary mb-4">Avisos del Sistema</h2>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarAviso">
                <i class="fas fa-plus me-1"></i>Añadir Aviso
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Clave</th>
                        <th>Descripción</th>
                        <th>Contenido</th>
                        <th>Editable</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($avisos as $aviso): ?>
                        <tr>
                            <td><?= htmlspecialchars($aviso['id_config']) ?></td>
                            <td><?= htmlspecialchars($aviso['clave']) ?></td>
                            <td><?= htmlspecialchars($aviso['descripcion']) ?></td>
                            <td><?= htmlspecialchars(substr($aviso['valor'], 0, 50)) . (strlen($aviso['valor']) > 50 ? '...' : '') ?></td>
                            <td>
                                <span class="badge <?= $aviso['editable'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $aviso['editable'] ? 'Sí' : 'No' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($aviso['editable']): ?>
                                    <button class="btn btn-sm btn-secondary btnEditarAviso"
                                        data-id="<?= $aviso['id_config'] ?>"
                                        data-clave="<?= htmlspecialchars($aviso['clave']) ?>"
                                        data-valor="<?= htmlspecialchars($aviso['valor']) ?>"
                                        data-descripcion="<?= htmlspecialchars($aviso['descripcion']) ?>"
                                        data-editable="<?= $aviso['editable'] ? '1' : '0' ?>"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarAviso">
                                        Editar
                                    </button>

                                    <form action="/GestiFit/src/php/eliminarAviso.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar este aviso?');">
                                        <input type="hidden" name="id_config" value="<?= $aviso['id_config'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">No editable</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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

<!-- Modal Agregar Aviso -->
<div class="modal fade" id="modalAgregarAviso" tabindex="-1" aria-labelledby="modalAgregarAvisoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/GestiFit/src/php/agregarAviso.php" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalAgregarAvisoLabel">Agregar Nuevo Aviso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body row">
                    <div class="mb-3 col-md-6">
                        <label for="clave" class="form-label">Clave</label>
                        <input type="text" class="form-control" id="clave" name="clave" required placeholder="aviso_nombre_descriptivo">
                        <small class="text-muted">Debe comenzar con 'aviso_' y ser único</small>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="editable" class="form-label">Editable</label>
                        <select class="form-select" id="editable" name="editable" required>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                        <small class="text-muted">Explica el propósito de este aviso</small>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="valor" class="form-label">Contenido del Aviso</label>
                        <textarea class="form-control" id="valor" name="valor" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Aviso -->
<div class="modal fade" id="modalEditarAviso" tabindex="-1" aria-labelledby="modalEditarAvisoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/GestiFit/src/php/editarAviso.php" method="POST">
                <input type="hidden" name="id_config" id="edit_id_config">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEditarAvisoLabel">Editar Aviso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body row">
                    <div class="mb-3 col-md-6">
                        <label for="edit_clave" class="form-label">Clave</label>
                        <input type="text" class="form-control" id="edit_clave" name="clave" required readonly>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="edit_editable" class="form-label">Editable</label>
                        <select class="form-select" id="edit_editable" name="editable" required>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="edit_descripcion" class="form-label">Descripción</label>
                        <input type="text" class="form-control" id="edit_descripcion" name="descripcion" required>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="edit_valor" class="form-label">Contenido del Aviso</label>
                        <textarea class="form-control" id="edit_valor" name="valor" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar el botón de edición
        document.querySelectorAll('.btnEditarAviso').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const clave = this.dataset.clave;
                const valor = this.dataset.valor;
                const descripcion = this.dataset.descripcion;
                const editable = this.dataset.editable === '1';

                // Llenar el formulario de edición
                document.getElementById('edit_id_config').value = id;
                document.getElementById('edit_clave').value = clave;
                document.getElementById('edit_valor').value = valor;
                document.getElementById('edit_descripcion').value = descripcion;
                document.getElementById('edit_editable').value = editable ? '1' : '0';
            });
        });
    });
</script>
</body>
</html>