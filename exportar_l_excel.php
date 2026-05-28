<?php

include 'conexion.php';

//Inicializar variables

$codigo=trim($_GET['codigop']);

$activado= 1;
$link=$mysqli;
$titulo="LISTA DE LICENCIAS POR DEPENDENCIA";


	
 if(!empty($codigo)){
    	
 	
	 
  $consulta=mysqli_query($link,"SELECT  Ts.licencia, Ts.factivacion, Ts.vigencia, Tl.id_placa, Tsg.etiqueta, Tp.placa, Tp.serial, Tp.codigo
        FROM (t_software Ts INNER JOIN t_licencia Tl ON Ts.id_software = Tl.id_software) LEFT JOIN t_placa Tp ON Tl.id_placa = Tp.id_placa
        INNER JOIN t_software_general Tsg ON Ts.id_sg = Tsg.id_sg
        WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."'
		  ORDER BY Ts.factivacion ASC") or die(mysqli_error($link));


 $filename = "Lista_licencias_dependencia".date('dmY') . ".xls";
 header("Content-Type: application/xls");
 header("Content-Disposition: attachment; filename='$filename'");
 


} else{
    header('Location: formulario_informe_licencia_codigo.php');
	exit;
}

?>


<table>
        <tr>
        		<th> <?php echo utf8_decode($titulo); ?> </th>
        </tr>
        
        
        <tr>
        		<th>  <?php echo utf8_decode('CÓDIGO: '); echo $codigo; ?> </th>
        </tr>
        <tr>
        		<th> FECHA:  <?php echo date('d-m-Y'); ?> </th>
        </tr>
        
</table> 
 <table>
        <tr>
        	<th>Licencia</th>
	        <th>Nombre</th>
	        <th>Placa</th>
            <th>Serial</th>
    	    <th>Fecha Acti</th>
	        <th>Vigencia</th>
	        <th>F.Expira</th>
            
        </tr>
 <?php
 
 
while ($activos=mysqli_fetch_array($consulta))  {
    
 ?>
        
     <tr>
		<td><?php echo $activos['licencia']?></td>
		<td><?php echo $activos['etiqueta']?></td>
		<td><?php echo $activos['placa']?></td>
		<td><?php echo $activos['serial']?></td>
		<td><?php echo $activos['factivacion']?></td>
		<td><?php echo $activos['vigencia']?></td>
		<td><?php $vence= $activos['factivacion']." + ".$activos['vigencia']. "month"; echo date("d-m-Y",strtotime($vence));?></td>
                    
	 </tr>
 <?php } ?>
 
 <?php 
	mysqli_close($link);	
 ?>
 
 </table>  
 
 


















