<?php 
session_start();
if (!$_SESSION){
    echo '<script language = javascript>
    alert("usuario no autenticado")
    self.location = "index.html"
    </script>';
}
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
$estafecha=date('d-m-Y h:i:s');
if (mysqli_connect_errno())
{
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
} else {
    // Charset configurado correctamente
}
date_default_timezone_set('America/Costa_Rica'); //configuro un nuevo timezone

function verificacion ($correousuario,$nombreusuario,$correosistema,$passmail,$fechora){ 
    $fh=$fechora;
    $para = $correousuario;
    $completo = $nombreusuario;
    $email_user = $correosistema;
    $email_password = $passmail;
    $the_subject = "Asignacion de soportista // Centro de Soporte Educativo";
    $address_to = $para;
    $from_name = "Tecnopresta";
    $phpmailer = new PHPMailer();
    $texto = "Test email";
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
    $phpmailer->setFrom($phpmailer->Username,$from_name);
    $phpmailer->AddAddress($address_to); // recipients email
    $phpmailer->AddEmbeddedImage('ico/bienvenida2.png', 'bienvenida', 'attachment', 'base64', 'image/png');
    $phpmailer->AddEmbeddedImage('ico/eserialb.png', 'piePagina', 'attachment', 'base64', 'image/png');

    $phpmailer->Subject = $the_subject;    
    $phpmailer->Body .="<h1 style='color:#3498db;'>Soporte</h1>";
    $phpmailer->Body .="<table border=\"1\" WIDTH=\"600\" HEIGHT=\"500\">
                        <tr>
                            <td align=\"center\" width=\"20%\">Asunto</td>
                            <td align=\"center\">El sistema le ha asignado al siguiente soportista</td>
                        </tr>
                        <tr>
                            <td align=\"center\" width=\"20%\">Soportista</td>
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
    $phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
    $phpmailer->Send();
}

$correot = "tecnopresta@mep.go.cr";
$ahora = date("Y-m-d");
$id = $_POST['id_soporte'];
$correosolicitante = $_POST['correosolicitante']; //correo del solicitante
$logcorreo = $_SESSION['correomep']; //correo del soportista que acepta el caso
$tomado = "Si";

if(empty($_POST['id_soporte']))
{
    echo '<script language = javascript>
    alert("No hay ningun caso seleccionado")
    self.location = "panel_soporte.php"
    </script>';
}
else {
    // Primero verificamos si el caso ya ha sido tomado
    $query = "SELECT tomado FROM soporte WHERE id = '".$id."'";
    $result = $link->query($query);
    
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['tomado'] == 'Si') {
            // El caso ya está tomado
            echo '<script language = javascript>
            alert("El caso ya fue tomado por otro soportista")
            self.location = "panel_soporte.php"
            </script>';
            exit(); // Terminamos la ejecución aquí
        }
    } else {
        echo "Error al verificar el estado del caso: " . mysqli_error($link);
        exit();
    }
    
    // Si llegamos aquí, el caso no está tomado y podemos proceder
    $update = "UPDATE soporte SET tomado = '".$tomado."', cedulatecnico = '".$logusuario."', nombretecnico = '".$lognombre."' WHERE id = '".$id."'";
    $link->query($update);  

    if (mysqli_query($link, $update)) {
        // Actualización exitosa
    } else {
        echo "Error al actualizar registro: " . mysqli_error($link);
        exit();
    }

    $saludo="Hola soy"." ".$lognombre." "."un gusto";
    $sql = "INSERT INTO `t_chat_soporte` (`id`, `id_caso`, `tipo`, `mensaje`, `fecha`) VALUES (NULL, '$id', '$logusuario', '$saludo', '$ahora')";
    
    if (mysqli_query($link, $sql)) {
        // Inserción exitosa
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($link);
        exit();
    }
    
    // Enviamos el correo de verificación
    verificacion($correosolicitante, $lognombre, $correot, $passemail, $estafecha);
    
    echo '<script language = javascript>
    alert("Guardado")
    self.location = "panel_soporte.php"
    </script>';
}

mysqli_close($link);
?>