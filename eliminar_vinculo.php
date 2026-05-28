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
 


if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];
$xeliminar = $_GET['gps']; 


		// sql para eliminar
		$sql = "DELETE FROM t_licencia WHERE id_licencia=$xeliminar";

		if (mysqli_query($link, $sql)) {
		    
		} else {
		    echo "Error al eliminar registro: " . mysqli_error($link);
		}

		mysqli_close($link);

		// Redireccion al index 
		header("location: formulario_seleccionar_desvincular.php?");
		exit();

?> 
