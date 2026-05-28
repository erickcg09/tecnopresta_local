<?php

session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3);
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
$logcodigo = $_SESSION['codigo'];

$fechaActual = date('Y-m-d');
$bandera = 0;
$falso = "2020-12-10";
?>

<!DOCTYPE html>
<html lang="es">  
  <head>    
    <title>Título de la WEB</title>    
    <meta charset="UTF-8">
    <meta name="title" content="Título de la WEB">
    <meta name="description" content="Descripción de la WEB">    
    <link href="http://dominio.com/hoja-de-estilos.css" rel="stylesheet" type="text/css"/>    
  </head>  
  <body> 
<table class="table table-hover">

	<h2></h2>
	<th>ID Prestamo</th>
	<th>Fecha Retiro</th>
	<th>Fecha Devoluci&oacute;n</th>
	<th>Nombre del Solicitante</th>
	<th>Devuelto</th>
	<th>Fecha Actual</th>
        <tbody class="BusquedaRapida">
	<?php
$consulta=mysqli_query($link,"SELECT Tpd.prestamo_Id, Tpd.prestamo_detalle_devuelto, Tpd.prestamo_detalle_fechaDevolucion, Tp.prestamo_fechaRetiro, Tp.prestamo_nombre_solicitante
		 FROM t_prestamo_detalle Tpd
		 INNER JOIN t_prestamo Tp ON Tpd.prestamo_Id = Tp.prestamo_Id
		 WHERE Tpd.prestamo_detalle_devuelto = '".$bandera."' AND Tpd.prestamo_detalle_fechaDevolucion < '".$fechaActual."'
		 ORDER BY Tpd.prestamo_Id ASC") or die(mysqli_error($link));


	while ($colores=mysqli_fetch_array($consulta)) { ?>
	<tr>
		<td><?php echo $colores['prestamo_Id']?></td>
		<td><?php echo $colores['prestamo_fechaRetiro']?></td>
		<td><?php echo $colores['prestamo_detalle_fechaDevolucion']?></td>
        <td><?php echo $colores['prestamo_nombre_solicitante']?></td>
        <td><?php echo $colores['prestamo_detalle_devuelto']?></td>
        <td><?php echo $fechaActual?></td>
	</tr>
	<?php }
	mysqli_close($link);	
	?>
</tbody>
</table>
  </body>  
</html>
