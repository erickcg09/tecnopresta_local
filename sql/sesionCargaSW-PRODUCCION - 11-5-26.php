<?php
//** CODIGO DE MAU */
/*
try {
	
	$json = array();
	$json = json_decode($_POST['data']);
	session_start();	
	$_SESSION['funcionario'] = $json;
	echo json_encode($_SESSION['funcionario']);
	exit;
    
} 
catch (PDOException $e) {		
	echo "Error al iniciar sesión: " . $e->getMessage() . "\n";
	exit;
}*/

/** NUEVO CÓDIGO ERICIK */
// Inicia la sesión de PHP
session_start();
/*if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
*/
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "error" => "Método no permitido",
        "method" => $_SERVER['REQUEST_METHOD']
    ]);
    exit();
}

// Validar que venga data
if (!isset($_POST['data'])) {
    echo json_encode([
        "error" => "No se recibieron datos",
        "post" => $_POST
    ]);
    exit;
}
// Convertir JSON a array
$json = json_decode($_POST['data'], true);

// Validar JSON
if (!$json) {
    echo json_encode([
        "error" => "JSON inválido",
        "raw" => $_POST['data']
    ]);
    exit;
}

// Guardar sesión
$_SESSION['funcionario'] = $json;


// Respuesta
echo json_encode([
    "ok" => true,
    "session" => $_SESSION
]);
?>
