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
$xeliminar = $_GET['idx']; 
$year = date('Y');
$devuelto="No";

$miconsulta = "select id from activos_beneficiarios_programa_3 where
id_benef='$xeliminar' AND periodo='$year' AND devuelto='$devuelto'";
$mirespuesta = $link->query($miconsulta);

if ($mirespuesta->num_rows >= 1) {
		echo '<script language = javascript>
                alert("No se puede eliminar el beneficiario, primero debe devolver el activo")
                self.location = "beneficiarios_programa_3.php"
                </script>';
} else {
		// sql para eliminar
		$sql = "DELETE FROM beneficiarios_programa_3 WHERE id=$xeliminar";
        
		if (mysqli_query($link, $sql)) {
		    
		} else {
		    echo "Error al eliminar registro: " . mysqli_error($link);
		}

		mysqli_close($link);

		// Redireccion al index 
		header('Location: beneficiarios_programa_3.php');
		exit();
} // Fin del IF
?> 