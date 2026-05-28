<?php  
session_start();
$tienellave = ($_SESSION['tipo']==1);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "inventario_reporte.php"
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
$logdependencia = $_SESSION['dependencia'];


$fonatel = 2;

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>Informe de instituciones que han subido inventario programa 3</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="fondoresponsive.css">
  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css"> 
 



</head>
<body>

<nav class="navbar navbar-expand-md bg-dark navbar-dark">
  <a class="navbar-brand" href="inventario_reporte.php">Volver</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link"><button id="btnImprimir" type="button" class="btn btn-secondary">Imprimir</button></a>
      </li> 
    </ul>
  </div>  
</nav>
<br>

<div class="container">
  
	  <h3>Usuario: <?php echo $lognombre; ?> </h3><br>
 <div id="imprimible">

        <h3> Informe de instituciones que han subido inventario de Programa 3 </h3>


<table class="table">  
<?php


$result = mysqli_query($link,"SELECT bk1.codigo,bk1.institucion
FROM  instituciones bk1 
JOIN(
SELECT codigo
FROM t_placa 
GROUP BY codigo) AS bk2
ON bk1.codigo=bk2.codigo Order by codigo") or die(mysqli_error($link));


while($crow = mysqli_fetch_assoc($result))
            			{	
?>
<tr>
    <td> <?php echo $crow['codigo'];?></td>
    <td> <?php echo $crow['institucion']; ?></td>
</tr>
<?php
  	    	}		
?>


</table>   

 </div>
</div>

  <script src="js/imprereporte.js"></script>
</body>
</html>
