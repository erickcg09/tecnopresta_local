<?php
session_start();
require_once("conexion.php");

if (!isset($_POST['camada']) || empty($_POST['camada'])) {
    echo '<script>alert("No se seleccionó una camada válida."); window.history.back();</script>';
    exit();
}

$camada = $_POST['camada'];

// Eliminar todos los registros con la camada seleccionada
$query = "DELETE FROM t_reservas WHERE camada = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $camada);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo '<script>alert("Reservas eliminadas exitosamente."); window.location.href = "formulario_registrar_reservas_alias.php";</script>';
} else {
    echo '<script>alert("No se encontraron reservas para eliminar."); window.history.back();</script>';
}

$stmt->close();
$mysqli->close();
?>
