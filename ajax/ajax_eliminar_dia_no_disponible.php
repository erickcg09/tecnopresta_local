<?php
// ajax/ajax_eliminar_dia_no_disponible.php
session_start();

require_once("../conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit;
}

date_default_timezone_set('America/Costa_Rica');

if(!isset($_SESSION['cedula'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id_dnod = $input['id_dnod'] ?? 0;

if(!$id_dnod) {
    echo json_encode(['success' => false, 'message' => 'ID no válido']);
    exit;
}

// Verificar que el día pertenezca al soportista y no esté aprobado
$sql_check = "SELECT d.id_dnod, d.aprobado, s.correo 
              FROM dias_no_disponibles d
              JOIN soportistas s ON d.id_soportista = s.id_soportista
              WHERE d.id_dnod = ? AND (s.correo = ? OR s.cedula = ?)";
$stmt_check = mysqli_prepare($link, $sql_check);
mysqli_stmt_bind_param($stmt_check, "iss", $id_dnod, $_SESSION['correomep'], $_SESSION['cedula']);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$dia = mysqli_fetch_assoc($result_check);
mysqli_stmt_close($stmt_check);

if(!$dia) {
    echo json_encode(['success' => false, 'message' => 'No tiene permiso para eliminar este registro']);
    exit;
}

if($dia['aprobado'] == 1) {
    echo json_encode(['success' => false, 'message' => 'No se puede eliminar un día ya aprobado']);
    exit;
}

// Eliminar
$sql_delete = "DELETE FROM dias_no_disponibles WHERE id_dnod = ?";
$stmt_delete = mysqli_prepare($link, $sql_delete);
mysqli_stmt_bind_param($stmt_delete, "i", $id_dnod);

if(mysqli_stmt_execute($stmt_delete)) {
    echo json_encode(['success' => true, 'message' => 'Día eliminado correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el día']);
}
mysqli_stmt_close($stmt_delete);
?>