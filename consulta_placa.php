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
$letrero = "Seleccione la placa";
$logcodigo = $_SESSION['codigo'];

if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}
if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}	
	$id_activo = $_POST['id_activo'];
	
	$query = "SELECT id_placa, placa FROM t_placa WHERE id_activo = '$id_activo' AND codigo= '$logcodigo' ORDER BY placa";
	$resultado=$link->query($query);
	        $html.= "<option value='".$cero."'>".$letrero."</option>";
	while($row = $resultado->fetch_assoc())
	{
		$html.= "<option value='".$row['placa']."'>".$row['placa']."</option>";
	}
	echo $html;
?>