<?php
session_start();
if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.html"
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

// Inicia detecci&oacute;n de campos vacios y el pase de variables 

$post = (isset($_POST['idsplacas']) && !empty($_POST['idsplacas'])) &&
	    (isset($_POST['cbx_regional']) && !empty($_POST['cbx_regional'])) &&
	    (isset($_POST['cbx_circuito']) && !empty($_POST['cbx_circuito'])) &&
        (isset($_POST['cbx_edificio']) && !empty($_POST['cbx_edificio'])) &&
        (isset($_POST['cbx_estancia']) && !empty($_POST['cbx_estancia'])) &&
        (isset($_POST['cubiculo']) && !empty($_POST['cubiculo']));

if($post)
{

$cadena = $_POST['idsplacas'];
$array = explode(",", $cadena);
$regional = $_POST['cbx_regional'];
$circuito = $_POST['cbx_circuito'];
$edificio = $_POST['cbx_edificio'];
$estancia = $_POST['cbx_estancia'];
$cubiculo = $_POST['cubiculo'];
$lista = array();
	foreach($array as $id_placa)
	{
	          $miconsulta = "select id_ubicacion from t_ubicacion_activo where id_placa='$id_placa'";
                  $mirespuesta = $link->query($miconsulta);
			if($mirespuesta->num_rows >= 1){
			  array_push($lista, $id_placa);
			}
	} // Cierre del foreach

   if(!empty($lista)){

			echo '<script language = javascript>
		        alert("La ubicaci\u00f3n del activo con ID '.implode(",", $lista).' ya se encuentra reportada")
		        self.location = "formulario_agregar_ubicacion.php"
		        </script>';
   } else {

	foreach($array as $idplaca)
	{
		        $consulta = "INSERT INTO t_ubicacion_activo (id_regional,id_circuito,id_placa,id_edificio,id_estancia,cubiculo)VALUES('".$regional."','".$circuito."','".$idplaca."','".$edificio."','".$estancia."','".$cubiculo."')";
		        $link->query($consulta);
	} // Cierre del foreach
			echo '<script language = javascript>
		        alert("Guardado correctamente")
		        self.location = "formulario_agregar_ubicacion.php"
		        </script>';
   } // Fin del if interno
  
} else {

			echo"<script type=\"text/javascript\">
                        alert(\"Debe completar todos los campos\");
                        window.location=\"formulario_reportar_ubicacion.php\"
                        </script>";
} //Cierre del if principal
?>
