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

$id_placa = $_GET['gps'];

$codigo = $_GET['varcodigo'];
$activo = $_GET['varactivo'];
$fondos = $_GET['varfondos'];
$quest = $_GET['varquest'];

$preguntar = mysqli_query($link,"SELECT Ta.id_activo, Tag.clase, Ta.modelo, Tm.marca, Tc.color
		 FROM t_activo Ta
		 INNER JOIN t_activo_general Tag ON Ta.id_ag = Tag.id_ag
		 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color 
		 WHERE Ta.id_activo = '".$activo."'
		 ORDER BY Tag.clase ASC") or die(mysqli_error($link));  
        $respuesta = mysqli_fetch_array($preguntar);
        $clase = $respuesta['clase'];
        $marca = $respuesta['marca'];
	    $modelo = $respuesta['modelo'];
	    $color = $respuesta['color'];

$preguntar2 = mysqli_query($link,"SELECT serial
		 FROM t_placa
		 WHERE id_placa = '".$id_placa."'
		 ORDER BY serial ASC") or die(mysqli_error($link));  
        $respuesta2 = mysqli_fetch_array($preguntar2);
        $serial = $respuesta2['serial'];


$preguntar3 = mysqli_query($link,"SELECT *
		 FROM t_puntos_a_revisar
		 WHERE id_puntos = '".$quest."'
		 ORDER BY etiqueta ASC") or die(mysqli_error($link));  
        $respuesta3 = mysqli_fetch_array($preguntar3);
        $energia = $respuesta3['r_energia]';
        $ruidos = $respuesta3['r_ruidos'];
        $carcasa = $respuesta3['r_carcasa'];
        $memoria = $respuesta3'[r_memoria'];
        $cpu = $respuesta3['r_cpu'];
        $comunicacion = $respuesta3['r_comunicacion'];
        $entrada = $respuesta3['r_entrada'];
        $salida = $respuesta3['r_salida'];
        $puertos = $respuesta3['r_puertos'];
        $botones = $respuesta3['r_botones'];
        $bisagras = $respuesta3['r_bisagras'];
        $sensores = $respuesta3['r_sensores'];
        $accesorios = $respuesta3['r_accesorios'];
        $controladores = $respuesta3['r_controladores'];
        $software = $respuesta3['r_software'];
        $dimension = $respuesta3['r_dimension'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Revisi&oacute;n</title>
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
    
	  <h3>Usuario: <?php echo $lognombre; ?> </h3><br>

<h3>Revisi&oacute;n Detallada</h3><a href="ayuda.html#au">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a><a>&nbsp;&nbsp;</a> <a href="contactenos.php?rep=Error en Revisi&oacute;n Detallada">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error ]</a>
<br><br>
<h3><?php echo $clase." ".$marca." ".$modelo." ".$color; ?></h3>
<h3>Serial: <?php echo $serial?></h3>

<form action="guardar_revision.php" method="post" >
    
    <h3>Rubros por revisar</h3><br>
    
    <blockquote class="blockquote">
        <p class="mb-0"><b>1. <?php echo $energia; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="energiaInline1" name="energia" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="energiaInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="energiaInline2" name="energia" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="energiaInline2">No aprueba</label>
        </div>
    </blockquote>
    
    <blockquote class="blockquote">
        <p class="mb-0"><b>2. <?php echo $ruidos; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="ruidosInline1" name="ruidos" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="ruidosInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="ruidosInline2" name="ruidos" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="ruidosInline2">No aprueba</label>
        </div>
    </blockquote>

    <blockquote class="blockquote">
        <p class="mb-0"><b>3. <?php echo $carcasa; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="carcasaInline1" name="carcasa" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="carcasaInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="carcasaInline2" name="carcasa" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="carcasaInline2">No aprueba</label>
        </div>
    </blockquote>

    <blockquote class="blockquote">
        <p class="mb-0"><b>4. <?php echo $memoria; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="memoriaInline1" name="memoria" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="memoriaInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="memoriaInline2" name="memoria" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="memoriaInline2">No aprueba</label>
        </div>
    </blockquote>

    <blockquote class="blockquote">
        <p class="mb-0"><b>5. <?php echo $cpu; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="cpuInline1" name="cpu" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="cpuInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="cpuInline2" name="cpu" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="cpuInline2">No aprueba</label>
        </div>
    </blockquote>

    <blockquote class="blockquote">
        <p class="mb-0"><b>6. <?php echo $comunicacion; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="comunicacionInline1" name="comunicacion" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="comunicacionInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="comunicacionInline2" name="comunicacion" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="comunicacionInline2">No aprueba</label>
        </div>
    </blockquote>

    <blockquote class="blockquote">
        <p class="mb-0"><b>7. <?php echo $entrada; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="entradaInline1" name="entrada" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="entradaInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="entradaInline2" name="entrada" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="entradaInline2">No aprueba</label>
        </div>
    </blockquote>
    
    <blockquote class="blockquote">
        <p class="mb-0"><b>8. <?php echo $salida; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="salidaInline1" name="salida" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="salidaInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="salidaInline2" name="salida" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="salidaInline2">No aprueba</label>
        </div>
    </blockquote>

    <blockquote class="blockquote">
        <p class="mb-0"><b>9. <?php echo $puertos; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="puertosInline1" name="puertos" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="puertosInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="puertosInline2" name="puertos" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="puertosInline2">No aprueba</label>
        </div>
    </blockquote>

    <blockquote class="blockquote">
        <p class="mb-0"><b>10. <?php echo $botones; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="botonesInline1" name="botones" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="botonesInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="botonesInline2" name="botones" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="botonesInline2">No aprueba</label>
        </div>
    </blockquote>

    <blockquote class="blockquote">
        <p class="mb-0"><b>11. <?php echo $bisagras; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="bisagrasInline1" name="bisagras" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="bisagrasInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="bisagrasInline2" name="bisagras" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="bisagrasInline2">No aprueba</label>
        </div>
    </blockquote>

    <blockquote class="blockquote">
        <p class="mb-0"><b>12. <?php echo $sensores; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="sensoresInline1" name="sensores" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="sensoresInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="sensoresInline2" name="sensores" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="sensoresInline2">No aprueba</label>
        </div>
    </blockquote>

    <blockquote class="blockquote">
        <p class="mb-0"><b>13. <?php echo $accesorios; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="accesoriosInline1" name="accesorios" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="accesoriosInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="accesoriosInline2" name="accesorios" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="accesoriosInline2">No aprueba</label>
        </div>
    </blockquote>

    <blockquote class="blockquote">
        <p class="mb-0"><b>14. <?php echo $controladores; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="controladoresInline1" name="controladores" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="controladoresInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="controladoresInline2" name="controladores" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="controladoresInline2">No aprueba</label>
        </div>
    </blockquote>
    
    <blockquote class="blockquote">
        <p class="mb-0"><b>15. <?php echo $software; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="softwareInline1" name="software" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="softwareInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="softwareInline2" name="software" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="softwareInline2">No aprueba</label>
        </div>
    </blockquote>

    <blockquote class="blockquote">
        <p class="mb-0"><b>16. <?php echo $dimension; ?></b></p>
          <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="dimensionInline1" name="dimension" class="custom-control-input" value="Aprueba">
          <label class="custom-control-label" for="dimensionInline1">Aprueba</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="dimensionInline2" name="dimension" class="custom-control-input" value="No aprueba">
          <label class="custom-control-label" for="dimensionInline2">No aprueba</label>
        </div>
    </blockquote>

    <div class="form-floating">
      <textarea class="form-control" name="detalle" placeholder="Detallar observaciones de la revisi&oacute;n" id="floatingTextarea"></textarea>
      <label for="floatingTextarea">Detalle / Otros</label>
    </div>
          <input id="idplaca" name="idplaca" type="hidden" value="<?php echo $id_placa;?>">
          <input id="idplaca" name="codigo" type="hidden" value="<?php echo $codigo;?>">
          <input id="idplaca" name="activo" type="hidden" value="<?php echo $activo;?>">
          <input id="idplaca" name="fondos" type="hidden" value="<?php echo $fondos;?>">
          <input id="idplaca" name="quest" type="hidden" value="<?php echo $quest;?>">
  <div>
         <button type="submit" class="btn btn-dark"><span class="icon icon-floppy-disk"> Guardar</span></button><br>
  </div>
<br>
</form>

    </div>
    <div class="col-md-6">

<div class="d-none d-sm-none d-md-block"><img src="img/revi2.png "width="600" height="600"></div>
          

    </div>
  </div>
</div>

</body>
</html>
