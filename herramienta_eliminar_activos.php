<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_principal.html";
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
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

if (empty($_POST['idsplacas'])) {
    echo '<script language="javascript">
    alert("No hay ningún activo seleccionado");
    self.location = "herramienta_formulario_eliminar_activos.php";
    </script>';
    exit();
} else {
    // Iniciar transacción para mayor seguridad
    mysqli_begin_transaction($link);
    
    try {
        $ids_eliminados = [];
        $errores = [];
        
        foreach ($_POST['idsplacas'] as $idplaca) {
            // Sanitizar el ID
            $idplaca = mysqli_real_escape_string($link, $idplaca);
            
            // Verificar que el ID sea numérico
            if (!is_numeric($idplaca)) {
                $errores[] = "ID no válido: $idplaca";
                continue;
            }
            
            // Consulta para eliminar el registro
            $query = "DELETE FROM t_placa WHERE id_placa = '$idplaca'";
            
            if (mysqli_query($link, $query)) {
                if (mysqli_affected_rows($link) > 0) {
                    $ids_eliminados[] = $idplaca;
                } else {
                    $errores[] = "No se encontró el registro con ID: $idplaca";
                }
            } else {
                $errores[] = "Error al eliminar ID $idplaca: " . mysqli_error($link);
            }
        }
        
        // Confirmar la transacción
        mysqli_commit($link);
        
        // Preparar mensaje de resultado
        $mensaje = "";
        
        if (!empty($ids_eliminados)) {
            $mensaje .= "Se eliminaron " . count($ids_eliminados) . " registros exitosamente. ";
            if (count($ids_eliminados) <= 10) {
                $mensaje .= "IDs: " . implode(", ", $ids_eliminados) . ". ";
            } else {
                $mensaje .= "Primeros 10 IDs: " . implode(", ", array_slice($ids_eliminados, 0, 10)) . "... ";
            }
        }
        
        if (!empty($errores)) {
            $mensaje .= "Ocurrieron " . count($errores) . " errores. ";
            if (count($errores) <= 5) {
                $mensaje .= "Errores: " . implode("; ", $errores);
            } else {
                $mensaje .= "Primeros 5 errores: " . implode("; ", array_slice($errores, 0, 5)) . "...";
            }
        }
        
        // Registrar en log de actividades (opcional)
        $usuario = $_SESSION['cedula'] ?? 'Desconocido';
        $fecha = date('Y-m-d H:i:s');
        $accion = "Eliminación de activos. " . $mensaje;
        error_log("[$fecha] Usuario: $usuario - $accion");
        
        echo '<script language="javascript">
        alert("' . addslashes($mensaje) . '");
        self.location = "herramienta_formulario_eliminar_activos.php";
        </script>';
        
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        mysqli_rollback($link);
        
        echo '<script language="javascript">
        alert("Error crítico durante la eliminación: ' . addslashes($e->getMessage()) . '");
        self.location = "herramienta_formulario_eliminar_activos.php";
        </script>';
    }
}

// Cerrar conexión
mysqli_close($link);
?>
        
        
