<?php

header('Content-Type: text/html; charset=UTF-8');

class Email_Boleta
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

    public function email_Boleta($Dependencia,
                                $para,                                 
                                $prestamo_nombre_solicitante,
                                $prestamo_Fecha_formato,
                                $prestamo_fechaRetiro_formato,
                                $prestamo_fechaDevolucion_formato,
                                $destino) 
    {
                    
        $txtDependencia = utf8_decode($Dependencia);
        $txtDestino = utf8_decode($destino);
       
        $the_subject = "Boleta Imprimible de Salida, para Oficial de Seguridad";
        $address_to = $para;
        $from_name = "Administrador";
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
        $phpmailer->AddEmbeddedImage('../img/correo/logomep.png', 'logomep', 'attachment', 'base64', 'image/png');
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
        $phpmailer->Body .= "<img src=\"cid:logomep\" width=\"75px\" height=\"50px\"/>";        
        $phpmailer->Body .= "<h3>Formulario para Salida de Activos (Disponible a partir del 15 de junio 2022)</h3>";

        $phpmailer->Body .= "<div id='container'> <table style='width:100%'>
        <tbody>
            <tr style='width:50%'>
                <th scope='row'>Fecha de Salida: </th>
                <td> ".$prestamo_fechaRetiro_formato."</td>
            </tr>        
            <tr style='width:50%'>
                <th scope='row'>Funcionario(a) que efect&uacute;a la salida de los bienes:</th>
                <td> ".$prestamo_nombre_solicitante."</td>
            </tr>
            <tr style='width:50%'>
                <th scope='row'>Dependencia del funcionario(a):</th>
                <td> ".$txtDependencia."</td>
            </tr>
            <tr style='width:50%'>
                <th scope='row'>Destino de los bienes</th>
                <td> ".$txtDestino. "</td>
            </tr>
            <tr style='width:50%'>
                <th scope='row'> Fecha de reingreso </th>
                <td> ".$prestamo_fechaDevolucion_formato."</td>
            </tr>                       
        </tbody> 
        </table> </div> <br/>";
                
        $phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
        $phpmailer->Send();
        
        return true; 
 
    }
    
}

?>