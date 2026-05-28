<?php
session_start();
// Verificar permisos (igual que en los archivos anteriores)
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false) {
    echo '<script language = javascript>
    alert("No tienes permisos")
    self.location = "index.html"
    </script>';
    exit();
}

require_once("conexion.php");
$link = $mysqli;

// Validar y obtener el ID de la placa
$id_placa = isset($_GET['id_placa']) ? intval($_GET['id_placa']) : 0;

if ($id_placa <= 0) {
    echo '<script language = javascript>
    alert("ID de placa inválido")
    self.location = "formulario_herramientas_con_tablas.php"
    </script>';
    exit();
}

// Preparar y ejecutar la consulta DELETE con sentencia preparada
$query = "DELETE FROM t_placa WHERE id_placa = ?";
$stmt = mysqli_prepare($link, $query);

if ($stmt === false) {
    echo '<script language = javascript>
    alert("Error al preparar la consulta: ' . mysqli_error($link) . '")
    self.location = "formulario_herramientas_con_tablas.php"
    </script>';
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $id_placa);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    echo '<script language = javascript>
    alert("Registro eliminado correctamente")
    self.location = "formulario_herramientas_con_tablas.php"
    </script>';
} else {
    echo '<script language = javascript>
    alert("Error al eliminar: ' . mysqli_error($link) . '")
    self.location = "formulario_herramientas_con_tablas.php"
    </script>';
}

// Cerrar la conexión
mysqli_stmt_close($stmt);
mysqli_close($link);
?>