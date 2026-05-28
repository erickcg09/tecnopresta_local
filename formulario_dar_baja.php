<?php  
session_start();
// Verificación de permisos más segura
$tienellave = isset($_SESSION['tipo']) && in_array($_SESSION['tipo'], [1, 2, 3, 4]);
if (!$tienellave) {
    header("Location: formulario_menu_inventario.html");
    exit();
}

require_once("variablesemail.php");
include "class.phpmailer.php";
include "class.smtp.php";
require_once("conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    die("Error de conexión a MySQL: " . mysqli_connect_error());
}

if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres utf8");
}

// Datos de sesión
$logusuario = $_SESSION['cedula'] ?? '';
$lognombre = $_SESSION['nombre'] ?? '';
$logtipo = $_SESSION['tipo'] ?? '';
$logcodigo = $_SESSION['codigo'] ?? '';
$logcorreo = $_SESSION['correomep'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta - Control de Bajas</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <!-- Alertify CSS -->
  <link rel="stylesheet" href="alertifyjs/css/alertify.min.css">
  <link rel="stylesheet" href="alertifyjs/css/themes/default.min.css">
  <link rel="stylesheet" href="fondoresponsive.css">
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">
  <style>
    .card {
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }
    .card:hover {
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    .search-box {
      position: relative;
    }
    .search-box .bi {
      position: absolute;
      top: 10px;
      left: 10px;
      color: #6c757d;
    }
    .search-box input {
      padding-left: 35px;
    }
    .table-responsive {
      max-height: 600px;
      overflow-y: auto;
    }
    .status-badge {
      font-size: 0.8rem;
      padding: 0.35em 0.65em;
    }
    .badge-active {
      background-color: #198754;
    }
    .badge-inactive {
      background-color: #dc3545;
    }
    .form-check-input:checked {
      background-color: #0d6efd;
      border-color: #0d6efd;
    }
    .user-info {
      background-color: #f8f9fa;
      border-radius: 5px;
      padding: 15px;
      margin-bottom: 20px;
    }
    .asset-img {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
    }
  </style>
</head>
<body class="bg-light">
  <!-- Navbar con Bootstrap 5 -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="formulario_menu_principal.html">
      <i class="bi bi-building"></i> Tecnopresta
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="inventario_activo.php">
            <i class="bi bi-arrow-left-circle"></i> Volver al Inventario
          </a>
        </li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="bi bi-person-circle"></i> <?php echo $lognombre; ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="gameover.php">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

  <div class="container py-4">
    <!-- Información de usuario -->
    <div class="user-info">
      <div class="d-flex justify-content-between align-items-center">
        <h5><i class="bi bi-person-circle me-2"></i>Usuario: <?php echo htmlspecialchars($lognombre); ?></h5>
        <div>
          <a href="ayuda.html#db" class="btn btn-sm btn-outline-primary me-2">
            <i class="bi bi-question-circle"></i> Ayuda
          </a>
          <a href="contactenos.php?rep=Error en Control de Activos Retirados" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-exclamation-triangle"></i> Reportar
          </a>
        </div>
      </div>
    </div>

    <!-- Card principal -->
    <div class="card">
      <div class="card-body">
        <h3 class="card-title mb-4"><i class="bi bi-clipboard-check me-2"></i>Control de Activos Retirados</h3>
        
        <!-- Búsqueda -->
        <div class="search-box mb-4">
          <i class="bi bi-search"></i>
          <input type="text" id="FiltrarContenido" class="form-control" placeholder="Buscar por tipo de activo, placa o serial...">
        </div>

        <!-- Formulario -->
        <form action="actualizar_dar_baja.php" method="post" id="bajaForm">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th width="40px">
                    <input type="checkbox" id="selectall" class="form-check-input">
                  </th>
                  <th>Activo</th>
                  <th>Marca</th>
                  <th>Modelo</th>
                  <th>Color</th>
                  <th>Placa</th>
                  <th>Serial</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody class="BusquedaRapida">
                <?php
                $consulta = mysqli_query($link, "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, 
                                                Tp.id_placa, Tp.placa, Tp.serial, Tp.activo, Tp.codigo
                                                FROM t_activo Ta
                                                INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
                                                INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
                                                INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
                                                INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
                                                WHERE Tp.codigo = '".mysqli_real_escape_string($link, $logcodigo)."'
                                                ORDER BY Tp.placa ASC") or die(mysqli_error($link));

                while ($activos = mysqli_fetch_array($consulta)) { 
                  $idactivo = $activos['activo'];
                ?>
                <tr>
                  <td>
                    <input type="checkbox" class="form-check-input selectall" name="idsplacas[]" value="<?php echo htmlspecialchars($activos['id_placa']); ?>"/>
                  </td>
                  <td><?php echo htmlspecialchars($activos['clase']); ?></td>
                  <td><?php echo htmlspecialchars($activos['marca']); ?></td>
                  <td><?php echo htmlspecialchars($activos['modelo']); ?></td>
                  <td><?php echo htmlspecialchars($activos['color']); ?></td>
                  <td><code><?php echo htmlspecialchars($activos['placa']); ?></code></td>
                  <td><code><?php echo htmlspecialchars($activos['serial']); ?></code></td>
                  <td>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="activado<?php echo htmlspecialchars($activos['id_placa']); ?>" 
                             id="activo<?php echo htmlspecialchars($activos['id_placa']); ?>" 
                             value="1" <?php echo ($idactivo == "1") ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="activo<?php echo htmlspecialchars($activos['id_placa']); ?>">
                        <span class="badge bg-success status-badge">Activo</span>
                      </label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="activado<?php echo htmlspecialchars($activos['id_placa']); ?>" 
                             id="retirado<?php echo htmlspecialchars($activos['id_placa']); ?>" 
                             value="0" <?php echo ($idactivo == "0") ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="retirado<?php echo htmlspecialchars($activos['id_placa']); ?>">
                        <span class="badge bg-danger status-badge">Retirado</span>
                      </label>
                    </div>
                  </td>
                </tr>
                <?php }
                mysqli_close($link); ?>
              </tbody>
            </table>
          </div>
          
          <div class="d-grid mt-3">
            <button type="submit" class="btn btn-primary" name="btnActualizar">
              <i class="bi bi-save"></i> Guardar Cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Términos y condiciones (oculto) -->
  <div id="terminosCondiciones" style="display: none;">
    <h4>Ministerio de Educación Pública (MEP)</h4>
    <p>Copyright (c) 2020</p>
    <p>La presente Política del Sistema TecnoPresta establece los términos en que usa y protege la información que es proporcionada por sus usuarios al momento de utilizar este sitio web. El Ministerio de Educación Pública está comprometido con la seguridad de los datos de sus activos. Cuando le pedimos llenar los campos de información referente a la existencia y permanencia de los activos en la Institución, lo hacemos asegurando que es verdadero.</p>
    <p><strong>POR LO ANTERIOR AL ACEPTAR ESTOS TÉRMINOS DOY MI CONSENTIMIENTO DE REPORTAR MIS DATOS DE USUARIO AL DAR DE BAJA DEL INVENTARIO ALGÚN ACTIVO.</strong></p>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Alertify JS -->
  <script src="alertifyjs/alertify.min.js"></script>
  
  <script>
    $(document).ready(function() {
      // Seleccionar/deseleccionar todos los checkboxes
      $('#selectall').on('click', function() {
        $(".selectall").prop('checked', this.checked);
      });
      
      // Si se desmarca un checkbox individual, desmarcar el "select all"
      $(".selectall").on('click', function() {
        if (!this.checked) {
          $("#selectall").prop('checked', false);
        }
      });

      // Filtrado de contenido
      $('#FiltrarContenido').on('keyup', function() {
        var searchText = $(this).val().toLowerCase();
        $('.BusquedaRapida tr').each(function() {
          var rowText = $(this).text().toLowerCase();
          $(this).toggle(rowText.indexOf(searchText) > -1);
        });
      });

      // Mostrar términos y condiciones al enviar el formulario
      $('#bajaForm').on('submit', function(e) {
        e.preventDefault();
        
        // Verificar si hay elementos marcados como retirados
        var hasRetirados = false;
        $('input[type="radio"]:checked').each(function() {
          if ($(this).val() === "0") {
            hasRetirados = true;
            return false; // Salir del each
          }
        });
        
        if (hasRetirados) {
          // Mostrar términos y condiciones
          alertify.confirm($('#terminosCondiciones').html(), 
            function() {
              // Aceptar términos
              alertify.success('Términos aceptados');
              $('#bajaForm').off('submit').submit();
            }, 
            function() {
              // Rechazar términos
              alertify.error('Debe aceptar los términos para continuar');
            }
          ).set({
            labels: {ok:'Acepto', cancel: 'Rechazo'}, 
            padding: true,
            title: 'Términos y Condiciones'
          });
        } else {
          // No hay retirados, enviar formulario directamente
          $('#bajaForm').off('submit').submit();
        }
      });
    });
  </script>
</body>
</html>

