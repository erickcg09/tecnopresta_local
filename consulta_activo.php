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
$cero = 0; 
$letrero = "Seleccione el activo";
$logcodigo = $_SESSION['codigo'];
$activado = 1;
if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}
if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}	
	$id_ag = $_POST['id_ag'];
	
$consulta=mysqli_query($link,"SELECT Ta.id_activo, Tm.marca, Ta.modelo, Tc.color
		 FROM t_activo Ta
		 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 WHERE Ta.id_ag = '".$id_ag."' 
		 ORDER BY Ta.modelo ASC") or die(mysqli_error($link));
	        $html.= "<option value='".$cero."'>".$letrero."</option>";
	while ($activos=mysqli_fetch_array($consulta)) {
	    $etiqueta=$activos['modelo']." ".$activos['marca']." ".$activos['color'];
		$html.= "<option value='".$activos['id_activo']."'>".$etiqueta."</option>";
	}
	echo $html;
?>