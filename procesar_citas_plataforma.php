<?php
session_start();

// Verificar permisos del usuario
$tienellave = in_array($_SESSION['tipo'] ?? 0, [1,7]);

if ($tienellave == false){
    echo '<script language = javascript>
    alert("No tienes permisos para acceder a esta funcionalidad")
    self.location = "index.html"
    </script>';
    exit();
}

// Incluir conexión y configuración de correo
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
require_once("variablesemail.php"); // aquí están $correo y $passemail

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require "/home/tecnopresta/PHPMailer/src/SMTP.php";
require "/home/tecnopresta/PHPMailer/src/PHPMailer.php";
require "/home/tecnopresta/PHPMailer/src/Exception.php";

// Obtener datos del usuario logueado
$logusuario = $_SESSION['cedula'] ?? '';
$lognombre = $_SESSION['nombre'] ?? '';
$logtipo = $_SESSION['tipo'] ?? 0;
$logcodigo = $_SESSION['codigo'] ?? '';

// Verificar si se recibieron datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recibir datos del formulario
    $eventSubject = $_POST['eventSubject'] ?? '';
    $eventStartTime = $_POST['eventStartTime'] ?? '';
    $eventDuration = $_POST['eventDuration'] ?? '';
    $eventDescription = $_POST['eventDescription'] ?? '';
    $sendReminder = isset($_POST['sendReminder']) ? 'S' : 'N';
    $teamsMeetingLink = $_POST['teamsMeetingLink'] ?? '';
    $eventId = $_POST['eventId'] ?? '';
    $userEmail = $_POST['userEmail'] ?? '';
    $userName = $_POST['userName'] ?? '';
    $accessToken = $_POST['accessToken'] ?? '';
    
    // Procesar destinatarios
    $destinatarios_array = [];
    if (isset($_POST['destinatarios']) && is_array($_POST['destinatarios'])) {
        $destinatarios_array = $_POST['destinatarios'];
    }
    
    // Convertir array de destinatarios a JSON para almacenamiento
    $destinatarios_json = json_encode($destinatarios_array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
    // Validar datos esenciales
    if (empty($eventSubject) || empty($eventStartTime) || empty($teamsMeetingLink)) {
        echo '<script>
            alert("Faltan datos esenciales: asunto, fecha/hora o enlace de Teams");
            window.history.back();
        </script>';
        exit();
    }
    
    // Convertir fecha al formato MySQL
    $fecha_hora_mysql = date('Y-m-d H:i:s', strtotime($eventStartTime));
    
    // Insertar en la base de datos
    $sql_insert = "INSERT INTO t_control_citas_teams (
        cedula_creador, 
        nombre_creador,
        asunto, 
        fecha_hora_inicio, 
        duracion_minutos, 
        descripcion, 
        enlace_teams, 
        evento_id, 
        recordatorio, 
        destinatarios,
        token_acceso,
        email_creador_microsoft,
        nombre_creador_microsoft
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $mysqli->prepare($sql_insert);
    
    if ($stmt) {
        $stmt->bind_param(
            "ssssissssssss",
            $logusuario,
            $lognombre,
            $eventSubject,
            $fecha_hora_mysql,
            $eventDuration,
            $eventDescription,
            $teamsMeetingLink,
            $eventId,
            $sendReminder,
            $destinatarios_json,
            $accessToken,
            $userEmail,
            $userName
        );
        
        if ($stmt->execute()) {
            $id_cita = $stmt->insert_id;
            $stmt->close();
            
            // Generar archivo ICS
            $ics_content = generarArchivoICS($eventSubject, $eventStartTime, $eventDuration, $eventDescription, $teamsMeetingLink);
            
            if ($ics_content) {
                // Actualizar que se generó el ICS
                $mysqli->query("UPDATE t_control_citas_teams SET archivo_ics_generado = 'S' WHERE id = $id_cita");
                
                // Enviar correos a destinatarios
                $correos_enviados = enviarCorreosCita(
                    $destinatarios_array, 
                    $eventSubject, 
                    $eventStartTime, 
                    $eventDuration, 
                    $eventDescription, 
                    $teamsMeetingLink, 
                    $ics_content,
                    $logusuario,
                    $lognombre
                );
                
                if ($correos_enviados) {
                    // Actualizar que se enviaron correos
                    $mysqli->query("UPDATE t_control_citas_teams SET correos_enviados = 'S' WHERE id = $id_cita");
                    
                    // Mostrar éxito
                    mostrarExito($id_cita, $eventSubject, count($destinatarios_array));
                } else {
                    mostrarError("Cita guardada pero hubo problemas enviando algunos correos. ID: $id_cita");
                }
            } else {
                mostrarError("Cita guardada pero no se pudo generar el archivo ICS. ID: $id_cita");
            }
            
        } else {
            mostrarError("Error al guardar la cita en la base de datos: " . $mysqli->error);
        }
    } else {
        mostrarError("Error preparando la consulta: " . $mysqli->error);
    }
    
} else {
    mostrarErrorMetodo();
}

