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
$xeliminar = $_GET['borra']; 


$preguntar = mysqli_query($link, "select file_name from uploads where id='$xeliminar'");   
$respuesta = mysqli_fetch_array($preguntar);
$archivo = $respuesta['file_name'];



		// sql para eliminar
		$sql = "DELETE FROM uploads WHERE id=$xeliminar";
        $espera = unlink("uploads/".$archivo);
		if (mysqli_query($link, $sql)) {
		    
		} else {
		    echo "Error al eliminar registro: " . mysqli_error($link);
		}

		mysqli_close($link);

		// Redireccion al index 
		header('Location: guardar_compromiso_firmado.php');
		exit();

?> 
