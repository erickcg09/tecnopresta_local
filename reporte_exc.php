<?php

include 'conectar.php';

if((trim($_POST['filas']))==0){ 
	header('Location: reportes.php');
	exit;
	} 
	
	
$query= trim($_POST['consulta_exc']);


 $filename = "Prestamo_de_equipo".date('dmY') . ".xls";
 header("Content-Type: application/xls");
 header("Content-Disposition: attachment; filename='$filename'");
 
$consulta_p = mysqli_query($conexion, "$query");

?>


<table>
        <tr>
        		<th> <?php echo utf8_decode('Préstamo de Equipo'); ?> </th>
        </tr>
        
        <tr>
        		<th> Del <?php echo date("d-m-Y",strtotime(trim($_POST['inicial']))); ?> Al <?php echo date("d-m-Y",strtotime(trim($_POST['fin']))); ?></th>
        </tr>
        
</table> 
 <table>
        <tr>
        		<th>Codigo</th>
            <th>Institucion</th> 
            <th>Cedula</th> 
            <th>Nombre</th>
            <th>I Apellido</th> 
            <th>II Apellido</th>
            <th>Solicitado</th> 
            <th>Devuelto</th>
            
        </tr>
 <?php
 
 //$datos="";
 while($datos=mysqli_fetch_assoc($consulta_p)) {
    
 ?>

         
     <tr>
     			<td> <?php echo $datos['codigo']; ?> </td>
            <td> <?php echo $datos['centro']; ?> </td>
            <td> <?php echo $datos['cedula']; ?> </td>
            <td> <?php echo $datos['nombre']; ?> </td>
            <td> <?php echo $datos['apellidop']; ?> </td>
            <td> <?php echo $datos['apellidom']; ?> </td>
            <td> <?php echo $datos['fecha_s']; ?> </td>
            <td> <?php echo $datos['fecha_d']; ?> </td>
         
               
    </tr>
 <?php } ?>
 
 </table>  */
 
 
 
 
 
 
 
 