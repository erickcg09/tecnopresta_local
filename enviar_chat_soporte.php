<?php 
session_start();
if (!$_SESSION){
    echo '<script language = javascript>
    alert("usuario no autenticado")
    self.location = "index.html"
    </script>';
}
date_default_timezone_set('America/Costa_Rica'); //configuro un nuevo timezone
require_once("conexion.php");
$link = $mysqli;
require_once("variablesemail.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require "/home/tecnopresta/PHPMailer/src/SMTP.php";
require "/home/tecnopresta/PHPMailer/src/PHPMailer.php";
require "/home/tecnopresta/PHPMailer/src/Exception.php";

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$estafecha = date('d-m-Y h:i:s');
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

function verificacion($correousuario, $nombreusuario, $correosistema, $passmail, $fechora, $referencia, $correos, $archivo) { 
    $csoporte = $correos;
    $refe = $referencia;
    $fh = $fechora;
    $para = $correousuario;
    $completo = $nombreusuario;
    $email_user = $correosistema;
    $email_password = $passmail;
    $the_subject = "Centro de Soporte Educativo";
    $address_to = $para;
    $from_name = "Tecnopresta";
    $phpmailer = new PHPMailer();

    // Configuración del servidor SMTP
    $phpmailer->Username = $email_user;
    $phpmailer->Password = $email_password; 
    $phpmailer->SMTPDebug = 0;  // Opciones 0, 1, 2
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->Host = "smtp.office365.com"; // Office365
    $phpmailer->Port = 587;
    $phpmailer->IsSMTP(); // use SMTP
    $phpmailer->SMTPAuth = true;
    $phpmailer->CharSet = 'UTF-8';
    $phpmailer->setFrom($phpmailer->Username, $from_name);
    $phpmailer->AddAddress($address_to); // recipients email
    $phpmailer->AddEmbeddedImage('ico/bienvenida2.png', 'bienvenida', 'attachment', 'base64', 'image/png');
    $phpmailer->AddEmbeddedImage('ico/eserialb.png', 'piePagina', 'attachment', 'base64', 'image/png');

    // Adjuntar archivo si existe
    if ($archivo['error'] == UPLOAD_ERR_OK) {
        $phpmailer->addAttachment($archivo['tmp_name'], $archivo['name']);
    }

    // Contenido del correo
    $phpmailer->Subject = $the_subject;    
    $phpmailer->Body = "<!DOCTYPE html>
                        <html lang='es'>
                        <head>
                            <meta charset='UTF-8'>
                            <title>Centro de Soporte Educativo</title>
                        </head>
                        <body>";
    $phpmailer->Body .= "<h2 style='color:#3498db;'>Información del soportista asignado</h2>";
    $phpmailer->Body .= "<table border=\"1\" WIDTH=\"600\" HEIGHT=\"500\">
                          <tr>
                            <td colspan=\"2\" align=\"center\" ><img src=\"http://tecnopresta.mep.go.cr/svg/centrosoporte.png\" alt=\"\" /></td>
                          </tr>
                          <tr>
                            <td align=\"center\" width=\"20%\">Asunto</td>
                            <td align=\"center\">$refe</td>
                          </tr>
                          <tr>
                            <td align=\"center\" width=\"20%\">Soportista que le atenderá</td>
                            <td align=\"center\">$from_name</td>
                          </tr>
                          <tr>
                            <td align=\"center\" width=\"20%\">Mensaje</td>
                            <td align=\"center\">$completo</td>
                          </tr>
                          <tr>
                            <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/reloj.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                            <td align=\"center\">$fh</td>
                          </tr>
                          <tr>
                            <td colspan=\"2\" align=\"center\" ><img src=\"http://tecnopresta.mep.go.cr/svg/centrosoporte.png\" alt=\"\" /></td>
                          </tr>
                        </table>";
    $phpmailer->Body .= "</body></html>";
    $phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
    $phpmailer->Send();
}

$correot = "tecnopresta@mep.go.cr";
$correodestino = $_POST['correo'];
$correosoportista = $_POST['correosoportista'];
$asunto = $_POST['asunto']; //precarga la placa del activo al cual se hace referencia pero puede ser cambiado
$mensaje = $_POST['mensaje']; //mensaje que se desea comunicar al usuario solicitante del soporte
$archivo = $_FILES['file']; // archivo adjunto

verificacion($correodestino, $mensaje, $correot, $passemail, $estafecha, $asunto, $correosoportista, $archivo);

echo '<script language = javascript>
        alert("Guardado")
        self.location = "miscasos.php"
      </script>';
?>