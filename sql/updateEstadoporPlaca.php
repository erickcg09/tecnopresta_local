<?php

require_once 'conexion.php';

class UpdateEstadoporPlaca {

    private $pdo;
    private $last;
    	
	function __construct()
	{
        $pdo = new \PDO(DB_Str, DB_USER, DB_PASS);		
        $this->pdo = $pdo;        
        
    }
        
    public function updateEstadoporPlaca($arrayArticulos,  
                                        $arrayEstado,$arrayEnUso,$arrayDonacion){
        
        if(!empty($arrayArticulos)) {

            $this->pdo->beginTransaction();
                     
            $i=0;
            foreach ($arrayArticulos as $key1 => $id_placa) {
                        
                    $sql = 'UPDATE t_placa 
                            SET id_estado = :id_estado, enuso = :enuso, donar = :donar
                            WHERE id_placa = :id_placa';
                            $stmt = $this->pdo->prepare($sql);              
                            $stmt->execute([':id_placa' => $id_placa, 
                                            ':donar' => $arrayDonacion[$i],
                                            ':enuso' => $arrayEnUso[$i], 
                                            ':id_estado' => $arrayEstado[$i]]);
                    
                    $i= $i+1;
                                
            }

            $this->pdo->commit();  
          
        }
        
        return true;
              
    }

}

?>