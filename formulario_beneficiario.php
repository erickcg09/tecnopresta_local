<?php  
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
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

	$query = "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tg.imagen
         FROM t_activo Ta
         INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
         INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
         INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
         WHERE id_activo IN (405,856)
         ORDER BY Tg.clase ASC";
	$resultado=$link->query($query);

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Editor de Cuestionario para Revisi&oacute;n</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">

  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">



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
        <a class="nav-link" href="guardar_compromiso_firmado.php"><span class="icon icon-undo2"></span> Regresar a Almacén de Contratos</a>
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

    
	  <h3> <b> USUARIO: <?php echo $lognombre; ?> </b> </h3><br>

<h3> <b> Equipamiento Programa  N°3 Fonatel </b> </h3><a href="ayuda.html#au">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a><a>&nbsp;&nbsp;</a> <a href="contactenos.php?rep=Error en Revisi&oacute;n Detallada">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error ]</a>
<br><br>

<form action="guardar_bene.php" method="post" >
    
    <h3> <b> Plantilla de Contrato para Beneficiarios(as) de equipo tecnológicos pr&eacute;stados al hogar </b> </h3><br>
    <h6 <b> <strong>Nota: El equipo se entrega en forma temporal, la administración se reserva el derecho de retirarlo si el estudiante pierde la condición de elegible.</strong>  </b> </h6><br>
    <br>

                    <div>
                        <label for="activo">Activo a pr&eacute;stamo</label>
    				<select id="activo" name="activo" class="form-select form-select-lg mb-3">
    					<option value="0">Seleccionar Activo</option>
    					<?php while($row = $resultado->fetch_assoc()) { 
    					$mostrar = $row['clase']." ".$row['marca']." ".$row['modelo']." ".$row['color'];
    					?>
    						<option value="<?php echo $mostrar; ?>"><?php echo $mostrar; ?></option>
    					<?php } ?>
    				</select>
                    </div>
            
                    <div>
                        <div>
                            <label for="placa" class="form-label">Placa</label>
                            <input type="text" class="form-control" id="placa" name="placa" required onkeypress="return event.charCode != 39">
                        </div>
                        <div>
                            <label for="serie" class="form-label">Serie</label>
                            <input type="text" class="form-control" id="serie" name="serie" required onkeypress="return event.charCode != 39">
                        </div>
                    </div>

                    <div>
                        <div>
                            <label for="encargado" class="form-label">Nombre del Padre o Madre de Familia o Encargado legal</label>
                            <input type="text" class="form-control" id="encargado" name="encargado" required onkeypress="return event.charCode != 39">
                        </div>
                        <div>
                            <label for="cedula" class="form-label">C&eacute;dula del Padre o Madre o Encargado</label>
                            <input type="text" class="form-control" id="cedula" name="cedula" required onkeypress="return event.charCode != 39">
                        </div>
                    </div>
                    <div>
                        <div>
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required onkeypress="return event.charCode != 39">
                        </div>
                        <div>
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required onkeypress="return event.charCode != 39">
                        </div>
                    </div>

                    <div>
                        <div>
                            <label for="estudiante" class="form-label">Nombre del Estudiante</label>
                            <input type="text" class="form-control" id="estudiante" name="estudiante" required onkeypress="return event.charCode != 39">
                        </div>
                        <div>
                            <label for="idest" class="form-label">C&eacute;dula del Estudiante</label>
                            <input type="text" class="form-control" id="idest" name="idest" required onkeypress="return event.charCode != 39">
                        </div>
                    </div>
                    <div>
                        <label for="direccion" class="form-label">Direcci&oacute;n o Lugar de Domicilio</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" required onkeypress="return event.charCode != 39">
                    </div>
                    <div>
                        <div>
                            <label for="insti" class="form-label">Instituci&oacute;n</label>
                            <input type="text" class="form-control" id="insti" name="insti" required onkeypress="return event.charCode != 39">
                        </div>
                        <div>
                            <label for="codigo" class="form-label">C&oacute;digo Presupuestario del Centro Educativo</label>
                            <input type="text" class="form-control" id="codigo" name="codigo" required onkeypress="return event.charCode != 39">
                        </div>
                        <div>
                            <label for="fecha_i" class="form-label">Fecha de inicio del Préstamo</label>
                            <input type="date" class="form-control" id="fecha_i" name="fecha_i" required>
                        </div>
                        <div>
                            <label for="fecha_f" class="form-label">Fecha de devoluci&oacute;n del Activo</label>
                            <input type="date" class="form-control" id="fecha_f" name="fecha_f" required>
                        </div>

<br>
<table>
    <tr>
  <td><div>
         <button type="submit" class="btn btn-dark"><span class="icon icon-floppy-disk"> Guardar y pasar a Almacenar Contrato</span></button><br>
  </div></td>
  </tr>
</table>
<br>
</form>

</div>
<div class="col-md-6">
    
</div>
  </div>
</div>

</body>
</html>

