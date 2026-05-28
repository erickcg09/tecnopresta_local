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
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];

	$query = "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tg.imagen
         FROM t_activo Ta
         INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
         INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
         INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
         ORDER BY Tg.clase ASC";
	$resultado=$link->query($query);


?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Revisi&oacute;n</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="fondoresponsive.css">
  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <script src="css/bootstrap-select.min.js"></script>
  <link rel="stylesheet" href="css/bootstrap-select.min.css">
  <script src="css/defaults-es_ES.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">

            <style>
		.button {
		  background-color: #58ACFA;
		  border: none;
		  color: white;
		  padding: 15px 32px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 18px;
		  margin: 4px 2px;
		  cursor: pointer;
		  width: 200px;
                 text-transform: uppercase;
                 letter-spacing: 2px;
                 border-radius: 10px;
                 transition: all 300ms;
		}
		.button:hover{
		   text-decoration: none;
                }
	     </style>
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
    
	  <h3>Usuario: <?php echo $lognombre; ?> </h3>
      <div class="row">

        <div class="col-md-6">

		<h3>Seleccionar Equipo para Revisi&oacute;n </h3><a href="ayuda.html#pa">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a> <a>&nbsp;&nbsp;</a><a href="contactenos.php?rep=Error en Pasar Activos entre Instancias">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error]</a><br><br>
		<form name="formulario" method="get" action="formulario_revision_activos.php">
		
		  <div class="form-group">
		    <label for="codigo">C&oacute;digo presupuestario que resguarda</label>
		    <input type="text" class="form-control" id="codigo" name="codigo" aria-describedby="codHelp" required>
		    <small id="codHelp" class="form-text text-muted">Por favor ingresar el c&oacute;digo presupuestario donde est&aacute; inscrito el activo.</small>
		  </div>


               <div class="form-group">
                    <label for="cbx_activo">Activo a revisar</label>
				<select name="cbx_activo" id="cbx_activo" class="selectpicker" data-show-subtext="true" data-live-search="true">
					<option value="0">Seleccionar Activo</option>
					<?php while($row = $resultado->fetch_assoc()) { 
					$mostrar = $row['clase']." ".$row['marca']." ".$row['modelo']." ".$row['color'];
					?>
						<option value="<?php echo $row['id_activo']; ?>"><?php echo $mostrar; ?></option>
					<?php } ?>
				</select>
                </div>



	<div class="input-group mb-3">
		  <div class="input-group-prepend">
		    <button class="btn btn-outline-secondary" type="button">Origen presupuestario con el cual se adquiere el activo</button>
		  </div>
      <select class="custom-select" id="fondos" name="fondos" aria-label="Example select with button addon" required>
        <option value="0">Seleccione..</option>
        <?php 
          $querz = $link -> query ("SELECT * FROM t_fondos");
          while ($valorez = mysqli_fetch_array($querz)) {
            echo '<option value="'.$valorez['id_fondos'].'">'.$valorez['fondos'].'</option>';
          }
        ?>
      </select>
		</div>

	<div class="input-group mb-3">
		  <div class="input-group-prepend">
		    <button class="btn btn-outline-secondary" type="button">Cuestionario</button>
		  </div>
      <select class="custom-select" id="quest" name="quest" aria-label="Example select with button addon" required>
        <option value="0">Seleccione..</option>
        <?php 
          $quers = $link -> query ("SELECT id_puntos, etiqueta FROM t_puntos_a_revisar");
          while ($valores = mysqli_fetch_array($quers)) {
            echo '<option value="'.$valores['id_puntos'].'">'.$valores['etiqueta'].'</option>';
          }
        ?>
      </select>
		</div>

		  <button type="submit" class="btn btn-dark"><span class="icon icon-loop2"> Visualizar</span></button>
		</form>

        </div>
        <div class="col-md-6">
<div class="d-none d-sm-none d-md-block"><img src="img/chequear.png "width="500" height="450"></div>
        </div>
      </div>

</div>
</body>
</html>