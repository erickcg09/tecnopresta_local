<?php
require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

$sql = "SELECT t_fondos.fondos, COUNT(t_placa.id_placa) as total_registros
        FROM t_placa
        JOIN t_fondos ON t_placa.id_fondos = t_fondos.id_fondos
        WHERE t_placa.id_estado = 5
        GROUP BY t_placa.id_fondos";

$result = $link->query($sql);

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$link->close();

echo json_encode($data);
?>
