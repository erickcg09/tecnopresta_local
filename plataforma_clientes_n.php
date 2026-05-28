<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 2, 3, 4, 5, 7]);

if ($tienellave == false) {
    echo '<script language="javascript">
        alert("No tienes permisos");
        self.location = "index.html";
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

date_default_timezone_set('America/Costa_Rica'); // Configuro un nuevo timezone
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
$loginstitucion = $_SESSION['dependencia'];
$logcorreo = $_SESSION['correomep'];
$regionallog = $_SESSION['direccionreg'];
$circuitolog = $_SESSION['circuito'];

$activado = 1;

$querygeneral = "SELECT id_ag, clase FROM t_activo_general ORDER BY clase";
$resultadog = $link->query($querygeneral);

$query = "SELECT id_placa, placa, serial
          FROM t_placa
          WHERE codigo = '" . $logcodigo . "' AND activo = '" . $activado . "'
          ORDER BY placa ASC";
$resultado = $link->query($query);

$time = time();
$fecha = date("d-m-Y", $time);
?>
<!doctype html>
<html lang="es">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <title>Soporte T&eacute;cnico</title>

    <style type="text/css">
        .select2-container--open {
            z-index: 999999 !important; /* Asegura que el dropdown aparezca sobre el modal */
        }
        .select2-container {
            width: 100% !important; /* Forza el ancho completo */
        }
    /* Ajusta el tamaño de las imágenes en el dropdown */
    .select2-container--default .select2-results__option .img-thumbnail {
        width: 30px !important;
        height: 30px !important;
        object-fit: cover;
    }
    
    /* Ajusta el tamaño de la imagen en la selección */
    .select2-container--default .select2-selection__rendered .img-thumbnail {
        width: 20px !important;
        height: 20px !important;
    }
    
    /* Alineación vertical del contenido */
    .select2-container--default .select2-results__option {
        display: flex !important;
        align-items: center;
    }
        .pagina {
            padding: 8px 16px;
            border: 1px solid #ccc;
            color: #333;
            font-weight: bold;
        }
    </style>

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">TecnoPresta</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link" aria-current="page" href="formulario_menu_principal.html">
                        <i class="bi bi-arrow-left-circle"></i> Regresar
                    </a>
                    <a class="nav-link" aria-current="page" href="panel_soporte.php">
                        <i class="bi bi-exclamation-circle"></i> Incidencias de Soporte sin Asignar
                    </a>
                    <a class="nav-link" href="gameover.php">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </a>
                </div>
            </div>
            <form class="d-flex">
                <!-- Botón para abrir el modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalmenu">Crear Nuevo Ticket</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="text-center">
                    <br>
                    <img src="img/centro de soporte22.png" class="img-fluid w-75" alt="...">
                    <br>
                    <br>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="panel-body">
                    <?php
                    $record_per_page = 5;
                    $pagina = '';
                    if (isset($_GET["pagina"])) {
                        $pagina = $_GET["pagina"];
                    } else {
                        $pagina = 1;
                    }

                    $start_from = ($pagina - 1) * $record_per_page;

                    $query = "SELECT id, funcionario, placa, descriactivo, problema, fecha, estatus, nombretecnico, codigo, correo, institucion, solucion FROM soporte ORDER BY id DESC LIMIT $start_from, $record_per_page";
                    $result = mysqli_query($link, $query);
                    ?>

                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th>#</th>
                                <th>Funcionario(a)</th>
                                <th>Asunto</th>
                                <th>Descripci&oacute;n del problema</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Caso tomado por</th>
                            </tr>
                            <?php
                            $number = 0;
                            while ($row = mysqli_fetch_array($result)) {
                                $number++;
                                if ($row["estatus"] == "Abierto") {
                                    $estilo = "danger";
                                } else {
                                    $estilo = "success";
                                }
                                $fecha = date("d/m/Y", strtotime($row["fecha"]));
                                ?>
                                <tr>
                                    <td><?php echo $number; ?></td>
                                    <td><?php echo $row["funcionario"] . " <br> " . $row["correo"] . " <br> " . $row["institucion"]; ?></td>
                                    <td><?php echo $row["placa"] . " " . $row["descriactivo"]; ?></td>
                                    <td><?php echo $row["problema"]; ?></td>
                                    <td><?php echo $fecha; ?></td>
                                    <td><span class="badge bg-<?php echo $estilo; ?>"><?php echo $row["estatus"]; ?></span></td>
                                    <td><?php echo $row["nombretecnico"]; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <div class="accordion accordion-flush" id="accordionFlush<?php echo $row['id']; ?>">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="flush-heading<?php echo $row['id']; ?>">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?php echo $row['id']; ?>" aria-expanded="false" aria-controls="flush-collapse<?php echo $row['id']; ?>">
                                                        <b> Clic aqui para ver Soluci&oacute;n de este caso (Documéntese, si su caso es similar) </b>
                                                    </button>
                                                </h2>
                                                <div id="flush-collapse<?php echo $row['id']; ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading<?php echo $row['id']; ?>" data-bs-parent="#accordionFlush<?php echo $row['id']; ?>">
                                                    <div class="accordion-body"><?php echo $row["solucion"]; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>    <a href="proyecto_chat.php?gps=<?php echo $row['id']; ?>" role="button" class="btn btn-link p-0" title="Ingresar al Chat [Zona Pública]">
                                                <i class="bi bi-chat fs-5"></i> <!-- Icono de chat -->
                                            </a></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                        <div align="center">
                            <br />
                            <?php
                            $page_query = "SELECT * FROM soporte ORDER BY id DESC";
                            $page_result = mysqli_query($link, $page_query);
                            $total_records = mysqli_num_rows($page_result);
                            $total_pages = ceil($total_records / $record_per_page);
                            $start_loop = $pagina;
                            $diferencia = $total_pages - $pagina;
                            if ($diferencia <= 5) {
                                $start_loop = $total_pages - 5;
                            }
                            $end_loop = $start_loop + 4;
                            if ($pagina > 1) {
                                echo "<a class='pagina' href='plataforma_clientes.php?pagina=1'>Primera</a>";
                                echo "<a class='pagina' href='plataforma_clientes.php?pagina=" . ($pagina - 1) . "'><<</a>";
                            }
                            for ($i = $start_loop; $i <= $end_loop; $i++) {
                                echo "<a class='pagina' href='plataforma_clientes.php?pagina=" . $i . "'>" . $i . "</a>";
                            }
                            if ($pagina <= $end_loop) {
                                echo "<a class='pagina' href='plataforma_clientes.php?pagina=" . ($pagina + 1) . "'>>></a>";
                                echo "<a class='pagina' href='plataforma_clientes.php?pagina=" . $total_pages . "'>Última</a>";
                            }
                            ?>
                        </div>
                        <br /><br />
                    </div>
                </div>
            </div>
        </div>
    </div>





<!-- Modal -->
<div class="modal fade" id="modalmenu" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nuevo reporte sobre un asunto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="recibir_variables_modal.php">
          <!-- Select de Tema o Asunto (ahora con Select2) -->
          <div class="mb-3">
            <label for="select-tema" class="col-form-label">Tema o asunto:</label>
                <select class="form-select" id="select-tema" name="asunto" required>
                    <?php
                    $query = "SELECT id_asunto, asunto, imagen FROM t_asunto_soporte";
                    $result = mysqli_query($link, $query);
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<option 
                                  value="' . $row['id_asunto'] . '" 
                                  data-imagen="' . htmlspecialchars($row['imagen']) . '">' 
                                  . $row['asunto'] . 
                                  '</option>';
                        }
                    }
                    ?>
                </select>
          </div>

          <!-- Select de Fondos (ahora con Select2) -->
          <div class="mb-3">
            <label for="select-fondos" class="col-form-label">Fondos:</label>
            <select class="form-select" id="select-fondos" name="fondos" required>
              <option value="0">Seleccione..</option>
              <?php
              $querz = $link->query("SELECT * FROM t_fondos");
              while ($valorez = mysqli_fetch_array($querz)) {
                  echo '<option value="' . $valorez['id_fondos'] . '">' . $valorez['fondos'] . '</option>';
              }
              ?>
            </select>
          </div>

          <!-- Campos ocultos y textarea (sin cambios) -->
          <input type="hidden" name="tema" id="tema" value="">
          <!-- ... resto de campos ocultos ... -->
          <div class="mb-3">
            <label for="message-text" class="col-form-label">Incidencia o consulta:</label>
            <textarea class="form-control" name="incidencia" required onkeypress="return event.charCode != 39"></textarea>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Crear Reporte</button>
      </div>
        </form>
    </div>
  </div>
 <script>
