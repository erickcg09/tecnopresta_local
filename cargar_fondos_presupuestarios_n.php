<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("conexion.php");
$link = $mysqli;
if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}
$idOrigen = $_POST['id_fondos'];

// Consulta para obtener los fondos presupuestarios excluyendo el del origen seleccionado
$query = "SELECT * FROM t_fondos WHERE id_fondos != $idOrigen";
$result = mysqli_query($link, $query);

if (mysqli_num_rows($result) > 0) {
    echo '<select class="form-select my-3 w-50" id="nuevoFondo" name="nuevoFondo" required>';
    echo '<option value="0">Seleccione un nuevo fondo presupuestario...</option>';
    while ($fondo = mysqli_fetch_array($result)) {
        echo '<option value="' . $fondo['id_fondos'] . '">' . $fondo['fondos'] . '</option>';
    }
    echo '</select>';
} else {
    echo '<p>No hay fondos disponibles.</p>';
}
?>