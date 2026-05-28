<?php
// Conexión a la base de datos
require_once("conexion.php");

// Verificar la conexión
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
    exit();
}

// Establecer la codificación a utf8
if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

// Definir el nombre del archivo CSV
$filename = "t_placa_export_" . date('Y-m-d_H-i-s') . ".csv";

// Crear el encabezado del archivo CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Abrir el archivo para escritura
$output = fopen('php://output', 'w');

// Escribir los encabezados del archivo CSV (nombre de las columnas)
$headers = ['id_placa', 'placa', 'serial', 'id_activo', 'codigo', 'id_estado', 'prestar', 'activo', 'id_fondos', 'alias_id', 'numero_activo', 'revisado', 'id_lugar', 'enuso', 'donar', 'marcado'];
fputcsv($output, $headers);

// Consultar los datos de la tabla t_placa
$query = "SELECT id_placa, placa, serial, id_activo, codigo, id_estado, prestar, activo, id_fondos, alias_id, numero_activo, revisado, id_lugar, enuso, donar, marcado FROM t_placa";
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
