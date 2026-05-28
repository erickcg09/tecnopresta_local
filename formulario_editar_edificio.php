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

$id_edificio = $_GET['gps'];


		$preguntar = mysqli_query($link, "select * from t_edificio where id_edificio='$id_edificio'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$edificio = $respuesta['edificio'];
		$id_circuito = $respuesta['id_circuito'];

		$preguntar2 = mysqli_query($link, "select circuito, id_regional from t_circuito where id_circuito='$id_circuito'");   
		$respuesta2 = mysqli_fetch_array($preguntar2);
		$circuito = $respuesta2['circuito'];
		$id_regional = $respuesta2['id_regional'];
		
		$preguntar3 = mysqli_query($link, "select regional from t_regional where id_regional='$id_regional'");   
		$respuesta3 = mysqli_fetch_array($preguntar3);
		$regional = $respuesta3['regional'];

	$query = "SELECT id_regional, regional FROM t_regional ORDER BY id_regional";
	$resultado=$link->query($query);
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
    $("#cbx_regional").on('change', function () {
        $("#cbx_regional option:selected").each(function () {
            var id_regional = $(this).val();
            $.post("dependiente_circuito.php", { id_regional: id_regional }, function(data) {
                $("#cbx_circuito").html(data);
            });			
        });
   });
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
        <a class="nav-link" href="formulario_crear_edificio.php"><span class="icon icon-undo2"></span> Campus / Edificios</a>
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

<h3>Editar Campus / Edificio</h3><br>

      <div class="row">

        <div class="col-md-6">
		<form action="editar_edificio.php" method="post">
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
                    <label for="cbx_circuito">Circuito: </label>
                    <select name="cbx_circuito" id="cbx_circuito" class="custom-select" required>
                        <option value="<?php echo $id_circuito;?>"><?php echo $circuito;?></option>
                    </select>
                </div>

		  <div class="form-group">
		    <label for="edificio">Campus / Edificio</label>
		    <input type="text" class="form-control" name="edificio" id="edificio" aria-describedby="edificioHelp" value="<?php echo $edificio;?>">
		    <small id="edificioHelp" class="form-text text-muted">Recuerde que este apartado es únicamente para corregir errores de escritura.</small>
		  </div>
  		    <input type="hidden" id="idedificio" name="idedificio" value="<?php echo $id_edificio;?>">
		  <button type="submit" class="btn btn-dark"><span class="icon icon-floppy-disk"> Guardar</span></button>
		</form>
        </div>
        <div class="col-md-6">
<div class="d-none d-sm-none d-md-block"><img src="img/edicion-campus.png "width="600" height="600"></div>
        </div>

      </div>

</div>
</body>
</html>
