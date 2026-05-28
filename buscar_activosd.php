<?php
session_start();
require_once("conexion.php");

$link = $mysqli;

if (mysqli_connect_errno()) {
    echo json_encode(["error" => "Error de conexión a MySQL: " . mysqli_connect_error()]);
    exit;
}

mysqli_set_charset($link, "utf8");

// Obtener el término de búsqueda
$termino = isset($_GET['termino']) ? $_GET['termino'] : '';

// Consulta SQL para buscar coincidencias
$query = "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color
          FROM t_activo Ta
          INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag
          INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca
          INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
          WHERE Tg.clase LIKE '%$termino%'
             OR Tm.marca LIKE '%$termino%'
             OR Ta.modelo LIKE '%$termino%'
             OR Tc.color LIKE '%$termino%'
          ORDER BY Tg.clase ASC";

$result = mysqli_query($link, $query);

if (!$result) {
    echo json_encode(["error" => "Error en la consulta: " . mysqli_error($link)]);
    exit;
}

$activos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $activos[] = $row;
}

echo json_encode($activos);

mysqli_close($link);
?>