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
date_default_timezone_set('America/Costa_Rica'); //configuro un nuevo timezone
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
$loginstitucion = $_SESSION['dependencia'];
$logcorreo = $_SESSION['correomep'];
$regionallog = $_SESSION['direccionreg'];
$circuitolog = $_SESSION['circuito'];
$year = date('Y');
?>
<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
      <script type="text/javascript" src="js/jquery.min.js"></script>

    <title>Beneficiarios Programa 3</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">TecnoPresta</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
          <div class="navbar-nav">
            <a class="nav-link" aria-current="page" href="inventario_reporte.php">Regresar</a>
            <a class="nav-link" href="gameover.php">Cerrar sesi&oacute;n</a>
          </div>
        </div>
            <form class="d-flex">
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalagregar">Agregar Beneficiario(a)
              </button>
            </form>
      </div>
    </nav>
<div class="container">
   <div class="container">
  <div class="row">
    <div class="col-12">
        <div class="text-center">
<br>
              <img src="img/insercionestudiantes-03.png" class="img-fluid w-75" alt="Imagen Frontal" width="100" height="100">
        </div>
    </div>  

    
<table class="table table-hover">

	<h2></h2>
	<th>C&eacute;dula</th>
	<th>Nombre</th>
	<th>Primer Apellido</th>
	<th>Segundo Apellido </th>
	<th>Equipo</th>
	<th>Eliminar</th>
	<th>Asignar</th>
        <tbody>
	<?php
$consulta=mysqli_query($link,"SELECT id, cedula, nombre, apellidop, apellidom, entregado
		 FROM beneficiarios_programa_3
		 WHERE codigo='$logcodigo' AND periodo='$year'
		 ORDER BY nombre ASC") or die(mysqli_error($link));


	while ($row=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $row['cedula']?></td>
		<td><?php echo $row['nombre']?></td>
		<td><?php echo $row['apellidop']?></td>
		<td><?php echo $row['apellidom']?></td>
		<td><h5><span class="badge bg-secondary"><?php echo $row['entregado']?></span></h5></td>
		<td><a class="btn btn-primary" href="eliminar_beneficiario_programa_3.php?idx=<?php echo $row["id"]?>" role="button" onclick="return confirm('Estás seguro que deseas eliminar el registro?');">Eliminar</a></td>
		<td><input type="button" name="view" value="Asignar y Devolver Equipo" id="<?php echo $row['id']; ?>" class="btn btn-primary view_data" data-bs-toggle="modal" data-bs-target="#modaladd" data-bs-whatever="@mdo" />
        </td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>

</div>    

    
</div>  <!-- Cierre del div container -->  

<div class="modal fade" id="modalagregar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
              <img src="img/area de prestamos3333.png" class="img-fluid w-75" alt="Imagen Frontal" width="100" height="100">
      <div class="modal-body">
        <form method="post" action="guardar_beneficiario_programa_3.php">
          <div class="mb-3">
            <label class="col-form-label">C&eacute;dula / Documento equivalente:</label>
            <input type="text" class="form-control" name="cedula" required>
          </div>
          <div class="mb-3">
            <label class="col-form-label">Nombre:</label>
            <input type="text" class="form-control" name="nombre" required>
          </div>
          <div class="mb-3">
            <label class="col-form-label">Primer Apellido:</label>
            <input type="text" class="form-control" name="apellidop" required>
          </div>
          <div class="mb-3">
            <label class="col-form-label">Segundo Apellido:</label>
            <input type="text" class="form-control" name="apellidom" required>
          </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="sexo" value="Hombre" required>
              <label class="form-check-label">
                Masculino
              </label>
            </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="sexo" value="Mujer" checked>
              <label class="form-check-label">
                Femenino
              </label>
            </div>
          </div>
          <div class="mb-3">
              <label class="col-form-label">
                Nivel
              </label>
                <select class="form-select" name="nivel">
                  <option selected></option>
                  <option value="1">Preescolar Materno</option>
                  <option value="2">Preescolar Transici&oacute;n</option>
                  <option value="3">I ciclo Primero</option>
                  <option value="4">I ciclo Segundo</option>
                  <option value="5">I ciclo Tercero</option>
                  <option value="6">II ciclo Cuarto</option>
                  <option value="7">II ciclo Quinto</option>
                  <option value="8">II ciclo Sexto</option>
                  <option value="9">III ciclo S&eacute;ptimo</option>
                  <option value="10">III ciclo Octavo</option>
                  <option value="11">III ciclo Noveno</option>
                  <option value="12">Diversificado D&eacute;cimo</option>
                  <option value="13">Diversificado Und&eacute;cimo</option>
                  <option value="14">Diversificado Duod&eacute;cimo</option>
                  <option value="15">Otro</option>
                </select>
          </div>
          <input type="hidden" name="codigo" value="<?php echo $logcodigo;?>">
          <input type="hidden" name="periodo" value="<?php echo $year;?>">
          <input type="hidden" name="estatus" value="Sin asignar">
          <input type="hidden" name="institucion" value="<?php echo $loginstitucion;?>">
          <input type="hidden" name="regional" value="<?php echo $regionallog;?>">
          <input type="hidden" name="circuito" value="<?php echo $circuitolog;?>">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Agregar</button>
      </div>
        </form>
    </div>
  </div>
</div>


<div class="modal fade" id="modaladd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Asignar Equipo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="guardar_asignado_programa_3.php" method="post">
      <div class="modal-body" id="beneficiario_detalles">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Agregar</button>
      </div>
      </form>
    </div>
  </div>
</div>
    <!-- Optional JavaScript; choose one of the two! -->
<script>  
$(document).ready(function(){

 $(document).on('click', '.view_data', function(){
  
  var beneficiario_id = $(this).attr("id");
  $.ajax({
   url:"precargar_asignados.php",
   method:"POST",
   data:{beneficiario_id:beneficiario_id},
   success:function(data){
    $('#beneficiario_detalles').html(data);
    $('#modaladd').modal('show');
   }
  });
 });
});  
 </script>
    <!-- Option 1: Bootstrap Bundle with Popper -->


    <!-- Option 2: Separate Popper and Bootstrap JS -->

  </body>
</html>