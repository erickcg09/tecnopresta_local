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
  <title>TecnoPresta Crear Estancia</title>
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
				$("#cbx_regional").change(function () {

					$('#cbx_estancia').find('option').remove().end().append('<option value="whatever"></option>').val('whatever');
					$('#cbx_circuito').find('option').remove().end().append('<option value="whatever"></option>').val('whatever');
					
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
				$("#cbx_circuito").change(function () {
					$("#cbx_circuito option:selected").each(function () {
						id_circuito = $(this).val();
						$.post("consulta_edificio.php", { id_circuito: id_circuito }, function(data){
							$("#cbx_edificio").html(data);
						});            
					});
				})
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
  <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
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

<h3>Estancia / Lugar</h3><a href="ayuda.html#el">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a><a>&nbsp;&nbsp;</a> <a href="contactenos.php?rep=Error en Estancia / Lugar">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error ]</a><br><br>
  
<form name="frm" action="guardar_estancia.php" method="post">
    
                <div class="form-group">
                    <label for="cbx_regional">DRE: </label>
				<select name="cbx_regional" id="cbx_regional" class="selectpicker" data-show-subtext="true" data-live-search="true">
					<option value="0">Seleccionar DRE</option>
					<?php while($row = $resultado->fetch_assoc()) { ?>
						<option value="<?php echo $row['id_regional']; ?>"><?php echo $row['regional']; ?></option>
					<?php } ?>
				</select>
                </div>
                <div class="form-group">
                    <label for="cbx_circuito">Circuito: </label>
                    <select name="cbx_circuito" id="cbx_circuito" class="custom-select"></select>
                </div>
                <div class="form-group">
                    <label for="cbx_edificio">Campus / Edificio</label>
                    <select name="cbx_edificio" id="cbx_edificio" class="custom-select" required></select>
                </div>
  <div class="form-group">
    <label for="exampleInputEmail1">Estancia / Lugar</label>
    <input type="text" class="form-control" id="estancia" name="estancia" aria-describedby="estanciaHelp" required onkeypress="return event.charCode != 39">
    <small id="estanciaHelp" class="form-text text-muted">Recuerde que este apartado es para corregir errores de escritura.</small>
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
	<th>Regional</th>
	<th>Circuito</th>
	<th>Campus / Edificio</th>
	<th>Estancia / Lugar</th>
	<th>Editar</th>
	<th>Eliminar</th>
        <tbody class="BusquedaRapida">
	<?php
$consulta=mysqli_query($link,"SELECT Te.id_estancia, Te.estancia, Te.codigo, Tc.edificio, Ts.circuito, Tr.regional
		 FROM t_estancia Te
		 INNER JOIN t_edificio Tc ON Te.id_edificio = Tc.id_edificio
		 INNER JOIN t_circuito Ts ON Tc.id_circuito = Ts.id_circuito
		 INNER JOIN t_regional Tr ON Ts.id_regional = Tr.id_regional
		 WHERE Te.codigo = '".$logcodigo."' ORDER BY estancia ASC") or die(mysqli_error($link));


	while ($estancias=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $estancias['id_estancia']?></td>
		<td><?php echo $estancias['regional']?></td>
		<td><?php echo $estancias['circuito']?></td>
		<td><?php echo $estancias['edificio']?></td>
		<td><?php echo $estancias['estancia']?></td>
		<td><a class="btn btn-dark" href="formulario_editar_estancia.php?gps=<?php echo $estancias['id_estancia']?>" role="button"><span class="icon-pencil2"></span></a></td>
            
                <td><a class="btn btn-dark" href="eliminar_estancia.php?gps=<?php echo $estancias['id_estancia']?>" role="button"><span class="icon icon-bin" onclick="return confirm('Estás seguro que deseas eliminar el registro?');"></span></a></td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>
    </div>
    <div class="col-md-6">
<div class="d-none d-sm-none d-md-block"><img src="img/cubiculo.png "width="600" height="600"></div>
    </div>
  </div>
</div>
</body>
</html>
