<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once("conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a MySQL: ' . mysqli_connect_error()]);
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo json_encode(['success' => false, 'error' => 'Error cargando el conjunto de caracteres utf8']);
    exit();
}

require_once __DIR__ . '/usuarioAzure.php';
$usuario_azure = obtenerUsuarioSesion();

if (!$usuario_azure) {
    echo json_encode(['success' => false, 'error' => 'Sesión no válida']);
    exit();
}

$logusuario = $usuario_azure['cedula'] ?? '';

$id_placa = intval($_POST['id_placa'] ?? 0);

if ($id_placa <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de placa inválido']);
    exit();
}

$update = "UPDATE t_placa SET marcado = 0 WHERE id_placa = '$id_placa'";
if ($link->query($update) === TRUE) {
    $delete = "DELETE FROM bitacora_eliminados WHERE id_placa = '$id_placa' AND usuario = '$logusuario'";
    $link->query($delete);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al revertir: ' . $link->error]);
}

mysqli_close($link);
?>
