<?php
// guardar_token.php - CREAR SESIÓN PREMATURA PARA EL TOKEN

// Iniciar sesión (si no existe, se crea una nueva)
session_start();

header('Content-Type: application/json');

// Leer el token del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['access_token'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Falta access_token']);
    exit;
}

// Guardar el token en la sesión (aunque sea prematura)
$_SESSION['azure_token'] = $input['access_token'];
$_SESSION['azure_token_created'] = time();

// Opcional: marcar que esta sesión se creó para el token temprano
$_SESSION['session_type'] = 'prelogin_azure_token';

// Responder éxito
echo json_encode([
    'status' => 'ok',
    'message' => 'Token de Azure guardado en sesión prematura',
    'session_id' => session_id(), // Para debug
    'storage' => 'session'
]);

// No es necesario llamar session_write_close() explícitamente, 
// PHP lo maneja automáticamente al final del script
?>
