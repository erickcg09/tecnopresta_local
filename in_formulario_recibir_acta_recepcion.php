<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 2, 3, 4]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}
date_default_timezone_set('America/Costa_Rica'); //timezone Costa Rica
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];


$logdependencia = $_SESSION['dependencia'];
$logregional = $_SESSION['direccionreg'];
$logcircuito = $_SESSION['circuito'];
$fechaHoraServidor = date('Y-m-d H:i:s');

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
            .card-custom {
                background-color: #f0f4f8; /* Color frío y suave */
                border: none;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }
            .card-body {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .card-img {
                width: 50px;
                height: 50px;
                object-fit: cover;
                border-radius: 50%;
                align-self: flex-end;
            }
            .btn-custom {
                background-color: #007bff; /* Color frío */
                border: none;
                border-radius: 5px;
            }
            .icon-large {
            font-size: 2em; /* Ajusta el tamaño del ícono */
            }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md bg-dark navbar-dark">
        <img src="img/logodelgobierno.png" width="35" height="30" alt="" loading="lazy">
        <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="in_panel_reportes.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply-all" viewBox="0 0 16 16">
                <path d="M8.098 5.013a.144.144 0 0 1 .202.134V6.3a.5.5 0 0 0 .5.5c.667 0 2.013.005 3.3.822.984.624 1.99 1.76 2.595 3.876-1.02-.983-2.185-1.516-3.205-1.799a8.7 8.7 0 0 0-1.921-.306 7 7 0 0 0-.798.008h-.013l-.005.001h-.001L8.8 9.9l-.05-.498a.5.5 0 0 0-.45.498v1.153c0 .108-.11.176-.202.134L4.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028zM9.3 10.386q.102 0 .223.006c.434.02 1.034.086 1.7.271 1.326.368 2.896 1.202 3.94 3.08a.5.5 0 0 0 .933-.305c-.464-3.71-1.886-5.662-3.46-6.66-1.245-.79-2.527-.942-3.336-.971v-.66a1.144 1.144 0 0 0-1.767-.96l-3.994 2.94a1.147 1.147 0 0 0 0 1.946l3.994 2.94a1.144 1.144 0 0 0 1.767-.96z"/>
                <path d="M5.232 4.293a.5.5 0 0 0-.7-.106L.54 7.127a1.147 1.147 0 0 0 0 1.946l3.994 2.94a.5.5 0 1 0 .593-.805L1.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028 4.012-2.954a.5.5 0 0 0 .106-.699"/>
                </svg> Panel de Reportes</a>
            </li> 
            <li class="nav-item">
                <a class="nav-link" href="gameover.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-door-open" viewBox="0 0 16 16">
                <path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1"/>
                <path d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117M11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5M4 1.934V15h6V1.077z"/>
                </svg> Cerrar Sesión</a>
            </li>  
            </ul>
        </div>  
    </nav>

  <div class="container mt-5">
    <h2>Formulario para el envío de archivos de actas de recepción de equipos tecnológicos</h2>
    <form action="in_procesamiento_recepcion_acta.php" method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="archivo" class="form-label">Subir archivo PDF:</label>
        <input class="form-control w-50" type="file" id="archivo" name="archivo" accept=".pdf" required>
      </div>
      <div class="mb-3">
        <label for="etiqueta" class="form-label">Etiqueta descriptiva:</label>
        <input type="text" class="form-control w-50" id="etiqueta" name="etiqueta" placeholder="Ingrese una etiqueta o descripción" required>
      </div>
      <div class="mb-3">
        <label for="origen" class="form-label">Origen Presupuestario</label>
          <select class="form-select my-3 w-50" id="fondos" name="fondos" aria-label="Example select with button addon" required>
            
            <?php 
              $querz = $link -> query ("SELECT * FROM t_fondos");
              while ($valorez = mysqli_fetch_array($querz)) {
                echo '<option value="'.$valorez['id_fondos'].'">'.$valorez['fondos'].'</option>';
              }
            ?>
          </select>
      </div>
      <input type="hidden" name="codigo" value="<?php echo $logcodigo; ?>">
      <input type="hidden" name="dependencia" value="<?php echo $logdependencia; ?>">
      <input type="hidden" name="regional" value="<?php echo $logregional; ?>">
      <input type="hidden" name="circuito" value="<?php echo $logcircuito; ?>">
      <input type="hidden" name="fecha_hora" value="<?php echo $fechaHoraServidor; ?>">
      
      <button type="submit" class="btn btn-primary mb-3">
          <i class="bi bi-save"></i> Guardar
      </button>

    </form>
<?php


$sql = "SELECT id_rae, etiqueta, archivo, fecha FROM t_recepcion_acta_equipo WHERE codigo = '$logcodigo' ORDER BY fecha";
$result = $link->query($sql);

if ($result->num_rows > 0) {
    echo '<table class="table table-striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Etiqueta</th>';
    echo '<th>Archivo</th>';
    echo '<th>Fecha</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    while($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id_rae'] . '</td>';
        echo '<td>' . $row['etiqueta'] . '</td>';
        echo '<td><a href="' . $row['archivo'] . '" target="_blank">Ver Archivo</a></td>';
        echo '<td>' . $row['fecha'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
} else {
    echo "No se encontraron resultados.";
}

$link->close();
?>

  </div>


    <footer class="bg-dark text-white pt-4 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="mb-0">Por favor, asegúrese de ingresar la información solicitada en cada instancia.</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12 text-center">
                    <div class="border border-light p-3">
                        <p class="mb-0">© 2024 Ministerio de Educación Pública. Todos los derechos reservados.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>