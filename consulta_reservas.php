<?php
session_start();
require_once("conexion.php");
$link = $mysqli;

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];

$codigo = $_SESSION['codigo']; // Obtener el valor de la variable de sesión
$year = date('Y'); // Obtener el año actual

$query = "SELECT r.id_reserva, r.fecha_inicio, r.fecha_fin, r.hora_inicio, r.hora_fin, a.alias 
          FROM t_reservas r
          INNER JOIN t_alias a ON r.id_alias = a.alias_id
          WHERE r.codigo = ? AND YEAR(r.fecha_inicio) = ?";
$stmt = $link->prepare($query);
$stmt->bind_param("si", $codigo, $year);
$stmt->execute();
$result = $stmt->get_result();

$reservas = array();
while ($row = $result->fetch_assoc()) {
    $reservas[] = $row;
}

echo json_encode($reservas);
?>