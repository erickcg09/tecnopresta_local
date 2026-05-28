<?php
$html = '';
require_once("conexion.php");
$link = $mysqli;


$id_regional = $_POST['id_regional'];

$result = $link->query(
    "SELECT id_circuito, circuito FROM t_circuito
     WHERE id_regional = ".$id_regional." ORDER BY id_circuito ASC"
);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {                
        $html .= '<option value="'.$row['id_circuito'].'">'.$row['circuito'].'</option>';
    }
}
echo $html;
?>