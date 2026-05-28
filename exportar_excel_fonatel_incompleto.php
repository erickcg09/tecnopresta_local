<?php

include 'conexion.php';
$link = $mysqli;
if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} 

$consulta=mysqli_query($link,"SELECT * FROM t_confirmacion_entrega_fonatel where completo = 0") 
                            or die(mysqli_error($link));

 $filename = "entrega_incompleta_fonatel_".date('dmY') . ".xls";
 header("Cache-Control: public");
 header("Content-Description: File Transfer");
 header("Content-Type: application/xls");
 header("Content-Disposition: attachment; filename=$filename");
 header("Content-Transfer-Encoding: binary");

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
        <th>Regional</th>
        <th>Circuito</th>    
        <th>Código</th>
        <th>Institución</th>
        <th>Cédula</th>
        <th>Responsable</th>
        <th>Comentario</th>                   
<?php
while ($activos=mysqli_fetch_array($consulta))  {
?>                 
     <tr>
        <td><?php echo $activos['direccion_r']?></td>
        <td><?php echo $activos['circuito']?></td>
        <td><?php echo $activos['codigo_i']?></td>
        <td><?php echo $activos['institucion']?></td>
        <td><?php echo $activos['cedula_f']?></td>
        <td><?php echo $activos['funcionario']?></td>
        <td><?php echo $activos['comentario']?></td>
	</tr>
 <?php } ?>
 
 </table>  
</body>
</html>