<?php  
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4 or $_SESSION['tipo']==5);
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
$logdependencia = $_SESSION['dependencia'];

$codigo_consultar = "4071";
$inicio_consultar = "2022-01-01";
$corte_consultar = "2023-01-01";
$activado = 1;

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>Reporte prestamos realizados por tipo de origen de fondos</title>
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
  <a class="navbar-brand" href="formulario_informe_solicitudes.php">Volver a consultar</a>
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
        <h2><?php echo $logdependencia; ?></h2> <br>
        <h3> Reporte de Préstamos tramitados de equipos Programa 3 </h3>


  
	<table class="table table-hover">

	<th>N&ordm; solicitud</th>
	<th>Solicitante</th>
	<th>Retiro</th>
    <th>Devoluci&oacute;n</th>
	<th>Uso</th>

	<tbody class="BusquedaRapida">
	<?php
	
	
	 	
   $consulta=mysqli_query($link,"SELECT a.solicitud_Id, a.solicitud_nombre_funcionario, a.solicitud_fechaRetiro, 
   a.solicitud_fechaDevolucion, a.solicitud_horaRetiro, a.solicitud_horaDevolucion, a.solicitud_uso, b.solicitud_detalle_cantidad, c.id_fondos
		 FROM t_solicitud a
		 INNER JOIN t_solicitud_detalle b
		  ON a.solicitud_Id = b.solicitud_Id
		 INNER JOIN t_placa c
		  ON b.solicitud_detalle_id_placa = c.id_placa
		 WHERE a.solicitud_fechaRetiro BETWEEN '".$inicio_consultar."' AND  '".$corte_consultar."' AND a.solicitud_codigo_presupuestario = '".$codigo_consultar."' AND a.solicitud_aprobada = '".$activado."' AND c.id_fondos = 2
		 ORDER BY a.solicitud_Id") or die(mysqli_error($link));

  	

	while ($activos=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $activos['solicitud_Id']?></td>
		<td><?php echo $activos['solicitud_nombre_funcionario']?></td>
		<td><?php echo $activos['solicitud_fechaRetiro']?></td>
		<td><?php echo $activos['solicitud_fechaDevolucion']?></td>
		<td><?php echo $activos['solicitud_uso']?></td>

	</tr>
	<?php } 
    $sql = "SELECT COUNT(*) solicitud_Id FROM t_solicitud WHERE solicitud_fechaRetiro BETWEEN '$inicio_consultar' AND '$corte_consultar' AND solicitud_codigo_presupuestario='$codigo_consultar' AND solicitud_aprobada = '$activado'";
    $query = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($query);
    $count = $row ['solicitud_Id'];
	?>
	<tr>
		<td colspan="4"><b>Total de pr&eacute;stamos realizados</b></td>
		<td colspan="2"><b><?php echo $count ?></b></td>
	</tr>	
	<?php
	mysqli_close($link);	
	?>
</tbody>
</table>

 </div>
</div>

  <script src="js/imprereporte.js"></script>
</body>
</html>
