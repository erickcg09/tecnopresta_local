<?php
require_once("conexion.php");
$link = $mysqli;

$sql = "SELECT 
            id_lugar,
            COUNT(id_placa) AS total_activos
        FROM 
            t_placa
        WHERE 
            id_fondos = 2
        GROUP BY 
            id_lugar";

$result = $link->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Establecemos el encabezado como JSON
header('Content-Type: application/json');
echo json_encode($data);
?>

