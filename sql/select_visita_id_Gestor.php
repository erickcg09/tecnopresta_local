<?php

require_once("select.php");		

try {
 
    $id_visita = $_GET['id_visita'];

	$db = new sql();		
	
    $rs = $db->conVisita_id($id_visita);
	
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
