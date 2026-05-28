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
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];

if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}
	
	$id_circuito = $_POST['id_circuito'];
	
	$query = "SELECT id_edificio, edificio FROM t_edificio WHERE id_circuito = '$id_circuito' AND codigo = '$logcodigo' ORDER BY edificio";
	$resultado=$link->query($query);
	        $html.= "<option value='".$cero."'>".$letrero."</option>";
	while($row = $resultado->fetch_assoc())
	{
		$html.= "<option value='".$row['id_edificio']."'>".$row['edificio']."</option>";
	}
	echo $html;
?>
