<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false){
    echo '<script language = javascript>
    alert("No tienes permisos")
    self.location = "index.html"
    </script>';
}
date_default_timezone_set('America/Costa_Rica');
require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id_caso = mysqli_real_escape_string($link, $_POST['id']);
    
    // 1. Obtener los datos del caso original
    $query_original = "SELECT * FROM soporte WHERE id = '$id_caso'";
    $result_original = mysqli_query($link, $query_original);
    
    if (!$result_original || mysqli_num_rows($result_original) == 0) {
        $_SESSION['error'] = "No se encontró el caso especificado.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    $caso_original = mysqli_fetch_assoc($result_original);
    
    // 2. Insertar el caso en la tabla de servicio técnico
    $query_insert = "
        INSERT INTO soporte_servicio_tecnico (
            id_caso_original,
            funcionario,
            placa,
            problema,
            fecha,
            estatus,
            codigo,
            correo,
            institucion,
            solucion,
            cedulatecnico,
            nombretecnico,
            tomado,
            dre,
            circuito,
            descriactivo,
            id_fondos,
            id_asunto,
            cedula_tecnico_servicio
        ) VALUES (
            '{$caso_original['id']}',
            '{$caso_original['funcionario']}',
            '{$caso_original['placa']}',
            '{$caso_original['problema']}',
            '{$caso_original['fecha']}',
            'Pendiente', -- Nuevo estatus para servicio técnico
            '{$caso_original['codigo']}',
            '{$caso_original['correo']}',
            '{$caso_original['institucion']}',
            '{$caso_original['solucion']}',
            '{$caso_original['cedulatecnico']}',
            '{$caso_original['nombretecnico']}',
            '{$caso_original['tomado']}',
            '{$caso_original['dre']}',
            '{$caso_original['circuito']}',
            '{$caso_original['descriactivo']}',
            '{$caso_original['id_fondos']}',
            '{$caso_original['id_asunto']}',
            NULL -- cedula_tecnico_servicio se deja NULL para que sea asignado después
        )
    ";
    
    if (mysqli_query($link, $query_insert)) {
        // 3. Actualizar el caso original en la tabla soporte
        $query_update = "
            UPDATE soporte 
            SET estatus = 'Atendido',
                solucion = 'Estimado usuario, su caso se a trasferido a un soportista técnico, el cual se pondrá en contacto con usted y seguirá el caso.'
            WHERE id = '$id_caso'
        ";
        
        if (mysqli_query($link, $query_update)) {
            $_SESSION['success'] = "Caso transferido exitosamente a servicio técnico.";
            
            // Opcional: Registrar en un log de actividades
            $log_usuario = $_SESSION['usuario'] ?? 'Sistema';
            $log_query = "INSERT INTO logs_transferencias (id_caso, usuario, fecha, accion) 
                         VALUES ('$id_caso', '$log_usuario', NOW(), 'Transferido a servicio técnico')";
            mysqli_query($link, $log_query);
            
        } else {
            $_SESSION['error'] = "Error al cerrar el caso original: " . mysqli_error($link);
        }
    } else {
        $_SESSION['error'] = "Error al transferir a servicio técnico: " . mysqli_error($link);
    }
    
    // Redirigir de vuelta
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
    
} else {
    $_SESSION['error'] = "Solicitud inválida.";
    header('Location: miscasos.php');
    exit();
}
?>