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

$software = $_POST['software'];
$fabricante = trim($_POST['search_marca']);
$id_sg = $_POST['idsoftware'];


	$sql1 = mysqli_query($link, "select id_marca from t_marca where marca='$fabricante'");   
	$resp1 = mysqli_fetch_array($sql1);
	$id_marca = $resp1[id_marca];
 
// sql para actualizar

$update = "UPDATE t_software_general SET etiqueta = '".$software."', id_marca = '".$id_marca."' WHERE id_sg = '".$id_sg."'";
$link->query($update);  

if (mysqli_query($link, $update)) {
    
} else {
    echo "Error al actualizar registro: " . mysqli_error($link);
}

mysqli_close($link);

// Redireccion al index 
header('Location: formulario_crear_software.php');
exit();  
?> 
