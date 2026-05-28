<?php
// Conexión a la base de datos
require_once("conexion.php");

// Verificar la conexión
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
    exit();
}

// Establecer la codificación a utf8
if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

// Definir el nombre del archivo CSV
$filename = "t_activo_export_" . date('Y-m-d_H-i-s') . ".csv";

// Crear el encabezado del archivo CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Abrir el archivo para escritura
$output = fopen('php://output', 'w');

// Escribir los encabezados del archivo CSV (nombre de las columnas)
$headers = ['id_activo', 'id_ag', 'id_marca', 'modelo', 'id_color'];
fputcsv($output, $headers);

// Consultar los datos de la tabla t_activo
$query = "SELECT id_activo, id_ag, id_marca, modelo, id_color FROM t_activo";
$result = mysqli_query($link, $query);

// Verificar si la consulta fue exitosa
if ($result) {
    // Recorrer los resultados y escribir cada fila en el archivo CSV
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }
} else {
    echo "Error en la consulta: " . mysqli_error($link);
}

// Cerrar el archivo
fclose($output);

// Cerrar la conexión
mysqli_close($link);
?>
