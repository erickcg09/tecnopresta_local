<?php
require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}



$fechaActual = date('Y-m-d');




	$ruta = 'popup/';	

	foreach ($_FILES as $key) {

		$nombre=$key["name"];
		$ruta_temporal=$key["tmp_name"];		
		
		$fecha=getdate();
		$nombre_v=$fecha["mday"]."-".$fecha["mon"]."-".$fecha["year"]."_".$fecha["hours"]."-".$fecha["minutes"]."-".$fecha["seconds"].".csv";		

		$destino=$ruta.$nombre_v;
		$explo=explode(".",$nombre);


		if($explo[1] != "csv"){
			$alert=1;
		}else{

			if(move_uploaded_file($ruta_temporal, $destino)){
				$alert=2;
			}

		}

	}

	$x=0;
	$data=array();
	$fichero=fopen($destino, "r");

	while(($datos= fgetcsv($fichero,1000)) != FALSE){

		$x++;
		if($x>1){

			$data[]='("'.$datos[0].'","'.$datos[1].'","'.$datos[2].'","'.$datos[3].'","'.$datos[4].'","'.$datos[5].'","'.$datos[6].'")';

		}

	}

	$query = "INSERT INTO b_programa_dos (imas, identificacion, apellidop, apellidom, nombrep, nombres, codigo)VALUES ". implode(",", $data);
	$link->query($query);
	mysqli_close($link);
	fclose($fichero);

	    
	    
	echo '<script language = javascript>
		  alert("Datos Importados correctamente")
		  self.location = "temp_form_importar.php"
		  </script>';

?>