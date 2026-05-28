<?php
session_start();

// Configuración
$tiposPermitidos = [1];
$paginaDefault = 'formulario_menu_inventario.html';
$mensajeError = "No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador.";

// Verificar permisos
if (!isset($_SESSION['tipo']) || !in_array($_SESSION['tipo'], $tiposPermitidos)) {
    // Determinar página de redirección
    $redireccion = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $paginaDefault;
    
    // Redireccionar con JavaScript
    echo "<script type='text/javascript'>
            alert('$mensajeError');
            window.location.href = '$redireccion';
          </script>";
    exit();
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

$id_color = $_GET['gps'];

		$preguntar = mysqli_query($link, "select color from t_color where id_color='$id_color'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$color = $respuesta['color'];
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
  <img src="img/logomep2020.png" width="45" height="30" alt="" loading="lazy"><a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="formulario_crear_color.php"> <span class="icon icon-undo2"></span> Colores</a>
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
<h3>Editar Color</h3><br>

      <div class="row">

        <div class="col-md-6">
		<form action="editar_color.php" method="post">
		  <div class="form-group">
		    <label for="color">Color para activo</label>
		    <input type="text" class="form-control" name="color" id="color" aria-describedby="colorHelp" value="<?php echo $color;?>">
		    <small id="colorHelp" class="form-text text-muted">Recuerde que este apartado es únicamente para corregir errores de escritura.</small>
		  </div>
  		    <input type="hidden" id="idcolor" name="idcolor" value="<?php echo $id_color;?>">
		  <button type="submit" class="btn btn-dark"><span class="icon icon-floppy-disk"> Guardar</span></button>
		</form>
        </div>
        <div class="col-md-6">
<div class="d-none d-sm-none d-md-block"><img src="img/edicion-05.png "width="600" height="600"></div>
        </div>

      </div>

</div>
</body>
</html>

