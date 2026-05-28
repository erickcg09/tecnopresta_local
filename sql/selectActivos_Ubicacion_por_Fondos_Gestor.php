<?php

require_once("select.php");		

try {
	
    $id_fondos = $_GET['id_fondos'];
	$codigo = $_GET['codigo'];

	$db = new sql();		
	$rs = $db->conActivos_Ubicacion_por_Fondos($id_fondos, $codigo);
	
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
