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
            ':prestamo_uso' => "por definir..."        
            ]);

        $prestamo_Id = $this->pdo->lastInsertId();

        $this->pdo->commit();     
        
        if(!empty($arrayArticulos)) {
            $i=0;
            $this->pdo->beginTransaction();
            foreach($arrayArticulos as $key => $articulos) {
                $prestamo_detalle_id_activo = $articulos;
                $i=$i+1;
                $sql = 'INSERT INTO t_prestamo_detalle (prestamo_Id, prestamo_detalle_id_activo, 
                        prestamo_detalle_devuelto, prestamo_detalle_irregularidad, prestamo_detalle_observacion, 
                        prestamo_detalle_fechaDevolucion) 
                        VALUES (:prestamo_Id, :prestamo_detalle_id_activo, :prestamo_detalle_devuelto, 
                        :prestamo_detalle_irregularidad, :prestamo_detalle_observacion, :prestamo_detalle_fechaDevolucion)';
                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute([
                            ':prestamo_Id' => $prestamo_Id,
                            ':prestamo_detalle_id_activo' => $prestamo_detalle_id_activo,
                            ':prestamo_detalle_devuelto' => 0,
                            ':prestamo_detalle_irregularidad' => 0,
                            ':prestamo_detalle_observacion' => "registrado mediante solicitud",
                            ':prestamo_detalle_fechaDevolucion' => $prestamo_fechaDevolucion        
                            ]);
            }
            $this->pdo->commit(); 
        }
        $stmt = null;
        $this->pdo = null;
                
        return true; 
        
    } catch (\Throwable $th) {
                echo "Error al enviar email: " . $th->getMessage() . "\n";				
            }    
              
    }
} 

?>