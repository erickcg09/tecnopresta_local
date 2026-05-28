<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 2, 3, 4, 5, 7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte con el administrador.");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}

require_once("conexion.php");
require_once("variablesemailn.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require "/home/tecnopresta/PHPMailer/src/SMTP.php";
require "/home/tecnopresta/PHPMailer/src/PHPMailer.php";
require "/home/tecnopresta/PHPMailer/src/Exception.php";

$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
$correo_funcionario_solicitante = $_SESSION['correomep'];

// Validar parámetros
if (!isset($_GET['fecha']) || !isset($_GET['hora']) || !isset($_GET['soportista'])) {
    $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Parámetros inválidos'];
    header("Location: index.html");
    exit();
}

try {
    // Iniciar transacción
    mysqli_begin_transaction($link);
    
    $fecha = mysqli_real_escape_string($link, $_GET['fecha']);
    $hora = mysqli_real_escape_string($link, $_GET['hora']);
    $soportista_id = mysqli_real_escape_string($link, $_GET['soportista']);
    
    // Verificar disponibilidad con LOCK para evitar condiciones de carrera
    $query_verificar = "SELECT id FROM citas 
                        WHERE fecha = '$fecha' AND hora_inicio = '$hora' 
                        AND soportista_id = '$soportista_id'
                        FOR UPDATE";
    
    $result = mysqli_query($link, $query_verificar);
    if (!$result) {
        throw new Exception("Error al verificar disponibilidad: " . mysqli_error($link));
    }
    
    if (mysqli_num_rows($result) > 0) {
        throw new Exception("Lo sentimos, la cita ya fue tomada por otro usuario. Por favor seleccione otro horario.");
    }

    // Insertar cita
    $hora_fin = date('H:i:s', strtotime($hora) + 1800); // 30 minutos después
    $query = "INSERT INTO citas (
                usuario_id, 
                soportista_id, 
                fecha, 
                hora_inicio, 
                hora_fin, 
                estado,
                nombre_funcionario,
                codigo,
                correo_funcionario,
                fecha_registro
              ) VALUES (
                  '".mysqli_real_escape_string($link, $_SESSION['cedula'])."',
                  '$soportista_id',
                  '$fecha',
                  '$hora',
                  '$hora_fin',
                  'pendiente',
                  '".mysqli_real_escape_string($link, $lognombre)."',
                  '".mysqli_real_escape_string($link, $logcodigo)."',
                  '".mysqli_real_escape_string($link, $correo_funcionario_solicitante)."',
                  NOW()
              )";
    
    if (!mysqli_query($link, $query)) {
        throw new Exception("Error al guardar la cita: " . mysqli_error($link));
    }

    $cita_id = mysqli_insert_id($link);

    // Obtener datos completos de la cita y soportista
    $query_cita = "SELECT 
                    c.id, c.fecha, c.hora_inicio, c.hora_fin, c.estado, 
                    c.nombre_funcionario, c.correo_funcionario,
                    s.id as soportista_id, s.nombre as nombre_soportista, s.correo as correo_soportista
                   FROM citas c
                   JOIN soportistas s ON c.soportista_id = s.id
                   WHERE c.id = $cita_id";
    
    $result_cita = mysqli_query($link, $query_cita);
    if (!$result_cita || mysqli_num_rows($result_cita) === 0) {
        throw new Exception("No se pudo recuperar la información de la cita recién creada");
    }
    
    $cita = mysqli_fetch_assoc($result_cita);

    // Función para generar archivo .ics con enlace de Teams
    function generarICS($cita) {
        $hora_inicio = date('Ymd\THis', strtotime($cita['fecha'] . ' ' . $cita['hora_inicio']));
        $hora_fin = date('Ymd\THis', strtotime($cita['fecha'] . ' ' . $cita['hora_fin']));
        
        // Generar enlace genérico de Teams con ID de cita y participantes
        $teams_link = "https://teams.microsoft.com/l/meeting/new?subject=Cita%20Soporte%20{$cita['id']}&attendees={$cita['correo_funcionario']},{$cita['correo_soportista']}";
        
        $icsContent = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Tecnopresta//Citas//ES
CALSCALE:GREGORIAN
METHOD:REQUEST
BEGIN:VEVENT
UID:" . md5($cita['id'] . time()) . "@tecnopresta.mep.go.cr
DTSTAMP:" . gmdate('Ymd\THis\Z') . "
DTSTART;TZID=America/Costa_Rica:{$hora_inicio}
DTEND;TZID=America/Costa_Rica:{$hora_fin}
SUMMARY:Cita de Soporte - {$cita['nombre_soportista']}
DESCRIPTION:Cita agendada con {$cita['nombre_soportista']}\\nSolicitante: {$cita['nombre_funcionario']} ({$cita['correo_funcionario']})\\n\\nPara unirse a la reunión: {$teams_link}\\n\\nID de Cita: {$cita['id']}
LOCATION:{$teams_link}
ORGANIZER;CN=Tecnopresta:mailto:tecnopresta@mep.go.cr
ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;
 RSVP=TRUE;CN={$cita['nombre_funcionario']}:mailto:{$cita['correo_funcionario']}
ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;
 RSVP=TRUE;CN={$cita['nombre_soportista']}:mailto:{$cita['correo_soportista']}
URL:{$teams_link}
SEQUENCE:0
STATUS:CONFIRMED
TRANSP:OPAQUE
END:VEVENT
END:VCALENDAR";

        return $icsContent;
    }

    $icsContent = generarICS($cita);

    // Función para enviar correo con el enlace de Teams destacado
    function enviarCorreoCita($destinatario, $nombre_destinatario, $cita, $icsContent) {
        global $correot, $passmail;
        
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor SMTP
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = $correot;
            $mail->Password = $passmail;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            // Remitente y destinatario
            $mail->setFrom($correot, 'Tecnopresta - Sistema de Citas');
            $mail->addAddress($destinatario, $nombre_destinatario);
            $mail->addReplyTo($correot, 'Tecnopresta');

            // Generar enlace de Teams
            $teams_link = "https://teams.microsoft.com/l/meeting/new?subject=Cita%20Soporte%20{$cita['id']}&attendees={$cita['correo_funcionario']},{$cita['correo_soportista']}";
            
            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Confirmación de Cita #' . $cita['id'];
            
            $html = '<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #3498db; color: white; padding: 10px; text-align: center; }
        .details { margin: 20px 0; }
        .teams-btn { 
            background-color: #6264A7; 
            color: white; 
            padding: 12px 20px; 
            text-decoration: none; 
            border-radius: 5px;
            display: inline-block;
            margin: 10px 0;
            font-weight: bold;
        }
        .footer { margin-top: 20px; font-size: 0.9em; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Confirmación de Cita de Soporte</h2>
        </div>
        
        <div class="details">
            <p>Hola ' . htmlspecialchars($nombre_destinatario) . ',</p>
            <p>Se ha agendado una cita de soporte con los siguientes detalles:</p>
            
            <ul>
                <li><strong>Número de cita:</strong> ' . $cita['id'] . '</li>
                <li><strong>Soportista:</strong> ' . htmlspecialchars($cita['nombre_soportista']) . '</li>
                <li><strong>Fecha:</strong> ' . $cita['fecha'] . '</li>
                <li><strong>Hora:</strong> ' . $cita['hora_inicio'] . ' - ' . $cita['hora_fin'] . '</li>
                <li><strong>Estado:</strong> ' . $cita['estado'] . '</li>
            </ul>
            
            <p>Para unirse a la reunión de Teams:</p>
            <a href="' . $teams_link . '" class="teams-btn">Unirse a la reunión en Teams</a>
            <p>O copie este enlace en su navegador:<br>
            <small>' . $teams_link . '</small></p>
            
            <p>Por favor agregue este evento a su calendario utilizando el archivo adjunto (.ics).</p>
        </div>
        
        <div class="footer">
            <p>Este es un mensaje automático, por favor no responda directamente a este correo.</p>
            <p>Tecnopresta - Ministerio de Educación Pública</p>
        </div>
    </div>
</body>
</html>';

            $mail->Body = $html;
            $mail->AltBody = "Confirmación de Cita de Soporte\n\n" .
                             "Número: " . $cita['id'] . "\n" .
                             "Soportista: " . $cita['nombre_soportista'] . "\n" .
                             "Fecha: " . $cita['fecha'] . "\n" .
                             "Hora: " . $cita['hora_inicio'] . " - " . $cita['hora_fin'] . "\n" .
                             "Estado: " . $cita['estado'] . "\n\n" .
                             "Para unirse a la reunión de Teams:\n" . $teams_link . "\n\n" .
                             "Por favor agregue este evento a su calendario utilizando el archivo adjunto (.ics).";

            // Adjuntar archivo .ics
            $mail->addStringAttachment($icsContent, 'cita_tecnopresta.ics', 'base64', 'text/calendar');

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar correo a $destinatario: " . $mail->ErrorInfo);
            return false;
        }
    }

    // Confirmar la transacción si todo está bien
    mysqli_commit($link);
    
    // Enviar correos a ambos participantes (fuera de la transacción)
    $envio_usuario = enviarCorreoCita(
        $cita['correo_funcionario'], 
        $cita['nombre_funcionario'], 
        $cita, 
        $icsContent
    );

    $envio_soportista = enviarCorreoCita(
        $cita['correo_soportista'], 
        $cita['nombre_soportista'], 
        $cita, 
        $icsContent
    );

    if (!$envio_usuario || !$envio_soportista) {
        error_log("Uno o más correos no se enviaron correctamente para la cita $cita_id");
    }

    $_SESSION['mensaje'] = [
        'tipo' => 'success', 
        'texto' => 'Cita agendada correctamente. Se han enviado las confirmaciones por correo con el enlace para la reunión en Teams.'
    ];
    header("Location: mis_citas.php");
    exit();

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    mysqli_rollback($link);
    
    $_SESSION['mensaje'] = [
        'tipo' => 'danger', 
        'texto' => $e->getMessage()
    ];
    header("Location: citas_agendar.php");
    exit();
}
?>