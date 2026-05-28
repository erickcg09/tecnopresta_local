<?php

try {
	
	$json = array();
	$json = json_decode($_POST['data']);
	session_start();	
	$_SESSION['funcionario'] = $json;
	
	exit;
    
} 
catch (PDOException $e) {		
	echo "Error al iniciar sesión: " . $e->getMessage() . "\n";
	exit;
}
?>
