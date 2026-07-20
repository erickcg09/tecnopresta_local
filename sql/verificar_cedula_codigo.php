<?php
require_once __DIR__ . '/../conexion.php';
$link = $mysqli;

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['cedula']) || !isset($_GET['codigo'])) {
    echo json_encode(['existe' => false]);
    exit;
}

$cedula = trim($_GET['cedula']);
$codigo = trim($_GET['codigo']);

$result = mysqli_query($link, "SELECT ur.id FROM usuarios u
    INNER JOIN usuarios_roles ur ON u.id = ur.usuario_id
    WHERE u.cedula = '$cedula'
    AND ur.codigo_presu = '$codigo'
    AND ur.eliminado = 0
    LIMIT 1");

$existe = (mysqli_num_rows($result) > 0);

echo json_encode(['existe' => $existe]);
mysqli_close($link);
?>
