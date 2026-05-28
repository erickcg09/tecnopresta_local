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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Activos para Préstamo</title>
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
    .radio-group {
      display: flex;
      gap: 10px;
      align-items: center;
    }
    .table-hover tbody tr:hover {
      background-color: rgba(13, 110, 253, 0.1);
    }
    .nav-user-info {
      color: rgba(255,255,255,.75);
      padding: 0.5rem 1rem;
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
        <h2 class="fw-bold"><i class="bi bi-clipboard-check"></i> Activos Destinados al Préstamo</h2>
        <p class="text-muted mb-0">Usuario: <?php echo $lognombre; ?></p>
      </div>
      <div>
        <a href="ayuda.html#adp" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-question-circle"></i> Ayuda
        </a>
        <a href="contactenos.php?rep=Error en Activos Destinados al Préstamo" class="btn btn-outline-secondary btn-sm">
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
            <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese el tipo del activo" onkeypress="return event.charCode != 39">
          </div>
        </div>

        <!-- Formulario -->
        <form action="actualizar_se_presta.php" method="post">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-primary">
                <tr>
                  <th width="50px">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="selectall">
                    </div>
                  </th>
                  <th>Activo</th>
                  <th>Marca</th>
                  <th>Modelo</th>
                  <th>Color</th>
                  <th>Placa</th>
                  <th>Serial</th>
                  <th colspan="2" class="text-center">Disponible para préstamo</th>
                </tr>
              </thead>
              <tbody class="BusquedaRapida">
                <?php
                $consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.codigo, Tp.prestar
                       FROM t_activo Ta
                       INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
                       INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
                       INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
                       INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
                       WHERE Tp.codigo = '".$logcodigo."'
                       ORDER BY Tp.placa ASC") or die(mysqli_error($link));

                while ($activos=mysqli_fetch_array($consulta)) { 
                  $prestar = $activos['prestar'];
                ?>
                <tr>
                  <td>
                    <div class="form-check">
                      <input class="form-check-input selectall" type="checkbox" name="idsplacas[]" value="<?php echo $activos['id_placa']?>">
                    </div>
                  </td>
                  <td><?php echo $activos['clase']?></td>
                  <td><?php echo $activos['marca']?></td>
                  <td><?php echo $activos['modelo']?></td>
                  <td><?php echo $activos['color']?></td>
                  <td><span class="badge bg-secondary"><?php echo $activos['placa']?></span></td>
                  <td><code><?php echo $activos['serial']?></code></td>
                  <td class="text-end">
                    <div class="radio-group">
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="presta<?php echo $activos['id_placa']; ?>" <?php if($prestar=="1"){?> checked <?php } ?> value="1" id="si<?php echo $activos['id_placa']; ?>">
                        <label class="form-check-label text-success" for="si<?php echo $activos['id_placa']; ?>">Sí</label>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="presta<?php echo $activos['id_placa']; ?>" <?php if($prestar=="0"){?> checked <?php } ?> value="0" id="no<?php echo $activos['id_placa']; ?>">
                      <label class="form-check-label text-danger" for="no<?php echo $activos['id_placa']; ?>">No</label>
                    </div>
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
    // Seleccionar/deseleccionar todos los checkboxes
    $('#selectall').on('click', function() {
      $(".selectall").prop('checked', this.checked);
    });

    // Búsqueda rápida
    $('#FiltrarContenido').keyup(function() {
      var ValorBusqueda = new RegExp($(this).val(), 'i');
      $('.BusquedaRapida tr').hide();
      $('.BusquedaRapida tr').filter(function() {
        return ValorBusqueda.test($(this).text());
      }).show();
    });

    // Validar al menos un checkbox seleccionado antes de enviar
    $('form').submit(function(e) {
      if($('input[name="idsplacas[]"]:checked').length === 0) {
        alert('Por favor seleccione al menos un activo para actualizar.');
        return false;
      }
      return true;
    });
  });
  </script>
</body>
</html>