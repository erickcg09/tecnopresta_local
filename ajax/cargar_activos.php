<?php
session_start();
header('Content-Type: application/json');
// Headers para evitar caché y especulación
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('X-Content-Type-Options: nosniff');
// Obtener código por GET o de sesión
$codigoCentro = $_GET['codigo'] ?? $_SESSION['codigo'] ?? '';

// Si no hay código, devolver error
if (empty($codigoCentro)) {
    echo json_encode([
        'success' => false,
        'error' => 'No se pudo determinar el código del centro',
        'codigo_get' => $_GET['codigo'] ?? 'no',
        'codigo_session' => $_SESSION['codigo'] ?? 'no'
    ]);
    exit();
}

require_once("funciones_tickets.php");

// Verificar conexión a BD
global $mysqli;
if (!$mysqli || mysqli_connect_errno()) {
    echo json_encode([
        'success' => false,
        'error' => 'Error de conexión a la base de datos'
    ]);
    exit();
}

// Consultar activos
$query = "SELECT 
            p.id_placa,
            p.placa,
            p.serial,
            a.modelo,
            ag.clase AS tipo_activo,
            m.marca AS nombre_marca
          FROM t_placa p
          JOIN t_activo a ON p.id_activo = a.id_activo
          JOIN t_activo_general ag ON a.id_ag = ag.id_ag
          LEFT JOIN t_marca m ON a.id_marca = m.id_marca
          WHERE p.codigo = ?
            AND p.activo = 1
          ORDER BY ag.clase, a.modelo, p.placa";

$stmt = mysqli_prepare($mysqli, $query);
mysqli_stmt_bind_param($stmt, "s", $codigoCentro);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$activos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $activos[] = $row;
}

mysqli_stmt_close($stmt);

echo json_encode([
    'success' => true,
    'codigo_centro' => $codigoCentro,
    'total' => count($activos),
    'activos' => $activos
]);
?>