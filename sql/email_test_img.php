<?php

header('Content-Type: text/html; charset=UTF-8');

class Email_Test_Img
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

    public function email_Test_Img() 
    {
        
        include "../class.phpmailer.php";
        include "../class.smtp.php";

        try {

           
            $phpmailer = new PHPMailer();
            $pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        
            // ---------- datos de la cuenta de Tecnopresta -------------------------------
            $phpmailer->Username = $this->correo;
            $phpmailer->Password = $this->passemail;
            //----------------------------------------------------------------------- 
        
            $the_subject = "AVISO / Imagen";       
            $from_name = "Administrador";

            $phpmailer->SMTPDebug = 0;  // Opciones 0, 1, 2
            $phpmailer->SMTPSecure = 'tls';
            $phpmailer->Host = "smtp.office365.com"; // Office365
            $phpmailer->Port = 587;
            $phpmailer->IsSMTP(); // use SMTP
            $phpmailer->SMTPAuth = true;
            $phpmailer->setFrom($phpmailer->Username,$from_name);
            $phpmailer->Subject = $the_subject;
                                                                                                                    
            $phpmailer->AddAddress("mauricio.bermudez.vargas@mep.go.cr");               
            $phpmailer->AddEmbeddedImage('../img/correo/preSolicitud.png', 'preSolicitud', 'attachment', 'base64', 'image/png');

            $phpmailer->Body .= "<head>
                                    <meta http-equiv='Content-type' content='text/html; charset=utf-8'/>
                                </head>                            
                                <h3>[Tecnopresta] le informa</h3>
                                <p><b>Existe Nueva Solicitud de Equipo en el sistema.</p>
                                <a href='https://tecnopresta.mep.go.cr'>Ir a Tecnopresta</a>";
                                
            $phpmailer->Body .= "<img src=\"cid:preSolicitud\" width=\"80%\" height=\"400px\" />";

            $phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
            $phpmailer->Send();

            return true;

        } catch (\Throwable $th) {
            echo "Error al guardar solicitud: " . $th->getMessage() . "\n";            				
        }    
                      
    }
    
}

?>