<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su administrador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}
date_default_timezone_set('America/Costa_Rica'); //configuro un nuevo timezone
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


if (isset($_POST['idags']) && isset($_POST['id_fondos'])) {
    // Recibir los arrays desde el formulario
    $idags = $_POST['idags'];
    $id_fondos = $_POST['id_fondos'];

    // Convertir los arrays a cadenas separadas por comas
    $id_ag_list = implode(',', $idags);
    $id_fondos_list = implode(',', $id_fondos);


} else {
    header("Location: in_seleccionar_para_concentrado.php");
    exit();
}


?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Concentrado</title>
 

    <!-- Bootstrap core CSS -->
    <script src="js/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .ui-autocomplete-row
      {
        padding:8px;
        background-color: #f4f4f4;
        border-bottom:1px solid #ccc;
        font-weight:bold;
      }
      .ui-autocomplete-row:hover
      {
        background-color: #ddd;
      }

      .dropbtn {
      background-color: #2b2827;
      color: white;
      padding: 16px;
      font-size: 16px;
      border: none;
      cursor: pointer;
    }

    .dropbtn:hover, .dropbtn:focus {
      background-color: #808b96;
    }

    #myInput {
      box-sizing: border-box;
      background-image: url('matricula_imagenes/searchicon.png');
      background-position: 14px 12px;
      background-repeat: no-repeat;
      font-size: 16px;
      padding: 14px 20px 12px 45px;
      border: none;
      border-bottom: 1px solid #ddd;
    }

    #myInput:focus {outline: 3px solid #ddd;}

    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #f6f6f6;
      min-width: 230px;
      overflow: auto;
      border: 1px solid #ddd;
      z-index: 1;
    }

    .dropdown-content a {
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
    }

    .dropdown a:hover {background-color: #ddd;}

    .show {display: block;}
    
    
    </style>

    
    <!-- Custom styles for this template -->
    <link href="css/dashboard.css" rel="stylesheet">
  </head>
  <body>
    
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">TecnoPresta</a>
  <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <input class="form-control form-control-dark w-100" type="text" id="FiltrarContenido" placeholder="Buscar" aria-label="Search">
  <div class="navbar-nav">
    <div class="nav-item text-nowrap">
      <a class="nav-link px-3" href="gameover.php">Cerrar sesi&oacute;n</a>
    </div>
  </div>
</header>

<?php
            // Incluye el menu izquierdo
            include('menu/menu_izquierdo.php');
?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4"> <!-- Apertura del main donde se coloca la informacion del panel derecho -->
<h2 class = "text-center mt-3">Concentrado general de activos en uso y no uso</h2><br><br>
<button type="button" class="btn btn-success" onclick="tableToExcel('testTable', 'Resumen')"><i class="bi bi-file-earmark-excel-fill"></i> Excel</button>

<?php
// Verificar la conexión
if ($link->connect_error) {
    die("Error de conexión: " . $link->connect_error);
}

// Crear la tabla temporal para los conteos de estado y uso
$create_temp_estado_uso = "
    CREATE TEMPORARY TABLE temp_estado_uso AS
    SELECT p.codigo, 
        COUNT(CASE WHEN p.id_estado = 1 AND p.enuso = 1 THEN 1 END) AS muy_bueno_uso,
        COUNT(CASE WHEN p.id_estado = 1 AND p.enuso = 0 THEN 1 END) AS muy_bueno_no_uso,
        COUNT(CASE WHEN p.id_estado = 2 AND p.enuso = 1 THEN 1 END) AS bueno_uso,
        COUNT(CASE WHEN p.id_estado = 2 AND p.enuso = 0 THEN 1 END) AS bueno_no_uso,
        COUNT(CASE WHEN p.id_estado = 3 AND p.enuso = 1 THEN 1 END) AS regular_uso,
        COUNT(CASE WHEN p.id_estado = 3 AND p.enuso = 0 THEN 1 END) AS regular_no_uso,
        COUNT(CASE WHEN p.id_estado = 4 AND p.enuso = 1 THEN 1 END) AS malo_uso,
        COUNT(CASE WHEN p.id_estado = 4 AND p.enuso = 0 THEN 1 END) AS malo_no_uso,
        COUNT(CASE WHEN p.id_estado = 5 AND p.enuso = 1 THEN 1 END) AS hurtado_uso,
        COUNT(CASE WHEN p.id_estado = 5 AND p.enuso = 0 THEN 1 END) AS hurtado_no_uso
    FROM t_placa p
    INNER JOIN t_activo a ON p.id_activo = a.id_activo
    INNER JOIN t_activo_general ag ON a.id_ag = ag.id_ag
    WHERE p.id_fondos IN ($id_fondos_list) AND ag.id_ag IN ($id_ag_list)
    GROUP BY p.codigo;
";
$link->query($create_temp_estado_uso);

// Consulta final para unir la tabla temporal con la tabla de instituciones
$final_query = "
    SELECT 
        i.codigo,
        i.institucion,
        e.muy_bueno_uso, e.muy_bueno_no_uso,
        e.bueno_uso, e.bueno_no_uso,
        e.regular_uso, e.regular_no_uso,
        e.malo_uso, e.malo_no_uso,
        e.hurtado_uso, e.hurtado_no_uso
    FROM t_instituciones i
    LEFT JOIN temp_estado_uso e ON i.codigo = e.codigo;
";
$result = $link->query($final_query);

// Generación de la tabla HTML con Bootstrap 5
if ($result && $result->num_rows > 0) {
    echo "<table class='table table-striped table-bordered' id='testTable'>
            <thead class='thead-dark'>
                <tr>
                    <th>Código</th>
                    <th>Institución</th>
                    <th>Muy Bueno (En Uso)</th>
                    <th>Muy Bueno (No Uso)</th>
                    <th>Bueno (En Uso)</th>
                    <th>Bueno (No Uso)</th>
                    <th>Regular (En Uso)</th>
                    <th>Regular (No Uso)</th>
                    <th>Malo (En Uso)</th>
                    <th>Malo (No Uso)</th>
                    <th>Hurtado (En Uso)</th>
                    <th>Hurtado (No Uso)</th>
                </tr>
            </thead>
            <tbody>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['codigo']) . "</td>
                <td>" . htmlspecialchars($row['institucion']) . "</td>
                <td>" . $row['muy_bueno_uso'] . "</td>
                <td>" . $row['muy_bueno_no_uso'] . "</td>
                <td>" . $row['bueno_uso'] . "</td>
                <td>" . $row['bueno_no_uso'] . "</td>
                <td>" . $row['regular_uso'] . "</td>
                <td>" . $row['regular_no_uso'] . "</td>
                <td>" . $row['malo_uso'] . "</td>
                <td>" . $row['malo_no_uso'] . "</td>
                <td>" . $row['hurtado_uso'] . "</td>
                <td>" . $row['hurtado_no_uso'] . "</td>
              </tr>";
    }
    
    echo "</tbody></table>";
} else {
    echo "<div class='alert alert-warning' role='alert'>No se encontraron resultados.</div>";
}

// Cerrar la conexión a la base de datos
$link->close();
?>




    </main> <!-- Cierre del main -->
  </div>
</div>

<!-- Seccion de modals -->
<script type="text/javascript">
$(document).ready(function () {
   (function($) {
       $('#FiltrarContenido').keyup(function () {
            var ValorBusqueda = new RegExp($(this).val(), 'i');
            $('.BusquedaRapida tr').hide();
             $('.BusquedaRapida tr').filter(function () {
                return ValorBusqueda.test($(this).text());
              }).show();
                })
      }(jQuery));
});
</script>


    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="js/tableToExcel.js"></script>
    <script src="js/feather.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/dashboard.js"></script>

  </body>
