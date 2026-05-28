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

$preguntar = mysqli_query($link, "select logo from t_marca where id_marca='$xeliminar'");   
$respuesta = mysqli_fetch_array($preguntar);
$archivo = $respuesta['logo'];


$miconsulta = "select id_marca from t_activo where id_marca='$xeliminar'";
$mirespuesta = $link->query($miconsulta);

$miconsultados = "select id_marca from t_software_general where id_marca='$xeliminar'";
$mirespuestados = $link->query($miconsultados);

if ($mirespuesta->num_rows >= 1) {
		echo '<script language = javascript>
                alert("No se puede eliminar la marca, se usa en registros de activos")
                self.location = "formulario_crear_marca.php"
                </script>';
} elseif ($mirespuestados->num_rows >= 1) {
		echo '<script language = javascript>
                alert("No se puede eliminar la marca, se usa en registros de software")
                self.location = "formulario_crear_marca.php"
                </script>';
} else {
		// sql para eliminar
		$sql = "DELETE FROM t_marca WHERE id_marca=$xeliminar";
        $espera = unlink("ico/".$archivo);
		if (mysqli_query($link, $sql)) {
		    
		} else {
		    echo "Error al eliminar registro: " . mysqli_error($link);
		}

		mysqli_close($link);

		// Redireccion al index 
		header('Location: formulario_crear_marca.php');
		exit();
} // Fin del IF
?> 
