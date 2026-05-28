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

$logusuario = $_SESSION['cedula'] ?? '';
$lognombre = $_SESSION['nombre'] ?? '';
$logcodigo = $_SESSION['codigo'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta - Marcas Comerciales</title>
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
  <style>
    .card {
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .img-thumbnail {
      max-width: 100px;
      height: auto;
    }
    .search-box {
      position: relative;
    }
    .search-box .bi {
      position: absolute;
      top: 10px;
      left: 10px;
    }
    .search-box input {
      padding-left: 35px;
    }
    .action-btn {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .table-responsive {
      max-height: 500px;
      overflow-y: auto;
    }
    .user-info {
      background-color: #f8f9fa;
      border-radius: 5px;
      padding: 10px;
    }
  </style>
</head>
<body class="bg-light">
  <!-- Navbar con Bootstrap 5 -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <img src="img/logomep2020.png" width="45" height="30" alt="Logo" class="me-2">
      <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="inventario_mantenimiento.php">
              <i class="bi bi-arrow-counterclockwise"></i> Mantenimiento
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
    <div class="row">
      <div class="col-lg-6">
        <!-- Información de usuario -->
        <div class="user-info mb-4">
          <h5><i class="bi bi-person-circle"></i> Usuario: <?php echo htmlspecialchars($lognombre . " " . $logcodigo); ?></h5>
        </div>

        <!-- Card para formulario -->
        <div class="card mb-4">
          <div class="card-body">
            <h3 class="card-title">Marcas Comerciales</h3>
            <div class="d-flex gap-2 mb-3">
              <a href="ayuda.html#mc" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-question-circle"></i> Ayuda
              </a>
              <a href="contactenos.php?rep=Error en Marcas Comerciales" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-envelope"></i> Reportar Incidencia
              </a>
            </div>
<?php
session_start();
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">'.$_SESSION['error_message'].'</div>';
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">'.$_SESSION['success_message'].'</div>';
    unset($_SESSION['success_message']);
}
?>
            <form name="frmarca" action="guardar_subir_marca.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
              <div class="mb-3">
                <label for="marca" class="form-label">Nombre de la Marca comercial</label>
                <input type="text" class="form-control" id="marca" name="marca" required 
                       maxlength="50" pattern="[A-Za-z0-9áéíóúÁÉÍÓÚñÑ ]+"
                       oninvalid="this.setCustomValidity('Por favor ingrese un nombre válido')"
                       oninput="this.setCustomValidity('')">
                <div class="invalid-feedback">Por favor ingrese un nombre válido para la marca.</div>
                <small class="text-muted">Antes de agregar una marca comercial, verifique si ya está en la lista.</small>
              </div>
              
              <div class="mb-3">
                <label for="imagen" class="form-label">Subir logo (solo PNG)</label>
                <input type="file" class="form-control" name="imagen" id="imagen" accept="image/png" required>
                <div class="invalid-feedback">Por favor seleccione una imagen PNG.</div>
              </div>
              
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Guardar
              </button>
            </form>
          </div>
        </div>

        <!-- Card para búsqueda y resultados -->
        <div class="card">
          <div class="card-body">
            <div class="search-box mb-3">
              <i class="bi bi-search"></i>
              <input type="text" id="FiltrarContenido" class="form-control" placeholder="Buscar marca comercial...">
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Marca</th>
                    <th>Logo</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody class="BusquedaRapida">
                  <?php
                  $consulta = mysqli_query($link, "SELECT id_marca, marca, logo FROM t_marca ORDER BY marca ASC") or die(mysqli_error($link));

                  while ($marcas = mysqli_fetch_array($consulta)) { ?>
                  <tr>
                    <td><?php echo htmlspecialchars($marcas['id_marca']); ?></td>
                    <td><?php echo htmlspecialchars($marcas['marca']); ?></td>
                    <td>
                      <img src="ico/<?php echo htmlspecialchars($marcas['logo']); ?>" class="img-thumbnail" alt="<?php echo htmlspecialchars($marcas['marca']); ?>">
                    </td>
                    <td>
                      <div class="d-flex gap-2">
                        <a href="formulario_editar_marca.php?gps=<?php echo $marcas['id_marca']; ?>" class="btn btn-sm btn-outline-primary action-btn" title="Editar">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <a href="eliminar_marca.php?gps=<?php echo $marcas['id_marca']; ?>" class="btn btn-sm btn-outline-danger action-btn" title="Eliminar" onclick="return confirm('¿Estás seguro que deseas eliminar esta marca?');">
                          <i class="bi bi-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                  <?php }
                  mysqli_close($link); ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6 d-none d-lg-block">
        <div class="card h-100">
          <div class="card-body d-flex align-items-center justify-content-center">
            <img src="img/activos2.png" class="img-fluid" alt="Imagen de activos" loading="lazy">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
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

    // Filtrado de contenido
    $(document).ready(function() {
      $('#FiltrarContenido').keyup(function() {
        var ValorBusqueda = new RegExp($(this).val(), 'i');
        $('.BusquedaRapida tr').hide();
        $('.BusquedaRapida tr').filter(function() {
          return ValorBusqueda.test($(this).text());
        }).show();
      });
    });

    // Previsualización de imagen antes de subir
    document.getElementById('imagen').addEventListener('change', function(event) {
      var output = document.createElement('img');
      output.className = 'img-thumbnail mt-2';
      output.style.maxWidth = '200px';
      
      if (event.target.files && event.target.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
          output.src = e.target.result;
          if (!document.getElementById('imagePreview')) {
            var previewDiv = document.createElement('div');
            previewDiv.id = 'imagePreview';
            previewDiv.appendChild(output);
            event.target.parentNode.appendChild(previewDiv);
          } else {
            document.getElementById('imagePreview').innerHTML = '';
            document.getElementById('imagePreview').appendChild(output);
          }
        };
        
        reader.readAsDataURL(event.target.files[0]);
      }
    });
  </script>
</body>
</html>
