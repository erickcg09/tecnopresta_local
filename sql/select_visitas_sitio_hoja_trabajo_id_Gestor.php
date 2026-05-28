<?php

/* require_once("select.php");		

try {
 
    $id_visita = $_GET['id_visita'];

	$db = new sql();		
	
    $rs = $db->convisitas_sitio_hoja_trabajo_id($id_visita);
	
	echo json_encode($rs);
   
    $rs = null;
    $db = null;
 
} 
catch (PDOException $e) {		
	$rs = null;
	$db = null;
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
	exit;
} */

require_once("select.php");
try {
    // Declarar el header ANTES de cualquier salida
    header("Content-Type: application/json; charset=utf-8");

    // Sanitizar el parámetro recibido
    $id_visita = isset($_GET['id_visita']) ? intval($_GET['id_visita']) : 0;

    $db = new sql();

    $rs = $db->convisitas_sitio_hoja_trabajo_id($id_visita);

    // Codificar en JSON sin escapar caracteres Unicode
    echo json_encode($rs, JSON_UNESCAPED_UNICODE);

    // Liberar recursos
    $rs = null;
    $db = null;

} catch (PDOException $e) {
    $rs = null;
    $db = null;

    // En caso de error, también enviar JSON
    http_response_code(500);
    echo json_encode([
        "error" => "Error al conectar con la base de datos",
        "detalle" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

?>
