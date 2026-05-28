<?php  
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4 or $_SESSION['tipo']==5);
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
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">
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
    
	  <h3>Usuario: <?php echo $lognombre; ?> </h3><br>

<h3>Listado de Activos por Dependencia </h3><a href="ayuda.html#rlad">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a><a>&nbsp;&nbsp;</a> <a href="contactenos.php?rep=Error en Listado de Activos por Dependencia">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error ]</a><br><br>

<div class="input-group mb-3">

	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
  
	
		  
     	  <div class="col-md-4">
  	   	
     			<input type="text" name="centro" id="centro" class="form-control " autocomplete="off" placeholder="Ingrese el c&oacute;digo presupuestario" /> 
         	<ul class="list-group">
         	</ul>
   	      </div>
   	      
   	      <div class="col-md-2">
   		
   		    <input type="submit" class="btn btn-dark "value="Buscar" name="btnBuscar"/>  
 					
 		  </div> 
   	   
      	
  	</form>
</div>


<form action="" method="POST" enctype="multipart/form-data">
  
<table class="table table-hover">

	<h2></h2>
	<th>Activo</th>
	<th>Marca</th>
	<th>Modelo</th>
   <th>Color</th>
	<th>Placa</th>
	<th>Serial</th>
	<th>Estado</th>
	<th><a class="btn btn-info" href="exportar_a_c.php?codigop=<?php echo$logcodigo?>" role="button"><span class="icon icon-file-pdf"></span> PDF</a></th>
	<th><a class="btn btn-info" href="exportar_excel.php?codigop=<?php echo$logcodigo?>" role="button"><span class="icon icon-file-excel"></span> EXCEL</a></th>
		
	<tbody class="BusquedaRapida">
	<?php
   $consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
		 FROM t_activo Ta
		 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
		 INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
		 WHERE Tp.codigo = '".$logcodigo."' AND Tp.activo = '".$activado."'
		 ORDER BY Tg.clase ASC") or die(mysqli_error($link));


	while ($activos=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $activos['clase']?></td>
		<td><?php echo $activos['marca']?></td>
		<td><?php echo $activos['modelo']?></td>
		<td><?php echo $activos['color']?></td>
		<td><?php echo $activos['placa']?></td>
		<td><?php echo $activos['serial']?></td>
                    <?php $idestado=$activos['id_estado'];?>
	   <td><?php if ($idestado==1){echo "Excelente";} 
	   			 if ($idestado==2){echo "Bueno";}
	   			 if ($idestado==3){echo "Regular";}
	   			 if ($idestado==4){echo "Malo";}	?></td>
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



