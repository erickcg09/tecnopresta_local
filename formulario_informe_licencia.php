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
$logdependencia = $_SESSION['dependencia'];
$activado = 1;

if (isset($_POST['btnBuscar'])){
    
  
    if(!empty(trim($_POST['centro']))) {
   		$logcodigo= trim($_POST['centro']);}
}


    
   
    
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
    <script>
      $(document).ready(function () {  
        //Detectar click en el checkbox superior de la lista
        $('#selectall').on('click', function () {
          //verificar el estado de ese checkbox si esta marcado o no
          var checked_status = this.checked;
 
          /*
           * asignarle ese estatus a cada uno de los checkbox
           * que tengan la clase "selectall"
          */
          $(".selectall").each(function () {
            this.checked = checked_status;
          });
        });
      });
    </script>

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

<script>
$(document).ready(function(){
 
 $('#centro').typeahead({
  source: function(query, result)
  {
   $.ajax({
    url:"codigop.php",
    method:"POST",
    data:{query:query},
    dataType:"json",
    success:function(data)
    {
     result($.map(data, function(item){
      return item;
     }));
    }
   })
  }
 });
 
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
    
	  <h3>Bienvenido: <?php echo $lognombre; ?> </h3><br>

<h1>Listado de Licencias  </h1>
<br>

<div class="input-group mb-3">


<div class="input-group mb-3">
  <div class="input-group-prepend">
      <span class="input-group-text" id="basic-addon1">Buscar</span>
  </div>
  <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese el tipo del activo" aria-describedby="basic-addon1">
</div> 
	
</div>


<form action="" method="POST" enctype="multipart/form-data">
  
<table class="table table-hover">

	<h2></h2>
	<th>Licencia </th>
	<th>Nombre </th>
	<th>Código  </th>
	<th>Placa  </th>
   <th>Serial </th>
	<th>F.Activación</th>
	<th>Vigencia</th>
	<th>F.Expira</th>
	
	<th><a class="btn btn-info" href="exportar_l.php?codigop=<?php echo$logcodigo?>&dependenciap=<?php echo$logdependencia?>" role="button"><span class="icon icon-file-pdf"></span> PDF</a></th>
	<th><a class="btn btn-info" href="exportar_li_excel.php?codigop=<?php echo$logcodigo?>&dependenciap=<?php echo$logdependencia?>" role="button"><span class="icon icon-file-excel"></span> EXCEL</a></th>
		
	<tbody class="BusquedaRapida">
	<?php
   $consulta=mysqli_query($link,"SELECT  Ts.licencia, Ts.factivacion, Ts.vigencia, Tl.id_placa, Tsg.etiqueta, Tp.placa, Tp.serial, Tp.codigo
        FROM (t_software Ts INNER JOIN t_licencia Tl ON Ts.id_software = Tl.id_software) LEFT JOIN t_placa Tp ON Tl.id_placa = Tp.id_placa
        INNER JOIN t_software_general Tsg ON Ts.id_sg = Tsg.id_sg
        WHERE Tp.activo = '".$activado."'
		  ORDER BY Tsg.etiqueta ASC") or die(mysqli_error($link));


	while ($activos=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $activos['licencia']?></td>
		<td><?php echo $activos['etiqueta']?></td>
		<td><?php echo $activos['codigo']?></td>
		<td><?php echo $activos['placa']?></td>
		<td><?php echo $activos['serial']?></td>
		<td><?php echo date("d-m-Y",strtotime($activos['factivacion']))?></td>
		<td><?php echo $activos['vigencia']?></td>
        <td><?php $vence= $activos['factivacion']." + ".$activos['vigencia']. "month";	
      			echo date("d-m-Y",strtotime($vence));?></td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>
</form>

</div>
</body>
</html>



