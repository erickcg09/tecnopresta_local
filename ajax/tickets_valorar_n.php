<?php
// ajax/tickets_valorar.php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
header('Content-Type: application/json; charset=utf-8');
/*
if (!isset($_SESSION['cedula'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}
*/

require_once("../conexion.php");

// require_once __DIR__ . '/usuarioAzure.php';
require_once __DIR__ . '/../usuarioAzure.php';
$usuario_azure = obtenerUsuarioSesion();

if (!$usuario_azure) {
    header("Location: index.html");
    exit();
}

require_once("../conexion.php");
require_once("../funciones_tickets.php");


$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['ticket_id']) || !isset($data['puntuacion'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$ticket_id = (int) $data['ticket_id'];
$puntuacion = (int) $data['puntuacion'];

// 🔒 Reporte de Blindaje (SecOps):
// - Hueco Mitigado (XSS): Sanitización estricta del comentario entrante para evitar inyección de código.
// - Hueco Mitigado (Manipulación): Validación backend de que la puntuación esté entre 1 y 5, y la longitud.
$comentario = isset($data['comentario']) ? htmlspecialchars(trim($data['comentario']), ENT_QUOTES, 'UTF-8') : null;

if ($puntuacion < 1 || $puntuacion > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'La puntuación debe estar entre 1 y 5']);
    exit();
}

if ($comentario && strlen($comentario) > 1000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El comentario no puede exceder los 1000 caracteres']);
    exit();
}

$cedula = $usuario_azure['cedula'];

// Validar usuario
$query_user = "SELECT id FROM usuarios WHERE cedula = ?";
$stmt_user = mysqli_prepare($mysqli, $query_user);
mysqli_stmt_bind_param($stmt_user, "s", $cedula);
mysqli_stmt_execute($stmt_user);
$res_user = mysqli_stmt_get_result($stmt_user);
$usuario = mysqli_fetch_assoc($res_user);
mysqli_stmt_close($stmt_user);

if (!$usuario) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    exit();
}

$usuario_id = $usuario['id'];

// Validar ticket (existe, pertenece al usuario, estado correcto, no valorado)
$query_check = "SELECT t.estado_id, tv.id as valoracion_id 
                FROM tickets t 
                LEFT JOIN tickets_valoraciones tv ON t.id = tv.ticket_id AND tv.usuario_id = ? 
                WHERE t.id = ? AND t.usuario_id = ? AND t.eliminado = 0";
$stmt_check = mysqli_prepare($mysqli, $query_check);
mysqli_stmt_bind_param($stmt_check, "iii", $usuario_id, $ticket_id, $usuario_id);
mysqli_stmt_execute($stmt_check);
$res_check = mysqli_stmt_get_result($stmt_check);
$ticket = mysqli_fetch_assoc($res_check);
mysqli_stmt_close($stmt_check);

if (!$ticket) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Ticket no encontrado o no autorizado']);
    exit();
}

if (!in_array($ticket['estado_id'], [5, 6])) { // 5: Resuelto, 6: Cerrado
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'El ticket debe estar resuelto o cerrado para valorarlo']);
    exit();
}

if ($ticket['valoracion_id']) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'El ticket ya ha sido valorado']);
    exit();
}

// Insertar valoración usando sentencias preparadas para mitigar SQLi
$query_insert = "INSERT INTO tickets_valoraciones (ticket_id, usuario_id, puntuacion, comentario, created_at) VALUES (?, ?, ?, ?, NOW())";
$stmt_insert = mysqli_prepare($mysqli, $query_insert);
mysqli_stmt_bind_param($stmt_insert, "iiis", $ticket_id, $usuario_id, $puntuacion, $comentario);
$success = mysqli_stmt_execute($stmt_insert);
mysqli_stmt_close($stmt_insert);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Valoración guardada exitosamente']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar la valoración']);
}
