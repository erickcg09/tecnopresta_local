<?php

require_once 'conexion.php';
require 'email_Solicitud_Notifica_Prestador.php';

class insertSolicitud {

    private $pdo;
    private $last;
    	
	function __construct()
	{
        $pdo = new \PDO(DB_Str, DB_USER, DB_PASS);		
        $this->pdo = $pdo;        
        
    }
        
    public function insertSolicitud($solicitud_codigo_presupuestario){


        $emailPrestador = new Email_Solicitud_Notifica_Prestador();        
        		
        $envia_emailPrestador = $emailPrestador->email_Solicitud_Notifica_Prestador($solicitud_codigo_presupuestario);
    
        $envia_emailPrestador = null;
        
        return true; 
                          
    }
} 

?>