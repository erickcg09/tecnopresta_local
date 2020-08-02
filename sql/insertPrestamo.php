<?php

require_once 'conexion.php';

class insertPrestamo {

    private $pdo;
    private $last;
    	
	function __construct()
	{
        $pdo = new \PDO(DB_Str, DB_USER, DB_PASS);		
        $this->pdo = $pdo;        
        
    }
        
    public function insertPrestamo($prestamo_fechaRetiro, $prestamo_fechaDevolucion, $arrayArticulos){
                        		
        date_default_timezone_set('America/Costa_Rica');		
        $prestamo_Fecha = date_create('now')->format('Y-m-d H:i:s');

        $sql = 'INSERT INTO t_prestamo (prestamo_fecha, prestamo_fechaDevolucion, 
                            prestamo_fechaRetiro, seccion_Id, software_Id, prestamo_uso) 
                VALUES (:prestamo_fecha, :prestamo_fechaDevolucion, :prestamo_fechaRetiro, 
                        :seccion_Id, :software_Id, :prestamo_uso)';
                
        try {
		
		$stmt = $this->pdo->prepare($sql);				
        $this->pdo->beginTransaction(); 			
        $stmt->execute([
            ':prestamo_fecha' => $prestamo_Fecha,
            ':prestamo_fechaDevolucion' => $prestamo_fechaDevolucion,
            ':prestamo_fechaRetiro' => $prestamo_fechaRetiro,
            ':seccion_Id' => 1,
            ':software_Id' => 1,
            ':prestamo_uso' => "prueba."        
            ]);
        $last = $this->pdo->lastInsertId();        
        $this->pdo->commit();     
                                    
        $stmt = null;
        $this->pdo = null;
                
        return "ok"; 
        
    } catch (\Throwable $th) {
                echo "Error al enviar email: " . $th->getMessage() . "\n";				
            }    
              
    }
} 

?>