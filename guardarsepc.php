<?php
session_start();
if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.php"
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

// Inicia deteccion de campos vacios y el pase de variables 

$post = (isset($_POST['buscar_placa']) && !empty($_POST['buscar_placa'])) &&
        (isset($_POST['id_software']) && !empty($_POST['id_software'])) &&
        (isset($_POST['permitidos']) && !empty($_POST['permitidos'])) ;
 

if($post)
{

$placa = trim($_POST['buscar_placa']);
$id_software = $_POST['id_software'];
$permitidos = $_POST['permitidos'];
$instalados = $_POST['instalados'];


settype($permitidos, 'int'); 
settype($instalados, 'int'); 
if ($permitidos > $instalados) {
  $n = "Permitido";
}

	switch ($n) {
	  case "Permitido":
	  $sqly = "select id_placa from t_placa where placa='$placa'";
	  $resulty = $link->query($sqly);
	  if ($resulty->num_rows >= 1) {

			$sql1 = mysqli_query($link, "select id_placa from t_placa where placa='$placa'");   
			$resp1 = mysqli_fetch_array($sql1);
			$id_placa = $resp1[id_placa];


			$sql = "select id_licencia from t_licencia where id_software='$id_software' and id_placa='$id_placa'";
			$result = $link->query($sql);
			
			if ($result->num_rows >= 1) {

				  $respuesta = new stdClass();
				  $respuesta->mensaje = "Los registros ya se encuentran reportados"."<script>error()</script>";
				  echo json_encode($respuesta);

			} else {

				  
				  $consulta = "INSERT INTO t_licencia (id_placa,id_software)VALUES('$id_placa','$id_software')";
				  
				  
				  $respuesta = new stdClass();
				  
				  if($link->query($consulta)){
				    $respuesta->mensaje = "Se guard&oacute; correctamente, desea agregar otro"."<script>ok()</script>";
				  }
				  else {
				    $respuesta->mensaje = "Ocurri&oacute; un error"."<script>error()</script>";
				  }
				  echo json_encode($respuesta);
			}  
	  } else {
		  $respuesta = new stdClass();
		  $respuesta->mensaje = "La placa del activo no ese registra entre los activos"."<script>error()</script>";
		  echo json_encode($respuesta);
	  } // Cierre if principal dentro del case switch
	    break;

	  default:
	    
		  $respuesta = new stdClass();
		  $respuesta->mensaje = "No se pueden dar mas licencias"."<script>error()</script>";
		  echo json_encode($respuesta);

	} //Cierre Switch Case 
} else {

	  $respuesta = new stdClass();
	  $respuesta->mensaje = "No deben haber campos en blanco"."<script>error()</script>";
	  echo json_encode($respuesta);

} //Cierre del if principal
?>

