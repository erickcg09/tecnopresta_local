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


$preguntar = mysqli_query($link, "select imagen from t_software_general where id_sg='$xeliminar'");   
$respuesta = mysqli_fetch_array($preguntar);
$archivo = $respuesta['imagen'];


$miconsulta = "select id_software from t_software where id_sg='$xeliminar'";
$mirespuesta = $link->query($miconsulta);


if ($mirespuesta->num_rows >= 1) {
		echo '<script language = javascript>
                alert("No se puede eliminar el software, se usa en otros registros")
                self.location = "formulario_crear_software.php"
                </script>';

} else {
		// sql para eliminar
		$sql = "DELETE FROM t_software_general WHERE id_sg=$xeliminar";
        $espera = unlink("ico/".$archivo);
		if (mysqli_query($link, $sql)) {
		    
		} else {
		    echo "Error al eliminar registro: " . mysqli_error($link);
		}

		mysqli_close($link);

		// Redireccion al index 
		header('Location: formulario_crear_software.php');
		exit();
} // Fin del IF
?> 
