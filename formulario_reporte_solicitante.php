<!doctype html>


 <?php
include 'conectar.php';
//include 'alertify.min.js';

//$query_s= "SELECT DISTINCT t_boleta.cedula AS cedula, t_padron.nombre AS nombre,t_padron.apellidop AS apellidop, t_padron.apellidom AS apellidom FROM t_boleta INNER JOIN t_padron ON  t_padron.cedula= t_boleta.cedula ORDER BY t_padron.apellidop ";
//$consultar_s=mysqli_query($conexion,"$query_s" );


    
 
if (isset($_POST['btn_generar'])){
     
      
   $fecha_i= date("Y-m-d",strtotime(trim($_POST['fecha_inicio'])));
   $fecha_f= date("Y-m-d",strtotime(trim($_POST['fecha_fin'])));
   $solicitante= trim($_POST['solicitante']);
   $totalfilas=0;
  
    	
   if(empty($solicitante)){
    	echo '1';		
     
     }else {
     	
     		if(!empty($fecha_i) and !empty($fecha_f)){
     	
    	  		$query= "SELECT t_boleta.codigo_pre AS codigo, t_centroEducativo.nombre AS centro,
    	  			 t_boleta.cedula AS cedula, t_padron.nombre AS nombre,t_padron.apellidop AS apellidop,
    	  			 t_padron.apellidom AS apellidom, t_boleta.fecha_s AS fecha_s, t_boleta.fecha_d AS fecha_d 
    	  			 FROM t_boleta INNER JOIN t_centroEducativo ON t_boleta.codigo_pre =t_centroEducativo.codigo_pre 
    	  			 INNER JOIN t_padron ON  t_padron.cedula= t_boleta.cedula  
    	  			 WHERE t_boleta.cedula = '$solicitante' AND DATE(t_boleta.fecha_s) BETWEEN '$fecha_i' AND '$fecha_f' 
    	  			 ORDER BY t_boleta.fecha_s";
       		
       		$consultar_p = mysqli_query($conexion, "$query");
       		$totalfilas= mysqli_num_rows($consultar_p);
        		$nombre_reporte= 'Reporte de Solicitudes por Usuario';
         }
     		         
   		if($totalfilas==0){
			echo '2';				
				
			}
		}
     
  }
 
  
 ?>

<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <title>Informes</title>
  
  <!-- Buscador -->
  <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>  
  
<!-- Fin buscador -->

<!-- Alertify -->
   
   <script type="text/javascript" src="js/alertify.min.js"></script> 
   <link rel="stylesheet" href="css/alertify.min.css">
   <link rel="stylesheet" href="css/alertify/themes/default.min.css">
 
  </head>
  <body>
    
    <nav class="navbar navbar-expand-sm navbar-expand-md navbar-expand-lg bg-dark navbar-dark">        
        <span class="navbar-brand mb-0 h1">Módulo de Informes</span>          
    </nav>
    <nav class="navbar navbar-expand-sm navbar-expand-md navbar-expand-lg bg-dark navbar-dark">         
        <span class="navbar-brand mb-0 h1">
            <img src="img/fondo.png" width="50" height="50" alt="" loading="lazy">
            <a class="navbar-brand" href="formulario_menu_principal.html">TecnoPresta</a>
        </span>        
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="formulario_reporte_solicitante.html">Solicitante</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="formulario_reporte_institución.html">Institución</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Devolución</a>
            </li>            
          </ul>
        </div>  
      </nav>
 
 
 <div class="container-sm container-md container-lg">
  
  			<div class="row justify-content-center">
  				 <div class="col-lg-2">
  				 		<br>
  		   	 </div>
   		</div> 
  <div class="row justify-content-center">
     
  	<form action="" method="POST" enctype="multipart/form-data">
              
	  <div class="form-group row">
        <!-- Esta col vacía es para hacer un espacio a la izquierda -->
      	
      	<label class="col-mb-2 col-form-label"> Solicitante</label>   
  		     		
     	  <div class="col-sm-3">
  	   	
     		<input type="text" name="solicitante" id="solicitante" class="form-control input-md" autocomplete="off" placeholder="Identificación" /> 
         		<ul class="list-group">
         		</ul>
        </div>
 
  			<div class="col-sm-1"> 
 			<label class="col-mb-1 col-form-label" > De </label> 
 			</div>
 			
   		<div class="col-mb-3"> 
			  		
   			<input type="date"  class=' input-group date ' name="fecha_inicio" step="1" min="2020-01-01"  value="<?php echo date("d-m-Y");?>"/>
   		</div>
			
			<div class="col-sm-1"> 	
				<label class="col-mb-1 col-form-label"> A </label> 
			</div> 
 			
 			<div class="col-mb-3"> 	
 				<input type="date" class=' input-group date ' name="fecha_fin" step="1" min="2020-01-01"  value="<?php echo date("d-m-Y");?>"/>
   		</div> 	
   		
   		<div class="col-sm-1">
   		
   		<input type="submit" class="btn btn-primary btn-sm" value="Generar" name="btn_generar"/>  
 					
 			</div>
 					
      </div>
      
     </form> 
  </div>
 
 <div class="row justify-content-center ">
 
