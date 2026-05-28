<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 2, 3, 4]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];

// Verificar que el parámetro POST está presente
if (!isset($_POST['id_fondos'])) {
    echo "Error: id_fondos no está definido.";
    exit();
}
$id_fondos = $_POST['id_fondos'];

// Ejecutar la consulta
$consulta = mysqli_query($link, "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
    FROM t_activo Ta
    INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
    INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
    INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
    INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
    WHERE Tp.codigo = '$logcodigo' AND Tp.id_fondos = '$id_fondos'
    ORDER BY Tp.placa ASC");

if (!$consulta) {
    echo "Error en la consulta: " . mysqli_error($link);
    exit();
}

if (mysqli_num_rows($consulta) > 0) {
    echo '<table class="table table-hover">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll" /></th>
                <th>Activo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Color</th>
                <th>Placa</th>
                <th>Serial</th>
                <th colspan="4"><button class="btn btn-dark btn-lg btn-block" type="submit" name="btnActualizar" id="btnActualizar">
                    <i class="bi bi-clipboard2-check-fill"></i> Actualizar
                </button></th>
            </tr>
        </thead>
        <tbody class="BusquedaRapida">';

    while ($activos = mysqli_fetch_array($consulta)) {
        echo '<tr>
            <td><input type="checkbox" class="selectall" name="idsplacas[]" value="' . $activos['id_placa'] . '"/></td>
            <td>' . $activos['clase'] . '</td>
            <td>' . $activos['marca'] . '</td>
            <td>' . $activos['modelo'] . '</td>
            <td>' . $activos['color'] . '</td>
            <td>' . $activos['placa'] . '</td>
            <td>' . $activos['serial'] . '</td>
        </tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No se encontraron datos.</p>';
}

mysqli_close($link);
?>

