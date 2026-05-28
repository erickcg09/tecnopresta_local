<?php

require_once 'conexion.php';
require_once 'email_test_img.php';

try {
		         
	$db = new Email_Test_Img();
    $db-> email_Test_Img();

    echo "Ok";
} 
catch (Exception $e) {		
	console.log("Error de la aplicación: " + $e->getMessage());
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
	$db = null;
	exit;
}

?>