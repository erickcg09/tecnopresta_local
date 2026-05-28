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
$marca = trim($mysqli->real_escape_string($_POST['marca'] ?? ''));
$idmarca = intval($_POST['idmarca'] ?? 0);

if (empty($marca) || $idmarca <= 0) {
    die("Datos de entrada inválidos");
}

// 1. Verificar si la marca nueva ya existe (excepto para el registro actual)
$check_query = "SELECT id_marca FROM t_marca WHERE marca = ? AND id_marca != ?";
$stmt = $mysqli->prepare($check_query);
$stmt->bind_param("si", $marca, $idmarca);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Marca ya existe
    $_SESSION['error_message'] = "Error: La marca '$marca' ya existe en la base de datos";
    header('Location: formulario_crear_marca.php');
    exit();
}

// 2. Actualizar la marca si no existe duplicado
$update_query = "UPDATE t_marca SET marca = ? WHERE id_marca = ?";
$stmt = $mysqli->prepare($update_query);
$stmt->bind_param("si", $marca, $idmarca);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Marca actualizada correctamente";
} else {
    $_SESSION['error_message'] = "Error al actualizar registro: " . $mysqli->error;
}

$stmt->close();
$mysqli->close();

// Redireccionar al formulario
header('Location: formulario_crear_marca.php');
exit();
?>
