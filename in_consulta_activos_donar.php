<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su administrador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}
require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}

if (isset($_POST['id_fondos'])) {
    $id_fondos = $_POST['id_fondos'];

    // Consulta SQL para contar el total de activos a donar por instituci贸n filtrado por id_fondos
    $count_sql = "
        SELECT t_instituciones.codigo, t_instituciones.institucion, COUNT(t_placa.codigo) AS total_activos_donar
        FROM t_placa
        INNER JOIN t_instituciones ON t_placa.codigo = t_instituciones.codigo
        WHERE t_placa.donar = 1 AND t_placa.id_fondos = ? AND (t_placa.id_estado = 1 OR t_placa.id_estado = 2)
        GROUP BY t_instituciones.codigo, t_instituciones.institucion
    ";

    if ($stmt = $link->prepare($count_sql)) {
        $stmt->bind_param("i", $id_fondos);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<table class="table" id="testTable">';
            echo '<thead><tr><th>Código</th><th>Institución</th><th>Total de Activos a Donar</th></tr></thead>';
            echo '<tbody class="BusquedaRapida">';
            while ($row = $result->fetch_assoc()) {
                echo '<tr><td>' . $row["codigo"] . '</td><td>' . $row["institucion"] . '</td><td>' . $row["total_activos_donar"] . '</td></tr>';
            }
            echo '</tbody></table>';
        } else {
            echo "No se encontraron resultados.";
        }

        $stmt->close();
    } else {
        echo "Error en la consulta: " . $link->error;
    }

    $link->close();
}
?>