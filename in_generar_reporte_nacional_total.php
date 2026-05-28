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
<h2 class = "text-center mt-3">Concentrado general seg&uacute;n fondos y variables seleccionadas</h2><br><br>
<button type="button" class="btn btn-success" onclick="tableToExcel('testTable', 'Resumen')"><i class="bi bi-file-earmark-excel-fill"></i> Excel</button>
<?php

if ($link->connect_error) {
    die("Error de conexión: " . $link->connect_error);
}

// Lista de ID de activos generales filtrados, asumimos que $id_ag_list contiene una lista separada por comas de los id
//$id_ag_list = '1,2,3'; // Ejemplo, reemplazar con valores dinámicos según sea necesario

// Paso 1: Crear la tabla temporal para los conteos de estado
$create_temp_estado = "
    CREATE TEMPORARY TABLE temp_estado AS
    SELECT p.codigo, 
        COUNT(CASE WHEN p.id_estado = 1 THEN 1 END) AS muy_bueno,
        COUNT(CASE WHEN p.id_estado = 2 THEN 1 END) AS bueno,
        COUNT(CASE WHEN p.id_estado = 3 THEN 1 END) AS regular,
        COUNT(CASE WHEN p.id_estado = 4 THEN 1 END) AS malo,
        COUNT(CASE WHEN p.id_estado = 5 THEN 1 END) AS hurtado
    FROM t_placa p
    WHERE p.id_fondos IN ($id_fondos_list)
    GROUP BY p.codigo;
";
$link->query($create_temp_estado);

// Paso 2: Crear la tabla temporal para los conteos de lugar
$create_temp_lugar = "
    CREATE TEMPORARY TABLE temp_lugar AS
    SELECT p.codigo,
        COUNT(CASE WHEN p.id_lugar = 1 THEN 1 END) AS Bodega,
        COUNT(CASE WHEN p.id_lugar = 2 THEN 1 END) AS Laboratorio,
        COUNT(CASE WHEN p.id_lugar = 3 THEN 1 END) AS Sala_de_robotica,
        COUNT(CASE WHEN p.id_lugar = 4 THEN 1 END) AS Aulas,
        COUNT(CASE WHEN p.id_lugar = 5 THEN 1 END) AS Biblioteca,
        COUNT(CASE WHEN p.id_lugar = 6 THEN 1 END) AS Oficinas_administrativas
    FROM t_placa p
    WHERE p.id_fondos IN ($id_fondos_list)
    GROUP BY p.codigo;
";
$link->query($create_temp_lugar);

// Paso 3: Crear la tabla temporal para los equipos filtrados según id_ag_list
$create_temp_equipos_filtrados = "
    CREATE TEMPORARY TABLE temp_equipos_filtrados AS
    SELECT p.codigo,
        COUNT(CASE WHEN ag.id_ag IN ($id_ag_list) THEN 1 END) AS Equipos_filtrados
    FROM t_placa p
    INNER JOIN t_activo a ON p.id_activo = a.id_activo
    INNER JOIN t_activo_general ag ON a.id_ag = ag.id_ag
    WHERE p.id_fondos IN ($id_fondos_list)
    GROUP BY p.codigo;
";
$link->query($create_temp_equipos_filtrados);

// Paso 4: Consulta final para unir las tablas temporales con la tabla de instituciones
$final_query = "
    SELECT 
        i.codigo,
        i.institucion,
        e.muy_bueno, e.bueno, e.regular, e.malo, e.hurtado,
        l.Bodega, l.Laboratorio, l.Sala_de_robotica, l.Aulas, l.Biblioteca, l.Oficinas_administrativas,
        ef.Equipos_filtrados
    FROM t_instituciones i
    LEFT JOIN temp_estado e ON i.codigo = e.codigo
    LEFT JOIN temp_lugar l ON i.codigo = l.codigo
    LEFT JOIN temp_equipos_filtrados ef ON i.codigo = ef.codigo;
";
$result = $link->query($final_query);

// Generación de la tabla HTML
if ($result && $result->num_rows > 0) {
    echo "<table class='table table-striped' id='testTable'>
    <thead>
        <tr>
            <th>Código</th>
            <th>Institución</th>
            <th>Muy Bueno</th>
            <th>Bueno</th>
            <th>Regular</th>
            <th>Malo</th>
            <th>Hurtado</th>
            <th>Bodega</th>
            <th>Laboratorio</th>
            <th>Sala de Robótica</th>
            <th>Aulas</th>
            <th>Biblioteca</th>
            <th>Oficinas Administrativas</th>
        </tr>
    </thead>
    <tbody class='BusquedaRapida'>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($row['codigo']) . "</td>
            <td>" . htmlspecialchars($row['institucion']) . "</td>
            <td>" . $row['muy_bueno'] . "</td>
            <td>" . $row['bueno'] . "</td>
            <td>" . $row['regular'] . "</td>
            <td>" . $row['malo'] . "</td>
            <td>" . $row['hurtado'] . "</td>
            <td>" . $row['Bodega'] . "</td>
            <td>" . $row['Laboratorio'] . "</td>
            <td>" . $row['Sala_de_robotica'] . "</td>
            <td>" . $row['Aulas'] . "</td>
            <td>" . $row['Biblioteca'] . "</td>
            <td>" . $row['Oficinas_administrativas'] . "</td>
        </tr>";
    }
    
    echo "</tbody></table>";
} else {
    echo "No se encontraron resultados.";
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