<?php
$html = '';
require_once("conexion.php");
$link = $mysqli;


$id_regional = $_POST['id_regional'];

$result = $link->query(
    "SELECT id_edificio, edificio FROM t_edificio
     WHERE id_regional = ".$id_regional." ORDER BY edificio ASC"
);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {                
        $html .= '<option value="'.$row['id_edificio'].'">'.$row['edificio'].'</option>';
    }
}
echo $html;
?>