$(document).ready(function() {
    // Inicializar Select2
    $('#select-tema').select2({
        dropdownParent: $('#modalmenu'),
        templateResult: formatOption,
        templateSelection: formatSelection
    });

    // Actualizar el campo hidden cuando cambia la selección
    $('#select-tema').on('change', function() {
        var textoSeleccionado = $(this).find('option:selected').text();
        $('#tema').val(textoSeleccionado);
    });

    // También actualizar al abrir el modal (por si hay una selección por defecto)
    $('#modalmenu').on('shown.bs.modal', function() {
        var textoSeleccionado = $('#select-tema').find('option:selected').text();
        $('#tema').val(textoSeleccionado);
    });
});

// Tus funciones formatOption y formatSelection (las que mostramos antes)
function formatOption(option) { /* ... */ }
function formatSelection(option) { /* ... */ }
</script>
</div>


    <div class="panel-footer">
        <div class="container"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Script para actualizar el campo oculto -->

    <script>
        function keepSessionAlive() {
            setInterval(function () {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "keep_alive.php", true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Opcional: Mantener la sesion activa
                        // console.log("Session kept alive");
                    }
                };
                xhr.send();
            }, 300000); // 300000 milisegundos = 5 minutos
        }

        // Iniciar la función cuando la página cargue
        window.onload = keepSessionAlive;
    </script>
    <script>
    $(document).ready(function() {
        // Inicializar Select2 en los selects del modal
        $('#select-tema, #select-fondos').select2({
            dropdownParent: $('#modalmenu'), // ¡IMPORTANTE! Vincula el dropdown al modal
            width: '100%', // Asegura el ancho responsive
            placeholder: "Seleccione una opción", // Texto por defecto
            allowClear: true // Permite borrar la selección
        });
    });
    </script>
    <script>
        $(document).ready(function() {
            $('#select-tema').select2({
                dropdownParent: $('#modalmenu'),
                templateResult: formatOption, // Función para mostrar imágenes en las opciones
                templateSelection: formatSelection // Función para mostrar la selección
            });
        });
        
        // Función para renderizar las opciones con imágenes
        function formatOption(option) {
            if (!option.id) return option.text; // Opción por defecto ("Seleccione...")
            
            var $container = $(
                '<div class="d-flex align-items-center">' +
                    '<img src="' + $(option.element).data('imagen') + '" class="img-thumbnail me-2" style="width: 30px; height: 30px; object-fit: cover;"/>' +
                    '<span>' + option.text + '</span>' +
                '</div>'
            );
            return $container;
        }
        
        // Función para renderizar la selección actual (más compacta)
        function formatSelection(option) {
            if (!option.id) return option.text;
            return $(
                '<div class="d-flex align-items-center">' +
                    '<img src="' + $(option.element).data('imagen') + '" class="img-thumbnail me-2" style="width: 20px; height: 20px; object-fit: cover;"/>' +
                    '<span>' + option.text + '</span>' +
                '</div>'
            );
        }    
    </script>
