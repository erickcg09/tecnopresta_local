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

$activo = $_POST['activo'];
$placa = $_POST['placa'];
$serie = $_POST['serie'];
$encargado = $_POST['encargado'];
$cedula = $_POST['cedula'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$estudiante = $_POST['estudiante'];
$idest = $_POST['idest'];
$direccion = $_POST['direccion'];
$insti = $_POST['insti'];
$codigo = $_POST['codigo'];
$fecha_i = $_POST['fecha_i'];
$fecha_f = $_POST['fecha_f'];

$fi_es = date("d/m/Y", strtotime($fecha_i));

$ff_es = date("d/m/Y", strtotime($fecha_f));

	
$query = "INSERT INTO t_beneficiario (activo,placa,serie,encargado,cedula,email,telefono,estudiante,idest,direccion,insti,codigo,fecha_i,fecha_f)VALUES('".$activo."', '".$placa."', '".$serie."', '".$encargado."', '".$cedula."', '".$email."', '".$telefono."', '".$estudiante."', '".$idest."', '".$direccion."', '".$insti."', '".$codigo."', '".$fecha_i."', '".$fecha_f."')";
	$link->query($query);
	mysqli_close($link);


?>
<html>
<head>
<title> <b>Imprimir Boleta </b> </title>
<style type="text/css">		
		.botonimprimir{
        	background-image:url(boleta/firmar.png);
        	background-repeat:no-repeat;
        	height:80px;
        	width:122px;
        	background-position:center;
        	}
</style>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
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
        <a class="nav-link" href="inventario_reporte.php"><span class="icon icon-undo2"></span> Principal</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><span class="icon icon-enter"></span> Cerrar Sesi&oacute;n</a>
      </li>  
    </ul>
  </div>  
</nav>

<div class="container">
<div class="row">
    <div class="col-md-6">    
    <br><br>
<h3> <b> Firmar compromiso y descargar el documento </b> </h3>
<input type="button" onclick="location.href='formulario_boleta.php?varactivo=<?php echo $activo?>&varplaca=<?php echo $placa?>&varserie=<?php echo $serie ?>&varencargado=<?php echo $encargado?>&varcedula=<?php echo $cedula?>&varemail=<?php echo $email?>&vartelefono=<?php echo $telefono?>&varestudiante=<?php echo $estudiante?>&varidest=<?php echo $idest?>&vardireccion=<?php echo $direccion?>&varinsti=<?php echo $insti?>&varcodigo=<?php echo $codigo?>&varfechai=<?php echo $fi_es?>&varfechaf=<?php echo $ff_es?>';" class="botonimprimir"/>
</div>
    <div class="col-md-6">
<br><br>
<div class="d-none d-sm-none d-md-block"><img src="img/compro.png "width="600" height="400"></div>
          

    </div>
</div>
</div>
</body>
</html>