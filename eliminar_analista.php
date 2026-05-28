<?php
session_start();
// Verificar permisos (similar al listado)
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false){
    echo '<script language = javascript>
    alert("No tienes permisos para esta acción")
    self.location = "index.html"
    </script>';
    exit();
}

require_once("conexion.php");
$link = $mysqli;

// Verificar si se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: listado_analistas.php");
    exit();
}

$id_analista = intval($_GET['id']);

// Obtener información del analista para eliminar su foto
$sql_info = "SELECT foto FROM t_analista WHERE id_analista = ?";
$stmt_info = $link->prepare($sql_info);
$stmt_info->bind_param("i", $id_analista);
$stmt_info->execute();
$result_info = $stmt_info->get_result();

if ($result_info->num_rows === 0) {
    header("Location: listado_analistas.php");
    exit();
}

$analista = $result_info->fetch_assoc();
$stmt_info->close();

// Eliminar la foto si existe y no es la imagen por defecto
if (!empty($analista['foto']) && $analista['foto'] != 'img/default-user.png' && file_exists($analista['foto'])) {
    unlink($analista['foto']);
}

// Eliminar el analista de la base de datos
$sql_delete = "DELETE FROM t_analista WHERE id_analista = ?";
$stmt_delete = $link->prepare($sql_delete);
$stmt_delete->bind_param("i", $id_analista);

if ($stmt_delete->execute()) {
    // Redirigir con mensaje de éxito
    header("Location: interfaz_principal_listado_ingenieros.php?success=El ingeniero ha sido eliminado correctamente");
} else {
    // Redirigir con mensaje de error
    header("Location: interfaz_principal_listado_ingenieros.php?error=Ocurrió un error al eliminar el ingeniero");
}

$stmt_delete->close();
$link->close();
exit();