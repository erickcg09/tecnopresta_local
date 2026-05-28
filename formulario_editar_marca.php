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

$id_marca = $_GET['gps'];

		$preguntar = mysqli_query($link, "select marca from t_marca where id_marca='$id_marca'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$marca = $respuesta['marca'];
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
        <a class="nav-link" href="formulario_crear_marca.php"> <span class="icon icon-undo2"></span> Marcas</a>
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

<h3>Editar Marca</h3><br>
      <div class="row">

        <div class="col-md-6">
		<form action="editar_marca.php" method="post">
		  <div class="form-group">
		    <label for="marca">Marca comercial</label>
		    <input type="text" class="form-control" name="marca" id="marca" aria-describedby="marcalHelp" value="<?php echo $marca;?>">
		    <small id="marcalHelp" class="form-text text-muted">Recuerde que este apartado es únicamente para corregir errores de escritura.</small>
		  </div>
  		    <input type="hidden" id="idmarca" name="idmarca" value="<?php echo $id_marca;?>">
		  <button type="submit" class="btn btn-dark"><span class="icon icon-floppy-disk"> Guardar</span></button>
		</form>
        </div>
        <div class="col-md-6">
<div class="d-none d-sm-none d-md-block"><img src="img/edicion-marca.png "width="600" height="600"></div>
        </div>

      </div>

</div>
</body>
</html>

