<?php
session_start();
if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.html"
</script>';
}
require_once("conexion.php");
require_once("variablesemail.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$link = $mysqli; 

if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];
$logcorreo = $_SESSION['correomep'];
$logdireccionreg = $_SESSION['direccionreg'];
$logcircuito = $_SESSION['circuito'];
$logclase = $_SESSION['clase'];
$logespecialidad = $_SESSION['especialidad'];
$logdependencia = $_SESSION['dependencia'];
$estafecha=date('d-m-Y h:i:s');

function verificacion ($correousuario,$nombreusuario,$correosistema,$passmail,$fechora){ 
            $fh=$fechora;
  			$para = $correousuario;
			$completo = $nombreusuario;
			$email_user = $correosistema;
			$email_password = $passmail;
			$the_subject = "Te damos la bienvenida";
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
			$phpmailer->Subject = $the_subject;	
			$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/bienvenida2.png\" alt=\"\" width=\"600px\" height=\"500px\" />";
			$phpmailer->Body .="<h2 style='color:#3498db;'>Tecnopresta Alerta</h2>";
			$phpmailer->Body .="<table border=\"1\" WIDTH=\"600\" HEIGHT=\"500\">
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/usuario.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">$completo</td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/mail.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">$para</td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/informacion.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\"><b>Gracias por utilizar TecnoPresta, nos esforzaremos al máximo para brindarle una excelente experiencia y le acompañaremos en esta noble profesión</b></td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/reloj.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">$fh</td>
                                  </tr>
                                </table>
			";
			$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/eserialb.png\" alt=\"\" width=\"600px\" height=\"150px\" />";
			$phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
			$phpmailer->Send();
}

$correo = "tecnopresta@mep.go.cr";

verificacion ($logcorreo,$lognombre,$correo,$passemail,$estafecha);

echo"<script type=\"text/javascript\">
        alert(\"Bienvenido\");
        window.location=\"perfil_usuario.php\"
     </script>";

?>