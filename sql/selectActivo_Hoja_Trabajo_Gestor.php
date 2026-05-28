<?php

require_once("select.php");		

try {

	$valor = $_GET['valor'];
	$codigo = $_GET['codigo'];
	
	$db = new sql();		
	$rs = $db->con_articulo_en_hoja_de_trabajo($valor, $codigo);
	
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
