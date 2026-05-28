<?php

error_reporting(-1);  //Remove from production version
ini_set("display_errors", "on");  //Remove from production version

try {

    session_start();
    
    var_dump($_SESSION['funcionario']);    

} catch (\Throwable $th) {
    echo "Error al conectar con la base de datos: " . $th->getMessage() . "\n";
	exit;		
}

?>