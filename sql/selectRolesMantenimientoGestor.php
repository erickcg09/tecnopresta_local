<?php

require_once("select.php");		

try {
	
    $id_lista_blanca = $_GET['id_lista_blanca'];

	$db = new sql();		
	$rs = $db->conRol_x_id_lista_blanca($id_lista_blanca);
	
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
