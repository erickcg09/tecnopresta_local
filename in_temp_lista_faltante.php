<?php
// Incluir el archivo de conexión
require_once("conexion.php");

// Verificar la conexión
$link = $mysqli;

if (mysqli_connect_errno()) {
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
    exit();
}

// Establecer el conjunto de caracteres UTF-8
if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

// Consulta para obtener los códigos e instituciones
$query = "
    SELECT codigo, institucion
    FROM t_instituciones
    WHERE codigo NOT IN (SELECT codigo FROM temp_codigos);
";

// Ejecutar la consulta
$result = mysqli_query($link, $query);

// Verificar si hay resultados
if ($result) {
    // Mostrar los resultados
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>Código: " . htmlspecialchars($row['codigo']) . " - Institución: " . htmlspecialchars($row['institucion']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "Error en la consulta: " . mysqli_error($link);
}

// Cerrar la conexión
mysqli_close($link);
?>
