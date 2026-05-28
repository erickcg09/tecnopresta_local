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

$edificio = $_POST['edificio'];
$id_regional = $_POST['cbx_regional'];
$id_circuito = $_POST['cbx_circuito'];

$query = "select id_edificio from t_edificio where edificio='$edificio'";
$result = mysqli_query($link,$query);
$check_user = mysqli_num_rows($result);


if($check_user>0){
  echo '<script language = javascript>
  alert("El Campus o Edificio ya existe")
  self.location = "formulario_crear_edificio.php"
  </script>';
} else {
mysqli_free_result($result);

	
$query = "INSERT INTO t_edificio (edificio,id_circuito,codigo)VALUES('".$edificio."','".$id_circuito."','".$logcodigo."')";
	$link->query($query);
	mysqli_close($link);


	echo '<script language = javascript>
	alert("Guardado correctamente")
	self.location = "formulario_crear_edificio.php"
	</script>';

} /* Cierre del else que corresponde a else del $check_user>0 */
?>
