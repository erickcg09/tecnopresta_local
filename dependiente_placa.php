<?php
$html = '';
require_once("conexion.php");
$link = $mysqli;


//$prestamo_id = $_POST['prestamo_Id'];

$prestamo_id = "13";

$result=mysqli_query($link,"SELECT Te.prestamo_Id,Te.prestamo_detalle_id_placa, Tc.placa
		 FROM t_prestamo_detalle Te
		 INNER JOIN t_placa Tc ON Te.prestamo_detalle_id_placa = Tc.id_placa
		 WHERE prestamo_Id = ".$prestamo_id."
		 ORDER BY Te.prestamo_detalle_id_placa ASC") or die(mysqli_error($link));


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {                
        $html .= '<option value="'.$row['prestamo_detalle_id_placa'].'">'.$row['placa'].'</option>';
    }
}
echo $html;
?>