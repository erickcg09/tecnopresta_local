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
$logcodigo = $_SESSION['codigo']; 
$tipollave = $_SESSION['tipo']; 
$estafecha=date('d-m-Y h:i:s');
if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}


function verificacion ($correousuario,$nombreusuario,$rolusuario,$correosistema,$passmail,$fechora){ 
            $fh=$fechora;
            $roles=$rolusuario;
  			$para = $correousuario;
			$completo = $nombreusuario;
			$email_user = $correosistema;
			$email_password = $passmail;
			$the_subject = "Asignación de Permisos Específicos";
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
			$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/mailpermiso.png\" alt=\"\" width=\"600px\" height=\"500px\" />";
			$phpmailer->Body .="<h2 style='color:#3498db;'>Tecnopresta Alerta</h2>";
			$phpmailer->Body .="<table border=\"1\" WIDTH=\"600\" HEIGHT=\"500\">
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/usuario.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">$completo</td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/mensaje.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">Estimado se&ntilde;or (a) se comunica que se le han otorgado el siguiente rol de permisos en el sistema</td>
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

$verificar = $_POST['cedula'];

if (substr($verificar, 0, 1) == '0') {
        echo '<script language = javascript>
        alert("La cedula no debe tener ceros al inicio")
        self.location = "formulario_crear_roles.php"
        </script>';
} else {

    switch ($tipollave) 
        {
        case 1:
            // Inicia detecci&oacute;n de campos vacios y el pase de variables 
    
            $post = (isset($_POST['rol']) && !empty($_POST['rol'])) &&
                    (isset($_POST['cedula']) && !empty($_POST['cedula'])) &&
                    (isset($_POST['nombre']) && !empty($_POST['nombre'])) &&
                    (isset($_POST['codigop']) && !empty($_POST['codigop']));
            
            if($post)
            {
            
            $rol = $_POST['rol'];
            $cedula = "0".$_POST['cedula'];
            $nombre = $_POST['nombre'];
            $codigop = $_POST['codigop'];
            
                        		$preguntar = mysqli_query($link, "select rol from t_roles where id_rol='$rol'");   
                		$respuesta = mysqli_fetch_array($preguntar);
                		$nrol = $respuesta['rol'];
            
            	          $miconsulta = "select id_lista_blanca from t_lista_blanca where cedula='$cedula' AND codigo='$codigop'";
                              $mirespuesta = $link->query($miconsulta);
            
            		  if($mirespuesta->num_rows >= 1){
            
            			echo '<script language = javascript>
            		        alert("El usuario al que intenta asignar permisos ya existe en ese centro")
            		        self.location = "formulario_crear_roles.php"
            		        </script>';
            	          } else {
            
            		        $consulta = "INSERT INTO t_lista_blanca (cedula,nombre,codigo,id_rol)VALUES('".$cedula."','".$nombre."','".$codigop."','".$rol."')";
            		        $link->query($consulta);
            		        
            		        	verificacion ($nombre,$cedula,$nrol,$correo,$passemail,$estafecha);
            		        
            			echo '<script language = javascript>
            		        alert("Guardado correctamente")
            		        self.location = "formulario_crear_roles.php"
            		        </script>';
            		  } // Fin del if interno
              
            } else {
            
            			echo"<script type=\"text/javascript\">
                                    alert(\"Debe completar todos los campos\");
                                    window.location=\"formulario_crear_roles.php\"
                                    </script>";
            } //Cierre del if principal
            
            break;
        case 2:
            $rol = $_POST['rol'];
            $cedula = "0".$_POST['cedula'];
            $nombre = $_POST['nombre'];
            $codigop = $_POST['codigop'];
            
            	$preguntar = mysqli_query($link, "select rol from t_roles where id_rol='$rol'");   
                		$respuesta = mysqli_fetch_array($preguntar);
                		$nrol = $respuesta['rol'];
            
            $incumple = ($rol<=2 or $codigop<>$logcodigo);
            if ($incumple == true){
            echo '<script language = javascript>
            alert("Solo un usuario Root puede asignar un rol root, administrador o en un codigo presupuestario diferente")
            self.location = "formulario_crear_roles.php"
            </script>';
            } else {
                
                $miconsulta = "select id_lista_blanca from t_lista_blanca where cedula='$cedula' AND codigo='$codigop'";
                              $mirespuesta = $link->query($miconsulta);
                
            		  if($mirespuesta->num_rows >= 1){
            
            			echo '<script language = javascript>
            		        alert("El usuario al que intenta asignar permisos ya existe")
            		        self.location = "formulario_crear_roles.php"
            		        </script>';
            	          } else {
            
            		        $consulta = "INSERT INTO t_lista_blanca (cedula,nombre,codigo,id_rol)VALUES('".$cedula."','".$nombre."','".$codigop."','".$rol."')";
            		        $link->query($consulta);
            		        
            		        verificacion ($nombre,$cedula,$nrol,$correo,$passemail,$estafecha);
            		        
            			echo '<script language = javascript>
            		        alert("Guardado correctamente")
            		        self.location = "formulario_crear_roles.php"
            		        </script>';
            		  } // Fin del if interno
            }
            
            break;
    }


} //fin del if verificar

?>