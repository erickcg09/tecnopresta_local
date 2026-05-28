<?php  
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3);
if ($tienellave == false){
    echo '<script language = javascript>
    alert("No tienes permisos")
    self.location = "formulario_menu_inventario.html"
    </script>';
}
require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
$activado = 1;
$cero = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Agrupar Activos</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }
    .table-responsive {
      overflow-x: auto;
    }
    .search-box {
      max-width: 400px;
    }
    .btn-save {
      min-width: 150px;
    }
    .nav-user-info {
      color: rgba(255,255,255,.75);
      padding: 0.5rem 1rem;
    }
    .table-hover tbody tr:hover {
      background-color: rgba(13, 110, 253, 0.1);
    }
    .number-input {
      max-width: 80px;
    }
    .select2-container--bootstrap5 .select2-selection {
      height: 38px;
    }
  </style>
</head>
<body>
  <!-- Navbar actualizado -->
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

  <!-- Contenedor principal -->
  <div class="container py-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold"><i class="bi bi-tags"></i> Agregar Alias y Numeración a los Activos</h2>
        <p class="text-muted mb-0">Usuario: <?php echo $lognombre; ?></p>
      </div>
      <div>
        <a href="ayuda.html#aan" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-question-circle"></i> Ayuda
        </a>
        <a href="contactenos.php?rep=Error en Agregar Alias y Numeración" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-exclamation-triangle"></i> Reportar Problema
        </a>
      </div>
    </div>

    <!-- Card principal -->
    <div class="card">
      <div class="card-body">
        <!-- Barra de búsqueda -->
        <div class="mb-4">
          <label for="FiltrarContenido" class="form-label">Buscar activos</label>
          <div class="input-group search-box">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese el tipo del activo">
          </div>
        </div>

        <!-- Formulario -->
        <form action="actualizar_alias_cero.php" method="post" id="mainForm">
          <input type="hidden" id="id_alias" name="id_alias">
          
          <!-- Selector de Alias -->
          <div class="row mb-4">
            <div class="col-md-6">
              <label for="aliasSelect" class="form-label">Seleccionar Alias:</label>
              <select class="form-select" id="aliasSelect" name="myDropdown" required>
                <option value="0">-- Seleccione un alias --</option>
                <?php  
                $regres=mysqli_query($link,"select * from t_alias WHERE codigo = '".$logcodigo."' order by alias") or die(mysqli_error($link));
                while ($regr=mysqli_fetch_array($regres)) {   
                  echo '<option value="'.$regr['alias_id'].'">'.$regr['alias'].'</option>';
                }   
                ?>       
              </select>
            </div>
          </div>

          <!-- Tabla de activos -->
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-primary">
                <tr>
                  <th width="50px">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="selectVisible">
                      <label class="form-check-label" for="selectVisible"></label>
                    </div>
                  </th>
                  <th>Activo</th>
                  <th>Marca</th>
                  <th>Modelo</th>
                  <th>Color</th>
                  <th>Placa</th>
                  <th>Serial</th>
                  <th>Número de Activo</th>
                </tr>
              </thead>
              <tbody class="BusquedaRapida">
                <?php
                $consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.codigo, Tp.activo, Tp.numero_activo, Tp.alias_id
                       FROM t_activo Ta
                       INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
                       INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
                       INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
                       INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag
                       WHERE Tp.prestar = 1 AND 
                       Tp.codigo = '".$logcodigo."' AND 
                       Tp.activo = '".$activado."' AND 
                       Tp.alias_id = '".$cero."'
                       ORDER BY Tp.placa ASC") or die(mysqli_error($link));

                while ($activos=mysqli_fetch_array($consulta)) { ?>
                <tr>
                  <td>
                    <div class="form-check">
                      <input class="form-check-input selectVisible" type="checkbox" name="idsplacas[]" value="<?php echo $activos['id_placa']?>">
                    </div>
                  </td>
                  <td><?php echo $activos['clase']?></td>
                  <td><?php echo $activos['marca']?></td>
                  <td><?php echo $activos['modelo']?></td>
                  <td><?php echo $activos['color']?></td>
                  <td><span class="badge bg-secondary"><?php echo $activos['placa']?></span></td>
                  <td><code><?php echo $activos['serial']?></code></td>
                  <td>
                    <input type="text" class="form-control form-control-sm number-input" name="numeracion<?php echo $activos['id_placa']; ?>" value="<?php echo $activos['numero_activo'];?>">
                  </td>
                </tr>
                <?php } 
                mysqli_close($link);	
                ?>
              </tbody>
            </table>
          </div>

          <!-- Botón de guardar -->
          <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button class="btn btn-primary btn-save" type="submit" name="btnActualizar">
              <i class="bi bi-save"></i> Guardar Cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  
  <!-- jQuery para mejor manejo de eventos -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <script>
  $(document).ready(function() {
    // Manejar selección de alias
    $('#aliasSelect').change(function() {
      $('#id_alias').val($(this).val());
    });

    // Búsqueda rápida - solo muestra/oculta filas
    $('#FiltrarContenido').keyup(function() {
      var ValorBusqueda = new RegExp($(this).val(), 'i');
      $('.BusquedaRapida tr').hide();
      $('.BusquedaRapida tr').filter(function() {
        return ValorBusqueda.test($(this).text());
      }).show();
    });

    // Seleccionar solo los checkboxes visibles
    $('#selectVisible').on('click', function() {
      var isChecked = $(this).prop('checked');
      $('.BusquedaRapida tr:visible .selectVisible').prop('checked', isChecked);
    });

    // Validación antes de enviar el formulario
    $('#mainForm').submit(function(e) {
      // Validar que se haya seleccionado un alias
      if($('#aliasSelect').val() == '0') {
        alert('Por favor seleccione un alias antes de continuar.');
        return false;
      }
      
      // Validar que se haya seleccionado al menos un activo
      if($('input[name="idsplacas[]"]:checked').length === 0) {
        alert('Por favor seleccione al menos un activo para actualizar.');
        return false;
      }
      
      // Validar que todos los números de activo sean válidos
      let valid = true;
      $('input[name^="numeracion"]').each(function() {
        const checkbox = $(this).closest('tr').find('input[name="idsplacas[]"]');
        if(checkbox.prop('checked')) {
          const value = $(this).val().trim();
          if(value === '' || isNaN(value)) {
            valid = false;
            $(this).addClass('is-invalid');
          } else {
            $(this).removeClass('is-invalid');
          }
        }
      });
      
      if(!valid) {
        alert('Por favor ingrese un número válido para todos los activos seleccionados.');
        return false;
      }
      
      return true;
    });
  });
  </script>
</body>
</html>