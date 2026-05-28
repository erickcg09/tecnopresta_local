<?php

include 'conexion.php';

//Inicializar variables

$codigo=trim($_GET['codigop']);
$dependencia= trim($_GET['dependenciap']);
$activado= 0;
$link=$mysqli;
$titulo="Listado de activos dados de baja";


 $consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, 
   								Tc.color, Tp.id_placa, Tp.placa, Tp.serial,Tp.id_estado, 
   								Tp.codigo, Tp.activo, Tls.cedula, Tls.nombre, Tls.fecha
								 FROM t_activo Ta
								 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
								 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
								 INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
								 INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
						       INNER JOIN t_log_sacar Tls ON Tp.id_placa = Tls.id_placa
								 WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."'
								 ORDER BY Tg.clase ASC") or die(mysqli_error($link));

  
 $filename = "Lista_activos_baja".date('dmY') . ".xls";
 header("Content-Type: application/xls");
 header("Content-Disposition: attachment; filename='$filename'");
 




?>


<table>
        <tr>
        		<th> <?php echo utf8_decode($titulo); ?> </th>
        </tr>
        
        <tr>
        		<th> <?php echo utf8_decode($dependencia); ?> </th>
        </tr>
        <tr>
        		<th>  <?php echo utf8_decode('Código: '); echo $codigo; ?> </th>
        </tr>
        <tr>
        		<th> Fecha:  <?php echo date('d-m-Y'); ?> </th>
        </tr>
        
</table> 
 <table>
        <tr>
        	<th>Activo</th>
	        <th>Marca</th>
	        <th>Modelo</th>
            <th>Color</th>
    	      <th>Placa</th>
	         <th>Fecha</th>
	         <th><?php echo utf8_decode('Cédula: '); ?></th>
	         <th>Nombre</th>
            
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
		<td><?php echo $activos['fecha']?></td>
		<td><?php echo $activos['cedula']?></td>
		<td><?php echo $activos['nombre']?></td>
		
      
	</tr>
 <?php } ?>
 
 </table>  */
 
 


?>



















