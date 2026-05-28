<?php
// Iniciar sesión
session_start();

$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4);
if ($tienellave == false){
    echo '<script language = javascript>
    alert("No tienes permisos")
    self.location = "formulario_menu_inventario.html"
    </script>';
}

// Conexión a la base de datos
require_once("conexion.php");
$link = $mysqli;

// Verificar conexión
if (mysqli_connect_errno()) {
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
    exit();
}

// Establecer conjunto de caracteres UTF-8
if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link);
    exit();
}

// Obtener datos de la sesión
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Estado de Activos</title>
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
    .loading-spinner {
      display: none;
      width: 2rem;
      height: 2rem;
      border: 0.25em solid currentColor;
      border-right-color: transparent;
      border-radius: 50%;
      animation: .75s linear infinite spinner-border;
    }
    @keyframes spinner-border {
      to { transform: rotate(360deg); }
    }
    .btn-action {
      min-width: 100px;
    }
    .form-select {
      max-width: 500px;
    }
  </style>
</head>
<body>
  <!-- Navbar actualizado con Bootstrap 5 -->
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
              <i class="bi bi-arrow-return-left"></i> Volver al Inventario
            </a>
          </li>
        </ul>
        <ul class="navbar-nav">
          <li class="nav-item">
            <span class="nav-link text-white">
              <i class="bi bi-person-circle"></i> <?php echo $lognombre; ?>
            </span>
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
    <!-- Card principal -->
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="bi bi-clipboard2-pulse"></i> Estado de los Activos</h4>
      </div>
      <div class="card-body">
        <!-- Acciones rápidas -->
        <div class="d-flex justify-content-between mb-4">
          <div>
            <a href="ayuda.html#ea" class="btn btn-outline-primary btn-sm">
              <i class="bi bi-question-circle"></i> Ayuda
            </a>
            <a href="contactenos.php?rep=Error en Estado de los Activos" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-envelope-exclamation"></i> Reportar Incidencia
            </a>
          </div>
        </div>

        <!-- Select para fondos -->
        <div class="mb-4">
          <label for="fondos" class="form-label fw-bold">Seleccione el Origen Presupuestario:</label>
          <div class="d-flex align-items-center gap-3">
            <select class="form-select" id="fondos" name="fondos" aria-label="Seleccione un fondo" required>
              <option value="0">-- Seleccione un origen presupuestario --</option>
              <?php
              $querz = $link->query("SELECT * FROM t_fondos");
              while ($valorez = mysqli_fetch_array($querz)) {
                echo '<option value="' . $valorez['id_fondos'] . '">' . $valorez['fondos'] . '</option>';
              }
              ?>
            </select>
            <span id="loading-spinner" class="loading-spinner text-primary"></span>
          </div>
        </div>

        <!-- Formulario para enviar los datos -->
        <form action="actualizar_estado_del_activo_nuevo.php" method="post">
          <!-- Div para cargar la tabla mediante AJAX -->
          <div id="tabla-activos" class="table-responsive">
            <div class="text-center py-5 text-muted">
              <i class="bi bi-database" style="font-size: 3rem;"></i>
              <p class="mt-3">Seleccione un origen presupuestario para mostrar los activos</p>
            </div>
          </div>
      
          <!-- Botón para enviar el formulario (oculto inicialmente) -->
          <div id="submit-container" class="text-center d-none">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="bi bi-save"></i> Guardar Cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS y dependencias -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  
  <!-- jQuery para simplificar AJAX -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <script>
  $(document).ready(function() {
    $('#fondos').change(function() {
      const idFondos = $(this).val();
      const tablaActivos = $('#tabla-activos');
      const submitContainer = $('#submit-container');
      const loadingSpinner = $('#loading-spinner');
      
      // Mostrar spinner de carga
      loadingSpinner.show();
      
      // Ocultar contenedor de submit
      submitContainer.addClass('d-none');
      
      if (idFondos == 0) {
        tablaActivos.html(`
          <div class="text-center py-5 text-muted">
            <i class="bi bi-database" style="font-size: 3rem;"></i>
            <p class="mt-3">Seleccione un origen presupuestario para mostrar los activos</p>
          </div>
        `);
        loadingSpinner.hide();
        return;
      }
      
      // Realizar petición AJAX con jQuery
      $.ajax({
        url: 'obtener_tabla.php',
        type: 'POST',
        data: { id_fondos: idFondos },
        beforeSend: function() {
          tablaActivos.html(`
            <div class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
              <p class="mt-2">Cargando activos...</p>
            </div>
          `);
        },
        success: function(response) {
          tablaActivos.html(response);
          
          // Mostrar el botón de submit si hay datos
          if (response.trim() !== '') {
            submitContainer.removeClass('d-none');
          }
        },
        error: function() {
          tablaActivos.html(`
            <div class="alert alert-danger">
              <i class="bi bi-exclamation-triangle"></i> Error al cargar los datos. Intente nuevamente.
            </div>
          `);
        },
        complete: function() {
          loadingSpinner.hide();
        }
      });
    });
  });
  </script>
</body>
</html>