<?php

include 'conexion.php';
$link = $mysqli;
if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}
//Inicializar variables

$codigo=trim($_GET['codigop']);
$estado= trim($_GET['estadop']);
$b_estado= trim($_GET['b_estadop']);
$dependencia= trim($_GET['dependenciap']);
$activado= 1;
$link=$mysqli;





	
 if(!empty($codigo)){
    	
 	if(($b_estado==1)) {
   		
			
    		$consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
		 	FROM t_activo Ta
		 	INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 	INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 	INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
		 	INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
		 	WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."'AND Tp.id_estado = '".$estado."'
		 	ORDER BY Tg.clase ASC") or die(mysqli_error($link));
		 	
		 	
	   	  	
    }else{
         
 
     $consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
		 FROM t_activo Ta
		 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
		 INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
		 WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."'
		 ORDER BY Tg.clase ASC") or die(mysqli_error($link));

     }	



 $filename = "Lista_activos".date('dmY') . ".xls";
 header("Cache-Control: public");
 header("Content-Description: File Transfer");
 header("Content-Type: application/xls");
 header("Content-Disposition: attachment; filename=$filename");
 header("Content-Transfer-Encoding: binary");        


}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>PNTM Principal</title>
  <meta charset="utf-8">
</head>
<body>
<table>
        <tr>
        		<th> <?php echo utf8_decode('LISTA DE ACTIVOS'); ?> </th>
        </tr>
        
        <tr>
        		<th> <?php echo utf8_decode($dependencia); ?> </th>
        </tr>
        <tr>
        		<th>  <?php echo utf8_decode('C&Oacute;DIGO: '); echo $codigo; ?> </th>
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
</body>
</html>
















