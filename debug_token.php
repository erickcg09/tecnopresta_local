<?php
// debug_token.php - Verificar token en sesión
session_start();

header('Content-Type: application/json');

$response = [
    'session_id' => session_id(),
    'session_status' => session_status(),
    'token_present' => false,
    'token_info' => null,
    'session_data' => $_SESSION
];

if (isset($_SESSION['azure_token'])) {
    $response['token_present'] = true;
    
    // Información básica del token (sin decodificar JWT completo por seguridad)
    $token = $_SESSION['azure_token'];
    $tokenParts = explode('.', $token);
    
    if (count($tokenParts) === 3) {
        $payload = json_decode(base64_decode($tokenParts[1]), true);
        $response['token_info'] = [
            'issued_at' => isset($payload['iat']) ? date('Y-m-d H:i:s', $payload['iat']) : 'N/A',
            'expires_at' => isset($payload['exp']) ? date('Y-m-d H:i:s', $payload['exp']) : 'N/A',
            'token_length' => strlen($token),
            'has_expired' => isset($payload['exp']) ? (time() > $payload['exp']) : 'unknown'
        ];
    }
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>