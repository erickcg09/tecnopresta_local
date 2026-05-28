<?php
session_start();
if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.html"
</script>';
}
require_once("conexion.php");
$link = $mysqli;
$cero = 0; 
$letrero = "Seleccione ...";  


if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}
	
	$id_edificio = $_POST['id_edificio'];
	
	$query = "SELECT id_estancia, estancia FROM t_estancia WHERE id_edificio = '$id_edificio' ORDER BY estancia";
	$resultado=$link->query($query);
	        $html.= "<option value='".$cero."'>".$letrero."</option>";
	while($row = $resultado->fetch_assoc())
	{
		$html.= "<option value='".$row['id_estancia']."'>".$row['estancia']."</option>";
	}
	echo $html;
?>
