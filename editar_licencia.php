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


if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {
    	
}

$licencia = $_POST['licencia'];
$idsoftware = $_POST['idsoftware'];

 
// sql para actualizar

$update = "UPDATE t_software SET licencia = '".$licencia."' WHERE id_software = '".$idsoftware."'";
$link->query($update);  

if (mysqli_query($link, $update)) {
    
} else {
    echo "Error al actualizar registro: " . mysqli_error($link);
}

mysqli_close($link);

// Redireccion al index 
header('Location: formulario_demostrar_serial.php');
exit();  
?> 
