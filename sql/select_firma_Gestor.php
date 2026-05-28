<?php

require_once("select.php");		

try {
 
    $visitas_sitio_hoja_trabajo_id = $_GET['visitas_sitio_hoja_trabajo_id'];

	$db = new sql();		
	
    $rs = $db->confirma($visitas_sitio_hoja_trabajo_id);
	
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