</body>

<footer class="bg-light text-center text-lg-start">
    <!-- Grid container -->
    <div class="container p-4">
        <!--Grid row-->
        <div class="row">
            <!--Grid column-->
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase">Objetivos del Centro de Soporte</h5>
                <p>
                    El Centro de Soporte es un canal o mesa de ayuda que resuelve problemas variados a funcionarios del MEP. El servicio que se encarga de responder una variedad de incidencias, por ejemplo, dudas sobre programas (software) o recibir solicitudes de reparaci&oacute;n de hadware, as&eacute; como atenci&oacute;n y apoyo de programas y proyectos espec&iacute;ficos.
                </p>
            </div>
            <!--Grid column-->

            <!--Grid column-->
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase">Alcances del Centro de Soporte</h5>
                <p>
                    El Centro de Soporte trabaja con un equipo base de funcionarios(as) capacitados. Se brinda soporte a programas y proyectos espec&iacute;ficos. Sus alcances estan limitados a soporte de software y atenci&oacute;n de consultas generales en tiempo real. Esperamos poder servirles en la mayor&iacute;a de casos posibles.
                </p>
            </div>
            <!--Grid column-->
        </div>
        <!--Grid row-->
    </div>
    <!-- Grid container -->

    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        TecnoPresta es realizado por gente MEP, para la gente del MEP.
    </div>
</footer>
</html>