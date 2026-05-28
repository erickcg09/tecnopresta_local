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
if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

$idcaso = $_POST['idcaso'];
$linkchat = "https://tecnopresta.mep.go.cr/proyecto_chat.php?gps=".$idcaso;
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$mensajito = $lognombre." ".$_POST['mensajito'];
$estafecha = date('d-m-Y h:i:s');

require_once("variablesemail.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

date_default_timezone_set('America/Costa_Rica'); //configuro un nuevo timezone

function generarResumen($mensaje, $longitud = 100) {
    if (strlen($mensaje) <= $longitud) {
        return $mensaje;
    } else {
        return substr($mensaje, 0, $longitud) . '...';
    }
}

function verificacion($correousuario, $correosistema, $passmail, $fechora, $resumen, $linkchat) { 
    $fh = utf8_encode($fechora);
    $para = utf8_encode($correousuario);
    $email_user = utf8_encode($correosistema);
    $email_password = utf8_encode($passmail);
    $the_subject = utf8_encode("Notificaci¨®n // Centro de Soporte Educativo");
    $address_to = utf8_encode($para);
    $from_name = utf8_encode("Tecnopresta");
    $phpmailer = new PHPMailer();
    $texto = utf8_encode("Test email");
    // ---------- datos de la cuenta de Tecnopresta -------------------------------
    $phpmailer->Username = $email_user;
    $phpmailer->Password = $email_password; 
    //----------------------------------------------------------------------- 

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

    $phpmailer->Subject = $the_subject;    
    $phpmailer->Body .= "<h1 style='color:#3498db;'>Foro Chat</h1>";
    $phpmailer->Body .= "<p>Has recibido un nuevo mensaje:</p>";
    $phpmailer->Body .= "<p><strong>Resumen:</strong> " . utf8_encode($resumen) . "</p>";
    $phpmailer->Body .= "<p>Para leer el mensaje completo, haz clic en el siguiente enlace: <a href='" . utf8_encode($linkchat) . "'>" . utf8_encode($linkchat) . "</a></p>";
    $phpmailer->Body .= "<table border=\"1\" WIDTH=\"600\" HEIGHT=\"500\">
                          <tr>
                            <td align=\"center\" width=\"20%\">Asunto</td>
                            <td align=\"center\">Usted ha recibido un mensaje en el Foro Chat de Soporte T¨¦cnico, ingrese a tecnopresta.mep.go.cr, al Centro de Soporte, busque su caso y responda</td>
                          </tr>
                          <tr>
                            <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/reloj.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                            <td align=\"center\">" . utf8_encode($fh) . "</td>
                          </tr>
                          <tr>
                            <td colspan=\"2\" align=\"center\" ><img src=\"http://tecnopresta.mep.go.cr/svg/centrosoporte.png\" alt=\"\" /></td>
                          </tr>
                        </table>";
    $phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
    $phpmailer->Send();
}


$correot = "tecnopresta@mep.go.cr";
$ahora = date("d-m-Y");
$logcorreo = $_SESSION['correomep']; //correo del soportista que acepta el caso

$preguntar = mysqli_query($link, "select correo from soporte where id='$idcaso'");   
$respuesta = mysqli_fetch_array($preguntar);
$correosolicitante = $respuesta['correo'];

if (empty($_POST['idcaso'])) {
    echo '<script language = javascript>
    alert("No hay ningun mensaje")
    self.location = "plataforma_clientes.php"
    </script>';
} else {
    $sql = "INSERT INTO `t_chat_soporte` (`id`, `id_caso`, `tipo`, `mensaje`, `fecha`) VALUES (NULL, '$idcaso', '$logusuario', '$mensajito', '$ahora')";
    
    if (mysqli_query($link, $sql)) {
        // Mensaje insertado correctamente
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($link);
    }
}

mysqli_close($link);

$resumen = generarResumen($mensajito);
verificacion($correosolicitante, $correot, $passemail, $estafecha, $resumen, $linkchat);

echo '<script type="text/javascript">
    window.location = "proyecto_chat.php?gps='.$idcaso.'"
    </script>';
?>

