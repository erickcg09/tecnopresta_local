<?php
session_start();
require_once("conexion.php");
$link = $mysqli;

$serie = $_GET['serie'];
$placa = $_GET['placa'];

$query = mysqli_query($link, "SELECT * FROM t_placa WHERE placa = '$placa' OR serial = '$serie'");
$existe = mysqli_num_rows($query) > 0;

echo json_encode(['existe' => $existe]);
?>