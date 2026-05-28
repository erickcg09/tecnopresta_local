<?php

require_once("conexion.php");


$link = mysqli_connect($servername, $username, $password, $dbname);

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}


$cedula = "0303460987"; 
// consulta
$sql = "SELECT id_rol FROM t_lista_blanca WHERE cedula='$cedula'";
$result = mysqli_query($link,$sql);

// Associative array
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$tipo = $row["id_rol"];
echo "El id_rol es: ".$tipo;
// Free result set
mysqli_free_result($result);

mysqli_close($link);
?>
