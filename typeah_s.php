<?php

include 'conectar.php';

// Consultar código de  Centro Educativo de tabla t_centroEducativo
 $query = "SELECT DISTINCT t_boleta.cedula AS cedula, t_padron.nombre AS nombre,
 					t_padron.apellidop AS apellidop, t_padron.apellidom AS apellidom 
 					FROM t_boleta INNER JOIN t_padron ON  t_padron.cedula= t_boleta.cedula 
					WHERE t_boleta.cedula LIKE '%".trim($_POST["query"])."%'LIMIT 20";
 					

$result = mysqli_query($conexion, $query);

$data = array();

if(mysqli_num_rows($result) > 0)
{
 while($row = mysqli_fetch_assoc($result))
 {
  $data[] = $row["cedula"];
   
 }
 echo json_encode($data);

}

?>