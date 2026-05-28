<?php  
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "inventario_reporte.php"
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

$funcionario = $_POST['funcionario'];
$cedula_f = $_POST['cedula_funcionario'];
$institucion = $_POST['institucion'];
$codigo_i = $_POST['codigo_institucion'];
$direccion_r = $_POST['direccion_reg'];
$circuito = $_POST['circuito'];
$completo = $_POST['completo'];
$comentario = $_POST['comentario'];

$queryX = "select id_confirmacion FROM t_confirmacion_entrega_fonatel WHERE codigo_i='$codigolog'";
$resultX = mysqli_query($link,$queryX);
$check_user = mysqli_num_rows($resultX);
        
if($check_user>0){
          echo '<script language = javascript>
          alert("Su instituci\u00f3n ya realiz\u00f3 el reporte")
          self.location = "inventario_reporte.php"
          </script>';
} else {   

	foreach($_POST['idsentregas'] as $identrega)
	{

        $tipotemp = $_POST['tipo'.$identrega];

		$update = "UPDATE t_entrega_fonatel SET recibido = '".$tipotemp."' WHERE id_entrega = '".$identrega."'";
		$link->query($update);  

		if (mysqli_query($link, $update)) {
		    
		} else {
		    echo "Error al actualizar registro: " . mysqli_error($link);
		}
    } // Cierre del foreach
    
    	$sql = "INSERT INTO `t_confirmacion_entrega_fonatel` (`funcionario`, `cedula_f`, `institucion`, `codigo_i`, `direccion_r`, `circuito`, `completo`, `comentario`) VALUES ('$funcionario', '$cedula_f', '$institucion', '$codigo_i', '$direccion_r', '$circuito', '$completo', '$comentario');";
		mysqli_query($link,$sql);
		mysqli_close($link);
		  echo '<script language = javascript>
          alert("Su reporte se realiz\u00f3 correctamente")
          self.location = "inventario_reporte.php"
          </script>';
} // Cierre del if else
 ?>