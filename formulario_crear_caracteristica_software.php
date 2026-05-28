<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4);
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
$logcodigo = $_SESSION['codigo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Caracter&iacute;sticas del Software</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="fondoresponsive.css">
  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">
            <style>
		.button {
		  background-color: #0080FF;
		  border: none;
		  color: white;
		  padding: 15px 32px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 18px;
		  margin: 4px 2px;
		  cursor: pointer;
		  width: 70px;
                  text-transform: uppercase;
                  letter-spacing: 2px;
                  border-radius: 10px;
                  transition: all 300ms;
		}
	     </style>
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
        <a class="nav-link" href="inventario_mantenimiento.php"><span class="icon icon-undo2"></span> Mantenimiento</a>
      </li> 
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><span class="icon icon-enter"></span> Cerrar Sesi&oacute;n</a>
      </li>  
    </ul>
  </div>  
</nav>
<br>

<div class="container">
  <div class="row">

    <div class="col-md-6">
	  <h3>Usuario: <?php echo $lognombre ." ". $logcodigo; ?> </h3><br>

<h3>Característica del Software</h3><a href="ayuda.html#cs">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a><a>&nbsp;&nbsp;</a> <a href="contactenos.php?rep=Error en Caracter&iacute;stica del Software">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error ]</a><br><br>
  
<form name="frmarca" action="guardar_caracteristica_software.php" method="post">
  <div class="form-group">
    <label for="caracteristica">Tipo de licencia</label>
    <input type="text" class="form-control" id="caracteristica" name="caracteristica" aria-describedby="caracteristicaAyuda" required maxlength="50" onkeypress="return event.charCode != 39">
    <small id="caracteristicaAyuda" class="form-text text-muted">Antes de agregar una caracteristica, verifica si ya esta en lista.</small>
  </div>
  <div>
         <button type="submit" class="btn btn-dark"><span class="icon icon-floppy-disk"> Guardar</span></button><br>
  </div>
</form>
<br>
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1">Buscar</span>
  </div>
  <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese la característica a filtrar" aria-describedby="basic-addon1">
</div>	 
<table class="table table-hover">

	<h2></h2>
	<th>ID</th>
	<th>Característica</th>
	<th>Editar</th>
	<th>Eliminar</th>
        <tbody class="BusquedaRapida">
	<?php
$consulta=mysqli_query($link,"SELECT id_cs, caracteristica
		 FROM t_caracteristica_software
		 ORDER BY caracteristica ASC") or die(mysqli_error($link));


	while ($carac=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $carac['id_cs']?></td>
		<td><?php echo $carac['caracteristica']?></td>
		<td><a class="btn btn-dark" href="formulario_editar_caracteristica_software.php?gps=<?php echo $carac['id_cs']?>" role="button"><span class="icon-pencil2"></span></a></td>
            
                <td><a class="btn btn-dark" href="eliminar_caracteristica_software.php?gps=<?php echo $carac['id_cs']?>" role="button" onclick="return confirm('Estás seguro que deseas eliminar el registro?');"><span class="icon icon-bin"></span></a></td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>
    </div>
    <div class="col-md-6">
<div class="d-none d-sm-none d-md-block"><img src="img/carateristicas.png"width="600" height="600"></div>
    </div>
  </div>
</div>
</body>
</html>
