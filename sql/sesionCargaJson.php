<?php

try {

	session_start();

	if(isset($_SESSION['funcionario'])){
		
		echo json_encode($_SESSION['funcionario']);		
	}

    exit;
} 
catch (PDOException $e) {		
	echo "Error al iniciar sesión: " . $e->getMessage() . "\n";
	exit;
}
?>
