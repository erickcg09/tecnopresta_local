<?php
session_start();
if (!isset($_SESSION['tipo']) || !in_array($_SESSION['tipo'], [1, 2, 3, 4])) {
    echo '<script>alert("No tienes permisos"); window.location.href = "formulario_menu_inventario.html";</script>';
    exit;
}

require_once("conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
    exit;
}

mysqli_set_charset($link, "utf8");

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Modelo General</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 y Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="fondoresponsive.css">
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">
  <style>
    .button {
      background-color: #0080FF;
      border: none;
      color: white;
      padding: 15px 32px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 18px;
      margin: 4px 2px;
      cursor: pointer;
      width: 70px;
      text-transform: uppercase;
      letter-spacing: 2px;
      border-radius: 10px;
      transition: all 300ms;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-md bg-dark navbar-dark">
  <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="formulario_agregar_equipo.php"><span class="icon icon-undo2"></span> Regresar</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><span class="icon icon-enter"></span> Cerrar Sesi&oacute;n</a>
      </li>  
    </ul>
  </div>  
</nav>
<br>

<div class="container">
  <div class="row">
    <div class="col-md-6">
      <h3>Usuario: <?php echo $lognombre . " " . $logcodigo; ?></h3><br>
      <h3>Agregar Modelo de Activo</h3>
      <a href="ayuda.html#mg">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a>
      <a href="contactenos.php?rep=Error en Agregar Modelo de Activo">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error]</a>
      <br><br>
      <form name="frmactivo" action="guardar_activo_general.php" method="post">
        <!-- Select para Clase General del Activo -->
        <div class="form-group">
          <label class="form-label">Clase General del Activo: </label>
          <select id="myDropdown2" name="myDropdown2" class="form-control select2">
            <option value="0" data-img-src="default">Seleccione: <i class="bi bi-question-circle"></i></option>
            <?php
            $regres1 = mysqli_query($link, "SELECT * FROM t_activo_general ORDER BY clase");
            while ($regr1 = mysqli_fetch_array($regres1)) {
              echo '<option value="' . $regr1['id_ag'] . '" data-img-src="img/' . $regr1['imagen'] . '">' . $regr1['clase'] . '</option>';
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <input type="hidden" id="clase" name="clase" required>
        </div>

        <!-- Select para Marca de Fabricante -->
        <div class="form-group">
          <label class="form-label mt-3">Marca de fabricante: </label>
          <select id="myDropdown" name="myDropdown" class="form-control select2">
            <option value="0" data-img-src="default">Seleccione: <i class="bi bi-question-circle"></i></option>
            <?php
            $regres = mysqli_query($link, "SELECT * FROM t_marca ORDER BY marca");
            while ($regr = mysqli_fetch_array($regres)) {
              echo '<option value="' . $regr['id_marca'] . '" data-img-src="ico/' . $regr['logo'] . '">' . $regr['marca'] . '</option>';
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <input type="hidden" id="marca" name="marca" required>
        </div>

        <div class="mb-3">
          <label for="modelo" class="form-label mt-3">Modelo del Activo</label>
          <input type="text" class="form-control" id="modelo" name="modelo" aria-describedby="modeloAyuda" required>
          <small id="modeloAyuda" class="form-text text-muted">Antes de agregar una clase general de activo, verifica si ya está en la lista.</small>
        </div>

        <!-- Select para Color Predominante -->
        <div class="form-group">
          <label class="form-label mt-3">Color predominante del activo: </label>
          <select id="myDropdown3" name="myDropdown3" class="form-control select2">
            <option value="0" data-img-src="default">Seleccione: <i class="bi bi-question-circle"></i></option>
            <?php
            $regres2 = mysqli_query($link, "SELECT * FROM t_color ORDER BY color");
            while ($regr2 = mysqli_fetch_array($regres2)) {
              echo '<option value="' . $regr2['id_color'] . '" data-img-src="ico/' . $regr2['imagen'] . '">' . $regr2['color'] . '</option>';
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <input type="hidden" id="color" name="color" required>
        </div>

        <!-- Botón de guardar -->
        <div class="form-group">
          <button type="submit" class="btn btn-dark mt-4"><span class="icon icon-floppy-disk"> Guardar</span></button>
        </div>
      </form>

    <br>
    <div class="input-group mb-3">
      <span class="input-group-text" id="basic-addon1">Buscar</span>
      <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese el modelo, marca, color o clase" aria-describedby="basic-addon1">
    </div>

    <!-- Tabla de resultados -->
    <table class="table table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Clase</th>
          <th>Modelo</th>
          <th>Marca</th>
          <th>Color</th>
          <th>Eliminar</th>
        </tr>
      </thead>
      <tbody id="resultadosBusqueda">
        <!-- Los resultados se cargarán aquí dinámicamente -->
      </tbody>
    </table>
    </div> <!-- Cierre del row col 6-->
    
    <div class="col-md-6">
      <div class="d-none d-md-block">
        <img src="img/hardware.png" class="img-fluid" alt="Hardware" width="600" height="600">
      </div>
    </div> <!-- Cierre del row col 6 -->
    
  </div> <!-- Cierre del row -->
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
    // Inicializar Select2 para todos los selects con la clase .select2
    $('.select2').select2({
      templateResult: formatOption, // Función para mostrar imágenes en el dropdown
      templateSelection: formatOption // Función para mostrar imágenes en la selección
    });

    // Función para formatear las opciones con imágenes
    function formatOption(option) {
      if (!option.id) return option.text; // Si no hay ID, devolver solo el texto

      var imgSrc = $(option.element).data('img-src');
      var $option;

      if (imgSrc === "default") {
        // Mostrar ícono de Bootstrap si no hay imagen específica
        $option = $(
          '<span><i class="bi bi-question-circle" style="margin-right: 10px;"></i>' + option.text + '</span>'
        );
      } else {
        // Mostrar la imagen específica
        $option = $(
          '<span><img src="' + imgSrc + '" class="img-fluid" style="width: 20px; margin-right: 10px;" />' + option.text + '</span>'
        );
      }

      return $option;
    }

    // Manejar el evento de selección para cada select
    $('#myDropdown').on('select2:select', function(e) {
      document.getElementById("marca").value = e.params.data.id; // Actualizar el valor del input hidden
    });

    $('#myDropdown2').on('select2:select', function(e) {
      document.getElementById("clase").value = e.params.data.id; // Actualizar el valor del input hidden
    });

    $('#myDropdown3').on('select2:select', function(e) {
      document.getElementById("color").value = e.params.data.id; // Actualizar el valor del input hidden
    });
  });
</script>
<script>
  document.getElementById('modelo').addEventListener('keypress', function(event) {
    if (event.charCode === 39) { // 39 es el código para el apóstrofe (')
      event.preventDefault(); // Evita que se ingrese el carácter
    }
  });
</script>
  <!-- Script para la búsqueda en tiempo real -->
  <script>
    $(document).ready(function() {
      // Escuchar el evento input en el campo de búsqueda
      $('#FiltrarContenido').on('input', function() {
        const termino = $(this).val(); // Obtener el término de búsqueda

        // Realizar la solicitud AJAX
        $.ajax({
          url: 'buscar_activos.php', // Archivo PHP que maneja la búsqueda
          method: 'GET',
          data: { termino: termino }, // Enviar el término de búsqueda
          dataType: 'json',
          success: function(response) {
            // Limpiar la tabla antes de agregar nuevos resultados
            $('#resultadosBusqueda').empty();

            // Agregar los resultados a la tabla
            response.forEach(function(activo) {
              const row = `
                <tr>
                  <td>${activo.id_activo}</td>
                  <td>${activo.clase}</td>
                  <td>${activo.modelo}</td>
                  <td>${activo.marca}</td>
                  <td>${activo.color}</td>
                  <td>
                    <a class="btn btn-dark" href="eliminar_activo.php?gps=${activo.id_activo}" role="button">
                      <span class="icon icon-bin"></span>
                    </a>
                  </td>
                </tr>
              `;
              $('#resultadosBusqueda').append(row);
            });
          },
          error: function(xhr, status, error) {
            console.error("Error en la solicitud AJAX:", error);
          }
        });
      });
    });
  </script>
</body>
</html>