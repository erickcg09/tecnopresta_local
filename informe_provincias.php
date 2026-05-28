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
if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
$activado = 1;

include "datab.php";
 ?>


<!DOCTYPE html>
<html lang="es">
<head>
  <title>PNTM Principal</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="fondoresponsive.css">
  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">




	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>  
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
google.charts.load('current', {
  'packages':['geochart'], //paquete para indicar a los charts que usaremos un mapa para mostrar los datos
  // Note: you will need to get a mapsApiKey for your project.
  // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
  'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
});
google.charts.setOnLoadCallback(drawRegionsMap);
 
var gmapa_dispersion = {};
google.charts.load('current', {
  'packages': ['geochart'],
  // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
  'mapsApiKey': 'XXXXX'
});
google.charts.setOnLoadCallback(drawRegionsMap);

function drawRegionsMap() {
  //mapa listo
  gmapa_dispersion = new google.visualization.GeoChart(document.getElementById('regions_div'));
  //Cargamos datos a arreglo de gmapData
  var gmapData = [];
  gmapData.push(['States', 'Estado', 'Solicitudes']);
  //definimos columnas States (clave ISO), Estado (etiqueta de region), Solicitudes (dato numerico a graficar se toman de la consulta de la tabla)
  for (d in regiones_ISO) {
    gmapData.push([regiones_ISO[d].clave, regiones_ISO[d].entidad, regiones_ISO[d].cuenta])
  } //cargamos los datos de regiones_ISO al arreglo de gmapData
  gmapData = google.visualization.arrayToDataTable(gmapData);
  //conversion de arreglo a DataTable para google
  gmapa_dispersion.draw(gmapData, {
    displayMode: 'regions', //centrado en la region
    region: 'CR', //mostrar solo Costa Rica
    resolution: 'provinces' //mostrar la division entre las 7 provincias 
  });
}

var regiones_ISO = [{
    'entidad': 'Alajuela',
    'cuenta': <?php echo $p1?>,
    'clave': 'CR-A'
  },
  {
    'entidad': 'Cartago',
    'cuenta': <?php echo $p2?>,
    'clave': 'CR-C'
  },
  {
    'entidad': 'Guanacaste',
    'cuenta': <?php echo $p3?>,
    'clave': 'CR-G'
  },
  {
    'entidad': 'Heredia',
    'cuenta': <?php echo $p4?>,
    'clave': 'CR-H'
  },
  {
    'entidad': 'Lim\u00f3n',
    'cuenta': <?php echo $p5?>,
    'clave': 'CR-L'
  },
  {
    'entidad': 'Puntarenas',
    'cuenta': <?php echo $p6?>,
    'clave': 'CR-P'
  },
  {
    'entidad': 'San Jos\u00e9',
    'cuenta': <?php echo $p7?>,
    'clave': 'CR-SJ'
  },

]


    </script> 
    
        <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Solicitudes', 'Hasta Hoy'],
          ['Alajuela',     <?php echo $p1?>],
          ['Cartago',      <?php echo $p2?>],
          ['Guanacaste',  <?php echo $p3?>],
          ['Heredia', <?php echo $p4?>],
          ['Lim\u00f3n', <?php echo $p5?>],
	  ['Puntarenas', <?php echo $p6?>],
          ['San Jos\u00e9',    <?php echo $p7?>]
        ]);

        var options = {
          title: 'Expresi\u00f3n Porcentual',
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
      }
    </script>

</head>
<body>

<nav class="navbar navbar-expand-md bg-dark navbar-dark">
  <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="inventario_reporte.php">Reportes</a>
      </li>   
    </ul>
  </div>  
</nav>
<br>

<div class="container">
  <div class="row">
    <div class="col-md-8">    
        <h2>Solicitudes Efectivas de Pr&eacute;stamos</h2><br>
        <div id="regions_div" style="width: 650px; height: 550px;"></div>
    </div>
    <div class="col-md-4">
        <h2>Gr&aacute;fico 1</h2><br>
        <div id="piechart_3d" style="width: 800px; height: 400px;"></div>
    </div>
  </div>
</div>
</body>
</html>

