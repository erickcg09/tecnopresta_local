<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 7]);
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

// Verificar que se recibieron los datos necesarios
if (isset($_POST['id']) && isset($_POST['id_soportista'])) {
    $id_cita = $_POST['id'];
    $id_soportista = $_POST['id_soportista'];
    
    // Preparar la consulta SQL con verificación del soportista
    $sql = "UPDATE citas SET estado = 'completada' 
            WHERE id = ? AND soportista_id = ? AND estado = 'pendiente'";
    
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ii", $id_cita, $id_soportista);
    
    if ($stmt->execute()) {
        // Verificar si se actualizó alguna fila
        if ($stmt->affected_rows > 0) {
            echo '<script language="javascript">
            alert("Cita completada exitosamente.");
            window.location.href = "ver_citas_de_soportista.php"; // Redirigir a la página de listado
            </script>';
        } else {
            echo '<script language="javascript">
            alert("No se pudo completar la cita. Verifique que la cita existe y está asignada a usted.");
            window.location.href = "ver_citas_de_soportista.php";
            </script>';
        }
    } else {
        echo '<script language="javascript">
        alert("Error al ejecutar la consulta: ' . $stmt->error . '");
        window.location.href = "ver_citas_de_soportista.php";
        </script>';
    }
    
    $stmt->close();
} else {
    echo '<script language="javascript">
    alert("Datos incompletos para completar la cita.");
    window.location.href = "ver_citas_de_soportista.php";
    </script>';
}

$link->close();
?>