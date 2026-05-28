<?php
session_start();
$tienellave = ($_SESSION['tipo']==1);
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
  <link rel="stylesheet" href="fondoresponsive.css">
  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
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
  <div class="row">

    <div class="col-md-6">
	  <h3>Usuario: <?php echo $lognombre ." ". $logcodigo; ?> </h3><br>

<h3>Alias de un Activo o Grupos de Activos</h3><br>
  
<form name="fralias" action="guardar_subir_alias.php" method="post" enctype="multipart/form-data">
  <div class="form-group">
    <label for="exampleInputAlias">Alias</label>
    <input type="text" class="form-control" id="alias" name="alias" aria-describedby="aliasAyuda">
    <small id="aliasAyuda" class="form-text text-muted">Antes de agregar un alias, verifica si ya existe en lista.</small>
  </div>
  <div class="form-group row">
    <!-- <label for="imagen">Subir la imagen al servidor</label> -->
    <!-- <input type="file" class="form-control-file" name="imagen" id="imagen" accept="image/png" required> -->
    <div class="col-md-4">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#imagenModal">
            Seleccionar imagen
          </button> 
    </div> 
    <div class="col-md-4">     
      <input type="text" class="form-control" name="imagen" id="imagen" readonly required>
    </div>
  </div>
  <div class="d-flex justify-content-center" style="padding: 1em;">
        <div class="row">
          <div class="col" id="colCards">
            <div class="card"></div>
          </div>
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
  <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese el alias a filtrar" aria-describedby="basic-addon1">
</div>	 
<table class="table table-hover">

	<h2></h2>
	<th>ID</th>
	<th>Alias</th>
	<th>Avatar</th>
	<th>Editar</th>
	<th>Eliminar</th>
        <tbody class="BusquedaRapida">
	<?php
$consulta=mysqli_query($link,"SELECT alias_id, alias, alias_imagen
		 FROM t_alias
		 ORDER BY alias ASC") or die(mysqli_error($link));


	while ($alias=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $alias['alias_id']?></td>
		<td><?php echo $alias['alias']?></td>
		<td><img src="img/alias/<?php echo $alias['alias_imagen']?>" width="70" ></td>
		<td><a class="btn btn-dark" href="formulario_editar_alias.php?gps=<?php echo $alias['alias_id']?>" role="button"><span class="icon-pencil2"></span></a></td>
                <td><a class="btn btn-dark" href="eliminar_alias.php?gps=<?php echo $alias['alias_id']?>" role="button"><span class="icon icon-bin"></span></a></td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>
    </div>
    <div class="col-md-6">

<div class="d-none d-sm-none d-md-block"><img src="img/Alias_Mesa de trabajo 1.png "width="550" height="550"></div>
          

    </div>
  </div>
</div>

<div class="modal fade" id="imagenModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelTipo" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-lg modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabelTipo">Seleccione la imagen...</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>  
          </div>
          <div class="modal-body">
            
            <!-- Plantilla que se carga en formulario_activo_nuevo.js -->
              
                <div class="container-fluid">
                  
                    <ul class="list-unstyled" id="listTipo">                     
                      
                      <li class="row" id="fila">

                         <div class="col-md-4" id="columna">
                         <!--  <a href="javascript:void(0)">                     
                            <img class="card-img-top" src="img/impresora.png">
                            <p class="text-center">
                              <small class="text-primary">impresora.png</small>
                            </p>  
                          </a>             -->                  
                        </div>

                      </li>  

                    </ul>

                </div>                 
                            
                </div>

          <div class="modal-footer">            
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>              
          </div>

        </div>
      </div>
  </div>         
  <script src="js/formulario_crear_alias.js"></script>
</body>
</html>
