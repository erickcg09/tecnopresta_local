<?php

require 'conexion.php';
require 'email_Devolucion.php';

class insertDevolucion {

    private $pdo;
    	
	function __construct()
	{
        $pdo = new \PDO(DB_Str, DB_USER, DB_PASS);		
        $this->pdo = $pdo;        
        
    }
        
    public function insertDevolucion($arrayArticulos, $prestamo_Id, 
                                        $prestamo_email_solicitante,
                                        $prestamo_nombre_solicitante,
                                        $arrayArticulosNombre){
        
        try {

            if(!empty($arrayArticulos)) {

                $this->pdo->beginTransaction();
                
                foreach($arrayArticulos as $key => $articulos) {
                
                    $prestamo_detalle_id_placa = $articulos;
                
                    $sql = "UPDATE t_prestamo_detalle SET prestamo_detalle_devuelto = :prestamo_detalle_devuelto 
                            WHERE prestamo_Id = :prestamo_Id AND prestamo_detalle_id_placa = :prestamo_detalle_id_placa";
                    
                    $stmt = $this->pdo->prepare($sql);
                    
                    $stmt->execute([':prestamo_Id' => $prestamo_Id, 
                                    ':prestamo_detalle_id_placa' => $prestamo_detalle_id_placa,
                                    ':prestamo_detalle_devuelto' => 1
                                ]);
                }

                $this->pdo->commit(); 
            }

            $stmt = null;
            $this->pdo = null;

            $email = new Email_Devolucion();		
            $evia_email = $email->email_Devolucion($prestamo_email_solicitante, 
                                                    $prestamo_nombre_solicitante, 
                                                        $arrayArticulosNombre);
        
            $evia_email = null;

            return true;    
        
    } catch (\Throwable $th) {
                echo "Error al enviar email: " . $th->getMessage() . "\n";				
            }    
              
    }
} 

?>