// FUNCIONES AUXILIARES

function generarArchivoICS($asunto, $fechaInicio, $duracion, $descripcion, $enlace) {
    // Configurar zona horaria de Costa Rica
    date_default_timezone_set('America/Costa_Rica');
    
    // Crear objetos DateTime con zona horaria explícita
    $timezone = new DateTimeZone('America/Costa_Rica');
    $startDateTime = new DateTime($fechaInicio, $timezone);
    
    // Calcular fecha de fin
    $endDateTime = clone $startDateTime;
    $endDateTime->add(new DateInterval('PT' . $duracion . 'M'));
    
    // Formatear fechas para ICS (SIN la Z, que indica UTC)
    $dtstart = $startDateTime->format('Ymd\THis');
    $dtend = $endDateTime->format('Ymd\THis');
    $dtstamp = date('Ymd\THis');
    
    // Generar UID único
    $uid = uniqid() . '@tecnopresta.mep.go.cr';
    
    // Crear contenido ICS con zona horaria específica
    $ics = "BEGIN:VCALENDAR\r\n";
    $ics .= "VERSION:2.0\r\n";
    $ics .= "PRODID:-//Tecnopresta//Cita Teams//ES\r\n";
    $ics .= "CALSCALE:GREGORIAN\r\n";
    $ics .= "METHOD:REQUEST\r\n";
    
    // Especificar la zona horaria
    $ics .= "BEGIN:VTIMEZONE\r\n";
    $ics .= "TZID:America/Costa_Rica\r\n";
    $ics .= "BEGIN:STANDARD\r\n";
    $ics .= "DTSTART:19700101T000000\r\n";
    $ics .= "TZOFFSETFROM:-0600\r\n";
    $ics .= "TZOFFSETTO:-0600\r\n";
    $ics .= "TZNAME:CST\r\n";
    $ics .= "END:STANDARD\r\n";
    $ics .= "END:VTIMEZONE\r\n";
    
    $ics .= "BEGIN:VEVENT\r\n";
    $ics .= "UID:$uid\r\n";
    $ics .= "DTSTAMP:$dtstamp\r\n";
    $ics .= "DTSTART;TZID=America/Costa_Rica:$dtstart\r\n";
    $ics .= "DTEND;TZID=America/Costa_Rica:$dtend\r\n";
    $ics .= "SUMMARY:" . ical_escape($asunto) . "\r\n";
    $ics .= "DESCRIPTION:" . ical_escape($descripcion . "\\n\\nEnlace: " . $enlace) . "\r\n";
    $ics .= "LOCATION:" . ical_escape($enlace) . "\r\n";
    $ics .= "URL:" . $enlace . "\r\n";
    $ics .= "STATUS:CONFIRMED\r\n";
    $ics .= "SEQUENCE:0\r\n";
    $ics .= "ORGANIZER;CN=Tecnopresta:mailto:no-reply@tecnopresta.mep.go.cr\r\n";
    $ics .= "END:VEVENT\r\n";
    $ics .= "END:VCALENDAR\r\n";
    
    return $ics;
}
function ical_escape($string) {
    $string = str_replace('\\', '\\\\', $string);
    $string = str_replace(';', '\;', $string);
    $string = str_replace(',', '\,', $string);
    $string = str_replace("\n", '\\n', $string);
    $string = str_replace("\r", '', $string);
    return $string;
}

