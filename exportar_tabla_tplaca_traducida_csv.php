<?php
// Conexión a la base de datos
require_once("conexion.php");
$link = $mysqli;

// Configurar la conexión para UTF-8
if (!$link->set_charset("utf8")) {
    echo "Error cargando el conjunto de caracteres utf8: " . $link->error;
    exit;
}

// Consulta SQL con INNER JOIN y traducciones
$sql = "
    SELECT 
        t_placa.id_placa,
        t_placa.placa,
        t_placa.serial,
        t_activo.modelo,
        t_activo_general.clase,
        t_placa.codigo,
        CASE 
            t_placa.id_estado
            WHEN 1 THEN 'Muy bueno'
            WHEN 2 THEN 'Bueno'
            WHEN 3 THEN 'Regular'
            WHEN 4 THEN 'Malo'
            WHEN 5 THEN 'Hurtado'
            ELSE 'Desconocido'
        END AS estado,
        CASE 
            t_placa.id_fondos
            WHEN 1 THEN 'MEP'
            WHEN 2 THEN 'FONATEL PROGRAMA 3 SUTEL'
            WHEN 3 THEN 'JUNTA ADMINISTRATIVA (RECURSOS PROPIOS)'
            WHEN 4 THEN 'PNTM TECNOAPRENDER'
            WHEN 5 THEN 'BEYCRA'
            WHEN 6 THEN 'JUNTA DE EDUCACIÓN (RECURSOS PROPIOS)'
            WHEN 7 THEN 'PRONIE-MEP-FOD'
            WHEN 8 THEN 'DONACIONES DE OTROS'
            WHEN 9 THEN 'LEY 7372'
            ELSE 'Desconocido'
        END AS fondos,
        CASE 
            t_placa.id_lugar
            WHEN 1 THEN 'Bodega'
            WHEN 2 THEN 'Laboratorio'
            WHEN 3 THEN 'Sala de robótica'
            WHEN 4 THEN 'Aulas'
            WHEN 5 THEN 'Biblioteca'
            WHEN 6 THEN 'Oficinas Administrativas'
            ELSE 'Desconocido'
        END AS lugar,
        CASE 
            t_placa.enuso
            WHEN 1 THEN 'Sí'
            WHEN 0 THEN 'No'
            ELSE 'Desconocido'
        END AS enuso,
        CASE 
            t_placa.donar
            WHEN 1 THEN 'Sí'
            WHEN 0 THEN 'No'
            ELSE 'Desconocido'
        END AS donar
    FROM 
        t_placa
    INNER JOIN t_activo ON t_placa.id_activo = t_activo.id_activo
    INNER JOIN t_activo_general ON t_activo.id_ag = t_activo_general.id_ag
";

// Ejecutar consulta
$result = $link->query($sql);

if (!$result) {
    echo "Error en la consulta: " . $link->error;
    exit;
}

// Nombre del archivo CSV
$filename = "exportacion_placas.csv";

// Encabezados para la descarga
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Crear archivo CSV
$output = fopen('php://output', 'w');

// Escribir encabezados del CSV
fputcsv($output, [
    'ID Placa', 
    'Placa', 
    'Serial', 
    'Modelo', 
    'Clase', 
    'Código', 
    'Estado', 
    'Fondos', 
    'Lugar', 
    'En uso', 
    'Donar'
]);

// Escribir los datos al CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id_placa'],
        $row['placa'],
        $row['serial'],
        $row['modelo'],
        $row['clase'],
        $row['codigo'],
        $row['estado'],
        $row['fondos'],
        $row['lugar'],
        $row['enuso'],
        $row['donar']
    ]);
}

// Cerrar archivo CSV
fclose($output);
exit;
?>
