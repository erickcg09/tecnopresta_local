<?php

require 'deleteRol.php';
	
try {

	$id_lista_blanca = $_POST['id_lista_blanca'];
	
 	$db = new DeleteRol();
 	$db-> deleteRol($id_lista_blanca);
 	$db = null;

} 
catch (Exception $e) {		
	console.log("Error de la aplicación: " + $e->getMessage());
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
	$db = null;
	exit;
}

?>