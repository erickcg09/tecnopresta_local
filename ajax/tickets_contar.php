<?php
// ajax/tickets_contar.php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['cedula'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once("../conexion.php");

$cedula = $_SESSION['cedula'];

// Obtener ID usuario
$query_user = "SELECT id FROM usuarios WHERE cedula = ?";
$stmt_user = mysqli_prepare($mysqli, $query_user);
mysqli_stmt_bind_param($stmt_user, "s", $cedula);
mysqli_stmt_execute($stmt_user);
$res_user = mysqli_stmt_get_result($stmt_user);
$usuario = mysqli_fetch_assoc($res_user);
mysqli_stmt_close($stmt_user);

if (!$usuario) {
    echo json_encode(['total' => 0]);
    exit();
}

$usuario_id = $usuario['id'];

// Contar tickets activos: no eliminados, no valorados y que NO estén en Resuelto(5) ni Cerrado(6)
$query_count = "SELECT COUNT(*) as total 
                FROM tickets t 
                LEFT JOIN tickets_valoraciones tv ON t.id = tv.ticket_id AND tv.usuario_id = ? 
                WHERE t.usuario_id = ? 
                  AND t.eliminado = 0 
                  AND tv.id IS NULL 
                  AND t.estado_id NOT IN (5, 6)";

$stmt_count = mysqli_prepare($mysqli, $query_count);
mysqli_stmt_bind_param($stmt_count, "ii", $usuario_id, $usuario_id);
mysqli_stmt_execute($stmt_count);
$res_count = mysqli_stmt_get_result($stmt_count);
$row = mysqli_fetch_assoc($res_count);
mysqli_stmt_close($stmt_count);

echo json_encode(['total' => (int)$row['total']]);
