<?php

require_once 'conexion.php';

// Filter the excel data 
function filterData($str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
}

try {

	$id_fondos = $_POST['id_fondos'];
	$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	// Excel file name for download
	$fileName = "inventario-origen_" . date('Y-m-d') . ".xls"; 
	// Column names 
	$fields = array('ORIGEN', 'CODIGO', 'INSTITUCION', 'PLACA', 
					'SERIE', 'CLASE', 'MODELO', 'MARCA', 'ESTADO',
					'ENUSO','DONAR', 'UBICACION');
	// Display column names as first row 
	$excelData = implode("\t", array_values($fields)) . "\n"; 					

	if ($pdo != null) {								

		$consultaSQL = "SELECT fondos, t_placa.codigo AS codigo, institucion, placa, serial AS serie, clase, modelo, 
						marca, estado, enuso, donar, lugar 
						FROM t_activo 
						INNER JOIN t_activo_general ON t_activo_general.id_ag = t_activo.id_ag 
						INNER JOIN t_marca ON t_activo.id_marca = t_marca.id_marca 
						INNER JOIN t_placa ON t_placa.id_activo = t_activo.id_activo 
						INNER JOIN t_lugar ON t_placa.id_lugar = t_lugar.id_lugar 
						INNER JOIN t_estado ON t_placa.id_estado = t_estado.id_estado 
						INNER JOIN t_fondos ON t_placa.id_fondos = t_fondos.id_fondos 
						INNER JOIN instituciones ON instituciones.codigo = t_placa.codigo 
						WHERE t_placa.id_fondos = $id_fondos and activo = 1 
						ORDER BY t_placa.codigo, placa, clase, marca ASC";

		$sql = $pdo->query($consultaSQL);

		$rs = [];
		while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
			$enuso = ($row['enuso'] == 1)?'Si':'No';
			$donar = ($row['donar'] == 1)?'Si':'No';			
			$rs = array($row['fondos'],						
						$row['codigo'],
						$row['institucion'],	                						
						$row['placa'],
						$row['serie'],				
						$row['clase'],
						$row['modelo'],
						$row['marca'],
						$row['estado'],
						$enuso,
						$donar,
						$row['lugar']);
			array_walk($rs, 'filterData');
			$excelData .= implode("\t", array_values($rs)) . "\n";
		}
		
	}

	// Headers for download 
	header("Content-Type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=\"$fileName\""); 
	$pdo = null;	
	// Render excel data 
	echo $excelData; 
	exit;
    
} 
catch (PDOException $e) {			
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
	exit;
}
?>
