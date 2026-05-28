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






function verificacion ($correousuario,$nombreusuario,$correosistema,$passmail,$fechora,$referencia){ 
            $refe=$referencia;
            $fh=$fechora;
  			$para = $correousuario;
			$completo = $nombreusuario;
			$email_user = $correosistema;
			$email_password = $passmail;
			$the_subject = "Solucion y Cierre de Caso";
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
			$phpmailer->Body .="<h1 style='color:#3498db;'>Informe de Cierre de Caso</h1>";
			$phpmailer->Body .="<h3 style='color:#242c30;'>Recuerde que para cada caso atendido deber&aacute crear una incidencia de soporte en el Centro de Soporte Educativo de Tecnopresta</h3>";
			$phpmailer->Body .="<table border=\"1\" WIDTH=\"600\" HEIGHT=\"500\">
                                  
                                 
                                  <tr>
                                   <td align=\"center\" width=\"20%\">Asunto</td>
                                    <td align=\"center\">$refe</td>
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
                                </table>
			";
			//$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/eserialb.png\" alt=\"\" width=\"600px\" height=\"150px\" />";
		/*	$phpmailer->Body .= "<img src=\"cid:piePagina\" width=\"100%\" height=\"150px\" />"; */
			$phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
			$phpmailer->Send();
}

$correot = "tecnopresta@mep.go.cr";



$estatus = $_POST['estatus'];
$id_solucion = $_POST['id_solucion'];
$solucion = $_POST['solucion'];
$caso="Atendido";

		$preguntar2 = mysqli_query($link, "select correo, placa from soporte where id='$id_solucion'");   
		$respuesta2 = mysqli_fetch_array($preguntar2);
		$correodestino = $respuesta2['correo'];
		$placa = $respuesta2['placa'];

	
if(empty($solucion) AND empty($estatus))
{
	  echo '<script language = javascript>
  alert("Ningun cambio efectuado")
  self.location = "miscasos.php"
  </script>';

    
}elseif (empty($solucion)) {

$update = "UPDATE soporte SET estatus = '".$caso."' WHERE id = '".$id_solucion."'";
		$link->query($update);  

		if (mysqli_query($link, $update)) {
		    
		} else {
		    echo "Error al actualizar registro: " . mysqli_error($link);
		}
		mysqli_close($link);
echo '<script language = javascript>
                alert("Cierro el caso")
                self.location = "miscasos.php"
                </script>';
                
}elseif (empty($estatus)) {
    
$update = "UPDATE soporte SET solucion = '".$solucion."' WHERE id = '".$id_solucion."'";
		$link->query($update);  

		if (mysqli_query($link, $update)) {
		    
		} else {
		    echo "Error al actualizar registro: " . mysqli_error($link);
		}
		mysqli_close($link);
verificacion ($correodestino,$solucion,$correot,$passemail,$estafecha,$placa);
echo '<script language = javascript>
                alert("Cierro el caso")
                self.location = "miscasos.php"
                </script>';    
    
} else {
		$update = "UPDATE soporte SET estatus = '".$caso."', solucion = '".$solucion."' WHERE id = '".$id_solucion."'";
		$link->query($update);  

		if (mysqli_query($link, $update)) {
		    
		} else {
		    echo "Error al actualizar registro: " . mysqli_error($link);
		}


mysqli_close($link);  

verificacion ($correodestino,$solucion,$correot,$passemail,$estafecha,$placa);
echo '<script language = javascript>
                alert("Guardado")
                self.location = "miscasos.php"
                </script>';
} // Cierre IF principal
?>