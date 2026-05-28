<?php
session_start();
if (!$_SESSION){
echo '<script language = javascript>
alert("Usuario no autenticado")
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
$parte1 = "<a class=\"btn btn-dark\" href=\"formulario_agregar_placas.php?idx=";
$parte2 = "\" role=\"button\"><span class=\"icon icon-plus\"> Placa y Serie</span></a>";
$part1 = "<img src=\"/img/";
$part2 = "\" width=\"70\" class=\"img-fluid img-thumbnail\">";
$salida = "";

$query = "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tg.imagen
         FROM t_activo Ta
         INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
         INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
         INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag
         ORDER BY Tg.clase ASC";



if(isset($_POST['consulta'])){
	$q = $link->real_escape_string($_POST['consulta']);

$query = "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tg.imagen
         FROM t_activo Ta
         INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
         INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
         INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
         WHERE Tg.clase LIKE '%".$q."%' OR Tm.marca LIKE '%".$q."%' OR Ta.modelo LIKE '%".$q."%'
         ORDER BY Tg.clase ASC";



}

$resultado = $link->query($query);

if($resultado->num_rows > 0){

	$salida.="<table class=\"table table-hover\">
		    <thead>
			<tr>
			    <td>#</td>
			    <td>Activo</td>
			    <td>Marca</td>
		        <td>Modelo</td>
			    <td>Color</td>
			    <td>Imagen</td>
			    <td>Agregar</td>
			</tr>
		    </thead>
		  <tbody>";

	while($fila = $resultado->fetch_assoc()){
		$salida.="<tr>
			    <td>".$fila['id_activo']."</td>
			    <td>".$fila['clase']."</td>
			    <td>".$fila['marca']."</td>
			    <td>".$fila['modelo']."</td>
			    <td>".$fila['color']."</td>
			    <td>".$part1.$fila['imagen'].$part2."</td>
			    <td>".$parte1.$fila['id_activo'].$parte2."</td>
		</tr>";
	}
	$salida.="</tbody></table>";

} else {
	$salida.="No hay activos";
}

echo $salida;

$link->close();
header('Content-type: application/json; charset=utf-8');

?>
