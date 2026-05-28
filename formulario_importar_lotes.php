<?php  
session_start();
$tienellave = ($_SESSION['tipo']==1);
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Importar Lotes</title>
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
      border-radius: 10px;
    }
    .upload-area {
      border: 2px dashed #dee2e6;
      border-radius: 10px;
      padding: 2rem;
      text-align: center;
      transition: all 0.3s;
      cursor: pointer;
    }
    .upload-area:hover {
      border-color: #0d6efd;
      background-color: #f0f7ff;
    }
    .btn-import {
      padding: 0.75rem 1.5rem;
      font-size: 1.1rem;
      font-weight: 500;
      letter-spacing: 0.5px;
    }
    .illustration {
      max-width: 100%;
      height: auto;
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
    <div class="row">
      <div class="col-12 mb-4">
        <h2 class="fw-bold"><i class="bi bi-upload"></i> Importar Placas y Seriales por Lotes</h2>
      </div>
    </div>

    <div class="row g-4">
      <!-- Formulario de importación -->
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-file-earmark-spreadsheet"></i> Cargar archivo CSV</h4>
          </div>
          <div class="card-body">
            <form action="importar_lotes.php" method="post" enctype="multipart/form-data" id="importForm">
              <div class="mb-4">
                <label for="pcsv" class="form-label fw-bold">Seleccionar archivo CSV</label>
                <div class="upload-area mb-3" onclick="document.getElementById('pcsv').click()">
                  <i class="bi bi-file-earmark-spreadsheet fs-1 text-primary"></i>
                  <p class="mt-2 mb-1">Arrastra tu archivo aquí o haz clic para seleccionar</p>
                  <p class="small text-muted">Formato CSV (Máx. 10MB)</p>
                  <input type="file" class="d-none" name="pcsv" id="pcsv" accept=".csv" required>
                  <div id="fileName" class="fw-bold text-success mt-2"></div>
                </div>
                <div class="alert alert-info">
                  <i class="bi bi-info-circle"></i> Utilice la plantilla oficial proporcionada por el Grupo Desarrollador de TecnoPresta.
                </div>
              </div>
              
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <a href="ayuda.html#lt" class="btn btn-outline-primary">
                    <i class="bi bi-question-circle"></i> Ayuda
                  </a>
                  <a href="contactenos.php?rep=Error en Importar Placas y Seriales por Lotes" class="btn btn-outline-secondary">
                    <i class="bi bi-exclamation-triangle"></i> Reportar Problema
                  </a>
                </div>
                <button type="submit" class="btn btn-primary btn-import">
                  <i class="bi bi-upload"></i> Importar Lote
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <!-- Ilustración -->
      <div class="col-lg-6 d-none d-lg-block">
        <div class="card h-100">
          <div class="card-body d-flex align-items-center justify-content-center">
            <img src="img/Importar-14.png" alt="Importación de datos" class="illustration">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  
  <!-- jQuery para mejor manejo de eventos -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <script>
  $(document).ready(function() {
    // Mostrar nombre del archivo seleccionado
    $('#pcsv').change(function() {
      const fileName = $(this).val().split('\\').pop();
      if(fileName) {
        $('#fileName').html('<i class="bi bi-check-circle-fill"></i> ' + fileName);
      } else {
        $('#fileName').html('');
      }
    });
    
    // Validación antes de enviar el formulario
    $('#importForm').submit(function(e) {
      const fileInput = $('#pcsv');
      if(fileInput.get(0).files.length === 0) {
        alert('Por favor seleccione un archivo CSV para importar.');
        return false;
      }
      
      const file = fileInput.get(0).files[0];
      if(file.size > 10 * 1024 * 1024) { // 10MB
        alert('El archivo es demasiado grande. El tamaño máximo permitido es 10MB.');
        return false;
      }
      
      // Mostrar spinner de carga
      $('button[type="submit"]').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Importando...');
    });
  });
  </script>
</body>
</html>