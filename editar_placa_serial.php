<?php
session_start();
if (!isset($_SESSION['cedula'])) {
    header("Location: index.html");
    exit();
}

require_once("conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    die("Error de conexión a MySQL: " . mysqli_connect_error());
}

// Configurar charset
if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres utf8");
}

// Obtener datos del formulario
$id_placa = mysqli_real_escape_string($link, $_POST['idplaca']);
$placa = mysqli_real_escape_string($link, $_POST['placa']);
$serial = mysqli_real_escape_string($link, $_POST['serial']);
$id_fondos = mysqli_real_escape_string($link, $_POST['fondos']);

// Verificar si los nuevos valores ya existen en otros registros
$check_query = "SELECT COUNT(*) as count FROM t_placa 
                WHERE (placa = '$placa' OR serial = '$serial') 
                AND id_placa != '$id_placa'";
$result = mysqli_query($link, $check_query);

if (!$result) {
    die("Error en la verificación: " . mysqli_error($link));
}

$row = mysqli_fetch_assoc($result);
if ($row['count'] > 0) {
    // Hay duplicados, redirigir con mensaje de error
    header('Location: formulario_editar_placa.php?error=duplicado&id='.$id_placa);
    exit();
}

// SQL para actualizar (preparado para evitar inyección SQL)
$update = "UPDATE t_placa 
           SET placa = '$placa', 
               serial = '$serial', 
               id_fondos = '$id_fondos' 
           WHERE id_placa = '$id_placa'";

if (mysqli_query($link, $update)) {
    // Redirigir con mensaje de éxito
    header('Location: formulario_editar_placa.php?success=1&id='.$id_placa);
} else {
    // Redirigir con mensaje de error
    header('Location: formulario_editar_placa.php?error=actualizacion&id='.$id_placa);
}

mysqli_close($link);
exit();
?>
