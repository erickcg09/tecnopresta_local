<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/usuarioAzure.php';
$usuario_azure = obtenerUsuarioSesion();

if (!$usuario_azure) {
    echo json_encode(['success' => false, 'error' => 'Sesión no válida']);
    exit();
}

require_once("conexion.php");
$link = $mysqli;

if ($link->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a MySQL: ' . $link->connect_error]);
    exit();
}
if (!$link->set_charset("utf8")) {
    echo json_encode(['success' => false, 'error' => 'Error cargando el conjunto de caracteres utf8']);
    exit();
}

if (empty($_POST['idsplacas'])) {
    echo json_encode(['success' => false, 'error' => 'No hay ningún activo seleccionado']);
    exit();
}

if (empty($_POST['modelos_select'])) {
    echo json_encode(['success' => false, 'error' => 'No se seleccionó un modelo']);
    exit();
}

$modelo = intval($_POST['modelos_select']);

if ($modelo <= 0) {
    echo json_encode(['success' => false, 'error' => 'El modelo seleccionado no es válido']);
    exit();
}

$query = "UPDATE t_placa SET id_activo = ? WHERE id_placa = ?";
$stmt = $link->prepare($query);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error preparando la consulta: ' . $link->error]);
    exit();
}

$errores = [];
foreach ($_POST['idsplacas'] as $idplaca) {
    if (!empty($modelo) && !empty($idplaca)) {
        $stmt->bind_param('ii', $modelo, $idplaca);
        if (!$stmt->execute()) {
            $errores[] = "Error al actualizar el activo con id de placa $idplaca";
        }
    }
}

$stmt->close();
$link->close();

if (empty($errores)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => true, 'warnings' => $errores]);
}
?>
