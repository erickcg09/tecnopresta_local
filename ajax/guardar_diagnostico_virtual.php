<?php
session_start();
require_once("../conexion.php");
$link = $mysqli; 
$input = json_decode(file_get_contents('php://input'), true);
$ticket_id = intval($input['ticket_id']);
$activos = $input['activos'];

// Verificar permisos (soporte virtual o coordinador)
// ...

foreach($activos as $activo) {
    $id = intval($activo['id']);
    $diagnostico = trim($activo['diagnostico']);
    $reparacion = trim($activo['reparacion']);
    $sql = "UPDATE tickets_activos SET diagnostico_tecnico = ?, reparacion_realizada = ?, updated_at = NOW() WHERE id = ? AND ticket_id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ssii", $diagnostico, $reparacion, $id, $ticket_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
echo json_encode(['success' => true]);
?>