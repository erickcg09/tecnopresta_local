<?php
require_once("conexion.php");
$link = $mysqli;

$sql = "SELECT 
            SUM(CASE WHEN t_placa.enuso = 1 THEN 1 ELSE 0 END) AS total_en_uso,
            SUM(CASE WHEN t_placa.enuso = 0 THEN 1 ELSE 0 END) AS total_no_en_uso
        FROM 
            t_placa
        WHERE 
            t_placa.id_fondos IN (2)";

$result = $link->query($sql);

$data = $result->fetch_assoc() ?: ['total_en_uso' => 0, 'total_no_en_uso' => 0];

// Establecemos el encabezado como JSON
header('Content-Type: application/json');
echo json_encode($data);
?>