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

$id_placa = $_GET['gps']; 

$miconsulta = "select id_ubicacion from t_ubicacion_activo where id_placa='$id_placa'";
$mirespuesta = $link->query($miconsulta);


if($mirespuesta->num_rows >= 1){

		header("location: formulario_editar_ubicacion.php?gps=".$id_placa);

} else {
		echo '<script language = javascript>
                alert("La ubicaci&oacute;n del activo no existe")
                self.location = "formulario_agregar_ubicacion.php"
                </script>';
}

?> 
