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

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}

$tipo = $_POST['tipo'];


$query = "select id_tipolicencia from t_tipolicencia where tipo='$tipo'";
$result = mysqli_query($link,$query);
$check_user = mysqli_num_rows($result);


if($check_user>0){
  echo '<script language = javascript>
  alert("El tipo de licencia ya est\u00e1 registrado")
  self.location = "formulario_crear_tipo_licencia.php"
  </script>';
} else {
mysqli_free_result($result);

	
$query = "INSERT INTO t_tipolicencia (tipo)VALUES('".$tipo."')";
	$link->query($query);
	mysqli_close($link);


	echo '<script language = javascript>
	alert("El tipo fue guardada correctamente")
	self.location = "formulario_crear_tipo_licencia.php"
	</script>';

} /* Cierre del else que corresponde a else del $check_user>0 */
?>
