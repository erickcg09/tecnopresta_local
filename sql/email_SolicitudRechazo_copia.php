<?php

header('Content-Type: text/html; charset=UTF-8');

class Email_SolicitudRechazo
{
    private $correo;
    private $passemail;
       	
	function __construct()
	{
                
        $correo = Email;
        $passemail = Email_PASS;
        $this->correo = $correo;
        $this->passemail = $passemail;
        
    }

    public function email_SolicitudRechazo($solicitud_email_funcionario,
                                           $solicitud_motivo_rechazo, 
                                           $prestamo_nombre_solicitante, 
                                           $arrayArticulosNombre) {

        $txtNombres = "";
        if(!empty($arrayArticulosNombre)) {

            if (count($arrayArticulosNombre)== 1) {

                foreach($arrayArticulosNombre as $key => $nombre) {
                    $txtNombres =  $nombre;
                }                

            } else {
                
                    foreach($arrayArticulosNombre as $key => $nombre) {
                        $txtNombres = $txtNombres . " " . $nombre . ", ";
                    }
            }
        }
        
        $txtMotivo_rechazo = utf8_decode($solicitud_motivo_rechazo);
        date_default_timezone_set('America/Costa_Rica');
        $rechazo_Fecha_formato = date_create('now')->format('d/m/Y H:i:s');
        $txtNombresFormato = utf8_decode($txtNombres);
      
        $the_subject = "Rechazado Solicitud de Equipo";
        $address_to = $solicitud_email_funcionario;
        $from_name = "Administrador";
        
//        include "../class.phpmailer.php";
//        include "../class.smtp.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . "/../../PHPMailer/src/SMTP.php";
require __DIR__ . "/../../PHPMailer/src/PHPMailer.php";
require __DIR__ . "/../../PHPMailer/src/Exception.php";

        $phpmailer = new PHPMailer();
    
        // ---------- datos de la cuenta de Tecnopresta -------------------------------
        $phpmailer->Username = $this->correo;
        $phpmailer->Password = $this->passemail;
        //----------------------------------------------------------------------- 
     
        $phpmailer->SMTPDebug = 0;  // Opciones 0, 1, 2
        $phpmailer->SMTPSecure = 'tls';
        $phpmailer->Host = "smtp.office365.com"; // Office365
        $phpmailer->Port = 587;
        $phpmailer->IsSMTP(); // use SMTP
        $phpmailer->SMTPAuth = true;
        $phpmailer->setFrom($phpmailer->Username,$from_name);
        $phpmailer->AddAddress($address_to); // recipients email
        $phpmailer->AddEmbeddedImage('../img/correo/rechazodesolicitud.png', 'rechazodesolicitud', 'attachment', 'base64', 'image/png');
        $phpmailer->AddEmbeddedImage('../img/correo/Emailtemplates-10.png', 'piePagina', 'attachment', 'base64', 'image/png');

        $phpmailer->Subject = $the_subject;

        $phpmailer->Body .= "<head>
                        <meta http-equiv='Content-type' content='text/html; charset=utf-8'/>
                        <style>
                            html {
                                overflow-y: auto;
                            }
                            
                            img
                            {
                                max-width: 100%;
                            }
                            
                            html, body {
                                height: 100%;
                            }
                            
                            body {
                                margin: 0%;                                                                                                   
                            }
                            
                            #container {
                                display: flex;
                            }

                            table, th, td {
                            border: 1px solid black;
                            border-collapse: collapse;
                            }
                        </style>
                        </head>";
        //$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/img/correo/rechazodesolicitud.png\" alt=\"\" width=\"100%\" height=\"500px\" />";
        $phpmailer->Body .= "<img src=\"cid:rechazodesolicitud\" width=\"80%\" height=\"400px\" />";        
        $phpmailer->Body .= "<h3> Estimado Funcionario (a) </h3>";
        $phpmailer->Body .="<p> <b> El Sistema de Inventario e Incidencias de Pr&eacute;stamos de Equipo  [Tecnopresta] </b> le informa:</p>";

        $phpmailer->Body .= "<div id='container'> <table style='width:100%'>
        <tbody>
        <tr style='width:50%'>
            <th scope='row'>Solicitante</th>
            <td> ".$prestamo_nombre_solicitante."</td>
        </tr>    
        <tr style='width:50%'>
            <th scope='row'> Estado del pr&eacute;stamo</th>
            <td> <strong>  Rechazado </strong> </td>
        </tr>
        <tr style='width:50%'>
            <th scope='row'>Motivo por el c&uacute;al se rechaza la solitud de equipo</th>
            <td> ".$txtMotivo_rechazo."</td>
        </tr>
        <tr style='width:50%'>
            <th scope='row'>Equipos solicitados</th>
            <td> ".$txtNombresFormato."</td>
        </tr>

        </tbody> 
        </table> </div> <br/>";
        
        //$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/img/correo/Emailtemplates-10.png\" alt=\"\" width=\"90%\" height=\"125px\" />";
        $phpmailer->Body .= "<img src=\"cid:piePagina\" width=\"100%\" height=\"150px\" />";
        $phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
        $phpmailer->Send();
        

        return true; 

    }
    
}

?>