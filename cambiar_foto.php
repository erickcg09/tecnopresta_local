<?php
//conexion a la base de datos
require_once("conexion.php");
$link = $mysqli;

//Iniciar Sesión
session_start();

//Validar si se está ingresando con sesión correctamente
if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.html"
</script>';
}


$cedula = $_POST["cedula"]; 
$imagen = $_FILES['imagen']['name'];



    if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
    } else {
    	
    }

$query = "select id_perfil from t_perfil where cedula='$cedula'";
$result = mysqli_query($link,$query);
$check_user = mysqli_num_rows($result);


if($check_user>0){
    
        	$permitidos = array("image/jpeg");
	$limite_kb = 180;

	if (in_array($_FILES['imagen']['type'], $permitidos) && $_FILES['imagen']['size'] <= $limite_kb * 1024){

		$ruta = "avatar/" . $_FILES['imagen']['name'];
                $resultado = @move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);

			$update = "UPDATE t_perfil SET foto = '".$imagen."' WHERE cedula = '".$cedula."'";
			$link->query($update);  

			if (mysqli_query($link, $update)) {
			    echo "Registro actualizado";
			} else {
			    echo "Error al actualizar registro: " . mysqli_error($link);
			}

			mysqli_close($link);

					echo"<script type=\"text/javascript\">
				        alert(\"Registro Guardado\");
				        window.location=\"perfil_usuario.php\"
				        </script>";

	} else {
			echo"<script type=\"text/javascript\">
                        alert(\"Tamano de archivo no permitido o La imagen no es jpg\");
                        window.location=\"perfil_usuario.php\"
                        </script>";
	}

} else {

    	$permitidos = array("image/jpeg");
	$limite_kb = 180;

	if (in_array($_FILES['imagen']['type'], $permitidos) && $_FILES['imagen']['size'] <= $limite_kb * 1024){

		$ruta = "avatar/" . $_FILES['imagen']['name'];
                $resultado = @move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
		$query = "INSERT INTO t_perfil (cedula, foto)VALUES('".$cedula."', '".$imagen."')";
		$link->query($query);
		mysqli_close($link);
			echo"<script type=\"text/javascript\">
                        alert(\"Registro Guardado\");
                        window.location=\"perfil_usuario.php\"
                        </script>";

	} else {
			echo"<script type=\"text/javascript\">
                        alert(\"Tamano de archivo no permitido o La imagen no es jpg\");
                        window.location=\"perfil_usuario.php\"
                        </script>";
	}


} /* Cierre del else que corresponde a else del $check_user>0 */
?>