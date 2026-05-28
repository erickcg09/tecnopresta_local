<?php
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


$chartQueryRecords = mysqli_query($link,"SELECT COUNT(Ta.id_activo) as n, Tg.clase
FROM t_activo Ta
INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag
GROUP BY Tg.clase") or die(mysqli_error($link));
?>

