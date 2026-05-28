<?php
require_once("conexion.php");

$link = $mysqli;

if (mysqli_connect_errno()) {
    die("Error de conexiÃ³n a MySQL: " . mysqli_connect_error());
}

if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link));
}

if (isset($_GET['id_regional'])) {
    $id_regional = $_GET['id_regional'];

    $query = "SELECT id_analista, nombre, foto FROM t_analista WHERE id_regional = ?";
    if ($stmt = mysqli_prepare($link, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $id_regional);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        echo '<select id="select_analistas" class="form-control select2">'; // Quit¨¦ el name porque usaremos el campo oculto
        echo '<option value="">Seleccione un analista</option>'; 

        while ($row = mysqli_fetch_assoc($result)) {
            $foto = !empty($row['foto']) ? $row['foto'] : 'imagenes/default.png';
            echo '<option value="' . $row['id_analista'] . '" data-foto="' . $foto . '">' . $row['nombre'] . '</option>';
        }

        echo '</select>';

        mysqli_stmt_close($stmt);
    } else {
        echo "Error en la consulta: " . mysqli_error($link);
    }
} else {
    echo "No se recibi¨® el ID de la regi¨®n.";
}

mysqli_close($link);
?>


