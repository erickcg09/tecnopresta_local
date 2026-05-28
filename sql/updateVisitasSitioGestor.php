<?php

require_once 'updateVisitasSitio.php';
	
try {
		    
    $visitas_sitio = array();
    $visitas_sitio = json_decode($_POST['jsonDatos'], true);

	$db = new UpdateVisitasSitio();
    $db-> updateVisitasSitio($visitas_sitio);

} 
catch (Exception $e) {		
	console.log("Error de la aplicación: " + $e->getMessage());
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
	$db = null;
	exit;
}

?>