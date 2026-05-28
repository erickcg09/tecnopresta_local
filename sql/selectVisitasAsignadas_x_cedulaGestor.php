<?php

require_once("select.php");		

try {

    $cedula = $_GET['cedula'];
	$estado = $_GET['estado'];

	$db = new sql();		
	$rs = $db->conVisitasAsignadas_x_cedula($cedula, $estado);
	
	echo json_encode($rs);
   
    $rs = null;
    $db = null;
 
} 
catch (PDOException $e) {		
	$rs = null;
	$db = null;
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
	exit;
}
?>
