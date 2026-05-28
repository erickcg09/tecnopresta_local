<?php

require 'insert_visitas_sitio_hoja_trabajo.php';

try {

    $visitas_sitio_hoja_trabajo = array();
    $visitas_sitio_hoja_trabajo = json_decode($_POST['jsonDatos'], true);

    $classInsert = new Insert_visitas_sitio_hoja_trabajo(); 	
    $classInsert-> insert_visitas_sitio_hoja_trabajo($visitas_sitio_hoja_trabajo);
    
    echo "ok";

} 

catch (PDOException $e) 
{		
    echo json_encode(array("error" => $e->getMessage()));
    exit;
}

?>