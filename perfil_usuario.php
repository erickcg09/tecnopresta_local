<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4 or $_SESSION['tipo']==5 or $_SESSION['tipo']==7);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
</script>';
}
require_once("conexion.php");
require_once("variablesemail.php");
include "class.phpmailer.php";
include "class.smtp.php"; 
$link = $mysqli; 

if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];
$logcorreo = $_SESSION['correomep'];
$logdireccionreg = $_SESSION['direccionreg'];
$logcircuito = $_SESSION['circuito'];
$logclase = $_SESSION['clase'];
$logespecialidad = $_SESSION['especialidad'];
$logdependencia = $_SESSION['dependencia'];
$mailtecno = "tecnopresta.mep.go.cr";
$logsesion = $_SESSION['tipo'];

$query = "select id_perfil from t_perfil where cedula='$logusuario'";
$result = mysqli_query($link,$query);
$check_user = mysqli_num_rows($result);


if($check_user>0){

	$sql1 = mysqli_query($link, "select foto from t_perfil where cedula='$logusuario'");   
	$resp1 = mysqli_fetch_array($sql1);
	$foto = $resp1['foto'];

} else {

    $foto = "defecto.png";

} /* Cierre del else que corresponde a else del $check_user>0 */

$sql = "SELECT COUNT(*) total FROM t_prestamo where prestamo_cedula_funcionario='$logusuario'";
$result = mysqli_query($link, $sql);
$fila = mysqli_fetch_assoc($result);
$solicitudes=$fila['total'];

if ($solicitudes > 20) {

$trofeo = "4.png";

} else if ($solicitudes > 10) {

$trofeo = "3.png";

} else if ($solicitudes > 1) {

$trofeo = "2.png";


} else {

$trofeo = "1.png";

}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>TecnoPresta Perfil</title>

  

    <!-- Bootstrap core CSS -->
<link rel="stylesheet" href="/css/bootstrap.min.css">
  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link href="/css/carousel.css" rel="stylesheet">
    <link href="/css/foto.css" rel="stylesheet">
  </head>
  <body>
    <header>
  <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <a class="navbar-brand" href="#">Perfil</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="formulario_menu_principal.html"><span class="icon icon-undo2"></span> Regresar</a>
        </li>

      </ul>
    </div>
  </nav>
</header>

<main role="main">

<div> 
<br>
</div>



  <div class="container marketing">

 
      <div class="col-lg-12">
		<center><div class="circular--portrait">
		  <img src="avatar/<?php echo $foto?>" />
		</div></center>
        <center><h2><?php echo $lognombre?> <?php echo $logcodigo?></h2></center><br>
        <div><center><img src="trofeo/<?php echo $trofeo?>" border="0" width="200" height="75"></center></div><br>
        
	<table class="table table-dark">

	  <tbody>
	    <tr>
	      <th scope="row">C&eacute;dula</th>
	      <td><?php echo $logusuario?></td>

	    </tr>

	    <tr>
	      <th scope="row">Clase de Puesto</th>
	      <td><?php echo $logclase?></td>

	    </tr>
	    <tr>
	      <th scope="row">Especialidad</th>
	      <td><?php echo $logespecialidad?></td>

	    </tr>
	    
	    <tr>
	      <th scope="row">Regional</th>
	      <td><?php echo $logdireccionreg?></td>

	    </tr>
	    
	    <tr>
	      <th scope="row">Circuito</th>
	      <td><?php echo $logcircuito?></td>

	    </tr>
	    
	    <tr>
	      <th scope="row">Dependencia</th>
	      <td><?php echo $logdependencia?></td>

	    </tr>
	  </tbody>

	</table>
<form name="frmfoto" action="cambiar_foto.php" method="post" enctype="multipart/form-data">
  <div class="form-group">
    <label for="imagen">Cambiar imagen a mi perfil</label>
    <input type="file" class="form-control-file" name="imagen" id="imagen" accept="image/jpeg">
  </div>
  <input type="hidden" id="cedula" name="cedula" value="<?php echo $logusuario;?>">
  <div>
         <button type="submit" class="btn btn-dark"><span class="icon icon-floppy-disk"> Guardar Imagen</span></button>
  </div>
  
</form> <br>
        <div><a class="btn btn-dark" href="eliminar_foto_perfil.php?gps=<?php echo $logusuario?>" role="button"><span class="icon icon-bin"> Eliminar Imagen</span></a></div>
  </div>

</main>

</html>