<?php
require 'conexion.php';
class Insert_visitas_sitio_hoja_trabajo {

    private $pdo;

	function __construct(){
        $pdo = new \PDO(DB_Str, DB_USER, DB_PASS);		
        $this->pdo = $pdo;        
    }
        
    public function insert_visitas_sitio_hoja_trabajo($visitas_sitio_hoja_trabajo){        
        try {
            if(!empty($visitas_sitio_hoja_trabajo)) {

                $id_visita = $visitas_sitio_hoja_trabajo["id_visita"];
                $visitas_sitio_hoja_trabajo_id = $visitas_sitio_hoja_trabajo["visitas_sitio_hoja_trabajo_id"];
                $estado = $visitas_sitio_hoja_trabajo["visitas_sitio_hoja_trabajo_estado"];
                // $visitas_sitio_hoja_trabajo_firma = $visitas_sitio_hoja_trabajo["visitas_sitio_hoja_trabajo_firma"];
                $requiere_segunda_visita = $visitas_sitio_hoja_trabajo["requiere_segunda_visita"];
                $horas_total_atencion = $visitas_sitio_hoja_trabajo["horas_total_atencion"];
                $minutos_total_atencion = $visitas_sitio_hoja_trabajo["minutos_total_atencion"];
                $visitas_sitio_hoja_trabajo_resultado = $visitas_sitio_hoja_trabajo["visitas_sitio_hoja_trabajo_resultado"];
                $visitas_sitio_hoja_trabajo_indicaciones = $visitas_sitio_hoja_trabajo["visitas_sitio_hoja_trabajo_indicaciones"];

                $this->pdo->beginTransaction();                

                $sql = "UPDATE visitas_sitio 
                        SET estado = :estado
                        WHERE id_visita = :id_visita";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':id_visita' => $id_visita,
                                ':estado' => $estado
                                ]);

                if ($visitas_sitio_hoja_trabajo_id > 0) {

                    /* $sql = "UPDATE visitas_sitio_hoja_trabajo_firma 
                            SET visitas_sitio_hoja_trabajo_firma = :visitas_sitio_hoja_trabajo_firma
                            WHERE visitas_sitio_hoja_trabajo_id = :visitas_sitio_hoja_trabajo_id";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([':visitas_sitio_hoja_trabajo_id' => $visitas_sitio_hoja_trabajo_id,
                                    ':visitas_sitio_hoja_trabajo_firma' => $visitas_sitio_hoja_trabajo_firma
                                    ]); */

                    $sql = "UPDATE visitas_sitio_hoja_trabajo 
                            SET requiere_segunda_visita = :requiere_segunda_visita,
                            horas_total_atencion = :horas_total_atencion,
                            minutos_total_atencion = :minutos_total_atencion,
                            visitas_sitio_hoja_trabajo_resultado = :visitas_sitio_hoja_trabajo_resultado,
                            visitas_sitio_hoja_trabajo_indicaciones = :visitas_sitio_hoja_trabajo_indicaciones
                            WHERE visitas_sitio_hoja_trabajo_id = :visitas_sitio_hoja_trabajo_id";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([':visitas_sitio_hoja_trabajo_id' => $visitas_sitio_hoja_trabajo_id,
                                    ':requiere_segunda_visita' => $requiere_segunda_visita,
                                    ':horas_total_atencion' => $horas_total_atencion,
                                    ':minutos_total_atencion' => $minutos_total_atencion,
                                    ':visitas_sitio_hoja_trabajo_resultado' => $visitas_sitio_hoja_trabajo_resultado,
                                    ':visitas_sitio_hoja_trabajo_indicaciones' => $visitas_sitio_hoja_trabajo_indicaciones
                                    ]);

                } elseif ($visitas_sitio_hoja_trabajo_id == 0) {

                    $sql = "INSERT INTO visitas_sitio_hoja_trabajo (
                            id_visita,
                            requiere_segunda_visita,
                            horas_total_atencion,
                            minutos_total_atencion,
                            visitas_sitio_hoja_trabajo_resultado,
                            visitas_sitio_hoja_trabajo_indicaciones) 
                            VALUES (
                            :id_visita, 
                            :requiere_segunda_visita, 
                            :horas_total_atencion,
                            :minutos_total_atencion,
                            :visitas_sitio_hoja_trabajo_resultado,
                            :visitas_sitio_hoja_trabajo_indicaciones)";
                            $stmt = $this->pdo->prepare($sql);
                            $stmt->execute([':id_visita' => $id_visita,
                                            ':requiere_segunda_visita' => $requiere_segunda_visita,
                                            ':horas_total_atencion' => $horas_total_atencion,
                                            ':minutos_total_atencion' => $minutos_total_atencion,
                                            ':visitas_sitio_hoja_trabajo_resultado' => $visitas_sitio_hoja_trabajo_resultado,
                                            ':visitas_sitio_hoja_trabajo_indicaciones' => $visitas_sitio_hoja_trabajo_indicaciones
                                        ]);

                    $visitas_sitio_hoja_trabajo_lastInsertId = $this->pdo->lastInsertId();

                    /* $sql = "INSERT INTO visitas_sitio_hoja_trabajo_firma  
                            (visitas_sitio_hoja_trabajo_id, visitas_sitio_hoja_trabajo_firma)
                            VALUES (:visitas_sitio_hoja_trabajo_id, :visitas_sitio_hoja_trabajo_firma)";
                            $stmt = $this->pdo->prepare($sql);
                            $stmt->execute([':visitas_sitio_hoja_trabajo_id' => $visitas_sitio_hoja_trabajo_lastInsertId,
                                            ':visitas_sitio_hoja_trabajo_firma' => $visitas_sitio_hoja_trabajo_firma
                                        ]); */ 

                }

                if($visitas_sitio_hoja_trabajo_id>0){

                    $sql = 'DELETE FROM visitas_sitio_hoja_trabajo_activos 
                            WHERE visitas_sitio_hoja_trabajo_id =:visitas_sitio_hoja_trabajo_id';
                    $stmt = $this->pdo->prepare($sql);                    
                    $stmt->execute([':visitas_sitio_hoja_trabajo_id' => $visitas_sitio_hoja_trabajo_id]);
                }

                $visitas_sitio_hoja_trabajo_id_en_activos=0;

                if ($visitas_sitio_hoja_trabajo_id>0) {

                    $visitas_sitio_hoja_trabajo_id_en_activos=$visitas_sitio_hoja_trabajo_id;

                } elseif ($visitas_sitio_hoja_trabajo_id == 0) {

                    $visitas_sitio_hoja_trabajo_id_en_activos=$visitas_sitio_hoja_trabajo_lastInsertId;
                
                }

                $arraylista_de_activos = array();
                $arraylista_de_activos=$visitas_sitio_hoja_trabajo["lista_de_activos"];

                if ($visitas_sitio_hoja_trabajo_id_en_activos>0 && !empty($arraylista_de_activos)) {
                                                         
                    foreach($arraylista_de_activos as $activos){
                        $sql = 'INSERT INTO visitas_sitio_hoja_trabajo_activos 
                                (visitas_sitio_hoja_trabajo_activos_id_activo, 
                                visitas_sitio_hoja_trabajo_id,
                                visitas_sitio_hoja_trabajo_id_placa)
                                VALUES 
                                (:visitas_sitio_hoja_trabajo_activos_id_activo, 
                                :visitas_sitio_hoja_trabajo_id,
                                :visitas_sitio_hoja_trabajo_id_placa)';
                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute([
                            ':visitas_sitio_hoja_trabajo_activos_id_activo' => $activos["id_activo"],
                            ':visitas_sitio_hoja_trabajo_id_placa' => $activos["id_placa"],
                            ':visitas_sitio_hoja_trabajo_id' => $visitas_sitio_hoja_trabajo_id_en_activos
                            ]);                        
                    }
                }

                // La misma lógica de guardar activos se usa para guardar indicadores
                
                if($visitas_sitio_hoja_trabajo_id>0){

                    $sql = 'DELETE FROM visitas_sitio_hoja_trabajo_procedimiento 
                            WHERE visitas_sitio_hoja_trabajo_id =:visitas_sitio_hoja_trabajo_id';
                    $stmt = $this->pdo->prepare($sql);                    
                    $stmt->execute([':visitas_sitio_hoja_trabajo_id' => $visitas_sitio_hoja_trabajo_id]);
                }

                $visitas_sitio_hoja_trabajo_id_en_indicadores=0;

                if ($visitas_sitio_hoja_trabajo_id>0) {

                    $visitas_sitio_hoja_trabajo_id_en_indicadores = $visitas_sitio_hoja_trabajo_id;

                } elseif ($visitas_sitio_hoja_trabajo_id == 0) {

                    $visitas_sitio_hoja_trabajo_id_en_indicadores = $visitas_sitio_hoja_trabajo_lastInsertId;
                
                }

                $arraylista_de_estado_inicial_equipo = array();
                $arraylista_de_procedimiento = $visitas_sitio_hoja_trabajo["lista_de_indicador_procedimiento"];

                if ($visitas_sitio_hoja_trabajo_id_en_indicadores>0 && !empty($arraylista_de_procedimiento)) {
                                                         
                    foreach($arraylista_de_procedimiento as $indicadores){
                        $sql = 'INSERT INTO visitas_sitio_hoja_trabajo_procedimiento 
                                (visitas_sitio_hoja_trabajo_id, visitas_procedimiento_id)
                                VALUES 
                                (:visitas_sitio_hoja_trabajo_id, 
                                :visitas_procedimiento_id)';
                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute([
                            ':visitas_procedimiento_id' => $indicadores["visitas_indicador_id"],
                            ':visitas_sitio_hoja_trabajo_id' => $visitas_sitio_hoja_trabajo_id_en_indicadores
                            ]);                        
                    }
                }

                                                                          
                $this->pdo->commit(); 
            }

            $stmt = null;
            $this->pdo = null;           
            return true;

        } catch (\Throwable $th) { echo "Error: " . $th->getMessage() . "\n";}                  
    }
} 

?>