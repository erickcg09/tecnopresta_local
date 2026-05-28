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
} else {

}
    $cedulalog = $_SESSION['cedula'];
    $nombrelog = $_SESSION['nombre'];
    $codigolog = $_SESSION['codigo'];
    $tipolog = $_SESSION['tipo'];
    $dependencialog = $_SESSION['dependencia'];
    $correolog = $_SESSION['correomep'];
    $regionallog = $_SESSION['direccionreg'];
    $circuitolog = $_SESSION['circuito'];


   
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
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">


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


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>  
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
  

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

<div class="container">
  <div class="row">
    <div class="col-12">
        <div class="text-center">

              <img src="img/imas8.png" class="img-fluid w-100" alt="..." width="100" height="100">
        </div>
    </div>
  </div>
  

 
     
  <div class="btn-group">
  <a href="/formulario_beneficiario.php" class="btn btn-primary active" aria-current="page">Editor de Contratos Programa N°3 ]</a>
  <a href="/formulario_informe_activos.php" class="btn btn-primary active">Lista de Activos de su Institución</a>
  <a href="/plataforma_clientes.php" class="btn btn-primary active">Centro de Soporte Educativo</a>
  </div>
    
 
  
  
  
  
  
  <div class="row">
    <div class="col-12">
	  <h3> <b> DEPENDENCIA: </b> <?php echo $dependencialog; ?> </h3><br>
    </div>
  </div>
   
<table class="table table-hover">

	<h2></h2>
	<th>ID</th>
	<th>Identificaci&oacute;n</th>
	<th>Apellido paterno</th>
   <th>Apellido materno</th>
	<th>Primer nombre</th>
	<th>Segundo nombre</th>
	<th>Instituci&oacute;n</th>
	<th>Grado de prioridad de entrega</th>
	<tbody class="BusquedaRapida">
	<?php
   $consulta=mysqli_query($link,"SELECT id, identificacion, apellido1, apellido2, nombre1, nombre2, cod, condicion
		 FROM beneficiarios_imas
		 WHERE cod = '".$codigolog."'
		 ORDER BY id ASC") or die(mysqli_error($link));


	while ($activos=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $activos['id']?></td>
		<td><?php echo $activos['identificacion']?></td>
		<td><?php echo $activos['apellido1']?></td>
		<td><?php echo $activos['apellido2']?></td>
		<td><?php echo $activos['nombre1']?></td>
		<td><?php echo $activos['nombre2']?></td>
		<td><?php echo $activos['cod']?></td>
		<td><?php echo $activos['condicion']?></td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>


</div>
</body>
</html>

<br>
<br>
<br>
<br>


<!-- Footer -->
<footer class="text-center text-lg-start bg-light text-muted">
 
  <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
    <!-- Left -->
    <div class="me-5 d-none d-lg-block">
      
    </div>
    <!-- Left -->

    <!-- Right -->
    <div>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-facebook-f"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-twitter"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-google"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-instagram"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-linkedin"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-github"></i>
      </a>
    </div>
    <!-- Right -->
  </section>
  <!-- Section: Social media -->

  <!-- Section: Links  -->
  <section class="">
    <div class="container text-center text-md-start mt-5">
      <!-- Grid row -->
      <div class="row mt-3">
        <!-- Grid column -->
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
          <!-- Content -->
          <h5 class="text-uppercase fw-bold mb-4">
            <i class="fas fa-gem me-3"></i> <b>TecnoPresta</b>
          </h5>
          <p>
            Sistema de Administración del Inventario Tecnológico y el Préstamo de Equipos del Ministerio de Educación Publica de Costa Rica. Desarrollado con la ayuda de la gente de nuestro Ministerio.
.
          </p>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
          <!-- Links -->
          <h5 class="text-uppercase fw-bold mb-4">
            <b>Relacionados</b>
          </h5>
          <p>
            <a href="/formulario_menu_prestamo.html" class="text-reset">Solicitar Activos </a>
          </p>
          <p>
            <a href="plataforma_clientes.php" class="text-reset">Centro de Soporte
Educativo</a>
          </p>
          <p>
            <a href="formulario_menu_inventario.html" class="text-reset">Módulo de Inventario</a>
          </p>
          <p>
            <a href="formacion.php" class="text-reset">Manuales y Webinarios</a>
          </p>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
          <!-- Links -->
          <h5 class="text-uppercase fw-bold mb-4">
            <b>Accesos Rápidos </b>
          </h5>
          <p>
            <a href="/formulario_beneficiario.php" class="text-reset">Editor de Contratos Programa N°3 Fonatel</a>
          </p>
          <p>
            <a href="/formulario_informe_beneficiarios_programa_dos.php" class="text-reset">Beneficiarios Reportados Programa N°2</a>
          </p>
          <p>
            <a href="/formulario_informe_activos.php" class="text-reset">Lista de todos sus Activos Inventariados</a>
          </p>
          <p>
            <a href="/formulario_informe_solicitudes.php" class="text-reset">Reporte de préstamos realizados en su Institución</a>
          </p>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
          <!-- Links -->
          <h5 class="text-uppercase fw-bold mb-4"> <b> Contactos </b>  </h5>
          <p><i class="fas fa-home me-3"></i> San Francisco de Calle Blancos, San José de Costa Rica</p>
          <p>
            <i class="fas fa-envelope me-3"></i>
            tecnopresta@mep.go.cr
          </p>
          <p><i class="fas fa-phone me-3"></i> </p>
          <p><i class="fas fa-print me-3"></i></p>
        </div>
        <!-- Grid column -->
      </div>
      <!-- Grid row -->
    </div>
  </section>
  <!-- Section: Links  -->

  <!-- Copyright -->
  <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
  <b>Ministerio de Educación Pública de la República de Costa Rica --2022 </b>
   
  </div>
  <!-- -->
</footer>
<!-- Footer -->