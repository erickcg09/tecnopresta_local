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
            <form class="d-flex" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
                <input type="text" name="codigo" placeholder="C&oacute;digo corto" maxlength="4" onkeypress="return valida(event)" required="" autofocus class="form-control" id="codigo" aria-describedby="codigoHelp">&nbsp;
              <button type="submit" name="submit" class="btn btn-primary">Consultar</button>
            </form>
     
     
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


	<?php
	
    if (isset($_POST['submit'])) {
        $codigo = mysqli_real_escape_string($link,$_POST['codigo']);
        
        
    $consulta=mysqli_query($link,"SELECT Tb.id, Tb.cedula, Tb.nombre, Tb.apellidop, Tb.apellidom, Tb.entregado, Ta.placa, Tb.codigo, Tb.periodo
		 FROM beneficiarios_programa_3 Tb
		 INNER JOIN activos_beneficiarios_programa_3 Ta ON Tb.id = Ta.id_benef 
		 WHERE Tb.codigo='$codigo' AND Tb.periodo='$year'
		 ORDER BY nombre ASC") or die(mysqli_error($link));
		 
	$preguntar = mysqli_query($link, "select institucion, regional, circuito from beneficiarios_programa_3 where codigo='$codigo' LIMIT 1");   
		$respuesta = mysqli_fetch_array($preguntar);
		$institucion = $respuesta['institucion'];
		$regional = $respuesta['regional'];
		$circuito = $respuesta['circuito'];
?>
<p><?php echo $institucion; ?><br/>
<?php echo $regional; ?><br/>
Circuito <?php echo $circuito; ?></p>
<table class="table table-hover">

	<h2></h2>
	<th>C&eacute;dula</th>
	<th>Nombre</th>
	<th>Primer Apellido</th>
	<th>Segundo Apellido </th>
	<th>Equipo</th>
	<th>Placa</th>
        <tbody>
<?php
	while ($row=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $row['cedula']?></td>
		<td><?php echo $row['nombre']?></td>
		<td><?php echo $row['apellidop']?></td>
		<td><?php echo $row['apellidom']?></td>
		<td><h5><span class="badge bg-secondary"><?php echo $row['entregado']?></span></h5>
		</td>
		<td><?php echo $row['placa']?></td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>
<?php
}   else {
  echo "<div class=\"alert alert-info\" role=\"alert\">
   </div>";
}
?>

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