<?php
require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

} 

// Comprobar si se envían los datos de búsqueda
if (isset($_POST['placa']) || isset($_POST['serie'])) {
    $placa = isset($_POST['placa']) ? $link->real_escape_string($_POST['placa']) : null;
    $serie = isset($_POST['serie']) ? $link->real_escape_string($_POST['serie']) : null;

    // Consulta para buscar en la tabla t_placa
    $query = "SELECT codigo FROM t_placa WHERE placa = '$placa' OR serial = '$serie' LIMIT 1";
    $result = $link->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $codigo = $row['codigo'];

        // Ahora buscar en la tabla t_instituciones
        $institucion_query = "SELECT institucion FROM t_instituciones WHERE codigo = '$codigo' LIMIT 1";
        $institucion_result = $link->query($institucion_query);

        if ($institucion_result && $institucion_result->num_rows > 0) {
            $institucion_row = $institucion_result->fetch_assoc();
            echo json_encode([
                'codigo' => $codigo,
                'institucion' => $institucion_row['institucion'],
                'status' => 'success'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontró la institución.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se encontró la placa o serie.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Datos de búsqueda no válidos.']);
}

$link->close();
?>
