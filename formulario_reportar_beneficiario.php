<?php
session_start();
$tienellave = ($_SESSION['tipo']==1);
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


	$query = "SELECT prestamo_Id, prestamo_uso FROM t_prestamo Where prestamo_codigo_presupuestario = '".$logcodigo."' ORDER BY prestamo_Id";
	$resultado=$link->query($query);
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <title>Tecnopresta</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-W8fXfP3gkOKtndU4JGtKDvXbO53Wy8SZCQHczT5FMiiqmQfUpWbYdTil/SxwZgAN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>


<link rel="stylesheet" type="text/css" href="css/dropzone.css" />
<script type="text/javascript" src="js/dropzone.js"></script>
<style type="text/css">
.file_upload{
	border: 4px dashed #292929;
	}
</style>
  </head>
  <body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Tecnopresta</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Principal</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Lenar plantilla de compromiso</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
<br>
	<h2>Almacenar compromisos de pr&eacute;stamos a estudiantes</h2>	
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title">Cargar archivos al sistema Tecnopresta</h3>
      </div>
      <div class="panel-body">
        <div class="col-lg-12">
        
        
	<div class="file_upload">
		<form action="file_upload.php" class="dropzone">
			<div class="dz-message needsclick">
				<strong>Arrastra archivos a cualquier lugar para subirlos.</strong><br /><br />
				<span class="note needsclick">
                <span class="glyphicon glyphicon-open" aria-hidden="true" style="font-size:60px;"></span>
                </span>
			</div>
		</form>		
	</div>
    	
  </div>	
 </div>	
</div>


</div>


  </body>
</html>