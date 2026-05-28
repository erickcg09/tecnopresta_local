<?php  
session_start();
$tienellave = ($_SESSION['tipo']==1);
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
    
	  <h3>Usuario: <?php echo $lognombre; ?> </h3><br>

<h3>Revisi&oacute;n Detallada</h3><a href="ayuda.html#au">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a><a>&nbsp;&nbsp;</a> <a href="contactenos.php?rep=Error en Revisi&oacute;n Detallada">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error ]</a>
<br><br>

<form action="guardar_editor.php" method="post" >
    
    <h3>Editor de Cuestionario para Revisi&oacute;n</h3><br>

<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon1">Referecia de la Revisi&oacute;n</span>
  <input type="text" class="form-control" name="etiqueta" placeholder="Escriba aqu&iacute; una etiqueta de referencia" aria-label="Escriba aqu&iacute; una etiqueta de referencia" aria-describedby="basic-addon1">
</div>

<label for="basic-url" class="form-label">1. Funcionamiento energ&eacute;tico.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_energia" id="basic-url" aria-describedby="basic-addon3">
</div>

<label for="basic-url" class="form-label">2. Genera o reproduce ruidos extra&ntilde;os.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_ruidos" id="basic-url" aria-describedby="basic-addon3">
</div>    

<label for="basic-url" class="form-label">3. Estado de la carcasa.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_carcasa" id="basic-url" aria-describedby="basic-addon3">
</div>  

<label for="basic-url" class="form-label">4. Memoria interna temporal y permanente.</label>
<div class="input-group mb-3">
  <span class="input-group-text"  id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_memoria" id="basic-url" aria-describedby="basic-addon3">
</div> 

<label for="basic-url" class="form-label">5. Procesamiento.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_cpu" id="basic-url" aria-describedby="basic-addon3">
</div> 

<label for="basic-url" class="form-label">6. Medios de comunicaci&oacute;n de datos.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_comunicacion" id="basic-url" aria-describedby="basic-addon3">
</div> 

<label for="basic-url" class="form-label">7. Medios de entrada.</label>
<div class="input-group mb-3">
  <span class="input-group-text"  id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_entrada" id="basic-url" aria-describedby="basic-addon3">
</div> 

<label for="basic-url" class="form-label">8. Medios de salida.</label>
<div class="input-group mb-3">
  <span class="input-group-text"  id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_salida" id="basic-url" aria-describedby="basic-addon3">
</div> 
    
<label for="basic-url" class="form-label">9. Puertos conectores.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_puertos" id="basic-url" aria-describedby="basic-addon3">
</div> 

<label for="basic-url" class="form-label">10. Botones y teclas.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_botones" id="basic-url" aria-describedby="basic-addon3">
</div> 

<label for="basic-url" class="form-label">11. Bisagras.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_bisagras" id="basic-url" aria-describedby="basic-addon3">
</div> 

<label for="basic-url" class="form-label">12. Sensores.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_sensores" id="basic-url" aria-describedby="basic-addon3">
</div> 

<label for="basic-url" class="form-label">13. Accesorios externos.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_accesorios" id="basic-url" aria-describedby="basic-addon3">
</div> 

<label for="basic-url" class="form-label">14. Controladores de hardware.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_controladores" id="basic-url" aria-describedby="basic-addon3">
</div> 

<label for="basic-url" class="form-label">15. Software.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_software" id="basic-url" aria-describedby="basic-addon3">
</div> 
    
<label for="basic-url" class="form-label">16. Dimensi&oacute;n, peso, colores y texturas.</label>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon3">Detalle:</span>
  <input type="text" class="form-control" name="r_dimension" id="basic-url" aria-describedby="basic-addon3">
</div> 

  <div>
         <button type="submit" class="btn btn-dark"><span class="icon icon-floppy-disk"> Guardar</span></button><br>
  </div>
<br>
</form>

    </div>
    <div class="col-md-6">

<div class="d-none d-sm-none d-md-block"><img src="img/editor.png "width="600" height="600"></div>
          

    </div>
  </div>
</div>

</body>
</html>