<div class="col-lg">
<br>
</div>

</div>
 <div class="row justify-content-right">
 
 <div class="col-mb-3">
 
 <form action="reporte_pdf.php" method="POST" enctype="multipart/form-data">
       
	<input type="hidden" value="<?php echo $query;?>" name="consulta_pdf"/>
	<input type="hidden" value="<?php echo $fecha_i;?>" name="inicial"/>
	<input type="hidden" value="<?php echo $fecha_f;?>" name="fin"/>
	<input type="hidden" value="<?php echo $totalfilas;?>" name="filas"/>
      
 	<span class="icon fas fa-file-pdf"></span> <input class="btn btn-secondary btn-sm " type="submit" value=" Descargar PDF" name="pdf"  /> <br>
 	
 </form>
 </div>
 <div id="col- mb-3">
     
 <form action="reporte_exc.php" method="POST" enctype="multipart/form-data">
   <input type="hidden" value="<?php echo $query;?>" name="consulta_exc"/>
   <input type="hidden" value="<?php echo $fecha_i;?>" name="inicial"/>
	<input type="hidden" value="<?php echo $fecha_f;?>" name="fin"/>
	<input type="hidden" value="<?php echo $totalfilas;?>" name="filas"/>
   <span class="icon-file-excel"></span> <input class="btn btn-success btn-sm" type="submit" value="Descargar Excel" name="exporta" > <br>
   
 </form>       
</div>
</div>

<div class="row justify-content-center ">
 
<div class="col-lg">
<br>
</div>

</div>

<div class="row justify-content-center ">
 
<div class="col-lg">

<?php


if(!$totalfilas==0){
	
$dato=mysqli_fetch_array($consultar_p);	
$cedula=$dato[2];
$nombre=$dato[3];
$apellido_p=$dato[4];
$apellido_m=$dato[5];

echo"<h5 class='text-center'>$nombre_reporte </h5>";
echo"</br>";
echo '<h5 class="text-center">  Solicitante: '. utf8_decode($nombre).' '. utf8_decode($apellido_p).' '. utf8_decode($apellido_m).'</h5>'; 
echo"</br>";
echo '<h5 class="text-center"> Identificación: '. $cedula.'</h5>'; 
echo"</br>";
echo '<h5 class="text-center"> De: '. date("d-m-Y",strtotime($fecha_i)).' A: ' .date("d-m-Y",strtotime($fecha_f)).'</h5>'; 
echo"</br>";
	
 echo"<table class='table table-hover'>
 		<thead class='thead-dark'>
        <tr>
        	   <th>Código</th> 
            <th>Institución</th>
            <th>Solicitado</th> 
            <th>Devuelto</th>
            
        </tr>
      </thead>";
  
        
     // ciclo de llenado de tabla con información obtenida de consulta
  $datos="";
  
   while($datos= mysqli_fetch_array($consultar_p) )
    {
  	  
     $codigo=$datos['codigo'];
     $centro=utf8_decode($datos['centro']);
     
     $fecha_s=date("d-m-Y",strtotime($datos['fecha_s']));
     $fecha_d=date("d-m-Y",strtotime($datos['fecha_d']));
    
    
   echo"
   		<tbody>
   		<tr>
     			
            <td> $codigo </td>
            <td> $centro </td>
            <td> $fecha_s </td>
            <td> $fecha_d</td>
        </tr>";
    }
  echo "
  			</tbody>
  			</table>"; 
  
  
  } 

?> 
</div>
</div>
</div>

</body>

</html>

<script type="text/javascript" >

$(document).ready(function(){
	
   
 
 $('#solicitante').typeahead({
  source: function(query, result)
  {
   $.ajax({
    url:"tcodigop.php",
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

<script type="text/javascript"> 
	   function alertas(){
	   	  alertify.alert("El usuario no posee solicitudes en el rango de fechas seleccionado");
	   	  }	   	       
		    
</script>


