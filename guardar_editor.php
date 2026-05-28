<?php 
session_start();
$tienellave = ($_SESSION['tipo']==1);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "formulario_menu_inventario.html"
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

$energia = $_POST['r_energia'];
$ruidos = $_POST['r_ruidos'];
$carcasa = $_POST['r_carcasa'];
$memoria = $_POST['r_memoria'];
$cpu = $_POST['r_cpu'];
$comunicacion = $_POST['r_comunicacion'];
$entrada = $_POST['r_entrada'];
$salida = $_POST['r_salida'];
$puertos = $_POST['r_puertos'];
$botones = $_POST['r_botones'];
$bisagras = $_POST['r_bisagras'];
$sensores = $_POST['r_sensores'];
$accesorios = $_POST['r_accesorios'];
$controladores = $_POST['r_controladores'];
$software = $_POST['r_software'];
$dimension = $_POST['r_dimension'];
$etiqueta = $_POST['etiqueta'];



$query = "select id_puntos from t_puntos_a_revisar where etiqueta='$etiqueta'";
$result = mysqli_query($link,$query);
$check_user = mysqli_num_rows($result);


if($check_user>0){
  echo '<script language = javascript>
  alert("Existe en la base de datos un cuestionario con ese t\u00edtulo")
  self.location = "formulario_editor_cuestionario.php"
  </script>';
} else {

mysqli_free_result($result);

	
$query = "INSERT INTO t_puntos_a_revisar (etiqueta,r_accesorios,r_bisagras,r_botones,r_carcasa,r_comunicacion,r_controladores,r_cpu,r_software,r_dimension,r_energia,r_entrada,r_memoria,r_puertos,r_ruidos,r_salida,r_sensores)VALUES('".$etiqueta."', '".$accesorios."', '".$bisagras."', '".$botones."', '".$carcasa."', '".$comunicacion."', '".$controladores."', '".$cpu."', '".$software."', '".$dimension."', '".$energia."', '".$entrada."', '".$memoria."', '".$puertos."', '".$ruidos."', '".$salida."', '".$sensores."')";
	$link->query($query);
	mysqli_close($link);


	echo '<script language = javascript>
	alert("Guardado correctamente")
	self.location = "formulario_editor_cuestionario.php"
	</script>';

} /* Cierre del else que corresponde a else del $check_user>0 */
?>