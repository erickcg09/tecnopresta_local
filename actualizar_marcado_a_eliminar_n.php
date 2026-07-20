<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once("conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    $msg = "Error de conexión a MySQL: " . mysqli_connect_error();
    echo json_encode(['success' => false, 'error' => $msg]); exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    $msg = "Error cargando el conjunto de caracteres utf8";
    echo json_encode(['success' => false, 'error' => $msg]); exit();
}

require_once __DIR__ . '/usuarioAzure.php';
$usuario_azure = obtenerUsuarioSesion();

if (!$usuario_azure) {
    echo json_encode(['success' => false, 'error' => 'Sesión no válida']); exit();
}

$logusuario = $usuario_azure['cedula'] ?? '';
$motivo = isset($_POST['motivo']) ? $_POST['motivo'] : '';

$params_ruta = '';
$sid = intval($_POST['subsistema_id'] ?? 0);
$mid = intval($_POST['modulo_id'] ?? 0);
if ($sid && $mid) {
    $params_ruta = '&subsistema_id=' . $sid . '&modulo_id=' . $mid;
}

if (empty($_POST['idsplacas'])) {
    echo json_encode(['success' => false, 'error' => 'No hay ningún activo seleccionado']);
    exit();
}

$errores = [];
foreach ($_POST['idsplacas'] as $idplaca) {
    $update = "UPDATE t_placa SET marcado = 1 WHERE id_placa = '$idplaca'";

    if ($link->query($update) === TRUE) {
        $query = "SELECT placa, serial, codigo, id_activo FROM t_placa WHERE id_placa = '$idplaca'";
        $result = $link->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $insert = "INSERT INTO bitacora_eliminados (id_placa, placa, serial, codigo, id_activo, usuario, motivo)
                       VALUES ('$idplaca', '{$row['placa']}', '{$row['serial']}', '{$row['codigo']}', '{$row['id_activo']}', '$logusuario', '$motivo')";
            if ($link->query($insert) !== TRUE) {
                $errores[] = "Error en bitácora para id_placa $idplaca: " . $link->error;
            }
        } else {
            $errores[] = "Error al obtener detalles para id_placa $idplaca";
        }
    } else {
        $errores[] = "Error al actualizar id_placa $idplaca: " . $link->error;
    }
}

mysqli_close($link);

if (empty($errores)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => true, 'warnings' => $errores]);
}
?>