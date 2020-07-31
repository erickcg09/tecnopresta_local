<?php

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
}
?>
