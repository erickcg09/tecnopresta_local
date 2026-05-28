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
$activado = 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Estado de Activos</title>
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
    <script>
      $(document).ready(function () {  
        //Detectar click en el checkbox superior de la lista
        $('#selectall').on('click', function () {
          //verificar el estado de ese checkbox si esta marcado o no
          var checked_status = this.checked;
 
          /*
           * asignarle ese estatus a cada uno de los checkbox
           * que tengan la clase "selectall"
          */
          $(".selectall").each(function () {
            this.checked = checked_status;
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
  <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="inventario_activo.php"> <span class="icon icon-undo2"></span> Inventario</a>
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

<h3>Estado de los Activos</h3><a href="ayuda.html#ea">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a><a>&nbsp;&nbsp;</a> <a href="contactenos.php?rep=Error en Estado de los Activos">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error ]</a>
<br><br>

<div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1">Buscar</span>
  </div>
  <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese el tipo del activo" aria-describedby="basic-addon1">
</div>	  
<form action="actualizar_estado_del_activo.php" method="post" >

<table class="table table-hover">

	<h2></h2>
	<th>Seleccione</th>
	<th>Activo</th>
	<th>Marca</th>
	<th>Modelo</th>
    <th>Color</th>
	<th>Placa</th>
	<th>Serial</th>
	<th colspan="4"><button class="btn btn-dark btn-lg btn-block" type="submit" name="btnActualizar"> <span class="icon icon-floppy-disk"></span> Guardar</button></th>
        <tbody class="BusquedaRapida">
	<?php
$consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
		 FROM t_activo Ta
		 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
		 INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
		 WHERE Tp.codigo = '".$logcodigo."' AND Tp.activo = '".$activado."'
		 ORDER BY Tp.placa ASC") or die(mysqli_error($link));


	while ($activos=mysqli_fetch_array($consulta)) { ?>
	<tr>
        <?php $idestado=$activos['id_estado'];?>
		<td><input type="checkbox" class="selectall" name="idsplacas[]" value="<?php echo $activos['id_placa']?>"/></td>
		<td><?php echo $activos['clase']?></td>
		<td><?php echo $activos['marca']?></td>
		<td><?php echo $activos['modelo']?></td>
		<td><?php echo $activos['color']?></td>
		<td><?php echo $activos['placa']?></td>
		<td><?php echo $activos['serial']?></td>
		<td><input type="radio" name="estado<?php echo $activos['id_placa']; ?>" <?php if($idestado=="1"){?> checked="true" <?php } ?> value="1"> Excelente</td>
		<td><input type="radio" name="estado<?php echo $activos['id_placa']; ?>" <?php if($idestado=="2"){?> checked="true" <?php } ?> value="2"> Bueno</td>
		<td><input type="radio" name="estado<?php echo $activos['id_placa']; ?>" <?php if($idestado=="3"){?> checked="true" <?php } ?> value="3"> Regular</td>
		<td><input type="radio" name="estado<?php echo $activos['id_placa']; ?>" <?php if($idestado=="4"){?> checked="true" <?php } ?> value="4"> Malo</td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>
</form>

</div>
</body>
</html>




