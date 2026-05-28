<?php
require_once("conexion.php");
$link = $mysqli;

$sql = "SELECT 
            t_instituciones.institucion, t_instituciones.codigo,
            SUM(CASE WHEN t_placa.enuso = 1 THEN 1 ELSE 0 END) AS total_en_uso,
            SUM(CASE WHEN t_placa.enuso = 0 THEN 1 ELSE 0 END) AS total_no_en_uso
        FROM 
            t_placa
        INNER JOIN 
            t_instituciones ON t_placa.codigo = t_instituciones.codigo
        WHERE 
            t_placa.id_fondos IN (2)
        GROUP BY 
            t_instituciones.institucion, t_instituciones.codigo";

$result = $link->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Código</th><th>Institución</th><th>Total en Uso</th><th>Total No en Uso</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['codigo'] . "</td>";
        echo "<td>" . $row['institucion'] . "</td>";
        echo "<td>" . $row['total_en_uso'] . "</td>";
        echo "<td>" . $row['total_no_en_uso'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No se encontraron resultados.";
}

$link->close();
?>

