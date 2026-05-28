<?php

include 'conexion.php';

//Inicializar variables

$codigo=trim($_GET['codigop']);
$b_dependencia= trim($_GET['dependencia_p']); 
$fuente= trim($_GET['fuentep']);
$activado= 1;
$fuentenombre=trim($_GET['fuente_n']);
$link=$mysqli;


if(!empty($codigo)){
   	if(!empty ($fuente)) {			
    		$consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
		 	FROM t_activo Ta
		 	INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 	INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 	INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
		 	INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
		 	WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."' AND Tp.id_fondos = '".$fuente."'
		 	ORDER BY Tg.clase ASC") or die(mysqli_error($link));
	   	  	
     }else{
 
     $consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo, Tp.id_fondos 
		 FROM t_activo Ta
		 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
		 INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
		 WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."'
		 ORDER BY Tg.clase ASC") or die(mysqli_error($link));

   } 
  
	



 $filename = "Lista_activos por financiamiento".date('dmY') . ".xls";
 header("Content-Type: application/xls");
 header("Content-Disposition: attachment; filename='$filename'");
 


}

?>


<table>
        <tr>
        		<th> <?php echo utf8_decode('ACTIVOS POR DEPENDENCIA Y FUENTE DE FINANCIAMIENTO'); ?> </th>
        </tr>
        
        
        <tr>
        		<th>  <?php echo utf8_decode('CÓDIGO: '); echo $codigo; ?> </th>
        </tr>
        
        <tr>
        		<th> <?php echo utf8_decode('FONDOS: '); echo utf8_decode($fuentenombre); ?> </th>
        </tr>
        <tr>
        		<th> FECHA:  <?php echo date('d-m-Y'); ?> </th>
        </tr>
        
</table> 
 <table>
        <tr>
        	<th>Activo</th>
	        <th>Marca</th>
	        <th>Modelo</th>
            <th>Color</th>
    	    <th>Placa</th>
	        <th>Serial</th>
	        <th>Estado</th>
            
        </tr>
 <?php
 
 
while ($activos=mysqli_fetch_array($consulta))  {
    
 ?>

         
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
 <?php } ?>
 
 </table>  
 
 


?>



















