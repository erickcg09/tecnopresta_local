<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 2, 3, 4, 5, 7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte con el administrador.");
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

try {
    $id_cita = mysqli_real_escape_string($link, $_POST['id']);
    $cedula_usuario = $_SESSION['cedula'];

    // Verificar que la cita pertenezca al usuario
    $query_verificar = "SELECT id FROM citas 
                        WHERE id = $id_cita AND usuario_id = '$cedula_usuario'";
    if (mysqli_num_rows(mysqli_query($link, $query_verificar)) == 0) {
        throw new Exception("No puedes cancelar esta cita");
    }

    // Actualizar estado
    $query = "UPDATE citas SET estado = 'cancelada' 
              WHERE id = $id_cita AND estado = 'pendiente'";
    
    if (!mysqli_query($link, $query)) {
        throw new Exception("Error al cancelar: " . mysqli_error($link));
    }

    $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Cita cancelada correctamente'];
} catch (Exception $e) {
    $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => $e->getMessage()];
}

header("Location: mis_citas.php");
exit();
?>