<?php  
session_start();
// Verificación de permisos más segura
$tienellave = isset($_SESSION['tipo']) && in_array($_SESSION['tipo'], [1, 2, 3]);
if (!$tienellave) {
    header("Location: formulario_menu_inventario.html");
    exit();
}

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

// Constantes
$activado = 1;
$cero = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta - Editar Alias/Numeración</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="fondoresponsive.css">
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- jQuery UI -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
  <!-- ddSlick -->
  <script src="js/jquery.ddslick.min.js"></script>
  <style>
    .card {
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }
    .card:hover {
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    .table-responsive {
      max-height: 600px;
      overflow-y: auto;
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
    .action-btn {
      min-width: 40px;
    }
    .number-input {
      width: 80px;
      text-align: center;
    }
    .select-all-checkbox {
      margin-right: 10px;
    }
    .alias-selector {
      min-width: 250px;
    }
  </style>
</head>
<body class="bg-light">
  <!-- Navbar con Bootstrap 5 -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="formulario_menu_principal.html">TecnoPresta</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="inventario_mantenimiento.php">
              <i class="bi bi-box-seam"></i> Inventario
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
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3><i class="bi bi-person-circle me-2"></i>Usuario: <?php echo htmlspecialchars($lognombre); ?></h3>
      <div>
        <a href="ayuda.html#eana" class="btn btn-sm btn-outline-primary me-2">
          <i class="bi bi-question-circle"></i> Ayuda
        </a>
        <a href="contactenos.php?rep=Error en Editar Alias y Numeración" class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-exclamation-triangle"></i> Reportar
        </a>
      </div>
    </div>

    <!-- Card principal -->
    <div class="card">
      <div class="card-body">
        <h3 class="card-title mb-4"><i class="bi bi-tags me-2"></i>Editar Alias y Numeración de Activos</h3>
        
        <!-- Búsqueda -->
        <div class="search-box mb-4">
          <i class="bi bi-search"></i>
          <input type="text" id="FiltrarContenido" class="form-control" placeholder="Buscar por tipo de activo...">
        </div>

        <!-- Formulario -->
        <form action="actualizar_alias_distintas_cero.php" method="post">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>
                    <input type="checkbox" id="selectall" class="form-check-input select-all-checkbox">
                  </th>
                  <th>Activo</th>
                  <th>Marca</th>
                  <th>Modelo</th>
                  <th>Color</th>
                  <th>Placa</th>
                  <th>Serial</th>
                  <th>Alias Actual</th>
                  <th>Número</th>
                  <th class="alias-selector">
                    <select id="myDropdown" name="myDropdown" class="form-select">
                      <option value="0" data-imagesrc="img/Alias-000.png" data-description="Activo sin alias">Sacar del alias</option>   
                      <?php  
                        $regres = mysqli_query($link, "SELECT * FROM t_alias WHERE codigo = '".mysqli_real_escape_string($link, $logcodigo)."' ORDER BY alias") or die(mysqli_error($link));
                        while ($regr = mysqli_fetch_array($regres)) {   
                      ?>
                      <option value="<?php echo htmlspecialchars($regr['alias_id']); ?>" 
                              data-imagesrc="img/alias/<?php echo htmlspecialchars($regr['alias_imagen']); ?>"
                              data-description="Alias de un activo">
                        <?php echo htmlspecialchars($regr['alias']); ?>
                      </option>
                      <?php } ?>       
                    </select>
                    <input type="hidden" id="id_alias" name="id_alias">
                  </th>

                </tr>
              </thead>
              <tbody class="BusquedaRapida">
                <?php
                $consulta = mysqli_query($link, "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, 
                                                Tp.id_placa, Tp.placa, Tp.serial, Tp.codigo, Tp.activo, 
                                                Tp.numero_activo, Tp.alias_id, Tz.alias
                                                FROM t_activo Ta
                                                INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
                                                INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
                                                INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
                                                INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag
                                                INNER JOIN t_alias Tz ON Tp.alias_id = Tz.alias_id
                                                WHERE Tp.codigo = '".mysqli_real_escape_string($link, $logcodigo)."' 
                                                AND Tp.activo = '".$activado."' 
                                                AND Tp.alias_id != '".$cero."'
                                                ORDER BY Tg.clase ASC") or die(mysqli_error($link));

                while ($activos = mysqli_fetch_array($consulta)) { ?>
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
                  <td><?php echo htmlspecialchars($activos['alias']); ?></td>
                  <td>
                    <input type="number" class="form-control number-input" 
                           name="numeracion<?php echo htmlspecialchars($activos['id_placa']); ?>" 
                           value="<?php echo htmlspecialchars($activos['numero_activo']); ?>">
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

  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Seleccionar/deseleccionar todos los checkboxes
    $(document).ready(function() {
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

      // Configuración del dropdown con imágenes
      $('#myDropdown').ddslick({
        onSelected: function(data){
          $('#id_alias').val(data.selectedData.value); 
        },
        imagePosition: "left",
        selectText: "Seleccione un alias",
        defaultSelectedIndex: 0
      });
    });
  </script>
</body>
</html>