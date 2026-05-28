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

//*** SE COMENTA EL PRIMER SQL PARA PONER EN O EL alias_Id
//*** CUANDO SE ELMINA UN ALIAS*/

// $miconsulta = "select id_activo from t_activo where alias_id='$xeliminar'";
// $mirespuesta = $link->query($miconsulta);

// if ($mirespuesta->num_rows >= 1) {
// 		echo '<script language = javascript>
//                 alert("No se puede eliminar el alias, es utilizado en activos")
//                 self.location = "formulario_crear_alias.php"
//                 </script>';
// } else {
		// sql para eliminar
		$sql = "DELETE FROM t_alias WHERE alias_id=$xeliminar";

		if (mysqli_query($link, $sql)) {
		    
		} else {
		    echo "Error al eliminar registro: " . mysqli_error($link);
		}

		// Poner Alias en cero
		$update = "UPDATE t_placa SET alias_id = 0 WHERE codigo='$logcodigo' and alias_id=$xeliminar";		
		//echo $update;
		if (mysqli_query($link, $update)) {
		    
		} else {
		    echo "Error al eliminar registro: " . mysqli_error($link);
		}

		mysqli_close($link);

		// Redireccion al index 
		header('Location: formulario_crear_alias.php');
		exit();

//} // Fin del IF
?> 
