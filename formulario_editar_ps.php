<?php  
session_start();
// Verificación de permisos más segura
$tienellave = isset($_SESSION['tipo']) && in_array($_SESSION['tipo'], [1, 2, 3, 4]);
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

// Obtener datos de sesión
$logusuario = $_SESSION['cedula'] ?? '';
$lognombre = $_SESSION['nombre'] ?? '';
$logtipo = $_SESSION['tipo'] ?? '';
$logcodigo = $_SESSION['codigo'] ?? '';

// Obtener ID de placa y validar
$id_placa = filter_input(INPUT_GET, 'gps', FILTER_VALIDATE_INT);
if (!$id_placa) {
    die("ID de placa no válido");
}

// Verificar si hay parámetros de error/éxito
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';

// Obtener datos de la placa
$stmt = $link->prepare("SELECT placa, serial, id_fondos FROM t_placa WHERE id_placa = ?");
$stmt->bind_param("i", $id_placa);
$stmt->execute();
$stmt->bind_result($placa, $serial, $id_fondos);
$stmt->fetch();
$stmt->close();

// Obtener tipo de fondos
$stmt = $link->prepare("SELECT fondos FROM t_fondos WHERE id_fondos = ?");
$stmt->bind_param("i", $id_fondos);
$stmt->execute();
$stmt->bind_result($fondos);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta - Editar Placa/Serial</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="fondoresponsive.css">
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <style>
     /* Asegurar que las alertas están por encima de otros elementos */
    .alert {
        z-index: 9999 !important;
    }
    /* Asegurar que el navbar no tape las alertas */
    .navbar {
        z-index: 1000;
    }
    .card {
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      border: none;
    }
    .card:hover {
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    .form-section {
      background-color: #f8f9fa;
      border-radius: 8px;
      padding: 25px;
    }
    .img-container {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
    }
    .img-fluid-custom {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
    }
    .required-field::after {
      content: " *";
      color: #dc3545;
    }
    .alert-fixed-top {
      position: fixed;
      top: 70px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1050;
      width: 80%;
      max-width: 800px;
    }
  </style>
</head>
<body class="bg-light">
  <!-- Navbar con Bootstrap 5 -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <img src="img/logomep2020.png" width="45" height="30" alt="Logo TecnoPresta" class="me-2">
      <a class="navbar-brand" href="formulario_menu_principal.html">TecnoPresta</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="formulario_editar_placa.php">
              <i class="bi bi-arrow-left-circle"></i> Volver a Placas
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
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <h3><i class="bi bi-person-circle me-2"></i>Usuario: <?php echo htmlspecialchars($lognombre); ?></h3>
          <div>
            <a href="ayuda.html" class="btn btn-sm btn-outline-secondary me-2">
              <i class="bi bi-question-circle"></i> Ayuda
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h3 class="card-title mb-4"><i class="bi bi-pencil-square me-2"></i>Editar Placa y Serial</h3>
            
            <form action="editar_placa_serial.php" method="post" class="needs-validation" novalidate>
              <div class="mb-3">
                <label for="placa" class="form-label required-field">Placa</label>
                <input type="text" class="form-control" name="placa" id="placa" 
                       value="<?php echo htmlspecialchars($placa); ?>" required
                       pattern="[A-Za-z0-9-]+" minlength="3" maxlength="50"
                       oninvalid="this.setCustomValidity('Por favor ingrese una placa válida')"
                       oninput="this.setCustomValidity('')">
                <div class="invalid-feedback">Por favor ingrese una placa válida</div>
                <small class="text-muted">Este campo es únicamente para corregir errores de escritura</small>
              </div>
              
              <div class="mb-3">
                <label for="serial" class="form-label required-field">Serial</label>
                <input type="text" class="form-control" name="serial" id="serial" 
                       value="<?php echo htmlspecialchars($serial); ?>" required
                       pattern="[A-Za-z0-9-]+" minlength="3" maxlength="50"
                       oninvalid="this.setCustomValidity('Por favor ingrese un serial válido')"
                       oninput="this.setCustomValidity('')">
                <div class="invalid-feedback">Por favor ingrese un serial válido</div>
                <small class="text-muted">Este campo es únicamente para corregir errores de escritura</small>
              </div>
              
              <div class="mb-4">
                <label for="fondos" class="form-label required-field">Origen de los fondos</label>
                <select class="form-select" id="fondos" name="fondos" required>
                  <option value="<?php echo htmlspecialchars($id_fondos); ?>">
                    <?php echo htmlspecialchars($fondos); ?>
                  </option>
                  <?php 
                    $query = $link->query("SELECT id_fondos, fondos FROM t_fondos ORDER BY fondos");
                    while ($row = $query->fetch_assoc()) {
                      if ($row['id_fondos'] != $id_fondos) {
                        echo '<option value="'.htmlspecialchars($row['id_fondos']).'">'.htmlspecialchars($row['fondos']).'</option>';
                      }
                    }
                  ?>
                </select>
                <div class="invalid-feedback">Por favor seleccione un origen de fondos</div>
              </div>
              
              <input type="hidden" id="idplaca" name="idplaca" value="<?php echo htmlspecialchars($id_placa); ?>">
              
              <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-save"></i> Guardar Cambios
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <div class="col-lg-6 d-none d-lg-block">
        <div class="card h-100">
          <div class="img-container p-4">
            <img src="img/edita-ps2.png" class="img-fluid-custom" alt="Ilustración de edición de placas" loading="lazy">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Validación de formulario
    (function() {
      'use strict';
      var forms = document.querySelectorAll('.needs-validation');
      
      Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    })();

    // Cerrar automáticamente las alertas después de 5 segundos
    setTimeout(function() {
      var alerts = document.querySelectorAll('.alert');
      alerts.forEach(function(alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      });
    }, 5000);
  </script>

</body>
</html>