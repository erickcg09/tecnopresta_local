<?php 
session_start();
$tienellave = ($_SESSION['tipo']==1);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "formulario_menu_inventario.html"
</script>';
}
require_once("conexion.php");
$link = $mysqli;
 


if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}

$energia = $_POST['energia'];
$ruidos = $_POST['ruidos'];
$carcasa = $_POST['carcasa'];
$memoria = $_POST['memoria'];
$cpu = $_POST['cpu'];
$comunicacion = $_POST['comunicacion'];
$entrada = $_POST['entrada'];
$salida = $_POST['salida'];
$puertos = $_POST['puertos'];
$botones = $_POST['botones'];
$bisagras = $_POST['bisagras'];
$sensores = $_POST['sensores'];
$accesorios = $_POST['accesorios'];
$controladores = $_POST['controladores'];
$software = $_POST['software'];
$dimension = $_POST['dimension'];
$detalle = $_POST['detalle'];
$id_placa = $_POST['idplaca'];
$codigo = $_POST['codigo'];
$activo = $_POST['activo'];
$fondos = $_POST['fondos'];
$quest = $_POST['quest'];
$hoy = date("d") . " del " . date("m") . " de " . date("Y");
$marcado = 1;

if ($energia == "Aprueba" AND $ruidos == "Aprueba" AND $carcasa == "Aprueba" AND $memoria == "Aprueba" AND $cpu == "Aprueba" AND $comunicacion == "Aprueba" AND $entrada == "Aprueba" AND $salida == "Aprueba" AND $puertos == "Aprueba" AND $botones == "Aprueba" AND $bisagras == "Aprueba" AND $sensores == "Aprueba" AND $accesorios == "Aprueba" AND $controladores == "Aprueba" AND $software == "Aprueba" AND $dimension == "Aprueba") {          
       
       $veredicto = "Aprobado";      
}
else {
       $veredicto = "Rechazado";
}  

$query = "select id_revision from t_revision where id_placa='$id_placa'";
$result = mysqli_query($link,$query);
$check_user = mysqli_num_rows($result);


if($check_user>0){
    
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
               <dialog id=\"dialogo\" open><p>La revisi&oacute;n del activo ya existe en el sistema</p><br><a href=\"formulario_revision_activos.php?codigo=$codigo&cbx_activo=$activo&fondos=$fondos&quest=$quest\">Continuar</a></dialog>
               
              </body>
              </html>";

} else {
mysqli_free_result($result);

	
$query = "INSERT INTO t_revision (id_placa,energia,ruidos,carcasa,memoria,cpu,comunicacion,entrada,salida,puertos,botones,bisagras,sensores,accesorios,controladores,software,dimension,detalle,fecha,veredicto)VALUES('".$id_placa."', '".$energia."', '".$ruidos."', '".$carcasa."', '".$memoria."', '".$cpu."', '".$comunicacion."', '".$entrada."', '".$salida."', '".$puertos."', '".$botones."', '".$bisagras."', '".$sensores."', '".$accesorios."', '".$controladores."', '".$software."', '".$dimension."', '".$detalle."', '".$hoy."', '".$veredicto."')";
	$link->query($query);
	
$update = "UPDATE t_placa SET revisado = '".$marcado."' WHERE id_placa = '".$id_placa."'";
$link->query($update);  

if (mysqli_query($link, $update)) {
    
} else {
    echo "Error al actualizar registro: " . mysqli_error($link);
}

	mysqli_close($link);


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
               <dialog id=\"dialogo\" open><p>La revisi&oacute;n del activo se registr&oacute; con exito</p><br><a href=\"formulario_revision_activos.php?codigo=$codigo&cbx_activo=$activo&fondos=$fondos&quest=$quest\">Continuar</a></dialog>
               
              </body>
              </html>";

} /* Cierre del else que corresponde a else del $check_user>0 */

?>