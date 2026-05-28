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

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>Tecnopresta Reporte</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/validar.css"> 

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
        <a class="nav-link" href="inventario_reporte.php">Reportes</a>
      </li>   
    </ul>
  </div>  
</nav>
<br>

<div class="container">

<h3>Usuario: <?php echo $lognombre ." ". $logcodigo; ?> </h3><br>
<form action="reporte_prestamos_aprobados.php" method="POST">
    <fieldset>
    <legend>Reporte de pr&eacute;stamos por instituci&oacute;n</legend>
  <div class="input-group mb-3">
   <span class="input-group-text">C&oacute;digo presupuestario</span>
   <input type="text" name="codigo" class="form-control">
  </div>
  <div>
    <label for="inicio">Elija la fecha de inicio (obligatorio):</label>
    <input type="date" id="inicio" name="inicio" min="2021-01-01" max="2030-12-31" required>
    <span class="validity"></span>
  </div>
    <div>
    <label for="corte">Elija la fecha de corte (obligatorio):</label>
    <input type="date" id="corte" name="corte" min="2021-01-01" max="2030-12-31" required>
    <span class="validity"></span>
  </div>
  <div>
    <p><button type="submit"><img src="imarep/reporte32.png" width="15px"> Generar</button></p>
  </div>
  </fieldset>
</form>


</div>

</body>
</html>
