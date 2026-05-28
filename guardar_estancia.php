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
 
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];

if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}

$id_edificio = $_POST['cbx_edificio'];
$estancia = $_POST['estancia'];


$query = "select id_estancia from t_estancia where estancia='$estancia' AND codigo='$logcodigo'";
$result = mysqli_query($link,$query);
$check_user = mysqli_num_rows($result);


if($check_user>0){
  echo '<script language = javascript>
  alert("La Estancia / Lugar ya existe")
  self.location = "formulario_crear_estancia.php"
  </script>';
} else {
mysqli_free_result($result);

	
$query = "INSERT INTO t_estancia (id_edificio,estancia,codigo)VALUES('".$id_edificio."', '".$estancia."', '".$logcodigo."')";
	$link->query($query);
	mysqli_close($link);


	echo '<script language = javascript>
	alert("Guardado correctamente")
	self.location = "formulario_crear_estancia.php"
	</script>';

} /* Cierre del else que corresponde a else del $check_user>0 */
?>
