<?php

require 'conexion.php';

class DeleteRol
{
	private $pdo;
	
	function __construct()
	{
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS);
		$this->pdo = $pdo;
	}

	public function deleteRol($id_lista_blanca){
						
		$sql = 'DELETE FROM t_lista_blanca WHERE id_lista_blanca = :id_lista_blanca';
		 				
		try {

            $stmt = $this->pdo->prepare($sql);
                    
            $stmt->execute([':id_lista_blanca' => $id_lista_blanca]);

            $stmt = null;
            $this->pdo = null;

            return true;

        } catch (Exception $e) {
		    echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
		    exit;				
	    }	

	}
}

?>