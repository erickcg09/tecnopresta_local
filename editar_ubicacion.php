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

$id_placa = $_POST['id_placa'];
$id_regional = $_POST['cbx_regional'];
$id_circuito = $_POST['cbx_circuito'];
$id_edificio = $_POST['cbx_edificio'];
$id_estancia = $_POST['cbx_estancia'];
$cubiculo = $_POST['cubiculo'];
 
// sql para actualizar

$update = "UPDATE t_ubicacion_activo SET id_regional = '".$id_regional."',id_circuito = '".$id_circuito."', id_edificio = '".$id_edificio."', id_estancia = '".$id_estancia."', cubiculo = '".$cubiculo."' WHERE id_placa = '".$id_placa."'";
$link->query($update);  

if (mysqli_query($link, $update)) {
    
} else {
    echo "Error al actualizar registro: " . mysqli_error($link);
}

mysqli_close($link);

// Redireccion al index 
header('Location: formulario_agregar_ubicacion.php');
exit();  
?> 
