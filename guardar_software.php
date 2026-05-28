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

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


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



function verificacion ($correousuario,$nombreusuario,$correosistema,$passmail,$nombresoftware,$numeroserial){ 

  			$para = $correousuario;
			$completo = $nombreusuario;
			$email_user = $correosistema;
			$email_password = $passmail;
			$the_subject = "Detalles del registro";
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
			$phpmailer->setFrom($phpmailer->Username,$from_name);
			$phpmailer->AddAddress($address_to); // recipients email
			$phpmailer->Subject = $the_subject;	
			$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/eseriala.png\" alt=\"\" width=\"600px\" height=\"500px\" />";
			$phpmailer->Body .="<h2 style='color:#3498db;'>Tecnopresta informa</h2>";
			$phpmailer->Body .= "<p>Estimado (a): </p>".$completo;
			$phpmailer->Body .= "<p>Se le facilita copia del registro efectuado sobre el software: </p>".$nombresoftware;
			$phpmailer->Body .= "<p>Serial: </p>".$numeroserial;
			$phpmailer->Body .= "<p>Por favor archive este mensaje por si necesita corregir o distribuir</p>";
			$phpmailer->Body .= "<p>Fecha y Hora: ".date("d-m-Y h:i:s")."</p>";
			$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/eserialb.png\" alt=\"\" width=\"600px\" height=\"150px\" />";	
			$phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
			$phpmailer->Send();
}




$etiqueta = trim($_POST['search_data']);
$licencia = $_POST['licencia'];
$tipo_licencia = $_POST['selecctipo'];
$tipo_software = $_POST['caracteristica'];
$ceal = $_POST['ceal'];
$factivacion = $_POST['factivacion'];
$vigencia = $_POST['vigencia'];
$contratacion = $_POST['contratacion'];
$asociar = $_POST['asociar'];
$fondos = $_POST['fondos'];


switch ($asociar) {

  case "Si":
$miconsulta = "select id_sg from t_software_general where etiqueta='$etiqueta'";
$mirespuesta = $link->query($miconsulta);


if($mirespuesta->num_rows >= 1){

	$sql = "SELECT * FROM t_software WHERE licencia = '$licencia'";
        $result = $link->query($sql);

	if ($result->num_rows >= 1) {

		echo '<script language = javascript>
                alert("Parece que hubo un error la licencia ya se encuentra en la base de datos")
                self.location = "formulario_software.php"
                </script>';

	} else {

		$preguntar = mysqli_query($link, "select * from t_software_general where etiqueta='$etiqueta'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$id_sg = $respuesta[id_sg];

		settype($id_sg,"integer");
		settype($tipo_licencia,"integer");
		settype($tipo_software,"integer");
		settype($ceal,"integer");
		settype($vigencia,"integer");
		settype($fondos,"integer");
		$formateo = str_replace(" ", "-", $factivacion);
		$fechita = date("Y-m-d", strtotime($formateo));


		$consulta = "INSERT INTO t_software (id_sg,licencia,id_tipolicencia,id_cs,ceal,factivacion,vigencia,contratacion,id_fondos)VALUES('".$id_sg."','".$licencia."','".$tipo_licencia."','".$tipo_software."','".$ceal."','".$fechita."','".$vigencia."','".$contratacion."','".$fondos."')";
		$link->query($consulta);
		

		$myquest = mysqli_query($link, "select id_software from t_software where licencia='$licencia'");   
		$myresp = mysqli_fetch_array($myquest);
		$id = $myresp[id_software];
		mysqli_close($link);
		
	
		verificacion ($logcorreo,$lognombre,$correo,$passemail,$etiqueta,$licencia);
		
		echo "<html>
              <head>
              <title>Tecnopresta</title>
                  <style>
                    a:link, a:visited, a:active {
                    text-decoration:none;
                    color: #85c1e9;
                    font-size: 30px;
                    }
                    p { color: #ffffff; 
                    font-size: 20px;
                    }
                    dialog {
                      background: black;
                      border: none;
                      border-radius: 10px;
                      text-align: center;
                    }
                  </style>
              </head>
              <body>
               <dialog id=\"dialogo\" open><p>Se le envi&oacute; un correo electr&oacute;nico con la licencia registrada, click en continuar para ligar la licencia(s) a uno o más dispositivo(s)</p><br><a href=\"formulario_asignar_licencia.php?gps=$id\">Continuar</a></dialog>
               
              </body>
              </html>";
		
	}  
	

} else {

		echo '<script language = javascript>
                alert("Parece que hubo un error con el nombre del software")
                self.location = "formulario_software.php"
                </script>';

} //Cierre del if principal
    break;
  
  default:

$miconsulta = "select * from t_software_general where etiqueta='$etiqueta'";
$mirespuesta = $link->query($miconsulta);


if($mirespuesta->num_rows >= 1){

	$sql = "SELECT * FROM t_software WHERE licencia = '$licencia'";
        $result = $link->query($sql);

	if ($result->num_rows >= 1) {

		echo '<script language = javascript>
                alert("Parece que hubo un error la licencia ya se encuentra en la base de datos")
                self.location = "formulario_software.php"
                </script>';

	} else {

		$preguntar = mysqli_query($link, "select * from t_software_general where etiqueta='$etiqueta'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$id_sg = $respuesta[id_sg];

		settype($id_sg,"integer");
		settype($tipo_licencia,"integer");
		settype($tipo_software,"integer");
		settype($ceal,"integer");
		settype($vigencia,"integer");
		settype($fondos,"integer");
		$formateo = str_replace(" ", "-", $factivacion);
		$fechita = date("Y-m-d", strtotime($formateo));


		$consulta = "INSERT INTO t_software (id_sg,licencia,id_tipolicencia,id_cs,ceal,factivacion,vigencia,contratacion,id_fondos)VALUES('".$id_sg."','".$licencia."','".$tipo_licencia."','".$tipo_software."','".$ceal."','".$fechita."','".$vigencia."','".$contratacion."','".$fondos."')";
		$link->query($consulta);
		mysqli_close($link);

	
		verificacion ($logcorreo,$lognombre,$correo,$passemail,$etiqueta,$licencia);  
		echo '<script language = javascript>
                alert("Datos guardados")
                self.location = "formulario_software.php"
                </script>';
	}  
	

} else {

		echo '<script language = javascript>
                alert("Parece que hubo un error con el nombre del software")
                self.location = "formulario_software.php"
                </script>';

} //Cierre del if principal

} //Cierre del Switch Case
?>
