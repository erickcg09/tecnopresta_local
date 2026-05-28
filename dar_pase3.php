<?php
//conexion a la base de datos
require_once("conexion.php");
$link = $mysqli;


session_start();


if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.html"
</script>';
}


$serial = trim($_POST['serial']);


    if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
    } else {
    	
    }
	  $sql = "select id_software from t_software where licencia='$serial'";
	  $result = $link->query($sql);
	  

if ($result->num_rows >= 1) {
			$preguntar = mysqli_query($link, "select id_software from t_software where licencia='$serial'");   
			$respuesta = mysqli_fetch_array($preguntar);
			$idsoftware = $respuesta['id_software'];
			header("location: formulario_editar_licencia.php?gps=".$idsoftware);
} else {
			echo "<script type=\"text/javascript\">
                        alert(\"Serial no registrado\");
                        window.location=\"formulario_demostrar_serial.php\"
                        </script>";
}


?>