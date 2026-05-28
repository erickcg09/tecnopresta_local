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
	
	$id_regional = $_POST['id_regional'];
	
	$query = "SELECT id_circuito, circuito FROM t_circuito WHERE id_regional = '$id_regional' ORDER BY id_circuito";
	$resultado=$link->query($query);
	        $html.= "<option value='".$cero."'>".$letrero."</option>";
	while($row = $resultado->fetch_assoc())
	{
		$html.= "<option value='".$row['id_circuito']."'>".$row['circuito']."</option>";
	}
	echo $html;
?>