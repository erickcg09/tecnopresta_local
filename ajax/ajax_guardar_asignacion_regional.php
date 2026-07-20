<?php
// ajax/ajax_guardar_asignacion_regional.php
session_start();

require_once("../conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit;
}

date_default_timezone_set('America/Costa_Rica');

// Verificar sesión
if(!isset($_SESSION['cedula'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada']);
    exit;
}

// Recibir datos JSON
$input = json_decode(file_get_contents('php://input'), true);
$id_soportista = $input['id_soportista'] ?? 0;
$id_regional = $input['id_regional'] ?? 0;
$atiende = $input['atiende'] ?? 0;

// Validar datos
if(!$id_soportista || !$id_regional) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Verificar que el usuario sea coordinador
$logcorreo = $_SESSION['correomep'];
$logusuario = $_SESSION['cedula'];
$es_coordinador = false;

$sql = "SELECT s.id_soportista 
        FROM soportistas s
        INNER JOIN soportistas_clasificaciones sc ON s.id_soportista = sc.id_soportista
        WHERE (s.correo = ? OR s.cedula = ?) AND sc.grupo = 4 AND sc.activo = 1
        LIMIT 1";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "ss", $logcorreo, $logusuario);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if(mysqli_stmt_num_rows($stmt) === 0) {
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => false, 'message' => 'No tiene permisos de coordinador']);
    exit;
}
mysqli_stmt_close($stmt);

$fecha_actual = date('Y-m-d H:i:s');

// Verificar si ya existe la asignación
$sql_check = "SELECT id_spr FROM soportistas_por_regionales 
              WHERE id_soportista = ? AND id_regional = ?";
$stmt_check = mysqli_prepare($link, $sql_check);
mysqli_stmt_bind_param($stmt_check, "ii", $id_soportista, $id_regional);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_store_result($stmt_check);

if(mysqli_stmt_num_rows($stmt_check) > 0) {
    mysqli_stmt_close($stmt_check);
    // Actualizar
    $sql_update = "UPDATE soportistas_por_regionales 
                   SET atiende = ?, updated_at = ? 
                   WHERE id_soportista = ? AND id_regional = ?";
    $stmt_update = mysqli_prepare($link, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "isii", $atiende, $fecha_actual, $id_soportista, $id_regional);
    
    if(mysqli_stmt_execute($stmt_update)) {
        echo json_encode(['success' => true, 'message' => 'Asignación actualizada']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
    }
    mysqli_stmt_close($stmt_update);
} else {
    mysqli_stmt_close($stmt_check);
    // Insertar nueva
    $sql_insert = "INSERT INTO soportistas_por_regionales (id_soportista, id_regional, atiende, created_at, updated_at) 
                   VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($link, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "iiiss", $id_soportista, $id_regional, $atiende, $fecha_actual, $fecha_actual);
    
    if(mysqli_stmt_execute($stmt_insert)) {
        echo json_encode(['success' => true, 'message' => 'Asignación guardada']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar']);
    }
    mysqli_stmt_close($stmt_insert);
}

mysqli_close($link);
?>