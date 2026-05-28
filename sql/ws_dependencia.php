<?php

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

//$url = "http://www.ws.mep.go.cr/wstecnopresta/servicio.asmx?WSDL";
$url = "http://ws.mep.go.cr/WSTecnoPresta/servicio.asmx?WSDL"; 
//$url = "http://ws.mep.go.cr/WSTecnoPresta/Servicio.asmx?WSDL";
//echo phpinfo();

//$codigoPre = "210-573-02-67-4139";
$cedula= "0701460180";

try {

    $client = new SoapClient($url);

  //  Obtiene la cedula del usuario mediante la validacion de correo y password    
    $resConsultaDependenciasMEP = $client->ConsultaDependenciasMEP(array(
                            'str_cedula' => $cedula));
    $resConsultaDependenciasMEP->ConsultaDependenciasMEPResult;
    
   // echo $resConsultaDependenciasMEP;
   
	$json = array();
	$json =$resConsultaDependenciasMEP->ConsultaDependenciasMEPResult;
   
    while( $q_dependencia=$json){
    
    /*	$query = "INSERT INTO t_dependencia (codigo_presupuestario,codigo_institucion,nombre,codigo_region,region,circuito,distrito,canton,provincia)
   						VALUES('".$q_dependencia[Codigo_Presupuestario]."','".$q_dependencia[CODIGOINST]."',
   						'".$q_dependencia[Nombre]."','".$q_dependencia[CODREG]."','".$q_dependencia[region]."',
   						'".$q_dependencia[CIRCUITO]."','".$q_dependencia[DISTRITO]."','".$q_dependencia[CANTON]."',
   						'".$q_dependencia[PROVINCIA]."')";
				$link->query($query); */
				
				echo $q_dependencia['Nombre'];
	}
				mysqli_close($link);
  
  
   
    
    
 

 
 
 
/* $resConsultaFuncionario = $client->ConsultaFuncionario(array('str_identificacion' => $cedula));
    echo $resConsultaFuncionario->ConsultaFuncionarioResult;

  */

} catch (\Throwable $th) {
    echo "Error al conectar con la base de datos: " . $th->getMessage() . "\n";
	exit;		
}
// Para prueba local.
//$url = "http://www.ws.mep.go.cr/wstecnopresta/servicio.asmx?WSDL";
?>