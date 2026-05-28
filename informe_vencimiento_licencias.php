<?php
//conexion a la base de datos
require_once("conexion.php");
$link = $mysqli;


session_start();

if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.html"
</script>';
}



$idsoftware = $_GET['gps'];
		$preguntar = mysqli_query($link, "select id_sg, factivacion, vigencia from t_software where id_software='$idsoftware'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$fechaact = $respuesta[factivacion];
		$vigencia = $respuesta[vigencia];
		$id_sg = $respuesta[id_sg];

		$preguntar2 = mysqli_query($link, "select etiqueta from t_software_general where id_sg='$id_sg'");   
		$respuesta2 = mysqli_fetch_array($preguntar2);
		$etiqueta = $respuesta2[etiqueta];

		
$fechavigencia = date("Y-m-d",strtotime($fechaact."+ $vigencia month"));

$anioi = date("Y", strtotime($fechaact));  
$mesi = date("m", strtotime($fechaact));
$diai = date("d", strtotime($fechaact));

$aniof = date("Y", strtotime($fechavigencia)); 
$mesf = date("m", strtotime($fechavigencia));
$diaf = date("d", strtotime($fechavigencia));

$primermes = 1;
$enanos = $vigencia / 12;

$medida = 350*$enanos;
?>
<html>
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">
    <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["calendar"]});
      google.charts.setOnLoadCallback(drawChart);

   function drawChart() {
       var dataTable = new google.visualization.DataTable();
       dataTable.addColumn({ type: 'date', id: 'Date' });
       dataTable.addColumn({ type: 'number', id: 'Won/Loss' });
       dataTable.addRows([
          // Fecha de activacion.
          [ new Date(<?php echo $anioi?>, <?php echo $mesi?>, <?php echo $diai?>), <?php echo $primermes?> ],

          // Fecha de vencimiento.
          [ new Date(<?php echo $aniof?>, <?php echo $mesf?>, <?php echo $diaf?>), <?php echo $enanos?> ]

        ]);

       var chart = new google.visualization.Calendar(document.getElementById('calendar_basic'));

       var options = {
         title: "Informe de Vigencia de la Licencia <?php echo $etiqueta?>",
       //  calendar: { cellSize: 10 },
         height: <?php echo $medida?>,
         noDataPattern: {
         backgroundColor: '#76a7fa',
         color: '#a0c3ff'
         }
       };


       chart.draw(dataTable, options);
   }
    </script>
  </head>
  <body>
      <nav class="navbar navbar-expand-md bg-dark navbar-dark">
  <img src="img/logomep2020.png" width="45" height="30" alt="" loading="lazy"><a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="formulario_preguntar_software.php"> <span class="icon icon-undo2"></span> Regresar</a>
      </li> 
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><span class="icon icon-enter"></span> Cerrar Sesi&oacute;n</a>
      </li>  
    </ul>
  </div>  
</nav>
<br>
<div class="container">
   <div><button type="button" class="btn btn-dark" onclick="window.print();"><span class="icon icon-printer"> Imprimir Reporte</span></button><br><br></div>
    
    
    <div id="calendar_basic" style="width: 1000px; height: <?php echo $medida?>px;"></div>
</div>
  </body>
</html>
