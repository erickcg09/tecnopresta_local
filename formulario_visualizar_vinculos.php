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


$id_software = $_GET['gps'];
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
        <a class="nav-link" href="formulario_seleccionar_desvincular.php"><span class="icon icon-undo2"></span> Regresar</a>
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

<h3>Desvincular Licencia del PC</h3>
<br>
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1">Buscar</span>
  </div>
  <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese el tipo del activo" aria-describedby="basic-addon1">
</div>	  


<table class="table table-hover">

	<h2></h2>
	<th>ID</th>
	<th>Software</th>
	<th>Licencia</th>
        <th>Placa del PC</th>
	<th><span class="icon icon-equalizer"></span> Desvincular</th>
        <tbody class="BusquedaRapida">
	<?php
$consulta=mysqli_query($link,"SELECT Tl.id_licencia, Tsg.etiqueta, Ts.licencia, Tp.placa, Tl.id_placa, Tl.id_software
		 FROM t_licencia Tl
		 INNER JOIN t_software Ts ON Tl.id_software = Ts.id_software 
		 INNER JOIN t_placa Tp ON Tl.id_placa = Tp.id_placa
		 INNER JOIN t_software_general Tsg ON Ts.id_sg = Tsg.id_sg
		 WHERE Tl.id_software = $id_software
		 ORDER BY Tl.id_licencia ASC") or die(mysqli_error($link));



	while ($vinculados=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $vinculados['id_licencia']?></td>
		<td><?php echo $vinculados['etiqueta']?></td>
		<td><?php echo $vinculados['licencia']?></td>
		<td><?php echo $vinculados['placa']?></td>
		<td><a class="btn btn-dark" href="eliminar_vinculo.php?gps=<?php echo $vinculados['id_licencia']?>" role="button"><span class="icon-bin"></span></a></td>

	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>
</form>

</div>
</body>
</html>
