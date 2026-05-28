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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Agregar Software</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="fondoresponsive.css">
  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <script src="css/jquery-ui.js"></script>
  <link rel="stylesheet" href="css/jquery-ui.css" /> 
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/jquery.ddslick.min.js" ></script>
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
  <img src="img/logomep2020.png" width="45" height="30" alt="" loading="lazy"><a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="inventario_mantenimiento.php"><span class="icon icon-undo2"></span> Mantenimiento</a>
      </li> 
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><span class="icon icon-enter"></span> Cerrar Sesi&oacute;n</a>
      </li>  
    </ul>
  </div>  
</nav>
<br>

<div class="container">
 
	  <h3>Usuario: <?php echo $lognombre ." ". $logcodigo; ?> </h3><br>

<h3>Software Adquirido</h3><a href="ayuda.html#sa">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a><a>&nbsp;&nbsp;</a> <a href="contactenos.php?rep=Error en Software Adquirido">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error ]</a><br><br>
  
<form name="frmarca" action="guardar_subir_software.php" method="post" enctype="multipart/form-data">
  <div class="form-group">
    <label for="exampleInputEmail1">Nombre del software</label>
    <input type="text" class="form-control" id="software" name="software" aria-describedby="marcaAyuda" required maxlength="50" onkeypress="return event.charCode != 39">
    <small id="marcaAyuda" class="form-text text-muted">Antes de agregar un software, verifica si ya esta en lista.</small>
  </div>
  <div class="form-group">
    <label for="imagen">Subir la imagen al servidor</label>
    <input type="file" class="form-control-file" name="imagen" id="imagen" accept="image/png" required>
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
<div class="form-group">
 <input type="hidden" id="marca" name="marca" required>
</div>    
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
	<th>Software</th>
	<th>Fabricante</th>
	<th>Logo</th>
	<th>Editar</th>
	<th>Eliminar</th>
        <tbody class="BusquedaRapida">
	<?php
$consulta=mysqli_query($link,"SELECT Tg.id_sg, Tg.etiqueta, Tm.marca, Tg.imagen
		 FROM t_software_general Tg
                 INNER JOIN t_marca Tm ON Tg.id_marca = Tm.id_marca 
		 ORDER BY Tg.etiqueta ASC") or die(mysqli_error($link));


	while ($programas=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $programas['id_sg']?></td>
		<td><?php echo $programas['etiqueta']?></td>
		<td><?php echo $programas['marca']?></td>
		<td><img src="ico/<?php echo $programas['imagen']?>" width="70" ></td>
		<td><a class="btn btn-dark" href="formulario_editar_software.php?gps=<?php echo $programas['id_sg']?>" role="button"><span class="icon-pencil2"></span></a></td>
                <td><a class="btn btn-dark" href="eliminar_software.php?gps=<?php echo $programas['id_sg']?>" role="button"><span class="icon icon-bin" onclick="return confirm('Estás seguro que deseas eliminar el registro?');"></span></a></td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>


</div>
<script type="text/javascript">
$('#myDropdown').ddslick({
    onSelected: function(data){
       document.getElementById("marca").value = data.selectedData.value; 
    }   
});
</script>
</body>
</html>
