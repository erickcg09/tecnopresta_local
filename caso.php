<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
</script>';
}
include 'global/config.php';
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
$loginstitucion = $_SESSION['dependencia'];
$logcorreo = $_SESSION['correomep'];
$regionallog = $_SESSION['direccionreg'];
$circuitolog = $_SESSION['circuito'];
$estatus = "Abierto";
$activado = 1;

$id_soporte = $_GET['idx'];
		$preguntar = mysqli_query($link, "select * from soporte where id='$id_soporte'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$funcionario = $respuesta['funcionario'];
		$placa = $respuesta['placa'];
		$problema = $respuesta['problema'];
		$fecha = $respuesta['fecha'];
		$estatus = $respuesta['estatus'];
		$codigo = $respuesta['codigo'];
		$correo = $respuesta['correo'];
		$institucion = $respuesta['institucion'];
		$dre = $respuesta['dre'];
		$circuito = $respuesta['circuito'];
		
//		$preguntar2 = mysqli_query($link, "select id_activo from t_placa where placa='$placa'");   
//		$respuesta2 = mysqli_fetch_array($preguntar2);
//		$id_activo = $respuesta2['id_activo'];

//$consulta=mysqli_query($link,"SELECT Ta.modelo, Ta.id_ag, Tm.marca, Ta.modelo, Tc.color, Tag.clase
//		 FROM t_activo Ta
//		 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
//		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
//		 INNER JOIN t_activo_general Tag ON Ta.id_ag = Tag.id_ag
//		 WHERE Ta.id_activo = '".$id_activo."' 
//		 ORDER BY Ta.modelo ASC") or die(mysqli_error($link));
	        
//	while ($activos=mysqli_fetch_array($consulta)) {
//	    $etiqueta=$activos['clase']." ".$activos['modelo']." ".$activos['marca']." "."color ".$activos['color'];

//	}
?>
<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Caso soporte t&eacute;cnico</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">TecnoPresta</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
          <div class="navbar-nav">
            <a class="nav-link" aria-current="page" href="panel_soporte.php">Regresar</a>
            <a class="nav-link" href="gameover.php">Cerrar sesi&oacute;n</a>
          </div>
        </div>
            <form class="d-flex" method="post" action="guardar_tomar_caso.php">
                <input type="hidden" name="id_soporte" value="<?php echo $id_soporte;?>">
                <input type="hidden" name="correosolicitante" value="<?php echo $correo;?>">
                <button type="submit" class="btn btn-primary position-relative">
                  Tomo el caso <svg width="1em" height="1em" viewBox="0 0 16 16" class="position-absolute top-100 start-50 translate-middle mt-1 bi bi-caret-down-fill" fill="#0275d8" xmlns="http://www.w3.org/2000/svg"><path d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/></svg>
                </button>
            </form>
      </div>
    </nav>
<div class="container"> 
  <div class="row">
    <div class="col-12">
        <div class="text-center">
<br>
              <img src="img/porasignar.png" class="img-fluid w-75" alt="...">
              
        </div>
    </div>
<table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col" colspan="2" class="text-center">Innformaci&oacute;n General</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Instituci&oacute;n</td>
      <td><?php echo $institucion; ?></td>
    </tr>
    <tr>
      <td>DRE</td>
      <td><?php echo $dre; ?></td>
    </tr>
    <tr>
      <td>Circuito</td>
      <td><?php echo $circuito; ?></td>
    </tr>
    <tr>
      <td>C&oacute;digo</td>
      <td><?php echo $codigo; ?></td>
    </tr>
    <tr>
      <td>Solicitante</td>
      <td><?php echo $funcionario; ?></td>
    </tr>
    <tr>
      <td>Correo</td>
      <td><?php echo $correo; ?></td>
    </tr>
    <tr>
      <th scope="col" colspan="2" class="text-center">Informaci&oacute;n del Activo</th>
    </tr>
    <tr>
      <td>Placa / Asunto</td>
      <td><?php echo $placa; ?></td>
    </tr>
    <tr>
      <td>Activo</td>
      <td></td>
    </tr>
    <tr>
      <th scope="col" colspan="2" class="text-center">Descripci&oacute;n del Problema</th>
    </tr>
    <tr>
      <td>Detalle</td>
      <td><?php echo $problema; ?></td>
    </tr>
  </tbody>
</table>

</div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


  </body>
  <br>
  <br>
  <br>
  <br>
  
  
  
<footer class="bg-light text-center text-lg-start">
  <!-- Grid container -->
  <div class="container p-4">
    <!--Grid row-->
    <div class="row">
      <!--Grid column-->
      <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
        <h5 class="text-uppercase">Objetivos del Centro de Soporte</h5>

        <p>
          El Centro de Soporte es un canal o mesa de ayuda que resuelve problemas simples de funcionarios del MEP. Es el servicio que se encarga de responder, por ejemplo, dudas sobre programas (software) o recibir solicitudes de reparación de hadware (hardware). 
        </p>
      </div>
      <!--Grid column-->

      <!--Grid column-->
      <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
        <h5 class="text-uppercase">Alcances del Centro de Soporte</h5>

        <p>
          El Centro de Soporte trabaja con un equipo base limitado, y recibe apoyo de funcionarios(as) voluntarios. Sus alcances estan limitados a soporte de Software y pueda que no se cuente con repuestos para atender soluciones que lo requieran. Sin embargo, esperamos poder servirles en la mayoría de casos posibles.
        </p>
      </div>
      <!--Grid column-->
    </div>
    <!--Grid row-->
  </div>
  <!-- Grid container -->

 
  <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
     TecnoPresta es realizado por gente MEP, para la gente del MEP.
     </div>
  
</footer>