<?php
require_once("conexion.php");
$link = $mysqli;

// Verificar conexión
if (mysqli_connect_errno()) {
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
    exit();
}

// Establecer charset
if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres UTF-8";
    exit();
}

// Consulta SQL parametrizada
$query = "
    SELECT 
        i.institucion,
        p.placa,
        p.serial,
        l.lugar,
        CASE WHEN p.enuso = 1 THEN 'Sí' ELSE 'No' END AS en_uso
    FROM 
        t_placa p
    INNER JOIN 
        instituciones i ON p.codigo = i.codigo
    INNER JOIN 
        t_lugar l ON p.id_lugar = l.id_lugar
    WHERE 
        p.id_fondos = 11
    ORDER BY 
        i.institucion, p.placa
";

$result = mysqli_query($link, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($link));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Placas por Institución</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Reporte de Placas (Fondos 11)</h1>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Institución</th>
                        <th>Placa</th>
                        <th>Serial</th>
                        <th>Lugar</th>
                        <th>En Uso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $current_institution = "";
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Mostrar el nombre de la institución solo cuando cambie
                        if ($current_institution != $row['institucion']) {
                            $current_institution = $row['institucion'];
                            echo "<tr class='table-primary'>";
                            echo "<td colspan='5'><strong>{$current_institution}</strong></td>";
                            echo "</tr>";
                        }
                        
                        echo "<tr>";
                        echo "<td></td>"; // Celda vacía para alinear con las demás filas
                        echo "<td>{$row['placa']}</td>";
                        echo "<td>{$row['serial']}</td>";
                        echo "<td>{$row['lugar']}</td>";
                        echo "<td>{$row['en_uso']}</td>";
                        echo "</tr>";
                    }
                    
                    if (mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='5' class='text-center'>No se encontraron resultados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Liberar resultados y cerrar conexión
mysqli_free_result($result);
mysqli_close($link);
?>