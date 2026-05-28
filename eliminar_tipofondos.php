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

$miconsulta = "select id_placa from t_placa where id_fondos='$xeliminar'";
$mirespuesta = $link->query($miconsulta);

if ($mirespuesta->num_rows >= 1) {
		echo '<script language = javascript>
                alert("No se puede eliminar el tipo, es utilizado en el registro de activos plaqueados")
                self.location = "formulario_crear_tipo_fondos.php"
                </script>';
} else {
		// sql para eliminar
		$sql = "DELETE FROM t_fondos WHERE id_fondos=$xeliminar";

		if (mysqli_query($link, $sql)) {
		    
		} else {
		    echo "Error al eliminar registro: " . mysqli_error($link);
		}

		mysqli_close($link);

		// Redireccion al index 
		header('Location: formulario_crear_tipo_fondos.php');
		exit();
} // Fin del IF
?> 