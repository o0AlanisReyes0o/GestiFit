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

// Obtener lista de rutinas
$queryRutinas = "SELECT * FROM rutinas";
$rutinas = $pdo->query($queryRutinas)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>GestiFit - Admin Rutinas</title>
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
    <style>
        .video-thumbnail {
            max-width: 100px;
            max-height: 60px;
            cursor: pointer;
        }
        .video-thumbnail:hover {
            opacity: 0.8;
        }
    </style>
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
              <a href="/GestiFit/public/admin/adminRutinas.php" class="nav-item nav-link active">
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
<!-- Navbar & Hero End -->

<div class="align-items-center" style="margin-top: 50px;">
    <h2 class="mb-4 text-center mt-5">Administración de Rutinas</h2>
</div>

<div class="container-fluid mt-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary mb-4">Rutinas Disponibles</h2>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarRutina">
                <i class="fas fa-plus me-1"></i>Añadir Rutina
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Nivel</th>
                        <th>Duración</th>
                        <th>Días/Semana</th>
                        <th>Objetivo</th>
                        <th>Video</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rutinas as $rutina): ?>
                        <tr>
                            <td><?= htmlspecialchars($rutina['id_rutina']) ?></td>
                            <td><?= htmlspecialchars($rutina['nombre_rutina']) ?></td>
                            <td><?= htmlspecialchars($rutina['nivel_rutina']) ?></td>
                            <td><?= htmlspecialchars($rutina['duracion_semanas']) ?> semanas</td>
                            <td><?= htmlspecialchars($rutina['dias_por_semana']) ?></td>
                            <td><?= htmlspecialchars($rutina['objetivo']) ?></td>
                            <td>
                                <?php if (!empty($rutina['video_url'])): ?>
                                    <img src="https://img.youtube.com/vi/<?= getYouTubeId($rutina['video_url']) ?>/default.jpg" 
                                         class="video-thumbnail" 
                                         onclick="window.open('<?= htmlspecialchars($rutina['video_url']) ?>', '_blank')"
                                         title="Ver video">
                                <?php else: ?>
                                    Sin video
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= $rutina['activa'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $rutina['activa'] ? 'Activa' : 'Inactiva' ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-secondary btnEditarRutina"
                                    data-id="<?= $rutina['id_rutina'] ?>"
                                    data-nombre="<?= htmlspecialchars($rutina['nombre_rutina']) ?>"
                                    data-nivel="<?= htmlspecialchars($rutina['nivel_rutina']) ?>"
                                    data-descripcion="<?= htmlspecialchars($rutina['descripcion']) ?>"
                                    data-duracion="<?= htmlspecialchars($rutina['duracion_semanas']) ?>"
                                    data-dias="<?= htmlspecialchars($rutina['dias_por_semana']) ?>"
                                    data-objetivo="<?= htmlspecialchars($rutina['objetivo']) ?>"
                                    data-equipamiento="<?= htmlspecialchars($rutina['equipamiento_necesario']) ?>"
                                    data-instrucciones="<?= htmlspecialchars($rutina['instrucciones']) ?>"
                                    data-video="<?= htmlspecialchars($rutina['video_url']) ?>"
                                    data-imagen="<?= htmlspecialchars($rutina['imagen_url']) ?>"
                                    data-activa="<?= $rutina['activa'] ? '1' : '0' ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalEditarRutina">
                                    Editar
                                </button>

                                <form action="/GestiFit/src/php/eliminarRutina.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar esta rutina?');">
                                    <input type="hidden" name="id_rutina" value="<?= $rutina['id_rutina'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
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

<!-- Modal Agregar Rutina -->
<div class="modal fade" id="modalAgregarRutina" tabindex="-1" aria-labelledby="modalAgregarRutinaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/GestiFit/src/php/agregarRutina.php" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalAgregarRutinaLabel">Agregar Nueva Rutina</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body row">
                    <div class="mb-3 col-md-6">
                        <label for="nombre_rutina" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre_rutina" name="nombre_rutina" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="nivel_rutina" class="form-label">Nivel</label>
                        <select class="form-select" id="nivel_rutina" name="nivel_rutina" required>
                            <option value="Principiante">Principiante</option>
                            <option value="Intermedio">Intermedio</option>
                            <option value="Avanzado">Avanzado</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="duracion_semanas" class="form-label">Duración (semanas)</label>
                        <input type="number" class="form-control" id="duracion_semanas" name="duracion_semanas" min="1" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="dias_por_semana" class="form-label">Días por semana</label>
                        <input type="number" class="form-control" id="dias_por_semana" name="dias_por_semana" min="1" max="7" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="objetivo" class="form-label">Objetivo</label>
                        <select class="form-select" id="objetivo" name="objetivo" required>
                            <option value="Perdida de peso">Pérdida de peso</option>
                            <option value="Ganancia muscular">Ganancia muscular</option>
                            <option value="Fuerza">Fuerza</option>
                            <option value="Resistencia">Resistencia</option>
                            <option value="Tonificación">Tonificación</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="equipamiento_necesario" class="form-label">Equipamiento necesario</label>
                        <input type="text" class="form-control" id="equipamiento_necesario" name="equipamiento_necesario">
                        <small class="text-muted">Separar por comas si hay múltiples elementos</small>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="instrucciones" class="form-label">Instrucciones</label>
                        <textarea class="form-control" id="instrucciones" name="instrucciones" rows="4" required></textarea>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="video_url" class="form-label">URL de video (YouTube)</label>
                        <input type="url" class="form-control" id="video_url" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="imagen_url" class="form-label">URL de imagen</label>
                        <input type="url" class="form-control" id="imagen_url" name="imagen_url" placeholder="https://...">
                    </div>
                    <div class="mb-3 col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="activa" name="activa" checked>
                            <label class="form-check-label" for="activa">Rutina activa</label>
                        </div>
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

<!-- Modal Editar Rutina -->
<div class="modal fade" id="modalEditarRutina" tabindex="-1" aria-labelledby="modalEditarRutinaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/GestiFit/src/php/editarRutina.php" method="POST">
                <input type="hidden" name="id_rutina" id="edit_id_rutina">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEditarRutinaLabel">Editar Rutina</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body row">
                    <div class="mb-3 col-md-6">
                        <label for="edit_nombre_rutina" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="edit_nombre_rutina" name="nombre_rutina" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="edit_nivel_rutina" class="form-label">Nivel</label>
                        <select class="form-select" id="edit_nivel_rutina" name="nivel_rutina" required>
                            <option value="Principiante">Principiante</option>
                            <option value="Intermedio">Intermedio</option>
                            <option value="Avanzado">Avanzado</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="edit_duracion_semanas" class="form-label">Duración (semanas)</label>
                        <input type="number" class="form-control" id="edit_duracion_semanas" name="duracion_semanas" min="1" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="edit_dias_por_semana" class="form-label">Días por semana</label>
                        <input type="number" class="form-control" id="edit_dias_por_semana" name="dias_por_semana" min="1" max="7" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="edit_objetivo" class="form-label">Objetivo</label>
                        <select class="form-select" id="edit_objetivo" name="objetivo" required>
                            <option value="Perdida de peso">Pérdida de peso</option>
                            <option value="Ganancia muscular">Ganancia muscular</option>
                            <option value="Fuerza">Fuerza</option>
                            <option value="Resistencia">Resistencia</option>
                            <option value="Tonificación">Tonificación</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="edit_equipamiento_necesario" class="form-label">Equipamiento necesario</label>
                        <input type="text" class="form-control" id="edit_equipamiento_necesario" name="equipamiento_necesario">
                        <small class="text-muted">Separar por comas si hay múltiples elementos</small>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="edit_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3" required></textarea>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="edit_instrucciones" class="form-label">Instrucciones</label>
                        <textarea class="form-control" id="edit_instrucciones" name="instrucciones" rows="4" required></textarea>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="edit_video_url" class="form-label">URL de video (YouTube)</label>
                        <input type="url" class="form-control" id="edit_video_url" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="edit_imagen_url" class="form-label">URL de imagen</label>
                        <input type="url" class="form-control" id="edit_imagen_url" name="imagen_url" placeholder="https://...">
                    </div>
                    <div class="mb-3 col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_activa" name="activa">
                            <label class="form-check-label" for="edit_activa">Rutina activa</label>
                        </div>
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
    // Función para extraer el ID de YouTube de una URL
    function getYouTubeId(url) {
        if (!url) return '';
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Manejar el botón de edición
        document.querySelectorAll('.btnEditarRutina').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;
                const nivel = this.dataset.nivel;
                const descripcion = this.dataset.descripcion;
                const duracion = this.dataset.duracion;
                const dias = this.dataset.dias;
                const objetivo = this.dataset.objetivo;
                const equipamiento = this.dataset.equipamiento;
                const instrucciones = this.dataset.instrucciones;
                const video = this.dataset.video;
                const imagen = this.dataset.imagen;
                const activa = this.dataset.activa === '1';

                // Llenar el formulario de edición
                document.getElementById('edit_id_rutina').value = id;
                document.getElementById('edit_nombre_rutina').value = nombre;
                document.getElementById('edit_nivel_rutina').value = nivel;
                document.getElementById('edit_descripcion').value = descripcion;
                document.getElementById('edit_duracion_semanas').value = duracion;
                document.getElementById('edit_dias_por_semana').value = dias;
                document.getElementById('edit_objetivo').value = objetivo;
                document.getElementById('edit_equipamiento_necesario').value = equipamiento;
                document.getElementById('edit_instrucciones').value = instrucciones;
                document.getElementById('edit_video_url').value = video;
                document.getElementById('edit_imagen_url').value = imagen;
                document.getElementById('edit_activa').checked = activa;
            });
        });
    });
</script>
</body>
</html>
<?php
// Función para extraer el ID de YouTube de una URL
function getYouTubeId($url) {
    if (empty($url)) return '';
    $regExp = '/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/';
    preg_match($regExp, $url, $match);
    return (isset($match[2]) && strlen($match[2]) === 11) ? $match[2] : '';
}
?>