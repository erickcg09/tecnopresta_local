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
include "class.phpmailer.php";
include "class.smtp.php"; 


if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}

$lognombre = $_SESSION['nombre'];
$logcorreo = $_SESSION['correomep'];



function verificacion ($correousuario,$nombreusuario,$correosistema,$passmail,$nombresoftware,$numeroserial,$especifico,$concopia,$rutaarchivo){ 

  			$para = $correousuario;
			$completo = $nombreusuario;
			$email_user = $correosistema;
			$email_password = $passmail;
			$the_subject = "Detalles del registro";
			$address_to = $para;
			$addCC = $concopia;
			$ruta = $rutaarchivo;
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
			$phpmailer->setFrom($phpmailer->Username,$from_name);
			$phpmailer->AddAddress($address_to); // recipients email
			$phpmailer->addCC($addCC);
			$phpmailer->AddAttachment($ruta);
			$phpmailer->Subject = $the_subject;	
			$phpmailer->Body .="<h2 style='color:#3498db;'>Reporte de Incidencia</h2>";
			$phpmailer->Body .= "<p>Estimado Equipo de Soporte, el usuario: </p>".$completo;
			$phpmailer->Body .= "<p>Asunto: </p>".$especifico;
			$phpmailer->Body .= "<p>Reporta que: </p>".$nombresoftware;
			$phpmailer->Body .= "<p>Datos para ubicar: </p>".$numeroserial;
			$phpmailer->Body .= "<p>Por favor revisar</p>";
			$phpmailer->Body .= "<p>Fecha y Hora: ".date("d-m-Y h:i:s")."</p>";
			$phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
			$phpmailer->Send();
}





$asunto = $_POST['asunto'];
$nombre = $_POST['emisor'];
$regional = $_POST['regional'];
$circuito = $_POST['circuito'];
$correofuncionario = $_POST['correo'];
$mensaje = $_POST['mensaje'];
$correotecno = $_POST['receptor'];
$cedula = $_POST['cedula'];
$union = $regional." "."Circuito".$circuito;
$nomcorreo = $nombre." "."correo: ".$correofuncionario;
$path = 'subidos/' . $_FILES["resume"]["name"];
move_uploaded_file($_FILES["resume"]["tmp_name"], $path);

		verificacion ($correotecno,$nomcorreo,$correotecno,$passemail,$mensaje,$union,$asunto,$correofuncionario,$path);
		


		echo '<script language = javascript>
                alert("Se ha envido su solicitud")
                self.location = "contactenos.php"
                </script>';


?>
