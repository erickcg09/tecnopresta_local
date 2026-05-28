<?php

error_reporting(-1);  //Remove from production version
ini_set("display_errors", "on");  //Remove from production version

$url = "https://apps.mep.go.cr/wstecnopresta/servicio.asmx?WSDL"; // granjeado 

//https://ws.mep.go.cr/wstecnopresta/servicio.asmx?WSDL

//$correo = $_POST['correo'];
//$pass = $_POST['pass'];
$cedula = "";

try {

    $client = new SoapClient($url);
       
     $resValidaCredenciales = $client->ValidaCredenciales(array(
                            'str_correo' => 'gonzalo.vargas.espinoza@mep.go.cr',
                            'str_pass' => ''));

    $cedula = $resValidaCredenciales->ValidaCredencialesResult;
    echo  $cedula;
    echo "<br>";
    
    $new_strcedula = str_replace('"', '', $cedula); //Las consulta devuelve "" de más y son eliminadas
                                                    // en la variable new_strcedula

    echo  $new_strcedula;
    echo "<br>";
    
    //JSON con información del usuario
    $resConsultaFuncionario = $client->ConsultaFuncionario(array('str_identificacion' => $new_strcedula));
            
    echo "<br>";            
    echo $resConsultaFuncionario->ConsultaFuncionarioResult;

} catch (\Throwable $th) {
    echo "Error al conectar con la base de datos: " . $th->getMessage() . "\n";
	exit;		
}

?>