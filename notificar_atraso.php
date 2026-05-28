<?php
require_once("conexion.php");
require_once("variablesemail.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require "/home/tecnopresta/PHPMailer/src/SMTP.php";
require "/home/tecnopresta/PHPMailer/src/PHPMailer.php";
require "/home/tecnopresta/PHPMailer/src/Exception.php";
$link = $mysqli;
if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}

function verificacion ($correousuario,$cantn,$correosistema,$passmail,$fechora){ 
            $fh=$fechora;
            $carticulos=$cantn;
  			$para = $correousuario;
			$email_user = $correosistema;
			$email_password = $passmail;
			$the_subject = "Morosidad";
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
			$phpmailer->AddEmbeddedImage('../ico/demora.png', 'demora', 'attachment', 'base64', 'image/png');
			$phpmailer->AddEmbeddedImage('../ico/eserialb.png', 'piePagina', 'attachment', 'base64', 'image/png');
	
			$phpmailer->Subject = $the_subject;	
			//$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/demora.png\" alt=\"\" width=\"600px\" height=\"500px\" />";
			$phpmailer->Body .= "<img src=\"cid:demora\" width=\"80%\" height=\"400px\" />";
			$phpmailer->Body .="<h2 style='color:#3498db;'>Tecnopresta Alerta</h2>";
			$phpmailer->Body .="<table border=\"1\" WIDTH=\"600\" HEIGHT=\"500\">
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/usuario.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">$para</td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/mensaje.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">Estimado se&ntilde;or (a) TecnoPresta le informa que tiene devoluciones de equipos pendientes, le rogamos ser puntual. Si usted ya devolvi&oacute; los recursos pongase en contacto con el administrador en su instituci&oacute;n, muchas gracias</td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/bolsa.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\"><b>$carticulos</b></td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/reloj.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">$fh</td>
                                  </tr>
                                </table>
			";
			//$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/eserialb.png\" alt=\"\" width=\"600px\" height=\"150px\" />";
			$phpmailer->Body .= "<img src=\"cid:piePagina\" width=\"100%\" height=\"150px\" />";
			$phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
			$phpmailer->Send();
}

$correo = "tecnopresta@mep.go.cr";


$fechaActual = date('Y-m-d');
$zero = 0;

$result = mysqli_query($link,"SELECT COUNT(Ta.prestamo_Id) as n, Tg.prestamo_email_solicitante
FROM t_prestamo_detalle Ta
INNER JOIN t_prestamo Tg ON Ta.prestamo_Id = Tg.prestamo_Id
WHERE Ta.prestamo_detalle_devuelto = '$zero' AND Ta.prestamo_detalle_fechaDevolucion < '$fechaActual'
GROUP BY Tg.prestamo_email_solicitante") or die(mysqli_error($link));


	while($row = mysqli_fetch_array($result)) {
	
	    verificacion ($row['prestamo_email_solicitante'],$row['n'],$correo,$passemail,$fechaActual);
		
	}
?>