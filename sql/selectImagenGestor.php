<?php

try {

	$dirpath = '../img/alias/';
	if (is_dir($dirpath)) {

		$isDirEmpty = new DirectoryIterator($dirpath);
		
		if ($isDirEmpty->valid()) {			

			$sorted_keys = array();
		
			foreach (new DirectoryIterator($dirpath) as $fileinfo ) {
		
				if (!$fileinfo->isDot()) {
					
					$filename = $fileinfo->getFilename();								
					$elements = array(									
									"archivo" => $filename,
									"nombre" => $fileinfo->getBasename('.png')
								);
					array_push($sorted_keys, $elements);				
				}
			}
						
			foreach ($sorted_keys as $key => $part) {
				$sort[$key] = strtotime($part['nombre']);
			}
			
			array_multisort($sort, SORT_ASC, $sorted_keys);
			echo json_encode($sorted_keys);
		}
		else {
			echo "0";
		} 
	}
	else {
		echo "0";
	}
} 
catch (PDOException $e) {		
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
	exit;
}
?>
