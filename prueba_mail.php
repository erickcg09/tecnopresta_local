<?php
require_once("variablesemail.php");
include "class.phpmailer.php";
include "class.smtp.php";

			$para = "sandro.yee.vasquez@mep.go.cr";
			$completo = "Equipo Desarrollardor";
			$email_user = $correo;
			$email_password = $passemail;
			$the_subject = "Se hizo click sobre ayuda";
			$address_to = $para;
			$from_name = "Administrador";
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
			$phpmailer->setFrom($phpmailer->Username,$from_name);
			$phpmailer->AddAddress($address_to); // recipients email
			$phpmailer->Subject = $the_subject;	
			$phpmailer->Body .="<h2 style='color:#3498db;'>Tecnopresta informa</h2>";
			$phpmailer->Body .= "<p>Estimado: </p>".$completo;
			$phpmailer->Body .= "<p>Este mensaje fue generado automaticamente desde el Sistema TecnoPresta</p>";
			$phpmailer->Body .= "<p>Muchas gracias </p>";
			$phpmailer->Body .= "<p>Fecha y Hora: ".date("d-m-Y h:i:s")."</p>";
			$phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
			$phpmailer->Send();

?>