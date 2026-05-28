<?php

$s= "localhost";
$u= "tecnopre_rootbd";
$p= "2020*tecnopresta";
$bd= "tecnopre_pntm";


$conexion = new mysqli($s,$u,$p,$bd);

if ($mysqli->connect_errno) {
    echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

?>
