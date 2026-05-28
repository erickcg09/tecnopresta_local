<?php

//$url = "http://webapps.intranet.mep.go.cr/WSTECNOPRESTA/";
//$url = "http://webapps.intranet.mep.go.cr/wstecnopresta/servicio.asmx?WSDL";
//$url = "http://webapps.intranet.mep.go.cr/wstecnopresta/servicio.asmx";
//$url = "http://webapps.intranet.mep.go.cr/WSTECNOPRESTA/servicio.asmx?WSDL";
//$url = "http://190.10.69.199/WSTECNOPRESTA/servicio.asmx?WSDL";
$url = "http://apps.mep.go.cr/wstecnopresta/servicio.asmx?WSDL";
echo($url."<br>\n");
//echo phpinfo();


$correo = "mauricio.bermudez.vargas";
$pass = "Hkfe3610";
$cedula = "";
$correoDominio = $correo . "@mep.go.cr";

try {

    $client = new SoapClient($url);

    //Obtiene la cédula del usuario mediante la validación de correo y password    
    $resValidaCredenciales = $client->ValidaCredenciales(array(
                            'str_correo' => $correoDominio,
                            'str_pass' => $pass));
    
    $cedula = $resValidaCredenciales->ValidaCredencialesResult;
    
    $new_strcedula = str_replace('"', '', $cedula); //Las consulta devuelve "" de más y son eliminadas
                                                    // en la variable new_strcedula

    //JSON con información del usuario
    $resConsultaFuncionario = $client->ConsultaFuncionario(array('str_identificacion' => $new_strcedula));
    echo $resConsultaFuncionario->ConsultaFuncionarioResult;

} catch (\Throwable $th) {
    echo "Error al conectar con la base de datos: " . $th->getMessage() . "\n";
	exit;		
}
echo("<br>\n".'--------------------'."<br>\n");
$url = "http://ws.mep.go.cr/wstecnopresta/servicio.asmx?WSDL";
echo($url."<br>\n");
//echo phpinfo();

$correo = "mauricio.bermudez.vargas";
$pass = "Hkfe3610";
$cedula = "";
$correoDominio = $correo . "@mep.go.cr";

try {

    $client = new SoapClient($url);

    //Obtiene la cédula del usuario mediante la validación de correo y password    
    $resValidaCredenciales = $client->ValidaCredenciales(array(
                            'str_correo' => $correoDominio,
                            'str_pass' => $pass));
    
    $cedula = $resValidaCredenciales->ValidaCredencialesResult;
    
    $new_strcedula = str_replace('"', '', $cedula); //Las consulta devuelve "" de más y son eliminadas
                                                    // en la variable new_strcedula

    //JSON con información del usuario
    $resConsultaFuncionario = $client->ConsultaFuncionario(array('str_identificacion' => $new_strcedula));
    echo $resConsultaFuncionario->ConsultaFuncionarioResult;

} catch (\Throwable $th) {
    echo "Error al conectar con la base de datos: " . $th->getMessage() . "\n";
	exit;		
}
// Para prueba local.
//$url = "http://www.ws.mep.go.cr/wstecnopresta/servicio.asmx?WSDL";
?>
