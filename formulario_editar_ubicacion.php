<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4);
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

$id_placa = $_GET['gps']; 

	$query = "SELECT id_regional, regional FROM t_regional ORDER BY id_regional";
	$resultado=$link->query($query);

	$consulta = mysqli_query($link, "select * from t_ubicacion_activo where id_placa='$id_placa'");   
        $result = mysqli_fetch_array($consulta);
	    $id_edificio = $result['id_edificio'];
	    $id_estancia = $result['id_estancia'];
	    $cubiculo = $result['cubiculo'];
	    $id_regional = $result['id_regional'];
		$id_circuito = $result['id_circuito'];

	$cslta = mysqli_query($link, "select edificio from t_edificio where id_edificio='$id_edificio'");   
        $resp = mysqli_fetch_array($cslta);
	$edificio = $resp[edificio];

	$cslta2 = mysqli_query($link, "select estancia from t_estancia where id_estancia='$id_estancia'");   
        $resp2 = mysqli_fetch_array($cslta2);
	$estancia = $resp2['estancia'];

	$cslta3 = mysqli_query($link, "select regional from t_regional where id_regional='$id_regional'");   
        $resp3 = mysqli_fetch_array($cslta3);
	$regional = $resp3['regional'];

	$cslta4 = mysqli_query($link, "select circuito from t_circuito where id_circuito='$id_circuito'");   
        $resp4 = mysqli_fetch_array($cslta4);
	$circuito = $resp4['circuito'];

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
  <script src="css/bootstrap-select.min.js"></script>
  <link rel="stylesheet" href="css/bootstrap-select.min.css">
  <script src="css/defaults-es_ES.min.js"></script>

		<script language="javascript">
			$(document).ready(function(){
				$("#cbx_regional").change(function () {

					$('#cbx_estancia').find('option').remove().end().append('<option value="whatever"></option>').val('whatever');
					$('#cbx_circuito').find('option').remove().end().append('<option value="whatever"></option>').val('whatever');
					$('#cbx_edificio').find('option').remove().end().append('<option value="whatever"></option>').val('whatever');
					
					$("#cbx_regional option:selected").each(function () {
						id_regional = $(this).val();
						$.post("consulta_circuito2.php", { id_regional: id_regional }, function(data){
							$("#cbx_circuito").html(data);
						});            
					});
				})
			});

			$(document).ready(function(){
				$("#cbx_circuito").change(function () {
					$("#cbx_circuito option:selected").each(function () {
						id_circuito = $(this).val();
						$.post("consulta_edificio.php", { id_circuito: id_circuito }, function(data){
							$("#cbx_edificio").html(data);
						});            
					});
				})
			});
			
			$(document).ready(function(){
				$("#cbx_edificio").change(function () {
					$("#cbx_edificio option:selected").each(function () {
						id_edificio = $(this).val();
						$.post("consulta_estancia.php", { id_edificio: id_edificio }, function(data){
							$("#cbx_estancia").html(data);
						});            
					});
				})
			});

		</script>


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
        <a class="nav-link" href="formulario_agregar_ubicacion.php"><span class="icon icon-undo2"></span> Regresar</a>
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

<h3>Editar Ubicaci&oacute;n del Activo</h3>
      <div class="row">

        <div class="col-md-6">

<form action="editar_ubicacion.php" method="post">
  <div class="form-group">
    <input type="hidden" class="form-control" id="id_placa" name="id_placa" value="<?php echo $id_placa;?>">
  </div>
               <div class="form-group">
                    <label for="cbx_regional">DRE</label>
				<select name="cbx_regional" id="cbx_regional" class="selectpicker" data-show-subtext="true" data-live-search="true">
					<option value="<?php echo $id_regional;?>"><?php echo $regional;?></option>
					<?php while($row = $resultado->fetch_assoc()) { ?>
						<option value="<?php echo $row['id_regional']; ?>"><?php echo $row['regional']; ?></option>
					<?php } ?>
				</select>
                </div>

                <div class="form-group">
                    <label for="cbx_circuito">Circuito</label>
                    <select name="cbx_circuito" id="cbx_circuito" class="custom-select">
			<option value="<?php echo $id_circuito;?>"><?php echo $circuito;?></option>
		    </select>
                </div>

                <div class="form-group">
                    <label for="cbx_edificio">Edificio</label>
                    <select name="cbx_edificio" id="cbx_edificio" class="custom-select">
			<option value="<?php echo $id_edificio;?>"><?php echo $edificio;?></option>
		    </select>
                </div>
                <div class="form-group">
                    <label for="cbx_estancia">Estancia</label>
                    <select name="cbx_estancia" id="cbx_estancia" class="custom-select">
			<option value="<?php echo $id_estancia;?>"><?php echo $estancia;?></option>
                    </select>
                </div>
	        <div class="form-group">
	          <label for="cubiculo">Cub&iacute;culo /  Otro</label>
	          <input type="cubiculo" class="form-control" id="cubiculo" name="cubiculo" value="<?php echo $cubiculo;?>" aria-describedby="cubiculoHelp">
	          <small id="cubiculoHelp" class="form-text text-muted">Los pisos de oficinas, laboratorios, bibliotecas puede que se dividan en segmentos y divisiones.</small>
	        </div>
		<div>
        	  <button type="submit" class="btn btn-dark"><span class="icon icon-floppy-disk"> Guardar</span></button>
		</div>
</form>
</div>
<div class="col-md-6">
<div class="d-none d-sm-none d-md-block"><img src="img/edicion-09.png "width="600" height="600"></div>
        </div>

      </div>

</div>


</body>
</html>

