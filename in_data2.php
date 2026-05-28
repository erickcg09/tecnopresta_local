<?php
require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

$sql = "SELECT codigo, COUNT(DISTINCT id_inrf) as total_registros
        FROM t_in_reportes_firmados
        GROUP BY codigo";

$result = $link->query($sql);

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$link->close();

echo json_encode($data);
?>