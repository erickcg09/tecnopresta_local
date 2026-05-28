<?php
require_once("select.php");		
try {
	$institucion = $_GET['institucion'];
	$db = new sql();		
	$rs = $db->conInstitucion($institucion);
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
