<?php
session_start();
require_once("conexion.php");
$link = $mysqli;
// Validar si se recibió el id_fondos
if (!isset($_POST['id_fondos'])) {
    echo "Error: No se recibió el ID del fondo.";
    exit();
}
// Establecer conjunto de caracteres UTF-8
if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link);
    exit();
}
$id_fondos = $_POST['id_fondos'];
$logcodigo = $_SESSION['codigo'];
$activado = 1;

// Realizar la consulta
$consulta = mysqli_query($link, "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo, Tp.enuso
    FROM t_activo Ta
    INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
    INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
    INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
    INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
    WHERE Tp.codigo = '$logcodigo' AND Tp.activo = '$activado' AND Tp.id_fondos = '$id_fondos'
    ORDER BY Tp.placa ASC") or die(mysqli_error($link));

// Generar la tabla
$tabla = '<table class="table table-hover">
    <thead>
        <tr>
            <th>Seleccione</th>
            <th>Activo</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Color</th>
            <th>Placa</th>
            <th>Serial</th>
            <th>Estado</th>
            <th>En Uso</th>
        </tr>
    </thead>
    <tbody>';

while ($activo = mysqli_fetch_array($consulta)) {
    $id_placa = $activo['id_placa'];
    $id_estado = $activo['id_estado'];
    $enuso = $activo['enuso'];

    $tabla .= '<tr>
        <td><input type="checkbox" class="selectall" name="idsplacas[]" value="' . $id_placa . '"/></td>
        <td>' . $activo['clase'] . '</td>
        <td>' . $activo['marca'] . '</td>
        <td>' . $activo['modelo'] . '</td>
        <td>' . $activo['color'] . '</td>
        <td>' . $activo['placa'] . '</td>
        <td>' . $activo['serial'] . '</td>
        <td>
            <input type="radio" name="estado' . $id_placa . '" value="1" ' . ($id_estado == 1 ? 'checked' : '') . '> Muy bueno<br>
            <input type="radio" name="estado' . $id_placa . '" value="2" ' . ($id_estado == 2 ? 'checked' : '') . '> Bueno<br>
            <input type="radio" name="estado' . $id_placa . '" value="3" ' . ($id_estado == 3 ? 'checked' : '') . '> Regular<br>
            <input type="radio" name="estado' . $id_placa . '" value="4" ' . ($id_estado == 4 ? 'checked' : '') . '> Malo<br>
            <input type="radio" name="estado' . $id_placa . '" value="5" ' . ($id_estado == 5 ? 'checked' : '') . '> Robado/Hurtado
        </td>
        <td><input type="checkbox" name="enuso' . $id_placa . '" ' . ($enuso == 1 ? 'checked' : '') . '></td>
    </tr>';
}

$tabla .= '</tbody></table>';

echo $tabla; // Devolver la tabla generada
mysqli_close($link);
?>