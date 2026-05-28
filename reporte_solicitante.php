<!DOCTYPE html>
 <?php
include 'conectar.php';


//$query_s= "SELECT DISTINCT t_boleta.cedula AS cedula, t_padron.nombre AS nombre,t_padron.apellidop AS apellidop, t_padron.apellidom AS apellidom FROM t_boleta INNER JOIN t_padron ON  t_padron.cedula= t_boleta.cedula ORDER BY t_padron.apellidop ";
//$consultar_s=mysqli_query($conexion,"$query_s" );

 function alerta($val){
	
   if ($val==1){ 
   	echo $val;
      $val=0;
 		echo '<script type="text/javascript"> 
				   function alertas(){
				      alertify.alert("Seleccione un solicitante");}
				        alertas();
				        
				        window.onload();
		      </script>';
		    
   }
   if ($val==2){
   	echo $val;
		$val=0; 		
 		echo '<script type="text/javascript"> 
				   function alertas(){
				     alertify.alert("El usuario no posee solicitudes en el ramgo de fechs seleccionado");}
				        alertas();
				       window.onload();
		      </script>';
   }
   
}
    
 
if (isset($_POST['btn_generar'])){
     
      
   $fecha_i= date("Y-m-d",strtotime(trim($_POST['fecha_inicio'])));
   $fecha_f= date("Y-m-d",strtotime(trim($_POST['fecha_fin'])));
   $solicitante= trim($_POST['solicitante']);
     
  
    	
   if(empty($solicitante)){
     		
     		alerta(1);
     	
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
				alerta (2);
			}
		}
     
  } 
 
  
 ?>


<html>
<body>
<head>
<title>Reportes</title>
<meta name="generator" content="Bluefish 2.2.10" >
<meta name="author" content="Grettel" >
<meta name="date" content="2020-07-07T20:38:16+0000" >
<meta name="copyright" content="">
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="ROBeOTS" content="NOINDEX, NOFOLLOW">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8">
<meta http-equiv="content-style-type" content="text/css">

<meta http-equiv="expires" content="0"><meta name="viewport" content="width=device-width, initial-scale=1.0">


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-latest.js"> </script>
<script src="menu.js"></script>
<script src="bootstrap.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

<link rel="stylesheet" href="css/fonts.css">
<link rel="stylesheet" href="css/menu.css">
<link rel="stylesheet" href="css/principal.css">
<link rel="stylesheet" href="estilo.css">
<link rel="stylesheet" href="bootstrap.min.css">

<!-- Buscador -->
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>  
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  

<!-- Fin buscador -->
<!--<script type="text/javascript" src="js/jquery.js"></script>


<!-- Alertas -->

  <!-- fin alertas -->
  
<script type="text/javascript" src="alertify.js"></script>



</head>

<header>

   <div class="menu_bar">
        <a href=# class="bt-menu"><span class="icon-menu"></span> MENU</a>
   </div>
          
<nav>
        <ul>
            <li><a href="reportes.php"> <span class="icon-home"></span>Inicio</a></li>
            <li><a href="reportes.php"> <span class="icon-spinner11"></span>Actualizar</a></li>
        </ul>
</nav>

<section>
<div class="container">
  
  <div class="row">
     
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
   
                      
	  <div class="form-group row">
        <!-- Esta col vacía es para hacer un espacio a la izquierda -->
       		              
        	<div class="col-sm-1">            
         </div>
      	
      	<label class="col-mb-2 col-form-label"  > Solicitante</label>   
  		     		
     	  <div class="col-sm-2">
  	   	
     		<input type="text" name="solicitante" id="solicitante" class="form-control input-m" autocomplete="off" placeholder="Identificación" /> 
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
   		
   		<input type="submit" class=" sm-2"value="Generar" name="btn_generar"/>  
 					
 			</div>
 					
      </div>
      
     </form> 
  </div>
 </div> 
 
 <div id="botones">
 
 <div id="pdf">
 
 <form action="reporte_pdf.php" method="POST" enctype="multipart/form-data">
       
	<input type="hidden" value="<?php echo $query;?>" name="consulta_pdf"/>
	<input type="hidden" value="<?php echo $fecha_i;?>" name="inicial"/>
	<input type="hidden" value="<?php echo $fecha_f;?>" name="fin"/>
	<input type="hidden" value="<?php echo $totalfilas;?>" name="filas"/>
      
 	<span class="icon-file-pdf"></span> <input type="submit" value=" Descargar PDF" name="pdf"  /> <br>
 	
 </form>
 </div>
 <div id="excel">
     
 <form action="reporte_exc.php" method="POST" enctype="multipart/form-data">
   <input type="hidden" value="<?php echo $query;?>" name="consulta_exc"/>
   <input type="hidden" value="<?php echo $fecha_i;?>" name="inicial"/>
	<input type="hidden" value="<?php echo $fecha_f;?>" name="fin"/>
	<input type="hidden" value="<?php echo $totalfilas;?>" name="filas"/>
   <span class="icon-file-excel"></span> <input type="submit" value="Descargar Excel" name="exporta" > <br>
   
 </form>       
</div>
</div>
<div id="resultado">

<div id="encabezado_informe">


</div>

<?php


if(!$totalfilas==0){
	
$dato=mysqli_fetch_array($consultar_p);	
$cedula=$dato[2];
$nombre=$dato[3];
$apellido_p=$dato[4];
$apellido_m=$dato[5];

echo"<h4>$nombre_reporte </h4>";
echo"</br>";
echo '<h5>  Solicitante:'. utf8_decode($nombre).' '. utf8_decode($apellido_p).' '. utf8_decode($apellido_m).'</h5>'; 
echo"</br>";
echo '<h5> Identificación:. '. $cedula.'</h5>'; 
echo"</br>";
echo '<h5> De:'. date("d-m-Y",strtotime($fecha_i)).' A: ' .date("d-m-Y",strtotime($fecha_f)).'</h5>'; 
echo"</br>";
	
 echo"<table class='table table-hover'>
 		<thead>
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

 
</section>
<footer>
			
	</footer>
        
</body>
</html>


<script type="text/javascript" >

$(document).ready(function(){
	
   
 
 $('#solicitante').typeahead({
  source: function(query, result)
  {
   $.ajax({
    url:"typeah_s.php",
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

<?php


?>

<script type="text/javascript" src="js/alertify.min.js"></script> 
<script type="text/javascript" src="js/alertify.js"></script> 

<link rel="stylesheet" href="css/alertify.css">

