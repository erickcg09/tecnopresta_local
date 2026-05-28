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

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}

$lognombre = $_SESSION['nombre'];
$logcorreo = $_SESSION['correomep'];


function verificacion($correousuario, $correosistema, $passmail, $fechora) {
    // Configuración inicial más limpia
    $phpmailer = new PHPMailer();
    
    // Configuración SMTP
    $phpmailer->SMTPDebug = 0;
    $phpmailer->isSMTP();
    $phpmailer->Host = "smtp.office365.com";
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = $correosistema;
    $phpmailer->Password = $passmail;
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->Port = 587;
    $phpmailer->CharSet = 'UTF-8';
    
    // Remitente y destinatario
    $phpmailer->setFrom($correosistema, "Tecnopresta");
    $phpmailer->addAddress($correousuario);
    
    // Asunto y cuerpo del mensaje
    $phpmailer->Subject = "Importe masivo";
    $phpmailer->isHTML(true);
    
    // Construcción más eficiente del cuerpo del mensaje
    $images = [
        'importacionbd' => 'http://tecnopresta.mep.go.cr/ico/importacionbd.png',
        'usuario' => 'http://tecnopresta.mep.go.cr/revi/usuario.png',
        'mensaje' => 'http://tecnopresta.mep.go.cr/revi/mensaje.png',
        'reloj' => 'http://tecnopresta.mep.go.cr/revi/reloj.png',
        'eserialb' => 'http://tecnopresta.mep.go.cr/ico/eserialb.png'
    ];
    
    $body = <<<HTML
    <img src="{$images['importacionbd']}" alt="" width="600px" height="500px" />
    <h2 style='color:#3498db;'>Tecnopresta Alerta</h2>
    <table border="1" width="600" height="500">
        <tr>
            <td align="center" width="20%"><img src="{$images['usuario']}" alt="" width="50px" height="50px" /></td>
            <td align="center">$correousuario</td>
        </tr>
        <tr>
            <td align="center" width="20%"><img src="{$images['mensaje']}" alt="" width="50px" height="50px" /></td>
            <td align="center">Estimado señor (a) TecnoPresta le informa que el proceso de importación de datos masiva se realizó correctamente. Es importante recalcar que la integridad de sus datos depende extrictamente de la información previa existente y la nueva inserción.</td>
        </tr>
        <tr>
            <td align="center" width="20%"><img src="{$images['reloj']}" alt="" width="50px" height="50px" /></td>
            <td align="center">$fechora</td>
        </tr>
    </table>
    <img src="{$images['eserialb']}" alt="" width="600px" height="150px" />
HTML;

    $phpmailer->Body = $body;
    
    // Envío del correo
    return $phpmailer->send();
}

$correo = "tecnopresta@mep.go.cr";


$fechaActual = date('Y-m-d');




	$ruta = 'Upload/';	

	foreach ($_FILES as $key) {

		$nombre=$key["name"];
		$ruta_temporal=$key["tmp_name"];		
		
		$fecha=getdate();
		$nombre_v=$fecha["mday"]."-".$fecha["mon"]."-".$fecha["year"]."_".$fecha["hours"]."-".$fecha["minutes"]."-".$fecha["seconds"].".csv";		

		$destino=$ruta.$nombre_v;
		$explo=explode(".",$nombre);


		if($explo[1] != "csv"){
			$alert=1;
		}else{

			if(move_uploaded_file($ruta_temporal, $destino)){
				$alert=2;
			}

		}

	}

	$x=0;
	$data=array();
	$fichero=fopen($destino, "r");

	while(($datos= fgetcsv($fichero,1000)) != FALSE){

		$x++;
		if($x>1){
			// Modificación aquí: agregar ,0 para id_lugar al final de cada conjunto de datos
			$data[]='("'.$datos[0].'","'.$datos[1].'",'.$datos[2].',"'.$datos[3].'",'.$datos[4].','.$datos[5].','.$datos[6].','.$datos[7].','.$datos[8].',0)';

		}

	}

	// Modificación aquí: agregar id_lugar a la lista de campos
	$query = "INSERT INTO t_placa (placa, serial, id_activo, codigo, id_estado, prestar, activo, id_fondos, alias_id, id_lugar)VALUES ". implode(",", $data);
	$link->query($query);
	mysqli_close($link);
	fclose($fichero);

	    verificacion ($logcorreo,$correo,$passemail,$fechaActual);
	    
	echo '<script language = javascript>
		  alert("Datos Importados correctamente")
		  self.location = "formulario_importar_lotes.php"
		  </script>';

?>