<?php
require_once("conexion.php");
$link = $mysqli;

// Consulta SQL para eliminar registros
$sql = "DELETE FROM t_placa WHERE marcado = 1";

// Ejecutar la consulta
if ($link->query($sql) === TRUE) {
    echo "Registros eliminados correctamente.";
} else {
    echo "Error al eliminar registros: " . $link->error;
}

// Cerrar la conexión
$link->close();
?>
