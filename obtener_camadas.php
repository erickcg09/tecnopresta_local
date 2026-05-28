<?php
session_start();
require_once("conexion.php");

$codigo = $_SESSION['codigo']; // Código del usuario en sesión

// Consulta para obtener camadas únicas filtradas por el código del usuario
$query = "SELECT DISTINCT camada FROM t_reservas WHERE codigo = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

$options = [];
while ($row = $result->fetch_assoc()) {
    $options[] = $row['camada'];
}

// Devolver los resultados como JSON
echo json_encode($options);

$stmt->close();
$mysqli->close();
?>
