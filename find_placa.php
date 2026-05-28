<?php

require_once 'conexionPDO.php';

if(isset($_GET["term"]))
{
	$connect = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);

	$query = "
	SELECT * FROM t_placa 
	WHERE placa LIKE '%".$_GET["term"]."%' 
	ORDER BY placa ASC
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
			$temp_array['value'] = $row['placa'];
			$temp_array['label'] = $row['placa'].'';
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
