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
date_default_timezone_set('America/Costa_Rica'); //configuro un nuevo timezone
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
// Obtener las variables desde la URL
$id_rae = $_GET['id_rae'];
$codigo = $_GET['codigo'];
$dependencia = $_GET['dependencia'];

// Preparar y ejecutar la consulta de eliminaci贸n
$sql = "DELETE FROM t_recepcion_acta_equipo WHERE id_rae = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $id_rae);

if ($stmt->execute()) {
    // Redirigir al archivo in_actas_institucion.php con las variables codigo y dependencia
    header("Location: in_actas_institucion.php?codigo=$codigo&dependencia=$dependencia");
    exit();
} else {
    echo "Error al eliminar el registro: " . $link->error;
}

// Cerrar la conexi贸n
$stmt->close();
$link->close();
?>