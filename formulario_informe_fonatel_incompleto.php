<?php  
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2);
if ($tienellave == false){
    echo '<script language = javascript>
                alert("No tienes permisos")
                self.location = "inventario_reporte.php"
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
}
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
$logdependencia = $_SESSION['dependencia'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>Entrega FONATEL</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="js/jquery.min.js"></script>
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" href="css/loader.css">
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
        <a class="nav-link" href="inventario_reporte.php">Reportes</a>
      </li>   
    </ul>
  </div>  
</nav>
<br>

<div class="contenedor_loader">
  <div class="loader"></div>
</div>
 
<div class="container">  
  <h3>Instituciones Entrega Incompleta FONATEL</h3><a href="ayuda.html#rla">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a><a>&nbsp;&nbsp;</a> <a href="contactenos.php?rep=Error en Listado de Activos">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error ]</a><br><br>
  <form action="" method="POST" enctype="multipart/form-data">  
	<table class="table table-hover">	    
	<th>Regional</th>
    <th>Circuito</th>    
    <th>Código</th>
    <th>Institución</th>
    <th>Cédula</th>
    <th>Responsable</th>
    <th>Comentario</th>
	<th><a class="btn btn-dark" href="exportar_excel_fonatel_incompleto.php" role="button"><span class="icon icon-file-excel"></span> EXCEL</a></th>
    <tbody>  
		<?php			 	
   			$consulta=mysqli_query($link,"SELECT * FROM t_confirmacion_entrega_fonatel where completo = 0") 
											or die(mysqli_error($link));		 
		while ($activos=mysqli_fetch_array($consulta)) { ?>
		<tr>
			<td><?php echo $activos['direccion_r']?></td>
			<td><?php echo $activos['circuito']?></td>
            <td><?php echo $activos['codigo_i']?></td>
            <td><?php echo $activos['institucion']?></td>
            <td><?php echo $activos['cedula_f']?></td>
            <td><?php echo $activos['funcionario']?></td>
            <td><?php echo $activos['comentario']?></td>
		</tr>	
		<?php }
		mysqli_close($link);	
		?>
	  </tbody>
	</table>
	</form>
</div>
<script src="js/loader.js"></script>
</body>
</html>


