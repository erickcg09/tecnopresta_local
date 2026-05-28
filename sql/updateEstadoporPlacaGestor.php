<?php

require_once 'updateEstadoporPlaca.php';
	
try {
		    
    $arrayArticulos = array();
    $arrayArticulos = json_decode($_POST['arrayArticulos']);

    $arrayEstado = array();
    $arrayEstado = json_decode($_POST['arrayEstado']);

    $arrayEnUso = array();
    $arrayEnUso = json_decode($_POST['arrayEnUso']);

    $arrayDonacion = array();
    $arrayDonacion = json_decode($_POST['arrayDonacion']);

	$db = new UpdateEstadoporPlaca();
    $db->updateEstadoporPlaca($arrayArticulos,$arrayEstado, $arrayEnUso, $arrayDonacion);

    echo "ok";

} 
catch (Exception $e) {		
	console.log("Error de la aplicación: " + $e->getMessage());
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
	$db = null;
	exit;
}

?>