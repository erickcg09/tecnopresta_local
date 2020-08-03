<?php

require_once 'insertPrestamo.php';
	
try {
		    
    $fechaRetiro = $_POST['prestamo_fechaRetiro'];
    $fechaDevolucion = $_POST['prestamo_fechaDevolucion'];
	$arrayArticulos = array();
    $arrayArticulos = json_decode($_POST['arrayArticulos']);
    
    $prestamo_fechaRetiroY = substr($fechaRetiro, -4); 
    $prestamo_fechaRetiroM = substr($fechaRetiro, 3, 2); 
    $prestamo_fechaRetiroD = substr($fechaRetiro, 0, 2);
    $prestamo_fechaRetiro = $prestamo_fechaRetiroY . "-" . $prestamo_fechaRetiroM . "-" . $prestamo_fechaRetiroD;
    
    $prestamo_fechaDevolucionY = substr($fechaDevolucion, -4); 
    $prestamo_fechaDevolucionM = substr($fechaDevolucion, 3, 2); 
    $prestamo_fechaDevolucionD = substr($fechaDevolucion, 0, 2);
    $prestamo_fechaDevolucion = $prestamo_fechaDevolucionY . "-" . $prestamo_fechaDevolucionM . "-" . $prestamo_fechaDevolucionD;

	$db = new insertPrestamo();
    $db-> insertPrestamo($prestamo_fechaRetiro, $prestamo_fechaDevolucion, $arrayArticulos);
    echo "ok";

} 
catch (Exception $e) {		
	console.log("Error de la aplicación: " + $e->getMessage());
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
	$db = null;
	exit;
}

?>