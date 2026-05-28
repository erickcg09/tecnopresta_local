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
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];

$id_estancia = $_GET['gps']; 


	$consulta = mysqli_query($link, "select * from t_estancia where id_estancia='$id_estancia'");   
        $result = mysqli_fetch_array($consulta);
	$id_edificio = $result['id_edificio'];
	$estancia = $result['estancia'];

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
  <script src="css/bootstrap-select.min.js"></script>
  <link rel="stylesheet" href="css/bootstrap-select.min.css">
  <script src="css/defaults-es_ES.min.js"></script>


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
        <a class="nav-link" href="formulario_crear_estancia.php"><span class="icon icon-undo2"></span> Regresar</a>
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

<h3>Editar Estancia / Lugar</h3>

      <div class="row">

        <div class="col-md-6">
<form action="editar_estancia.php" method="post">
  <div class="form-group">
    <input type="hidden" class="form-control" id="id_estancia" name="id_estancia" value="<?php echo $id_estancia;?>">
  </div>

	        <div class="form-group">
	          <label for="estancia">Estancia / Lugar</label>
	          <input type="estancia" class="form-control" id="estancia" name="estancia" value="<?php echo $estancia;?>" aria-describedby="estanciaHelp">
	          <small id="estanciaHelp" class="form-text text-muted">Las Estancias pueden ser laboratorios, bibliotecas, pisos de edificios, segmentos y divisiones.</small>
	        </div>
		<div>
        	  <button type="submit" class="btn btn-dark"><span class="icon icon-floppy-disk"> Guardar</span></button>
		</div>
</form>

</div>
        <div class="col-md-6">
<div class="d-none d-sm-none d-md-block"><img src="img/edicion_estancia.png "width="600" height="600"></div>
        </div>

      </div>
</body>
</html>
