<?php
// tokenHandler.php
session_start();
header('Content-Type: application/json');

// Configuración de seguridad
header("Access-Control-Allow-Origin: https://tecnopresta.mep.go.cr");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'store_token') {
        $access_token = $_POST['access_token'] ?? '';
        
        if (!empty($access_token)) {
            // Almacenar en sesión
            $_SESSION['graph_access_token'] = $access_token;
            $_SESSION['token_timestamp'] = time();
            
            // Opcional: almacenar en base de datos asociado a los usuarios que estan en la paltaforma de servicios 
            storeTokenInDatabase($access_token);
            
            echo json_encode(['success' => true, 'message' => 'Token almacenado']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Token vacío']);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'check_token') {
        $hasToken = isset($_SESSION['graph_access_token']) && 
                   !empty($_SESSION['graph_access_token']);
        
        echo json_encode(['hasToken' => $hasToken]);
    } elseif ($action === 'get_token') {
        // Endpoint seguro para obtener el token cuando se necesite
        if (isset($_SESSION['graph_access_token'])) {
            echo json_encode([
                'access_token' => $_SESSION['graph_access_token'],
                'expires_in' => 3600 - (time() - $_SESSION['token_timestamp'])
            ]);
        } else {
            echo json_encode(['error' => 'No token available']);
        }
    }
}

function storeTokenInDatabase($token) {
    // Aquí podriamos almacenar en base de datos
    // pero tendriamos que iniciar a crear los usurios
    // no es muy viable pero para dejarnos el token seria bueno si no cambia
    // pero como cada usuario se debe logear lo almacenamos en una variable de sesion
    // asi que esto no lo vamos a programar por el momento
}
?>