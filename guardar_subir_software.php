<?php
//conexion a la base de datos
require_once("conexion.php");
$link = $mysqli;

//Iniciar Sesion
session_start();

//Validar si se esta ingresando con sesion correctamente
if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.html"
</script>';
}


$software = $_POST["software"]; 
$imagen = $_FILES['imagen']['name'];
$id_marca = $_POST["marca"];

    if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
    } else {
    	
    }


$post = (isset($_POST['software']) && !empty($_POST['software'])) &&
        (isset($_POST['marca']) && !empty($_POST['marca']));

if($post)
{

    $consulg = "select id_sg from t_software_general where etiqueta='$software'";
    $resultg = mysqli_query($link,$consulg);
    $check_user = mysqli_num_rows($resultg);
    
    
    if($check_user>0){
      echo '<script language = javascript>
      alert("Ya existe en la base de datos")
      self.location = "formulario_crear_software.php"
      </script>';
    } else {

        //comprobamos si ha ocurrido un error.
        if ($_FILES["imagen"]["error"] > 0){
        	echo "ha ocurrido un error";
        } else {
        	//ahora vamos a verificar si el tipo de archivo es un tipo de imagen permitido.
        	//y que el tamano del archivo no exceda los 100kb
        	$permitidos = array("image/png");
        	$limite_kb = 180;
        
        	if (in_array($_FILES['imagen']['type'], $permitidos) && $_FILES['imagen']['size'] <= $limite_kb * 1024){
        		//esta es la ruta donde copiaremos la imagen
        		//recuerden que deben crear un directorio con este mismo nombre
        		//en el mismo lugar donde se encuentra el archivo subir.php
        		$ruta = "ico/" . $_FILES['imagen']['name'];
        		//comprobamos si este archivo existe para no volverlo a copiar.
        		//pero si quieren pueden obviar esto si no es necesario.
        		//o pueden darle otro nombre para que no sobreescriba el actual.
        		if (!file_exists($ruta)){
        			//aqui movemos el archivo desde la ruta temporal a nuestra ruta
        			//usamos la variable $resultado para almacenar el resultado del proceso de mover el archivo
        			//almacenara true o false
        			$resultado = @move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
        			if ($resultado){
        				$nombre = $_FILES['imagen']['name'];
        				$query = "INSERT INTO t_software_general (etiqueta, id_marca, imagen)VALUES('".$software."', '".$id_marca."', '".$imagen."')";
        $link->query($query);
        mysqli_close($link);
        			echo"<script type=\"text/javascript\">
                                alert(\"Archivo movido correctamente\");
                                window.location=\"formulario_crear_software.php\"
                                </script>";
        			} else {
        				echo"<script type=\"text/javascript\">
                                alert(\"Error al mover el archivo\");
                                window.location=\"formulario_crear_software.php\"
                                </script>";
        			}
        			} else {
        			echo"<script type=\"text/javascript\">
                                alert(\"Archivo ya existe\");
                                window.location=\"formulario_crear_software.php\"
                                </script>";
        			}
        			} else {
        			echo"<script type=\"text/javascript\">
                                alert(\"Tama\u00f1o de archivo no permitido\");
                                window.location=\"formulario_crear_software.php\"
                                </script>";
        			}
        }
    }
} else {

			echo"<script type=\"text/javascript\">
                        alert(\"Debe completar todos los campos\");
                        window.location=\"formulario_crear_software.php\"
                        </script>";


} //Cierre del if principal

?>
