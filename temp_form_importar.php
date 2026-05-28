<?php  
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>importe temporal</title>
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


</head>
<body>


<div class="container">

      <div class="row">

        <div class="col-md-6">


		<form action="temp_importar.php" method="post" enctype="multipart/form-data">

		  <div class="form-group">
		    <label for="exampleInputEmail1">Seleccionar Plantilla CSV</label>
		    <input type="file" name="pcsv" id="pcsv" aria-describedby="emailHelp" required>
		    <small id="emailHelp" class="form-text text-muted">Por favor utilizar la plantilla con formato oficial facilitado por el Grupo Desarrollador de TecnoPresta.</small>
		  </div>
		  <button type="submit" class="btn btn-dark"><span class="icon icon-truck"> Importar Lote de Datos</span></button>
		</form>

        </div>

      </div>

</div>

</body>
</html>