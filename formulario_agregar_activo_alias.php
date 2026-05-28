<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3);
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>PNTM Principal</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="/css/fondoresponsive.css">
  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <script src="css/jquery-ui.js"></script>
  <link rel="stylesheet" href="css/jquery-ui.css" /> 
  <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
  <script type="text/javascript" src="js/jquery.ddslick.min.js" ></script>  
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

      .ui-autocomplete-row
      {
        padding:8px;
        background-color: #f4f4f4;
        border-bottom:1px solid #ccc;
        font-weight:bold;
      }
      .ui-autocomplete-row:hover
      {
        background-color: #ddd;
      }

	     </style>
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
        <a class="nav-link" href="formulario_agregar_equipo.php"><span class="icon icon-undo2"></span> Regresar</a>
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

<h3>Agregar Modelo de Activo</h3>
<br>
  
<form name="frmactivo" action="guardar_activo_general.php" method="post">
  <div class="form-group">
	<label>Clase General del Activo: </label><select id="myDropdown2" name="myDropdown2">
                          <option value="0">Seleccione:</option>   
    			<?php  

     			 $regres1=mysqli_query($link,"select * from t_activo_general order by clase") or
      			 die(mysqli_error($link));

 
    			while ($regr1=mysqli_fetch_array($regres1))   
    			{
        		?>
   
        		<option value="<?php echo $regr1['id_ag']; ?>" data-imagesrc="img/<?php echo $regr1['imagen']; ?>"
            data-description="Clase general del activo">
        		<?php echo $regr1['clase']; ?>
        		</option>
       
        		<?php
    			}   
    			?>       
</select><br>
 </div>
<div class="form-group">
 <input type="hidden" id="clase" name="clase">
</div> 
  <div class="form-group">
	<label>Marca de frabricante: </label><select id="myDropdown" name="myDropdown">
                          <option value="0">Seleccione:</option>   
    			<?php  

     			 $regres=mysqli_query($link,"select * from t_marca order by marca") or
      			 die(mysqli_error($link));

 
    			while ($regr=mysqli_fetch_array($regres))   
    			{
        		?>
   
        		<option value="<?php echo $regr['id_marca']; ?>" data-imagesrc="ico/<?php echo $regr['logo']; ?>"
            data-description="Marca de un activo">
        		<?php echo $regr['marca']; ?>
        		</option>
       
        		<?php
    			}   
    			?>       
</select><br>
 </div>
<div class="form-group">
 <input type="hidden" id="marca" name="marca">
</div>    

  <div class="form-group">
    <label for="modelo">Modelo del Activo</label>
    <input type="text" class="form-group mx-sm-3 mb-2" id="modelo" name="modelo" aria-describedby="modeloAyuda" onkeypress="return event.charCode != 39">
    <small id="modeloAyuda" class="form-text text-muted">Antes de agregar una clase general de activo, verifica si ya esta en lista.</small>
  </div>
  
  <div class="form-group">
	<label>Color predominante del activo: </label><select id="myDropdown3" name="myDropdown3">
                          <option value="0">Seleccione:</option>   
    			<?php  

     			 $regres2=mysqli_query($link,"select * from t_color order by color") or
      			 die(mysqli_error($link));

 
    			while ($regr2=mysqli_fetch_array($regres2))   
    			{
        		?>
   
        		<option value="<?php echo $regr2['id_color']; ?>" data-imagesrc="ico/<?php echo $regr2['imagen']; ?>"
            data-description="Color general del activo">
        		<?php echo $regr2['color']; ?>
        		</option>
       
        		<?php
    			}   
    			?>       
</select><br>
 </div>

 <div class="form-group">
	<label>Alias: </label><select id="myDropdownAlias" name="myDropdownAlias">
                          <option value="0">Seleccione:</option>   
    			<?php  

     			 $regres2=mysqli_query($link,"select * from t_alias order by alias") or
      			 die(mysqli_error($link));

 
    			while ($regr2=mysqli_fetch_array($regres2))   
    			{
        		?>
   
        		<option value="<?php echo $regr2['alias_id']; ?>" data-imagesrc="img/<?php echo $regr2['alias_imagen']; ?>"
            data-description="Alias del activo">
        		<?php echo $regr2['alias']; ?>
        		</option>
       
        		<?php
    			}   
    			?>       
</select><br>
 </div>

 <div class="form-group">
    <label for="exampleInputEmail1">Número del equipo</label>
    <input type="text" class="form-control" id="numero" name="numero" aria-describedby="numeroAyuda" onkeypress="return event.charCode != 39">
    <small id="numeroAyuda" class="form-text text-muted">Opcional</small>
</div>

<div class="form-group">
 <input type="hidden" id="color" name="color">
</div>
 
<div class="form-group">
 <input type="hidden" id="alias" name="alias">
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
  <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese el software a filtrar" aria-describedby="basic-addon1">
</div>	  
<table class="table table-hover">

	<h2></h2>
	<th>ID</th>
	<th>Clase</th>
	<th>Modelo</th>
	<th>Marca</th>
	<th>Color</th>
	<th>Eliminar</th>
        <tbody class="BusquedaRapida">
	<?php
$consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color
		 FROM t_activo Ta
                 INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag
                 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca
                 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 ORDER BY Tg.clase ASC") or die(mysqli_error($link));


	while ($programas=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $programas['id_activo']?></td>
		<td><?php echo $programas['clase']?></td>
		<td><?php echo $programas['modelo']?></td>
		<td><?php echo $programas['marca']?></td>
		<td><?php echo $programas['color']?></td>
                <td><a class="btn btn-dark" href="eliminar_activo.php?gps=<?php echo $programas['id_activo']?>" role="button"><span class="icon icon-bin"></span></a></td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>

    </div>
    <div class="col-md-6">
     <div class="d-none d-sm-none d-md-block"><img src="img/hardware.png "width="600" height="600"></div>
    </div>
</div>
<script type="text/javascript">
$('#myDropdown').ddslick({
    onSelected: function(data){
       document.getElementById("marca").value = data.selectedData.value; 
    }   
});
</script>
<script type="text/javascript">
$('#myDropdown2').ddslick({
    onSelected: function(data){
       document.getElementById("clase").value = data.selectedData.value; 
    }   
});
</script>
<script type="text/javascript">
$('#myDropdown3').ddslick({
    onSelected: function(data){
       document.getElementById("color").value = data.selectedData.value; 
    }   
});
</script>
<script type="text/javascript">
$('#myDropdownAlias').ddslick({
    onSelected: function(data){
       document.getElementById("alias").value = data.selectedData.value; 
    }   
});
</script>
</body>
</html>
