<?php
session_start();
$tienellave = ($_SESSION['tipo']==1);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
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
$year = date('Y');


$result = $link->query("SELECT COUNT(*) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022'");
$row = $result->fetch_row();
$conteo = $row[0];


$resultado = $link->query("SELECT COUNT(DISTINCT(codigo)) AS total FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022'");
$row = $resultado->fetch_row();
$conteocodigo = $row[0];

$resultM = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND sexo='Mujer'");
$row = $resultM->fetch_row();
$conteoM = $row[0];

$resultH = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND sexo='Hombre'");
$row = $resultH->fetch_row();
$conteoH = $row[0];

$resultMaterno = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='1'");
$row = $resultMaterno->fetch_row();
$conteoMaterno = $row[0];

$resultTran = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='2'");
$row = $resultTran->fetch_row();
$conteoTran = $row[0];

$resultPrimero = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='3'");
$row = $resultPrimero->fetch_row();
$conteoPrimero = $row[0];

$resultSegundo = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='4'");
$row = $resultSegundo->fetch_row();
$conteoSegundo = $row[0];

$resultTercero = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='5'");
$row = $resultTercero->fetch_row();
$conteoTercero = $row[0];

$resultCuarto = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='6'");
$row = $resultCuarto->fetch_row();
$conteoCuarto = $row[0];

$resultQuinto = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='7'");
$row = $resultQuinto->fetch_row();
$conteoQuinto = $row[0];

$resultSexto = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='8'");
$row = $resultSexto->fetch_row();
$conteoSexto = $row[0];

$resultSeptimo = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='9'");
$row = $resultSeptimo->fetch_row();
$conteoSeptimo = $row[0];

$resultOctavo = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='10'");
$row = $resultOctavo->fetch_row();
$conteoOctavo = $row[0];

$resultNoveno = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='11'");
$row = $resultNoveno->fetch_row();
$conteoNoveno = $row[0];

$resultDecimo = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='12'");
$row = $resultDecimo->fetch_row();
$conteoDecimo = $row[0];

$resultUndecimo = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='13'");
$row = $resultUndecimo->fetch_row();
$conteoUndecimo = $row[0];

$resultDuodecimo = $link->query("SELECT COUNT(id) FROM `beneficiarios_programa_3` WHERE entregado='Asignado' AND periodo='2022' AND nivel='14'");
$row = $resultDuodecimo->fetch_row();
$conteoDuodecimo = $row[0];
?>

<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Reporte Beneficiarios Programa 3</title>
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
            <a class="nav-link" aria-current="page" href="inventario_reporte.php">Regresar</a>
            <a class="nav-link" aria-current="page" href="javascript:imprSelec('seleccion')"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
  <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
  <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
</svg>&nbsp;Imprimir</a>
            <a class="nav-link" href="gameover.php">Cerrar sesi&oacute;n</a>
          </div>
        </div>
      </div>
    </nav>      
<div class="container" id="seleccion">

  <div class="row">
    <div class="col-12">
        <div class="text-center">
<br>
              <img src="img/rb3.png" class="img-fluid w-75" alt="Imagen Frontal">
        </div>
    </div>  
  </div>
<br>

<table class="table">

  <tbody>
    <tr>
      <th scope="row">Estudiantes con dispositivo asignado</th>
      <td><?php echo $conteo; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de mujeres</th>
      <td colspan="2"><?php echo $conteoM; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de hombres</th>
      <td colspan="2"><?php echo $conteoH; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de preescolar materno</th>
      <td colspan="2"><?php echo $conteoMaterno; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de preescolar transici&oacute;n</th>
      <td colspan="2"><?php echo $conteoTran; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de primer grado</th>
      <td colspan="2"><?php echo $conteoPrimero; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de segundo grado</th>
      <td colspan="2"><?php echo $conteoSegundo; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de tercero grado</th>
      <td colspan="2"><?php echo $conteoTercero; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de cuarto grado</th>
      <td colspan="2"><?php echo $conteoCuarto; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de quinto grado</th>
      <td colspan="2"><?php echo $conteoQuinto; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de sexto grado</th>
      <td colspan="2"><?php echo $conteoSexto; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de s&eacute;ptimo grado</th>
      <td colspan="2"><?php echo $conteoSeptimo; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de octavo grado</th>
      <td colspan="2"><?php echo $conteoOctavo; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de noveno grado</th>
      <td colspan="2"><?php echo $conteoNoveno; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de d&eacute;cimo grado</th>
      <td colspan="2"><?php echo $conteoDecimo; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de und&eacute;cimo grado</th>
      <td colspan="2"><?php echo $conteoUndecimo; ?></td>
    </tr>
    <tr>
      <th scope="row">Total de duod&eacute;cimo grado</th>
      <td colspan="2"><?php echo $conteoDuodecimo; ?></td>
    </tr>
    <tr>
      <th scope="row">Cantidad de instituciones que reportan</th>
      <td><?php echo $conteocodigo; ?></td>
    </tr>
  </tbody>
</table>


</div> <!-- Cierre de container-->
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->

<script language="Javascript">
	function imprSelec(nombre) {
	  var ficha = document.getElementById(nombre);
	  var ventimp = window.open(' ', 'popimpr');
	  ventimp.document.write( ficha.innerHTML );
	  ventimp.document.close();
	  ventimp.print( );
	  ventimp.close();
	}
</script>
  </body>
</html>