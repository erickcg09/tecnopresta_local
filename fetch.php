<?php

require_once 'conexionPDO.php';

if(isset($_GET["term"]))
{
	$connect = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);

	$query = "
	SELECT * FROM t_software_general 
	WHERE etiqueta LIKE '%".$_GET["term"]."%' 
	ORDER BY etiqueta ASC
	";

	$statement = $connect->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	$total_row = $statement->rowCount();

	$output = array();
	if($total_row > 0)
	{
		foreach($result as $row)
		{
			$temp_array = array();
			$temp_array['value'] = $row['etiqueta'];
			$temp_array['label'] = '<img src="ico/'.$row['imagen'].'" width="70" />&nbsp;&nbsp;&nbsp;'.$row['etiqueta'].'';
			$output[] = $temp_array;
		}
	}
	else
	{
		$output['value'] = '';
		$output['label'] = 'No se encontró el registro';
	}

	echo json_encode($output);
}

?>
