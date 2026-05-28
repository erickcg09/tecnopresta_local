<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "formulario_menu_inventario.html"
</script>';
}

require_once("conexion.php");

// Verificar conexión
if ($mysqli->connect_errno) {
    die("Error de conexión a MySQL: " . $mysqli->connect_error);
}

// Configurar charset
if (!$mysqli->set_charset("utf8")) {
    die("Error cargando el conjunto de caracteres utf8: " . $mysqli->error);
}

// Obtener datos del formulario con validación básica
$clase = trim($mysqli->real_escape_string($_POST['clase'] ?? ''));
$idag = intval($_POST['idag'] ?? 0);

if (empty($clase) || $idag <= 0) {
    $_SESSION['error_message'] = "Datos de entrada inválidos";
    header('Location: formulario_crear_activo_general.php');
    exit();
}

// 1. Verificar si la clase nueva ya existe (excepto para el registro actual)
$check_query = "SELECT id_ag FROM t_activo_general WHERE clase = ? AND id_ag != ?";
$stmt = $mysqli->prepare($check_query);
$stmt->bind_param("si", $clase, $idag);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Clase ya existe
    $_SESSION['error_message'] = "Error: La clase '$clase' ya existe en la base de datos";
    header('Location: formulario_crear_activo_general.php');
    exit();
}

// 2. Actualizar la clase si no existe duplicado
$update_query = "UPDATE t_activo_general SET clase = ? WHERE id_ag = ?";
$stmt = $mysqli->prepare($update_query);
$stmt->bind_param("si", $clase, $idag);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Clase de activo actualizada correctamente";
} else {
    $_SESSION['error_message'] = "Error al actualizar registro: " . $mysqli->error;
}

$stmt->close();
$mysqli->close();

// Redireccionar al formulario
header('Location: formulario_crear_activo_general.php');
exit();
?>
