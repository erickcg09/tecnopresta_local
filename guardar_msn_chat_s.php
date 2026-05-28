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

}
date_default_timezone_set('America/Costa_Rica'); //configuro un nuevo timezone
function verificacion ($correousuario,$correosistema,$passmail,$fechora){ 
            $fh=$fechora;
  			$para = $correousuario;
			$email_user = $correosistema;
			$email_password = $passmail;
			$the_subject = "Notificaci贸n // Centro de Soporte Educativo";
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
			//$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/bienvenida2.png\" alt=\"\" width=\"600px\" height=\"500px\" />";
			//$phpmailer->Body .= "<img src=\"cid:bienvenida\" width=\"80%\" height=\"400px\" />";        
			$phpmailer->Body .="<h1 style='color:#3498db;'>Foro Chat</h1>";
			$phpmailer->Body .="<table border=\"1\" WIDTH=\"600\" HEIGHT=\"500\">
                                  <tr>
                                    <td align=\"center\" width=\"20%\">Asunto</td>
                                    <td align=\"center\">Usted ha recibido un mensaje en el Foro Chat de Soporte T&eacute;cnico, ingrese a Tecnopresta.mep.go.cr, al Centro de Soporte, busque su caso y responda al Soportista</td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/reloj.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">$fh</td>
                                  </tr>
                                  <tr>
                                    <td colspan=\"2\" align=\"center\" ><img src=\"http://tecnopresta.mep.go.cr/svg/centrosoporte.png\" alt=\"\" /></td>
                                  </tr>
                                </table>
			";
			//$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/eserialb.png\" alt=\"\" width=\"600px\" height=\"150px\" />";
		/*	$phpmailer->Body .= "<img src=\"cid:piePagina\" width=\"100%\" height=\"150px\" />"; */
			$phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
			$phpmailer->Send();
}

$correot = "tecnopresta@mep.go.cr";

$ahora = date("d-m-Y");

$idcaso = $_POST['idcaso'];
$mensajito = $lognombre." ".$_POST['mensajito'];

$logcorreo = $_SESSION['correomep']; //correo del soportista que acepta el caso

		$preguntar = mysqli_query($link, "select correo from soporte where id='$idcaso'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$correosolicitante = $respuesta['correo'];
	
if(empty($_POST['idcaso']))
{
	  echo '<script language = javascript>
  alert("No hay ningun mensaje")
  self.location = "miscasos.php"
  </script>';
}
else {
		

    $sql = "INSERT INTO `t_chat_soporte` (`id`, `id_caso`, `tipo`, `mensaje`, `fecha`) VALUES (NULL, '$idcaso', '$logusuario', '$mensajito', '$ahora')";
    
    if (mysqli_query($link, $sql)) {
      
    } else {
      echo "Error: " . $sql . "<br>" . mysqli_error($link);
    }
    


} // Cierre IF principal
mysqli_close($link);

verificacion ($correosolicitante,$correot,$passemail,$estafecha);
echo '<script type="text/javascript">
    window.location = "proyecto_chat_s.php?gps='.$idcaso.'"
    </script>';

?>
