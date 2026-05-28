<?php

include 'conexion.php';

// Consultar c&oacute;digo de  Centro Educativo de tabla t_centroEducativo
 $query = "
 SELECT DISTINCT codigo FROM t_placa 
 WHERE codigo LIKE '%".trim($_POST["query"])."%' LIMIT 20
 ";

$result = mysqli_query($mysqli, $query);

$data = array();

if(mysqli_num_rows($result) > 0)
{
 while($row = mysqli_fetch_assoc($result))
 {
  $data[] = $row["codigo"];
   
 }
 echo json_encode($data);

}

?>