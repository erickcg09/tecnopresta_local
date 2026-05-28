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
$logcodigo = $_SESSION['codigo'];


	$query = "SELECT id_regional, regional FROM t_regional ORDER BY id_regional";
	$resultado=$link->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Campus</title>
  <link rel="shortcut icon" href="ico/favicon.png">
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
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">
            <style>
		.button {
		  background-color: #0080FF;
		  border: none;
		  color: white;
		  padding: 15px 32px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 18px;
		  margin: 4px 2px;
		  cursor: pointer;
		  width: 70px;
                  text-transform: uppercase;
                  letter-spacing: 2px;
                  border-radius: 10px;
                  transition: all 300ms;
		}
	     </style>
	     
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
	     
<script type="text/javascript">
$(document).ready(function () {
   (function($) {
       $('#FiltrarContenido').keyup(function () {
            var ValorBusqueda = new RegExp($(this).val(), 'i');
            $('.BusquedaRapida tr').hide();
             $('.BusquedaRapida tr').filter(function () {
                return ValorBusqueda.test($(this).text());
              }).show();
                })
      }(jQuery));
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
        <a class="nav-link" href="inventario_activo.php"><span class="icon icon-undo2"></span> Inventario</a>
      </li> 
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><span class="icon icon-enter"></span> Cerrar Sesi&oacute;n</a>
      </li>  
    </ul>
  </div>  
</nav>
<br>

<div class="container">
  <div class="row">

    <div class="col-md-6">
	  <h3>Usuario: <?php echo $lognombre ." ". $logcodigo; ?> </h3><br>

<h3>Campus / Edificio</h3><a href="ayuda.html#ce">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a><a>&nbsp;&nbsp;</a><a href="contactenos.php?rep=Error en Campus / Edificio">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error ]</a><br><br>
  
<form name="frm" action="guardar_edificio.php" method="post">
              <div class="form-group">
                    <label for="cbx_regional">DRE: </label>
				<select name="cbx_regional" id="cbx_regional" class="selectpicker" data-show-subtext="true" data-live-search="true" required>
					<option value="0">Seleccionar DRE</option>
					<?php while($row = $resultado->fetch_assoc()) { ?>
						<option value="<?php echo $row['id_regional']; ?>"><?php echo $row['regional']; ?></option>
					<?php } ?>
				</select>
                </div>
                <div class="form-group">
                    <label for="cbx_circuito">Circuito: </label>
                    <select name="cbx_circuito" id="cbx_circuito" class="custom-select" required></select>
                </div>
  <div class="form-group">
    <label for="edificio">Campus / Edificio</label>
    <input type="text" class="form-control" id="edificio" name="edificio" aria-describedby="edificioAyuda" onkeypress="return event.charCode != 39" required>
    <small id="edificioAyuda" class="form-text text-muted">Antes de agregar un Campus o Edificio, verifica si ya esta en lista.</small>
  </div>
  <div>
         <button type="submit" class="btn btn-dark"><span class="icon icon-floppy-disk"> Guardar</span></button><br>
  </div>
</form>
<br>
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1">Buscar</span>
  </div>
  <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese el tipo a filtrar" aria-describedby="basic-addon1">
</div>	 
<table class="table table-hover">

	<h2></h2>
	<th>ID</th>
	<th>DRE</th>
	<th>Circuito</th>
	<th>Campus / Edificio</th>
	<th>Editar</th>
	<th>Eliminar</th>
        <tbody class="BusquedaRapida">
	<?php
$consulta=mysqli_query($link,"SELECT Te.id_edificio, Te.edificio, Te.codigo, Tc.circuito, Tr.regional
		 FROM t_edificio Te
		 INNER JOIN t_circuito Tc ON Te.id_circuito = Tc.id_circuito
		 INNER JOIN t_regional Tr ON Tc.id_regional = Tr.id_regional
		 WHERE Te.codigo = '".$logcodigo."' ORDER BY Te.edificio ASC") or die(mysqli_error($link));


	while ($edificios=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $edificios['id_edificio']?></td>
		<td><?php echo $edificios['regional']?></td>
		<td><?php echo $edificios['circuito']?></td>
		<td><?php echo $edificios['edificio']?></td>
		<td><a class="btn btn-dark" href="formulario_editar_edificio.php?gps=<?php echo $edificios['id_edificio']?>" role="button"><span class="icon-pencil2"></span></a></td>
            
                <td><a class="btn btn-dark" href="eliminar_edificio.php?gps=<?php echo $edificios['id_edificio']?>" role="button"><span class="icon icon-bin" onclick="return confirm('Estás seguro que deseas eliminar el registro?');"></span></a></td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>
    </div>
        <div class="col-md-6">
<div class="d-none d-sm-none d-md-block"><img src="img/campus1.png "width="600" height="600"></div>
        </div>
  </div>
</div>
</body>
</html>
