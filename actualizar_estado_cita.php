<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 7]);
if (!$tienellave) {
    echo "No tienes permisos para realizar esta acción.";
    exit();
}

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($link));
    exit();
}

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recoger y validar los datos
    $id_mis_citas = intval($_POST['id_mis_citas']);
    $estado = intval($_POST['estado']);
    
    // Validar que el ID sea válido
    if ($id_mis_citas <= 0) {
        echo "ID de cita inválido";
        exit();
    }
    
    // Preparar la consulta SQL
    $sql = "UPDATE t_citas_siguimientos SET estado = ? WHERE id_mis_citas = ?";
    $stmt = mysqli_prepare($link, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $estado, $id_mis_citas);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "Estado de la cita actualizado correctamente";
        } else {
            echo "Error al actualizar el estado: " . mysqli_error($link);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "Error al preparar la consulta: " . mysqli_error($link);
    }
    
} else {
    echo "Método no permitido";
}

// Cerrar conexión
mysqli_close($link);
?>