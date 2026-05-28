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

$tipollave = $_SESSION['tipo'];  


if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];
$xeliminar = $_GET['gps']; 
$estafecha=date('d-m-Y h:i:s');

function verificacion ($correousuario,$nombreusuario,$rolusuario,$correosistema,$passmail,$fechora){ 
            $fh=$fechora;
            $roles=$rolusuario;
  			$para = $correousuario;
			$completo = $nombreusuario;
			$email_user = $correosistema;
			$email_password = $passmail;
			$the_subject = "Revocación de Permisos Específicos";
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
			$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/permisorevocado.png\" alt=\"\" width=\"600px\" height=\"500px\" />";
			$phpmailer->Body .="<h2 style='color:#3498db;'>Tecnopresta Alerta</h2>";
			$phpmailer->Body .="<table border=\"1\" WIDTH=\"600\" HEIGHT=\"500\">
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/usuario.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">$completo</td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/mensaje.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">Estimado se&ntilde;or (a) se comunica que se le han revocado el siguiente rol de permisos en el sistema</td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/candado.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\"><b>$roles</b></td>
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

$preguntar = mysqli_query($link, "select * from t_lista_blanca where id_lista_blanca='$xeliminar'");   
        $respuesta = mysqli_fetch_array($preguntar);
        $id_rol = $respuesta['id_rol'];
        $xmail = $respuesta['nombre'];
        $cedula = $respuesta['cedula'];
        $codigo= $respuesta['codigo'];
        
$preguntar2 = mysqli_query($link, "select rol from t_roles where id_rol='$id_rol'");   
        $respuesta2 = mysqli_fetch_array($preguntar2);
        $nrol = $respuesta2['rol'];



switch ($tipollave) 
    {
    case 1:
        
		// sql para eliminar
		$sql = "DELETE FROM t_lista_blanca WHERE id_lista_blanca=$xeliminar";

		if (mysqli_query($link, $sql)) {
		    
		} else {
		    echo "Error al eliminar registro: " . mysqli_error($link);
		}
        
		mysqli_close($link);
        verificacion ($xmail,$cedula,$nrol,$correo,$passemail,$estafecha);
		// Redireccion al index 
	    echo '<script language = javascript>
        alert("Eliminado correctamente")
        self.location = "formulario_crear_roles.php"
        </script>';
		exit();
        
          
        break;
        
    case 2:
		$preguntar = mysqli_query($link, "select codigo, id_rol from t_lista_blanca where id_lista_blanca='$xeliminar'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$codigo_x = $respuesta['codigo'];
		$id_rol_x = $respuesta['id_rol'];
		$rol_root = 1;
		$rol_adm = 2;
        
        $incumple = ($id_rol_x==$rol_root or $id_rol_x==$rol_adm);
        if ($incumple == true){
        echo '<script language = javascript>
        alert("No puede eliminar cuentas de super usuarios o administradores")
        self.location = "formulario_crear_roles.php"
        </script>';
        } else {
		// sql para eliminar
		$sql = "DELETE FROM t_lista_blanca WHERE id_lista_blanca=$xeliminar";

    		if (mysqli_query($link, $sql)) {
    		    
    		} else {
    		    echo "Error al eliminar registro: " . mysqli_error($link);
    		}
        
		mysqli_close($link);
        verificacion ($xmail,$cedula,$nrol,$correo,$passemail,$estafecha);
		// Redireccion al index 
	    echo '<script language = javascript>
        alert("Eliminado correctamente")
        self.location = "formulario_crear_roles.php"
        </script>';
		exit();            

        }
        
        break;
}

?> 