function enviarCorreosCita($destinatarios, $asunto, $fechaInicio, $duracion, $descripcion, $enlace, $ics_content, $cedula_creador, $nombre_creador) {
    global $correo, $passemail;
    
    // Configurar fechas para el correo
    $startDateTime = new DateTime($fechaInicio);
    $endDateTime = clone $startDateTime;
    $endDateTime->add(new DateInterval('PT' . $duracion . 'M'));
    
    $fecha_formateada = $startDateTime->format('d/m/Y');
    $hora_inicio = $startDateTime->format('H:i');
    $hora_fin = $endDateTime->format('H:i');
    
    $enviados_exitosamente = true;
    
    foreach ($destinatarios as $dest) {
        $nombre_dest = $dest['nombre'] ?? '';
        $correo_dest = $dest['correo'] ?? '';
        
        if (empty($correo_dest)) continue;
        
        // Crear PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = $correo;
            $mail->Password = $passemail;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';
            
            // Remitente y destinatario
            $mail->setFrom($correo, 'Tecnopresta - Sistema de Citas');
            $mail->addAddress($correo_dest, $nombre_dest);
            
            // Asunto y cuerpo del correo
            $mail->Subject = "Invitación a reunión: " . $asunto;
            
            $cuerpo = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #0078d4; color: white; padding: 20px; text-align: center; }
                    .content { background-color: #f9f9f9; padding: 20px; }
                    .event-details { background-color: white; border: 1px solid #ddd; padding: 15px; margin: 15px 0; }
                    .footer { background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #666; }
                    .button { background-color: #0078d4; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Tecnopresta - Invitación a Reunión</h2>
                    </div>
                    <div class="content">
                        <p>Estimado(a) ' . htmlspecialchars($nombre_dest) . ',</p>
                        <p>Ha sido invitado(a) a una reunión programada por <strong>' . htmlspecialchars($nombre_creador) . '</strong> (Cédula: ' . htmlspecialchars($cedula_creador) . ').</p>
                        
                        <div class="event-details">
                            <h3>📅 Detalles de la reunión:</h3>
                            <p><strong>Asunto:</strong> ' . htmlspecialchars($asunto) . '</p>
                            <p><strong>Fecha:</strong> ' . $fecha_formateada . '</p>
                            <p><strong>Hora:</strong> ' . $hora_inicio . ' - ' . $hora_fin . ' (' . $duracion . ' minutos)</p>
                            <p><strong>Descripción:</strong><br>' . nl2br(htmlspecialchars($descripcion)) . '</p>
                            <p><strong>Enlace de Teams:</strong> <a href="' . htmlspecialchars($enlace) . '">' . htmlspecialchars($enlace) . '</a></p>
                        </div>
                        
                        <p style="text-align: center; margin: 20px 0;">
                            <a href="' . htmlspecialchars($enlace) . '" class="button">🔗 Unirse a la reunión</a>
                        </p>
                        
                        <p><strong>Archivo adjunto:</strong> Se incluye un archivo .ics que puede agregar a su calendario.</p>
                    </div>
                    <div class="footer">
                        <p>Este correo fue generado automáticamente por el Sistema de Control de Citas de Tecnopresta.</p>
                        <p>Si tiene alguna duda, por favor contacte al creador de la reunión.</p>
                    </div>
                </div>
            </body>
            </html>';
            
            $mail->isHTML(true);
            $mail->Body = $cuerpo;
            $mail->AltBody = "Invitación a reunión: $asunto\nFecha: $fecha_formateada\nHora: $hora_inicio - $hora_fin\nDuración: $duracion minutos\nDescripción: $descripcion\nEnlace: $enlace\n\nCreador: $nombre_creador ($cedula_creador)";
            
            // Adjuntar archivo ICS
            $mail->addStringAttachment($ics_content, 'reunion-tecnopresta.ics', 'base64', 'text/calendar');
            
            // Enviar correo
            if (!$mail->send()) {
                error_log("Error enviando correo a $correo_dest: " . $mail->ErrorInfo);
                $enviados_exitosamente = false;
            }
            
        } catch (Exception $e) {
            error_log("Excepción enviando correo a $correo_dest: " . $e->getMessage());
            $enviados_exitosamente = false;
        }
    }
    
    return $enviados_exitosamente;
}

function mostrarExito($id_cita, $asunto, $num_destinatarios) {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cita Programada Exitosamente</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #f0f2f5 0%, #e6e9f0 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                padding: 20px;
            }
            .container {
                background-color: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
                text-align: center;
                max-width: 600px;
                border-left: 5px solid #28a745;
            }
            .success-icon {
                color: #28a745;
                font-size: 64px;
                margin-bottom: 20px;
            }
            h1 {
                color: #28a745;
                margin-bottom: 20px;
            }
            .details {
                background-color: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
                text-align: left;
            }
            .details p {
                margin: 10px 0;
            }
            .buttons {
                display: flex;
                gap: 15px;
                justify-content: center;
                margin-top: 30px;
                flex-wrap: wrap;
            }
            .btn {
                padding: 12px 30px;
                border-radius: 8px;
                border: none;
                font-weight: 600;
                cursor: pointer;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                transition: all 0.3s ease;
            }
            .btn-primary {
                background: linear-gradient(135deg, #0078d4, #106ebe);
                color: white;
            }
            .btn-secondary {
                background: linear-gradient(135deg, #6c757d, #5a6268);
                color: white;
            }
            .btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            }
            @media (max-width: 768px) {
                .container { padding: 20px; }
                .buttons { flex-direction: column; }
                .btn { width: 100%; justify-content: center; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>✅ Cita Programada Exitosamente</h1>
            
            <div class="details">
                <p><strong>ID de Cita:</strong> ' . $id_cita . '</p>
                <p><strong>Asunto:</strong> ' . htmlspecialchars($asunto) . '</p>
                <p><strong>Destinatarios:</strong> ' . $num_destinatarios . ' persona(s) notificada(s)</p>
                <p><strong>Estado:</strong> Registrada en el sistema de control</p>
            </div>
            
            <p>La cita ha sido registrada en el sistema y se han enviado las invitaciones por correo electrónico con el archivo adjunto para calendario.</p>
            
            <div class="buttons">
                <a href="ver_citas_de_soportista.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Volver al Inicio
                </a>
                <a href="administrar_citas_teams.php" class="btn btn-secondary">
                    <i class="fas fa-calendar-check"></i> Ver Mis Citas
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Imprimir Confirmación
                </button>
            </div>
        </div>
    </body>
    </html>';
}

function mostrarError($mensaje) {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error en Proceso</title>
        <style>
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #f0f2f5 0%, #e6e9f0 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .container {
                background-color: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
                text-align: center;
                max-width: 600px;
                border-left: 5px solid #dc3545;
            }
            .error-icon {
                color: #dc3545;
                font-size: 48px;
                margin-bottom: 20px;
            }
            h1 { color: #dc3545; margin-bottom: 20px; }
            .btn {
                background: linear-gradient(135deg, #0078d4, #106ebe);
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 16px;
                margin-top: 20px;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 10px;
            }
            .btn:hover { background: linear-gradient(135deg, #106ebe, #0a5a9e); }
        </style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body>
        <div class="container">
            <div class="error-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <h1>Error en el Proceso</h1>
            <p>' . htmlspecialchars($mensaje) . '</p>
            <a href="javascript:history.back()" class="btn">
                <i class="fas fa-arrow-left"></i> Volver e Intentar Nuevamente
            </a>
        </div>
    </body>
    </html>';
}

function mostrarErrorMetodo() {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - Método No Permitido</title>
        <style>
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #f0f2f5 0%, #e6e9f0 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .container {
                background-color: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
                text-align: center;
                max-width: 600px;
                border-left: 5px solid #dc3545;
            }
            .error-icon {
                color: #dc3545;
                font-size: 48px;
                margin-bottom: 20px;
            }
            h1 { color: #dc3545; margin-bottom: 20px; }
            .btn {
                background: linear-gradient(135deg, #0078d4, #106ebe);
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 16px;
                margin-top: 20px;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 10px;
            }
            .btn:hover { background: linear-gradient(135deg, #106ebe, #0a5a9e); }
        </style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body>
        <div class="container">
            <div class="error-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <h1>Método No Permitido</h1>
            <p>Esta página solo acepta solicitudes POST desde el formulario de creación de eventos.</p>
            <a href="ver_citas_de_soportista.php" class="btn"><i class="fas fa-home"></i> Volver al Inicio</a>
        </div>
    </body>
    </html>';
}
?>