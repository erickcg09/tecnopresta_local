<?php
session_start();
require_once("conexion.php");
require_once("variablesemail.php"); // aquí están $correo y $passemail

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/* Erick require "/home/tecnopresta/PHPMailer/src/SMTP.php";
require "/home/tecnopresta/PHPMailer/src/PHPMailer.php";
require "/home/tecnopresta/PHPMailer/src/Exception.php";
*/
require __DIR__ . '/PHPMailer/5.5/SMTP.php';
require __DIR__ . '/PHPMailer/5.5/PHPMailer.php';
require __DIR__ . '/PHPMailer/5.5/Exception.php';

$link = $mysqli;

// Verificar permisos
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos para realizar esta acción");
    window.history.back();
    </script>';
    exit();
}

// Configurar conexión y charset
if (mysqli_connect_errno()) {
    die("Error de conexión a MySQL: " . mysqli_connect_error());
}

if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link));
}

// Recoger y sanitizar datos del formulario
$codigo_institucion = mysqli_real_escape_string($link, $_POST['codigo_i']);
$nombre_institucion = mysqli_real_escape_string($link, $_POST['institucion']);
$telefono = mysqli_real_escape_string($link, $_POST['telefono']);
$correo_institucional = mysqli_real_escape_string($link, $_POST['correo_institucional']);
$direccion = mysqli_real_escape_string($link, $_POST['direccion']);
$persona_contacto = mysqli_real_escape_string($link, $_POST['persona_contacto']);
$fecha_visita = mysqli_real_escape_string($link, $_POST['fecha_visita']);
$hora_visita = mysqli_real_escape_string($link, $_POST['hora_visita']);
$titulo_problema = mysqli_real_escape_string($link, $_POST['titulo_problema']);
$descripcion_problema = mysqli_real_escape_string($link, $_POST['descripcion_problema']);
$prioridad = mysqli_real_escape_string($link, $_POST['prioridad']);
$equipos_afectados = mysqli_real_escape_string($link, $_POST['equipos_afectados']);
$labor_realizar = mysqli_real_escape_string($link, $_POST['labor_realizar']);
$observaciones = mysqli_real_escape_string($link, $_POST['observaciones']);
$problema_original = mysqli_real_escape_string($link, $_POST['problema']);
$id_usuario_registro = $_SESSION['cedula'];

// Procesar analistas seleccionados
$analistas_seleccionados = json_decode($_POST['analistas_seleccionados'], true);

// Procesar fondos seleccionados
$fondos_seleccionados = [];
if (isset($_POST['fondos']) && is_array($_POST['fondos'])) {
    foreach ($_POST['fondos'] as $fondo_id) {
        $fondos_seleccionados[] = mysqli_real_escape_string($link, $fondo_id);
    }
}

// Validar que haya al menos un analista seleccionado
if (empty($analistas_seleccionados)) {
    echo '<script language="javascript">
    alert("Debe seleccionar al menos un analista para la visita");
    window.history.back();
    </script>';
    exit();
}

// Validar que haya al menos un fondo seleccionado
if (empty($fondos_seleccionados)) {
    echo '<script language="javascript">
    alert("Debe seleccionar al menos un fondo para la visita");
    window.history.back();
    </script>';
    exit();
}

// Convertir array de fondos a string separado por comas
$arreglo_id_fondos = implode(',', $fondos_seleccionados);

// Función para enviar correos
function enviarCorreoVisita($destinatario, $asunto, $cuerpo, $correosistema, $passmail) {
    $email_user = $correosistema;
    $email_password = $passmail;
    $from_name = "Tecnopresta";
    
    $phpmailer = new PHPMailer();
    
    // Configuración SMTP
    $phpmailer->Username = $email_user;
    $phpmailer->Password = $email_password;
    $phpmailer->SMTPDebug = 0;
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->Host = "smtp.office365.com";
    $phpmailer->Port = 587;
    $phpmailer->IsSMTP();
    $phpmailer->SMTPAuth = true;
    $phpmailer->CharSet = 'UTF-8';
    
    // Configuración del mensaje
    $phpmailer->setFrom($phpmailer->Username, $from_name);
    $phpmailer->AddAddress($destinatario);
    $phpmailer->Subject = $asunto;
    $phpmailer->Body = $cuerpo;
    $phpmailer->IsHTML(true);
    
    return $phpmailer->Send();
}

// Función para obtener nombres de fondos por IDs
function obtenerNombresFondos($link, $ids_fondos) {
    if (empty($ids_fondos)) return [];
    
    $ids = explode(',', $ids_fondos);
    $ids_escaped = array_map(function($id) use ($link) {
        return mysqli_real_escape_string($link, $id);
    }, $ids);
    
    $ids_string = "'" . implode("','", $ids_escaped) . "'";
    $query = "SELECT fondos FROM t_fondos WHERE id_fondos IN ($ids_string)";
    $result = mysqli_query($link, $query);
    
    $nombres = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $nombres[] = $row['fondos'];
        }
    }
    
    return $nombres;
}

