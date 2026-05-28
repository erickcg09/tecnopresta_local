<?php

require_once 'conexionPDO.php';

if(isset($_GET["term"]))
{
	$connect = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);

	$query = "
	SELECT * FROM t_marca 
	WHERE marca LIKE '%".$_GET["term"]."%' 
	ORDER BY marca ASC
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
			$temp_array['value'] = $row['marca'];
			$temp_array['label'] = '<img src="ico/'.$row['logo'].'" width="70" />&nbsp;&nbsp;&nbsp;'.$row['marca'].'';
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
