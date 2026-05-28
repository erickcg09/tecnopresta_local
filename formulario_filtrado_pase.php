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

$codigo = $_POST["codigo"];
$cantidad = $_POST["cantidad"]; 
$fondos = $_POST["fondos"]; 
$activo = $_POST["cbx_activo"];
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
        <a class="nav-link" href="inventario_activo.php"> <span class="icon icon-undo2"></span> Inventario</a>
      </li> 
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><span class="icon icon-enter"></span> Cerrar Sesi&oacute;n</a>
      </li>  
    </ul>
  </div>  
</nav>
<br>

<div class="container">
    
	  <h3>Usuario: <?php echo $lognombre; ?> </h3><br>

<h3>Asignar Activos a una Nueva Instancia</h3>
<br>

  
<form action="actualizar_pase_activo.php" method="post" >

<table class="table table-hover">

	<h2></h2>
	<th>Placa del Activo</th>
	<th>Serial del Activo</th>
	<th>Código Presupuestario</th>
	<th colspan="4"><button class="btn btn-dark btn-lg btn-block" type="submit" name="btnActualizar"> <span class="icon icon-floppy-disk"></span> Proceder</button></th>
        <tbody class="BusquedaRapida">
	<?php
$consulta=mysqli_query($link,"SELECT id_placa, placa, serial, codigo
		 FROM t_placa
		 WHERE codigo = '".$codigo."' AND id_fondos = '".$fondos."' AND id_activo = '".$activo."'
		 ORDER BY serial ASC LIMIT $cantidad") or die(mysqli_error($link));


	while ($activos=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $activos['placa']?></td>
		<td><?php echo $activos['serial']?></td>
		<td><?php echo $activos['codigo']?></td>
		<td><input type="hidden" name="idsplacas[]" value="<?php echo $activos['id_placa']?>"/></td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="inputGroup-sizing-default">Código presupuestario al que desea traspasar los activos</span>
  </div>
  <input type="text" name="nuevocod" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
</div>
</form>

</div>
</body>
</html>
