<?php

require_once 'conexionPDO.php';

if(isset($_GET["term"]))
{
	$connect = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);

	$query = "
	SELECT Tp.placa, Ta.imagen FROM t_placa Tp
        INNER JOIN t_activo Ta ON Tp.id_activo = Ta.id_activo
	WHERE Tp.placa LIKE '%".$_GET["term"]."%' 
	ORDER BY Tp.placa ASC
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
			$temp_array['label'] = '<img src="img/'.$row['imagen'].'" width="70" />&nbsp;&nbsp;&nbsp;'.$row['placa'].'';
			$output[] = $temp_array;
		}
	}
	else
	{
		$output['value'] = '';
		$output['label'] = 'No Record Found';
	}

	echo json_encode($output);
}

?>
