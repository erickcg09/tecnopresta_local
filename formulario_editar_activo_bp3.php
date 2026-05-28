<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
</script>';
}
require_once("conexion.php");
$link = $mysqli;
date_default_timezone_set('America/Costa_Rica'); //configuro un nuevo timezone
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
$year = date('Y');

$idabp3 = $_GET['gps'];

		$preguntar = mysqli_query($link, "select placa, serial
		from activos_beneficiarios_programa_3 where id='$idabp3'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$placa = $respuesta['placa'];
		$serial = $respuesta['serial'];

?>
<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
      <script type="text/javascript" src="js/jquery.min.js"></script>

    <title>Editar Activo BP3</title>
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
            <a class="nav-link" aria-current="page" href="beneficiarios_programa_3.php">Regresar</a>
            <a class="nav-link" href="gameover.php">Cerrar sesi&oacute;n</a>
          </div>
        </div>
      </div>
    </nav> 
<div class="container">
<br><br><p class="h2">Editar Placa y Serie del Equipo</p><br>
<form action="actualizar_activo_bp3.php" method="post">
    <input type="hidden" name="idabp3" value="<?php echo $idabp3;?>">
   
          <div class="mb-3">
            <label class="col-form-label">Placa:</label>
            <input type="text" class="form-control" name="placa" value="<?php echo $placa;?>" required onkeypress="return event.charCode != 39">
          </div>
          <div class="mb-3">
            <label class="col-form-label">Serial:</label>
            <input type="text" class="form-control" name="serial" value="<?php echo $serial;?>" required onkeypress="return event.charCode != 39">
          </div>
    <br>
    <button type="submit" class="btn btn-primary">Guardar</button>
</form>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
</div> <!-- Cierre del container -->
  </body>
</html>