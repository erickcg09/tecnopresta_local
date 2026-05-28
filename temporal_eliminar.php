<?php
require_once("conexion.php");

// Configurar conexión
$link = $mysqli;
if (mysqli_connect_errno()) {
    die("Error de conexión a MySQL: " . mysqli_connect_error());
}

if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres utf8");
}

// Array de IDs a eliminar (ejemplo)
$ids_a_eliminar = [1023,27937,28124,32892,32893,32895,32897,32901,32902,32903,32904,32906,32907,32908,32909,32912,32913,32914,32918,32919,32920,32923,32924,32926,32927,32932,32933,32934,32939,32945,32946,32947,32948,32949,32950,32951,33387,33388,33389,33390,33461,33506,33508,33552,33641,33721,33722,33728,33729,33730,33731,33732,33804,33859,33991,33992,34168,34322,34752,34754,34755,34757,34758,34759,34906,35450,35472,35486,35487,35532,35688,35703,35759,35867,36038,36727,37057,37317,37651,38015,38086,38089,38091,38094,38097,38099,38100,38104,38106,38108,38110,38114,38233,38294,38661,38713,38889,38979,39084,39087,39091,39287,39580,39591,39593,39604,39605,39613,39614,39618,39621,39622,39635,39642,39650,39688,39695,39702,40435,40448,40496,40513,40568,40778,40780,40810,40846,40848,41073,41239,41243,41245,41248,41249,41269,41270,41271,41275,41287,41288,41289,41290,41291,41292,41293,41350,41515,41550,41557,41666,41816,41981,42018,42020,42021,42022,42039,42144,42164,42165,42167,42168,42169,42170,42171,42172,42173,42174,42175,42176,42177,42178,42179,42180,42181,42182,42183,42647,42710]; // 

// ---------------------------------------------------------------
// PARTE 1: Eliminación segura con parámetros preparados
// ---------------------------------------------------------------

// Crear cadena de placeholders (?,?,?...)
$placeholders = implode(',', array_fill(0, count($ids_a_eliminar), '?'));
$types = str_repeat('i', count($ids_a_eliminar)); // 'i' para integers

// Consulta preparada
$query = "DELETE FROM t_activo WHERE id_activo IN ($placeholders)";

$stmt = mysqli_prepare($link, $query);
if (!$stmt) {
    die("Error preparando consulta: " . mysqli_error($link));
}

// Vincular parámetros
$params = array_merge([$stmt, $types], $ids_a_eliminar);
call_user_func_array('mysqli_stmt_bind_param', $params);

// Ejecutar eliminación
$resultado = mysqli_stmt_execute($stmt);

// ---------------------------------------------------------------
// PARTE 2: Mostrar resultados
// ---------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminación de registros</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: #2ecc71; font-weight: bold; }
        .error { color: #e74c3c; font-weight: bold; }
        .info { background-color: #f8f9fa; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Resultado de eliminación</h1>
    
    <div class="info">
        <p><strong>IDs a eliminar:</strong> <?= implode(', ', $ids_a_eliminar) ?></p>
    </div>

    <?php if ($resultado): ?>
        <p class="success">✓ Registros eliminados correctamente.</p>
        <p>Filas afectadas: <?= mysqli_stmt_affected_rows($stmt) ?></p>
    <?php else: ?>
        <p class="error">✗ Error al eliminar registros: <?= mysqli_error($link) ?></p>
    <?php endif; ?>

    <p><a href="#" onclick="window.history.back();">← Volver</a></p>
</body>
</html>
<?php
// Cerrar conexión
mysqli_stmt_close($stmt);
mysqli_close($link);
?>