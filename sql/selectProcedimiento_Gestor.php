<?php

require_once("select.php");		

try {

	$valor = $_GET['valor'];
	
	$db = new sql();		
	$rs = $db->con_Procedimiento($valor);
	
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
