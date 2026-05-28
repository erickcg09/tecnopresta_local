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


$alajuela = "ALAJUELA";
$cartago = "CARTAGO";
$guanacaste = "GUANACASTE";
$heredia = "HEREDIA";
$limon = "LIMON";
$puntarenas = "PUNTARENAS";
$sanjose = "SAN JOSE"; 

$consulta7 = mysqli_query($link,"SELECT COUNT(Ta.prestamo_Id) as n, Tg.provincia
FROM t_prestamo Ta
INNER JOIN t_provincia_cp Tg ON Ta.prestamo_codigo_presupuestario = Tg.prestamo_cp
WHERE Tg.provincia = '$sanjose'") or die(mysqli_error($link));
while ($fila7 = mysqli_fetch_array($consulta7)) {
    $p7 =  $fila7["n"];
}

$consulta1 = mysqli_query($link,"SELECT COUNT(Ta.prestamo_Id) as n, Tg.provincia
FROM t_prestamo Ta
INNER JOIN t_provincia_cp Tg ON Ta.prestamo_codigo_presupuestario = Tg.prestamo_cp
WHERE Tg.provincia = '$alajuela'") or die(mysqli_error($link));
while ($fila1 = mysqli_fetch_array($consulta1)) {
    $p1 =  $fila1["n"];
}

$consulta2 = mysqli_query($link,"SELECT COUNT(Ta.prestamo_Id) as n, Tg.provincia
FROM t_prestamo Ta
INNER JOIN t_provincia_cp Tg ON Ta.prestamo_codigo_presupuestario = Tg.prestamo_cp
WHERE Tg.provincia = '$cartago'") or die(mysqli_error($link));
while ($fila2 = mysqli_fetch_array($consulta2)) {
    $p2 =  $fila2["n"];
}

$consulta3 = mysqli_query($link,"SELECT COUNT(Ta.prestamo_Id) as n, Tg.provincia
FROM t_prestamo Ta
INNER JOIN t_provincia_cp Tg ON Ta.prestamo_codigo_presupuestario = Tg.prestamo_cp
WHERE Tg.provincia = '$guanacaste'") or die(mysqli_error($link));
while ($fila3 = mysqli_fetch_array($consulta3)) {
    $p3 =  $fila3["n"];
}

$consulta4 = mysqli_query($link,"SELECT COUNT(Ta.prestamo_Id) as n, Tg.provincia
FROM t_prestamo Ta
INNER JOIN t_provincia_cp Tg ON Ta.prestamo_codigo_presupuestario = Tg.prestamo_cp
WHERE Tg.provincia = '$heredia'") or die(mysqli_error($link));
while ($fila4 = mysqli_fetch_array($consulta4)) {
    $p4 =  $fila4["n"];
}

$consulta5 = mysqli_query($link,"SELECT COUNT(Ta.prestamo_Id) as n, Tg.provincia
FROM t_prestamo Ta
INNER JOIN t_provincia_cp Tg ON Ta.prestamo_codigo_presupuestario = Tg.prestamo_cp
WHERE Tg.provincia = '$limon'") or die(mysqli_error($link));
while ($fila5 = mysqli_fetch_array($consulta5)) {
    $p5 =  $fila5["n"];
}

$consulta6 = mysqli_query($link,"SELECT COUNT(Ta.prestamo_Id) as n, Tg.provincia
FROM t_prestamo Ta
INNER JOIN t_provincia_cp Tg ON Ta.prestamo_codigo_presupuestario = Tg.prestamo_cp
WHERE Tg.provincia = '$puntarenas'") or die(mysqli_error($link));
while ($fila6 = mysqli_fetch_array($consulta6)) {
    $p6 =  $fila6["n"];
}

?>