<?php

require_once 'insertPrestamo.php';
	
try {
		    
    $fechaRetiro = $_POST['prestamo_fechaRetiro'];
    $fechaDevolucion = $_POST['prestamo_fechaDevolucion'];
	$arrayArticulos = array();
    $arrayArticulos = json_decode($_POST['arrayArticulos']);
    
    $prestamo_fechaRetiro = substr($fechaRetiro, -4) . "-"; 
    $prestamo_fechaRetiro = $prestamo_fechaRetiro . substr($fechaRetiro, 3, 4) . "-"; 
    $prestamo_fechaRetiro = $prestamo_fechaRetiro . substr($fechaRetiro, 0, 1);
    $prestamo_fechaDevolucion = substr($fechaDevolucion, -4) . "-" . substr($fechaDevolucion, 3, 4) . "-" . substr($fechaDevolucion, 0, 1);
		 
	//$db = new insertPrestamo();
    //$db-> insertPrestamo($prestamo_fechaRetiro,  $prestamo_fechaDevolucion, $arrayArticulos);
    //echo $db;
    echo $prestamo_fechaRetiro;	
} 
catch (Exception $e) {		
	console.log("Error de la aplicación: " + $e->getMessage());
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
	$db = null;
	exit;
}

?>