// Iniciar transacción para asegurar la integridad de los datos
mysqli_begin_transaction($link);

try {
    // Insertar en la tabla visitas_sitio (INCLUYENDO EL NUEVO CAMPO)
    $query_visita = "INSERT INTO visitas_sitio (
        codigo_institucion, nombre_institucion, telefono, correo_institucional, 
        direccion, persona_contacto, fecha_visita, hora_visita, titulo_problema, 
        descripcion_problema, prioridad, equipos_afectados, labor_realizar, 
        observaciones, problema_original, id_usuario_registro, arreglo_id_fondos
    ) VALUES (
        '$codigo_institucion', '$nombre_institucion', '$telefono', '$correo_institucional',
        '$direccion', '$persona_contacto', '$fecha_visita', '$hora_visita', '$titulo_problema',
        '$descripcion_problema', '$prioridad', '$equipos_afectados', '$labor_realizar',
        '$observaciones', '$problema_original', '$id_usuario_registro', '$arreglo_id_fondos'
    )";
    
    if (!mysqli_query($link, $query_visita)) {
        throw new Exception("Error al insertar la visita: " . mysqli_error($link));
    }
    
    // Obtener el ID de la visita recién insertada
    $id_visita = mysqli_insert_id($link);
    
    // Obtener nombres de fondos para los correos
    $nombres_fondos = obtenerNombresFondos($link, $arreglo_id_fondos);
    $fondos_texto = !empty($nombres_fondos) ? implode(', ', $nombres_fondos) : 'No especificados';
    
    // Array para almacenar información de analistas para el correo
    $info_analistas = [];
    
    // Insertar analistas en la tabla analistas_visita y recolectar información
    foreach ($analistas_seleccionados as $analista) {
        $id_analista = mysqli_real_escape_string($link, $analista['id']);
        
        $query_analista = "INSERT INTO analistas_visita (id_visita, id_analista) 
                           VALUES ('$id_visita', '$id_analista')";
        
        if (!mysqli_query($link, $query_analista)) {
            throw new Exception("Error al asignar analista: " . mysqli_error($link));
        }
        
        // Obtener información del analista para el correo
        $query_info_analista = "SELECT nombre, correo_analista, foto FROM t_analista 
                       WHERE id_analista = '$id_analista'";
        $result_info = mysqli_query($link, $query_info_analista);
        
        if ($result_info && $row = mysqli_fetch_assoc($result_info)) {
            $info_analistas[] = [
                'nombre' => $row['nombre'],
                'correo' => $row['correo_analista'],
                'foto' => $row['foto']  // la ruta de la foto
            ];
        }
    }
    
    // Confirmar la transacción
    mysqli_commit($link);
    
    // 🔔 ENVIAR NOTIFICACIONES POR CORREO
    
    // 1. Correo para el centro educativo
    if (!empty($correo_institucional)) {
        $asunto_centro = "Visita Técnica Programada - $nombre_institucion";
        
        $cuerpo_centro = "
        <h2 style='color:#3498db;'>Tecnopresta - Notificación de Visita Técnica</h2>
        <p>Estimado(a) $persona_contacto,</p>
        <p>Le informamos que se ha programado una visita técnica para su institución:</p>
        
        <table border='1' style='border-collapse: collapse; width: 100%;'>
            <tr style='background-color: #f2f2f2;'>
                <th style='padding: 8px; text-align: left;'>Detalle</th>
                <th style='padding: 8px; text-align: left;'>Información</th>
            </tr>
            <tr>
                <td style='padding: 8px;'><strong>Institución</strong></td>
                <td style='padding: 8px;'>$nombre_institucion</td>
            </tr>
            <tr>
                <td style='padding: 8px;'><strong>Fuente de Fondos</strong></td>
                <td style='padding: 8px;'>$fondos_texto</td>
            </tr>
            <tr>
                <td style='padding: 8px;'><strong>Fecha de Visita</strong></td>
                <td style='padding: 8px;'>$fecha_visita</td>
            </tr>
            <tr>
                <td style='padding: 8px;'><strong>Hora de Visita</strong></td>
                <td style='padding: 8px;'>$hora_visita</td>
            </tr>
            <tr>
                <td style='padding: 8px;'><strong>Persona de Contacto</strong></td>
                <td style='padding: 8px;'>$persona_contacto</td>
            </tr>
            <tr>
                <td style='padding: 8px;'><strong>Problema a tratar</strong></td>
                <td style='padding: 8px;'>$titulo_problema</td>
            </tr>
            <tr>
                <td style='padding: 8px;'><strong>Labor a realizar</strong></td>
                <td style='padding: 8px;'>$labor_realizar</td>
            </tr>
            <tr>
                <td style='padding: 8px;'><strong>Analistas asignados</strong></td>
                <td style='padding: 8px;'>
                    <div style='display: flex; flex-wrap: wrap; gap: 15px;'>";
        
        // Generar las tarjetas de cada analista con su foto
        foreach ($info_analistas as $analista) {
            $ruta_foto = $analista['foto'];
            // RUTA DEL SERVIDOR:
            $url_foto = 'http://tecnopresta.mep.go.cr/' . $ruta_foto;
            
            $cuerpo_centro .= "
                        <div style='text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 8px; min-width: 100px;'>
                            <img src='$url_foto' alt='Foto de {$analista['nombre']}' 
                                 style='width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #3498db;'>
                            <div style='margin-top: 8px; font-weight: bold; font-size: 14px;'>{$analista['nombre']}</div>
                        </div>";
        }
        
        $cuerpo_centro .= "
                    </div>
                </td>
            </tr>
            <tr>
                <td style='padding: 8px;'><strong>Prioridad</strong></td>
                <td style='padding: 8px;'>$prioridad</td>
            </tr>
            <tr>
                <td style='padding: 8px;'><strong>Observaciones</strong></td>
                <td style='padding: 8px;'>$observaciones</td>
            </tr>
        </table>
        
        <p><strong>Dirección:</strong> $direccion</p>
        <p><strong>Teléfono:</strong> $telefono</p>
        
        <p>Por favor, asegúrese de estar disponible en la fecha y hora programadas.</p>
        <p>Saludos cordiales,<br>Equipo de Soporte Técnico - Tecnopresta</p>
        ";
        
        // Enviar correo al centro educativo
        enviarCorreoVisita($correo_institucional, $asunto_centro, $cuerpo_centro, $correo, $passemail);
    }
    
    // 2. Correos para los analistas asignados
    foreach ($info_analistas as $analista) {
        if (!empty($analista['correo'])) {
            $asunto_analista = "Asignación de Visita Técnica - $fecha_visita";
            
            $cuerpo_analista = "
            <h2 style='color:#3498db;'>Tecnopresta - Asignación de Visita Técnica</h2>
            <p>Estimado(a) {$analista['nombre']},</p>
            <p>Se le ha asignado una visita técnica con los siguientes detalles:</p>
            
            <table border='1' style='border-collapse: collapse; width: 100%;'>
                <tr style='background-color: #f2f2f2;'>
                    <th style='padding: 8px; text-align: left;'>Detalle</th>
                    <th style='padding: 8px; text-align: left;'>Información</th>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Institución</strong></td>
                    <td style='padding: 8px;'>$nombre_institucion</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Código</strong></td>
                    <td style='padding: 8px;'>$codigo_institucion</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Fuente de Fondos</strong></td>
                    <td style='padding: 8px;'>$fondos_texto</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Fecha de Visita</strong></td>
                    <td style='padding: 8px;'>$fecha_visita</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Hora de Visita</strong></td>
                    <td style='padding: 8px;'>$hora_visita</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Persona de Contacto</strong></td>
                    <td style='padding: 8px;'>$persona_contacto</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Teléfono</strong></td>
                    <td style='padding: 8px;'>$telefono</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Problema a tratar</strong></td>
                    <td style='padding: 8px;'>$titulo_problema</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Descripción del problema</strong></td>
                    <td style='padding: 8px;'>$descripcion_problema</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Labor a realizar</strong></td>
                    <td style='padding: 8px;'>$labor_realizar</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Equipos afectados</strong></td>
                    <td style='padding: 8px;'>$equipos_afectados</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Prioridad</strong></td>
                    <td style='padding: 8px;'>$prioridad</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Observaciones</strong></td>
                    <td style='padding: 8px;'>$observaciones</td>
                </tr>
                <tr>
                    <td style='padding: 8px;'><strong>Dirección</strong></td>
                    <td style='padding: 8px;'>$direccion</td>
                </tr>
            </table>
            
            <p>Por favor, confirme su disponibilidad y prepare los recursos necesarios.</p>
            <p>Saludos cordiales,<br>Equipo de Soporte Técnico - Tecnopresta</p>
            ";
            
            // Enviar correo al analista
            enviarCorreoVisita($analista['correo'], $asunto_analista, $cuerpo_analista, $correo, $passemail);
        }
    }

    echo '<script language = javascript>
    alert("Visita registrada y notificaciones enviadas correctamente");
    self.location = "administracion_plataforma_soporte.php";
    </script>';
    exit;  

    
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    mysqli_rollback($link);
    
    // Registrar error
    error_log("Error al procesar visita: " . $e->getMessage());
    
    // Mostrar mensaje de error
    echo '<script language="javascript">
    alert("Error al procesar la solicitud: ' . addslashes($e->getMessage()) . '");
    window.history.back();
    </script>';
    exit();
}

// Cerrar conexión
mysqli_close($link);
?>