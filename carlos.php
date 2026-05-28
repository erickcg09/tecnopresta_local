<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require "/home/tecnopresta/PHPMailer/src/SMTP.php";
require "/home/tecnopresta/PHPMailer/src/PHPMailer.php";
require "/home/tecnopresta/PHPMailer/src/Exception.php";

$phpmailer = new PHPMailer();

$phpmailer->Username = "tecnopresta@mep.go.cr";
$phpmailer->Password = "Coqueta2020";
//$phpmailer->SMTPDebug = 4;
$phpmailer->SMTPSecure = true;
$phpmailer->SMTPAutoTLS = true;
$phpmailer->SMTPAuth = true;
$phpmailer->Host = "smtp.office365.com";
$phpmailer->Port = '587';
$phpmailer->IsSMTP();
$phpmailer->SMTPAuth = true;
$phpmailer->CharSet= "utf-8";
$phpmailer->setFrom($phpmailer->Username, "InformesJuntas");
$phpmailer->addAddress("sandro.yee.vasquez@mep.go.cr");
$phpmailer->Subject = "Informes Juntas";
$phpmailer->Body .="<h2 style='color:#3498db;'>InformesJuntas informa</h2>";
$phpmailer->Body .= "<p>Envío de correo desde MEP</p>";
$phpmailer->isHTML(true);

if(!$phpmailer->Send()){
    echo 'No se pudo enviar el correo. Error: ' . $$phpmailer->ErrorInfo;
    } else{
    echo 'Correo enviado exitosamente';
}

?>
