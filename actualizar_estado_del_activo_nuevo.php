<?php
session_start();
if (!$_SESSION) {
    echo '<script language="javascript">
    alert("Usuario no autenticado");
    self.location = "index.html";
    </script>';
    exit();
}

require_once("conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres UTF-8";
    exit();
}

// Verificar si no hay activos seleccionados
if (empty($_POST['idsplacas'])) {
    echo '<script language="javascript">
    alert("No hay ningún activo seleccionado");
    self.location = "formulario_estado_de_los_activos.php";
    </script>';
    exit();
}

// Recorrer los activos seleccionados
foreach ($_POST['idsplacas'] as $idplaca) {
    $id_estado = $_POST['estado' . $idplaca];
    $enuso = isset($_POST['enuso' . $idplaca]) ? 1 : 0; // Verificar si el checkbox está marcado

    // Actualizar el estado y el campo enuso
    $update = "UPDATE t_placa SET id_estado = '$id_estado', enuso = '$enuso' WHERE id_placa = '$idplaca'";
    if (!mysqli_query($link, $update)) {
        echo "Error al actualizar registro: " . mysqli_error($link);
    }
}

mysqli_close($link);

echo '<script language="javascript">
alert("Cambios realizados correctamente");
self.location = "formulario_estado_de_los_activos.php";
</script>';
?>