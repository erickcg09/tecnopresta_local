<?php
// ajax/tickets_listar.php
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

// require_once __DIR__ . '/usuarioAzure.php';
require_once __DIR__ . '/../usuarioAzure.php';
$usuario_azure = obtenerUsuarioSesion();

if (!$usuario_azure) {
    header("Location: index.html");
    exit();
}

require_once("../conexion.php");
require_once("../funciones_tickets.php");

//$cedula = $_SESSION['cedula'];
$cedula = $usuario_azure['cedula'];

// Obtener ID de usuario logueado
$usuario_id = null;
$query_user = "SELECT id FROM usuarios WHERE cedula = ?";
$stmt_user = mysqli_prepare($mysqli, $query_user);
mysqli_stmt_bind_param($stmt_user, "s", $cedula);
mysqli_stmt_execute($stmt_user);
$res_user = mysqli_stmt_get_result($stmt_user);
if ($row = mysqli_fetch_assoc($res_user)) {
    $usuario_id = $row['id'];
}
mysqli_stmt_close($stmt_user);

if (!$usuario_id) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    exit();
}

//  Reporte de Blindaje (SecOps):
// - Hueco Mitigado (SQLi): Uso estricto de mysqli_prepare en lugar de concatenar ID.
// - Acceso: Restricci��n absoluta para solo retornar datos asociados a $usuario_id.

// Tickets Activos (NO resueltos/cerrados y NO valorados)
$query_activos = "SELECT
    t.id,
    t.codigo_tkt,
    t.asunto,
    t.descripcion,
    t.tipo,
    t.created_at AS fecha_creacion,
    t.updated_at AS ultima_actualizacion,
    te.id AS estado_id,
    te.estado AS estado_nombre
FROM tickets t
INNER JOIN tickets_estados te ON t.estado_id = te.id
LEFT JOIN tickets_valoraciones tv
    ON t.id = tv.ticket_id
    AND tv.usuario_id = ?
WHERE t.usuario_id = ?
  AND t.eliminado = 0
  AND tv.id IS NULL
  AND t.estado_id NOT IN (5,6)
ORDER BY t.updated_at DESC";

$stmt_activos = mysqli_prepare($mysqli, $query_activos);
mysqli_stmt_bind_param($stmt_activos, "ii", $usuario_id, $usuario_id);
mysqli_stmt_execute($stmt_activos);
$res_activos = mysqli_stmt_get_result($stmt_activos);

$tickets_activos = [];
while ($row = mysqli_fetch_assoc($res_activos)) {
    $row['tipo_nombre'] = $row['tipo'] == 1 ? 'Técnico' : 'Administrativo';
    
    // Asignaci��n de colores para dise�0�9o y UI (Direcci��n de Arte)
    $color_badge = '#0D6EFD'; // Azul por defecto
    switch($row['estado_id']) {
        case 1: $color_badge = '#0D6EFD'; break; // Abierto - Azul
        case 2: $color_badge = '#F57C00'; break; // En proceso - Naranja
        case 3: $color_badge = '#FFC107'; break; // Pendiente - Amarillo
        case 4: $color_badge = '#6C757D'; break; // En espera - Gris
        case 7: $color_badge = '#6F42C1'; break; // Virtual - Púrpura
        case 8: $color_badge = '#DC3545'; break; // Sitio - Rojo
    }
    $row['estado_color'] = $color_badge;
    
    // Compatibilidad con frontend
    $row['historial_estados'] = [];
    $tickets_activos[] = $row;
}
mysqli_stmt_close($stmt_activos);

// Tickets Para Valorar (Resueltos/Cerrados y NO valorados)
$query_valorar = "SELECT 
    t.id, t.codigo_tkt, t.asunto, t.tipo, 
    te.estado AS estado_nombre, t.fecha_cierre, t.updated_at
FROM tickets t
INNER JOIN tickets_estados te ON t.estado_id = te.id
LEFT JOIN tickets_valoraciones tv ON t.id = tv.ticket_id AND tv.usuario_id = ?
WHERE t.usuario_id = ? 
  AND t.eliminado = 0 
  AND tv.id IS NULL 
  AND t.estado_id IN (5, 6)
ORDER BY t.updated_at DESC";

$stmt_valorar = mysqli_prepare($mysqli, $query_valorar);
mysqli_stmt_bind_param($stmt_valorar, "ii", $usuario_id, $usuario_id);
mysqli_stmt_execute($stmt_valorar);
$res_valorar = mysqli_stmt_get_result($stmt_valorar);

$tickets_valorar = [];
while ($row = mysqli_fetch_assoc($res_valorar)) {
    $tickets_valorar[] = $row;
}
mysqli_stmt_close($stmt_valorar);

echo json_encode([
    'success' => true,
    'tickets' => $tickets_activos,
    'para_valorar' => $tickets_valorar
]);
