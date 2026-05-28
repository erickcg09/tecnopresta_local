<?php

include 'conexion.php';
$link = $mysqli;
if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} 

$consulta=mysqli_query($link,"SELECT MAX(C.codigo) AS codigo, MAX(C.institucion) AS institucion 
                                FROM t_entrega_fonatel A 
                                INNER JOIN t_confirmacion_entrega_fonatel B 
                                ON A.codigo <> B.codigo_i
                                INNER JOIN instituciones C
                                ON C.codigo = A.codigo
                                GROUP by A.codigo ORDER BY MAX(C.codigo)") 
                            or die(mysqli_error($link));

 $filename = "sin_confirmar_entrega_fonatel_".date('dmY') . ".xls";
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
        	<th>Código</th>
	        <th>Institución</th>	                   
        </tr>
<?php
while ($activos=mysqli_fetch_array($consulta))  {
?>                 
     <tr>
        <td><?php echo $activos['codigo']?></td>
		<td><?php echo $activos['institucion']?></td>
	</tr>
 <?php } ?>
 
 </table>  
</body>
</html>