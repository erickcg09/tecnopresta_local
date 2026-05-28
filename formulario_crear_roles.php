<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2);
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
  <title>TecnoPresta Roles</title>
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
  <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="inventario_mantenimiento.php"><span class="icon icon-undo2"></span> Regresar</a>
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

        <h3>Agregar Usuarios con Permisos</h3><a href="ayuda.html#mg">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a> <a>&nbsp;&nbsp;</a> <a href="contactenos.php?rep=Error en Agregar Modelo de Activo">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error]</a>
        <br><br>
          
        <form name="frmrol" action="guardar_rol_del_usuario.php" method="post">
          <div class="form-group">
        	<label>Nivel de Usuario: </label><select id="myDropdown2" name="myDropdown2">
                                  <option value="0">Seleccione:</option>   
            			<?php  
        
             			 $regres1=mysqli_query($link,"select * from t_roles order by id_rol") or
              			 die(mysqli_error($link));
        
         
            			while ($regr1=mysqli_fetch_array($regres1))   
            			{
                		?>
           
                		<option value="<?php echo $regr1['id_rol']; ?>" data-imagesrc="img/<?php echo $regr1['imagen']; ?>"
                    data-description="<?php echo $regr1['descripcion']; ?>">
                		<?php echo $regr1['rol']; ?>
                		</option>
               
                		<?php
            			}   
            			?>       
        </select><br>
         </div>
        <div class="form-group">
         <input type="hidden" id="rol" name="rol" required>
        </div> 
        
        <div class="input-group mb-3">
          <span class="input-group-text" id="basic-addon1">C&eacute;dula</span>
          <input type="text" name="cedula" class="form-control" placeholder="C&eacute;dula o Similar" aria-label="Username" aria-describedby="basic-addon1" required maxlength="9" onkeypress="return event.charCode != 39">
        </div>
        
        <div class="input-group mb-3">
          <span class="input-group-text" id="basic-addon1">Correo MEP</span>
          <input type="text" name="nombre" class="form-control" placeholder="nombre.apellido.apellido@mep.go.cr" aria-label="Username" aria-describedby="basic-addon1" required onkeypress="return event.charCode != 39">
        </div>
        
        <div class="input-group mb-3">
          <span class="input-group-text" id="basic-addon1">C&oacute;digo</span>
          <input type="text" name="codigop" class="form-control" placeholder="C&oacute;digo presupuestario donde se utilizar&aacute;" aria-label="Username" aria-describedby="basic-addon1" value="<?php echo $logcodigo;?>" required onkeypress="return event.charCode != 39">
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
          <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese el usuario a filtrar" aria-describedby="basic-addon1">
        </div>	  
        <table class="table table-hover">
        
        	<h2></h2>
        	<th>ID</th>
        	<th>C&eacute;dula o Similar</th>
        	<th>Correo MEP</th>
        	<th>Rol</th>
        	<th>Eliminar</th>
                <tbody class="BusquedaRapida">
        	<?php
        $consulta=mysqli_query($link,"SELECT Ta.id_lista_blanca, Ta.cedula, Ta.nombre, Ta.codigo, Tb.rol
        		 FROM t_lista_blanca Ta
                 INNER JOIN t_roles Tb ON Ta.id_rol = Tb.id_rol
        		 WHERE Ta.codigo = $logcodigo
        		 ORDER BY Ta.cedula ASC") or die(mysqli_error($link));
        
        
        	while ($programas=mysqli_fetch_array($consulta)) { ?>
        	<tr>
        		<td><?php echo $programas['id_lista_blanca']?></td>
        		<td><?php echo $programas['cedula']?></td>
        		<td><?php echo $programas['nombre']?></td>
        		<td><?php echo $programas['rol']?></td>
                <td><a class="btn btn-dark" href="eliminar_rol.php?gps=<?php echo $programas['id_lista_blanca']?>" role="button"><span class="icon icon-bin" onclick="return confirm('Estás seguro que deseas eliminar el registro?');"></span></a></td>
        	</tr>
        	<?php }
        	mysqli_close($link);	
        	?>
        </tbody>
        </table>

    </div>

    
 </div>
</div>
<script type="text/javascript">
$('#myDropdown2').ddslick({
    onSelected: function(data){
       document.getElementById("rol").value = data.selectedData.value; 
    }   
});
</script>

</body>
</html>