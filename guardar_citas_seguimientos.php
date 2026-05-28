<?php
session_start();
// Buffer de salida
ob_start();

require_once("conexion.php");
require_once("variablesemail.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require "/home/tecnopresta/PHPMailer/src/SMTP.php";
require "/home/tecnopresta/PHPMailer/src/PHPMailer.php";
require "/home/tecnopresta/PHPMailer/src/Exception.php";

// ESTABLECER HEADER JSON AL INICIO - ESTO ES CRÍTICO
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(0);

// Log de errores
ini_set('log_errors', 1);
ini_set('error_log', '/home/tecnopresta/php_errors.log');

// Función para enviar respuesta JSON consistente
function sendJsonResponse($success, $data = [], $error = '') {
    // Limpiar buffer antes de enviar JSON
    while (ob_get_level() > 0) {
        $output = ob_get_clean();
        if ($output && trim($output) !== '') {
            error_log("Salida no esperada detectada: " . substr($output, 0, 200));
        }
    }
    
    $response = ['success' => $success];
    if ($success) {
        $response = array_merge($response, $data);
    } else {
        $response['error'] = $error;
    }
    
    // Forzar la salida JSON
    echo json_encode($response);
    exit();
}

// Verificar permisos
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if (!$tienellave) {
    sendJsonResponse(false, [], 'No tienes permisos para realizar esta acción');
}

// Configurar conexión
$link = $mysqli;
if (mysqli_connect_errno()) {
    sendJsonResponse(false, [], 'Error de conexión a MySQL');
}

if (!mysqli_set_charset($link, "utf8")) {
    sendJsonResponse(false, [], 'Error cargando charset UTF-8');
}

// Función para generar archivo ICS (SIMPLIFICADA)
function generarICS($evento, $teamsUrl = null) {
    try {
        $location = $teamsUrl ? "Microsoft Teams: " . $teamsUrl : "Reunión Virtual - TecnoPresta MEP";
        
        // Escapar caracteres para ICS
        $descripcion = str_replace(["\r\n", "\n", "\r"], "\\n", $evento['descripcion']);
        $asunto = $evento['asunto'];
        
        $ics = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//TecnoPresta//Citas//ES
CALSCALE:GREGORIAN
METHOD:REQUEST
BEGIN:VEVENT
UID:" . uniqid() . "@tecnopresta.mep.go.cr
DTSTAMP:" . gmdate('Ymd\THis\Z') . "
ORGANIZER;CN={$evento['organizador_nombre']}:mailto:{$evento['organizador_correo']}
ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN={$evento['destinatario_nombre']}:mailto:{$evento['destinatario_correo']}
DTSTART:" . gmdate('Ymd\THis\Z', strtotime($evento['inicio'])) . "
DTEND:" . gmdate('Ymd\THis\Z', strtotime($evento['fin'])) . "
SUMMARY:{$asunto}
DESCRIPTION:{$descripcion}
LOCATION:{$location}
SEQUENCE:0
PRIORITY:5
CLASS:PUBLIC
STATUS:CONFIRMED
TRANSP:OPAQUE
END:VEVENT
END:VCALENDAR";
        
        return $ics;
    } catch (Exception $e) {
        error_log("Error generando ICS: " . $e->getMessage());
        return null;
    }
}

// Función MEJORADA para enviar correo con ICS - CAPTURA TODOS LOS ERRORES
function enviarCorreoCita($destinatario, $asunto, $cuerpo, $correosistema, $passmail, $icsContent = null) {
    try {
        // Verificar que los parámetros necesarios estén presentes
        if (empty($correosistema) || empty($passmail)) {
            throw new Exception('Credenciales de correo no configuradas');
        }
        
        $email_user = $correosistema;
        $email_password = $passmail;
        $from_name = "Tecnopresta MEP";
        
        $phpmailer = new PHPMailer(true);
        
        // Configuración SMTP MEJORADA
        $phpmailer->isSMTP();
        $phpmailer->Host = "smtp.office365.com";
        $phpmailer->Port = 587;
        $phpmailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $email_user;
        $phpmailer->Password = $email_password;
        $phpmailer->SMTPDebug = 0; // Mantener en 0 para producción
        $phpmailer->CharSet = 'UTF-8';
        $phpmailer->Encoding = 'base64';
        
        // Configuración del mensaje
        $phpmailer->setFrom($email_user, $from_name);
        $phpmailer->addAddress($destinatario);
        $phpmailer->Subject = $asunto;
        $phpmailer->Body = $cuerpo;
        $phpmailer->isHTML(true);
        
        // Adjuntar ICS si se proporciona
        if ($icsContent) {
            $phpmailer->addStringAttachment($icsContent, 'invitacion-cita.ics', 'base64', 'text/calendar; method=REQUEST');
        }
        
        $resultado = $phpmailer->send();
        
        if (!$resultado) {
            throw new Exception('PHPMailer Error: ' . $phpmailer->ErrorInfo);
        }
        
        return $resultado;
        
    } catch (Exception $e) {
        error_log("Error CRÍTICO en enviarCorreoCita: " . $e->getMessage());
        return false;
    }
}

// MAIN EXECUTION - CON MANEJO MEJORADO DE CORREO
try {
    // Recoger datos del formulario
    $asunto = $_POST['asunto'] ?? '';
    $detalle = $_POST['detalle'] ?? '';
    $a_quien_cita = $_POST['a_quien_cita'] ?? '';
    $quien_realiza = $_POST['quien_realiza'] ?? '';
    $correo_realiza = $_POST['correo_realiza'] ?? '';
    $cedula = $_POST['cedula'] ?? '';
    $correo_cita = $_POST['correo_cita'] ?? '';
    $fecha_reunion = $_POST['fecha_reunion'] ?? '';
    $hora_inicio = $_POST['hora_inicio'] ?? '';
    $hora_fin = $_POST['hora_fin'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $id_fondos = $_POST['id_fondos'] ?? '';
    $enlace_teams = $_POST['enlace_teams'] ?? '';

    error_log("=== INICIO PROCESO CITA ===");
    error_log("Correo destino: " . $correo_cita);

    // Validaciones básicas
    $required_fields = [
        'asunto' => 'Asunto de la reunión',
        'detalle' => 'Detalle de la reunión', 
        'a_quien_cita' => 'Persona a citar',
        'correo_cita' => 'Correo de contacto',
        'fecha_reunion' => 'Fecha de reunión',
        'hora_inicio' => 'Hora de inicio',
        'hora_fin' => 'Hora de fin',
        'id_fondos' => 'Fondos'
    ];
    
    foreach ($required_fields as $field => $name) {
        if (empty($_POST[$field])) {
            throw new Exception("Campo requerido faltante: $name");
        }
    }

    // Validar formato de correo
    if (!filter_var($correo_cita, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El correo electrónico no tiene un formato válido");
    }

    // Validar que la fecha no sea anterior a hoy
    $fecha_actual = date('Y-m-d');
    if ($fecha_reunion < $fecha_actual) {
        throw new Exception("La fecha de la reunión no puede ser anterior a la fecha actual");
    }

    // Validar que hora fin sea mayor que hora inicio
    if ($hora_fin <= $hora_inicio) {
        throw new Exception("La hora de finalización debe ser mayor a la hora de inicio");
    }

    // Validar enlace Teams si se proporciona
    if (!empty($enlace_teams) && !filter_var($enlace_teams, FILTER_VALIDATE_URL)) {
        throw new Exception("El enlace de Teams proporcionado no es una URL válida");
    }

    // Insertar en base de datos
    mysqli_begin_transaction($link);

    $query = "INSERT INTO t_citas_siguimientos 
              (asunto_reunion, detalle_reunion, a_quien_cita, quien_realiza, correo_realiza, cedula, correo_cita, fecha_reunion, hora_inicio, hora_fin, codigo, id_fondos, enlace_teams, estado) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";

    $stmt = $link->prepare($query);
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $link->error);
    }

    $stmt->bind_param("sssssssssssis", $asunto, $detalle, $a_quien_cita, $quien_realiza, $correo_realiza, $cedula, $correo_cita, $fecha_reunion, $hora_inicio, $hora_fin, $codigo, $id_fondos, $enlace_teams);

    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando consulta: " . $stmt->error);
    }

    $id_cita = $stmt->insert_id;
    $stmt->close();

    error_log("✅ Cita insertada con ID: " . $id_cita);

    // ENVÍO DE CORREO CON ICS - MANEJO SEGURO
    $correoSuccess = false;
    $correoMessage = 'No intentado';
    
    // VERIFICAR CRÍTICAMENTE LAS VARIABLES DE EMAIL ANTES DE PROCEDER
    if (!isset($correo) || empty($correo) || !isset($passemail) || empty($passemail)) {
        error_log("❌ Variables de correo no configuradas o vacías");
        $correoMessage = 'Configuración de correo no disponible';
        $correoSuccess = false;
    } else {
        try {
            error_log("📧 Intentando enviar correo a: " . $correo_cita);
            error_log("📧 Usando cuenta: " . $correo);
            
            // Generar contenido ICS
            $datosICS = [
                'organizador_nombre' => $quien_realiza,
                'organizador_correo' => $correo_realiza,
                'destinatario_nombre' => $a_quien_cita,
                'destinatario_correo' => $correo_cita,
                'asunto' => $asunto,
                'descripcion' => $detalle,
                'inicio' => $fecha_reunion . ' ' . $hora_inicio,
                'fin' => $fecha_reunion . ' ' . $hora_fin
            ];
            
            // Incluir enlace de Teams si se proporcionó
            $contenidoICS = generarICS($datosICS, $enlace_teams);
            
            // Preparar contenido del correo
            $enlaceTeamsHTML = !empty($enlace_teams) ? 
                "<div style='margin: 15px 0; padding: 15px; background-color: #e8f4fd; border-radius: 8px; border-left: 4px solid #0066cc;'>
                    <strong style='color: #0066cc;'>🔗 Enlace de reunión Teams:</strong><br>
                    <a href='{$enlace_teams}' style='color: #0066cc; word-break: break-all;'>{$enlace_teams}</a>
                </div>" : 
                "";
                
            $asuntoCorreo = "Invitación a cita: " . $asunto;
            $cuerpoCorreo = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #2c3e50; border-bottom: 2px solid #4a6baf; padding-bottom: 10px;'>Invitación a Reunión - TecnoPresta MEP</h2>
                    <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 15px 0;'>
                        <p><strong>📋 Asunto:</strong> {$asunto}</p>
                        <p><strong>📝 Detalles:</strong> {$detalle}</p>
                        <p><strong>📅 Fecha:</strong> {$fecha_reunion}</p>
                        <p><strong>⏰ Hora:</strong> {$hora_inicio} - {$hora_fin}</p>
                        <p><strong>👤 Organizador:</strong> {$quien_realiza} ({$correo_realiza})</p>
                        {$enlaceTeamsHTML}
                    </div>
                    <p style='color: #666; margin-top: 20px;'>
                        <strong>📎 Se ha adjuntado un archivo .ics</strong> a este correo para agregar la cita a su calendario.
                    </p>
                    <p style='color: #666;'>Por favor, confirme su asistencia.</p>
                    <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                    <small style='color: #999;'>Este correo fue generado automáticamente por el sistema TecnoPresta MEP</small>
                </div>
            ";
            
            // Enviar correo - ESTA ES LA LÍNEA CRÍTICA
            $correoSuccess = enviarCorreoCita($correo_cita, $asuntoCorreo, $cuerpoCorreo, $correo, $passemail, $contenidoICS);
            $correoMessage = $correoSuccess ? 'Correo enviado exitosamente' : 'Error en el envío del correo';
            error_log("📧 Resultado del envío: " . ($correoSuccess ? 'ÉXITO' : 'FALLO') . " - " . $correoMessage);
            
        } catch (Exception $e) {
            $correoMessage = 'Excepción en envío de correo: ' . $e->getMessage();
            error_log("❌ Excepción en correo: " . $e->getMessage());
            $correoSuccess = false;
        }
    }
    
    // Confirmar transacción
    mysqli_commit($link);
    
    error_log("✅ Proceso completado - Enviando respuesta JSON");
    
    // Respuesta JSON exitosa - INCLUSO SI EL CORREO FALLA
    sendJsonResponse(true, [
        'id_cita' => $id_cita,
        'teams' => [
            'success' => !empty($enlace_teams),
            'message' => !empty($enlace_teams) ? 'Enlace de Teams incluido' : 'Sin enlace de Teams',
            'joinUrl' => $enlace_teams
        ],
        'correo' => [
            'success' => $correoSuccess,
            'message' => $correoMessage
        ],
        'message' => 'Cita creada exitosamente' . ($correoSuccess ? ' y correo enviado' : ' (correo no enviado)')
    ]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if (isset($link)) {
        mysqli_rollback($link);
    }
    
    error_log("❌ Error general en guardar_citas_seguimientos: " . $e->getMessage());
    sendJsonResponse(false, [], $e->getMessage());
} finally {
    // Cerrar conexión
    if (isset($link)) {
        $link->close();
    }
    error_log("=== FIN PROCESO CITA ===");
}
?>