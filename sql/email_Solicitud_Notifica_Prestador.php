<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

header('Content-Type: text/html; charset=UTF-8');

class Email_Solicitud_Notifica_Prestador
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

    public function email_Solicitud_Notifica_Prestador($solicitud_codigo_presupuestario) 
    {
        

        try {

           
            $phpmailer = new PHPMailer();
            $pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        
            // ---------- datos de la cuenta de Tecnopresta -------------------------------
            $phpmailer->Username = $this->correo;
            $phpmailer->Password = $this->passemail;
            //----------------------------------------------------------------------- 
        
            $the_subject = "AVISO / Un funcionario(a) a realizado una solicitud en Tecnopresta";       
            $from_name = "Administrador";

            $phpmailer->SMTPDebug = 0;  // Opciones 0, 1, 2
            $phpmailer->SMTPSecure = 'tls';
            $phpmailer->Host = "smtp.office365.com"; // Office365
            $phpmailer->Port = 587;
            $phpmailer->IsSMTP(); // use SMTP
            $phpmailer->SMTPAuth = true;
            $phpmailer->setFrom($phpmailer->Username,$from_name);
            $phpmailer->Subject = $the_subject;
                                                                    
            if ($pdo != null) {		
                
                $consultaSQL = "SELECT nombre FROM t_lista_blanca WHERE id_rol = 3 AND codigo='$solicitud_codigo_presupuestario'";

                $sql = $pdo->query($consultaSQL);
                            
                while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
                                        
                    $phpmailer->AddAddress($row['nombre']);               
                                        
                }

            }

            $phpmailer->AddEmbeddedImage('../img/correo/pide1.png', 'pide', 'attachment', 'base64', 'image/png');

            $pdo = null;

            $phpmailer->Body .= "<head>
                                    <meta http-equiv='Content-type' content='text/html; charset=utf-8'/>
                                </head>";
            $phpmailer->Body .= "<img src=\"cid:pide\" width=\"100%\" height=\"100%\" />";                                                                    
            $phpmailer->Body .= "<h3>[Tecnopresta] le informa</h3>
            <p><b>Existe Nueva Solicitud de Equipo en el sistema.</p>
            <a href='https://tecnopresta.mep.go.cr'>Ir a Tecnopresta</a>";

            $phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
            $phpmailer->Send();

            return true;

        } catch (\Throwable $th) {
            echo "Error al guardar solicitud: " . $th->getMessage() . "\n";            				
        }    
                      
    }
    
}

?>