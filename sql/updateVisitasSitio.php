<?php

require 'conexion.php';

class UpdateVisitasSitio {

    private $pdo;
       	
	function __construct()
	{
        $pdo = new \PDO(DB_Str, DB_USER, DB_PASS);		
        $this->pdo = $pdo;        
        
    }
        
    public function updateVisitasSitio($visitas_sitio){
                
        try {
		
            if(!empty($visitas_sitio)){

                $sql = 'UPDATE visitas_sitio SET
                        telefono=:telefono,
                        correo_institucional=:correo_institucional,
                        direccion=:direccion,
                        persona_contacto=:persona_contacto
                        WHERE id_visita = :id_visita';

                $this->pdo->beginTransaction();                            
                $stmt = $this->pdo->prepare($sql);

                $stmt->execute([
                    ':id_visita' => $visitas_sitio["id_visita"],
                    ':telefono' => $visitas_sitio["telefono"],
                    ':correo_institucional' => $visitas_sitio["correo_institucional"],
                    ':direccion' => $visitas_sitio["direccion"],
                    ':persona_contacto' => $visitas_sitio["persona_contacto"]
                    ]);

            }

            $this->pdo->commit(); 

            $stmt = null;
            $this->pdo = null;          

            return true; 
        
    } catch (\Throwable $th) {
                echo "Error al actualizar solicitud: " . $th->getMessage() . "\n";				
            }    
              
    }
} 

?>