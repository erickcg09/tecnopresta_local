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
$id_placa = $_GET['gps']; 

$consulta=mysqli_query($link,"SELECT Tu.id_placa, Tu.cubiculo, Tp.placa, Te.edificio, Ti.estancia, Tr.regional, Tc.circuito
		 FROM t_ubicacion_activo Tu
		 INNER JOIN t_placa Tp ON Tu.id_placa = Tp.id_placa 
		 INNER JOIN t_edificio Te ON Tu.id_edificio = Te.id_edificio
		 INNER JOIN t_estancia Ti ON Tu.id_estancia = Ti.id_estancia
		 INNER JOIN t_regional Tr ON Tu.id_regional = Tr.id_regional
                 INNER JOIN t_circuito Tc ON Tu.id_circuito = Tc.id_circuito
		 WHERE Tu.id_placa = '".$id_placa."'
		 ORDER BY Tu.id_placa ASC") or die(mysqli_error($link));

                 $mostrar = mysqli_fetch_array($consulta);
		         $laplaca = $mostrar['placa'];
		         $eledificio = $mostrar['edificio'];
		         $laestancia = $mostrar['estancia'];
		         $elcubiculo = $mostrar['cubiculo'];
		         $laregional = $mostrar['regional'];
                 $elcircuito = $mostrar['circuito'];

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
  <script src="css/bootstrap-select.min.js"></script>
  <link rel="stylesheet" href="css/bootstrap-select.min.css">
  <script src="css/defaults-es_ES.min.js"></script>


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
        <a class="nav-link" href="formulario_agregar_ubicacion.php"><span class="icon icon-undo2"></span> Regresar</a>
      </li>   
    </ul>
  </div>  
</nav>
<br>

<div class="container">
<h3>Usuario: <?php echo $lognombre; ?> </h3><br>
  <div class="row">
    <div class="col">
      <h3>Ubicar Activo</h3>

          <div class="card">
            <img class="card-img-top" src="img/editar_u1.png">            
            <div class="card-img-overlay">
              <h4 class="card-title"><?php echo $laplaca?></h4>
              <p class="card-text">Ubicado en <?php echo $laregional?> , Circuito <?php echo $elcircuito?> ,<?php echo $eledificio?> , <?php echo $laestancia?>, <?php echo $elcubiculo?>. </p>
              <a href="formulario_agregar_ubicacion.php" class="btn btn-primary"><span class="icon-enter"></span> Cerrar</a>
            </div>
          </div> 
  
    </div>
    <div class="col">
    
    </div>
  </div>    
	  
</div>

</body>
</html>

