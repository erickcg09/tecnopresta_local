<?php

require_once 'test_insertSolicitud.php';

try {
	    
    $solicitud_codigo_presupuestario = "5300";
   
	$db = new insertSolicitud();
    $db-> insertSolicitud($solicitud_codigo_presupuestario);
    
    echo "OK";
} 
catch (Exception $e) {		
	console.log("Error de la aplicación: " + $e->getMessage());
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
	$db = null;
	exit;
}

?>