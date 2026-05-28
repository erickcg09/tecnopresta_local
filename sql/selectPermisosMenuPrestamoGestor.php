<?php

require_once("select.php");		

try {

    $cedula = $_GET['cedula'];
    $codigo = $_GET['codigo'];

	$db = new sql();		
	$rs = $db->obtienePermisosMenuPrestamo($cedula, $codigo);
	
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
