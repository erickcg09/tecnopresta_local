<?php
//conexion a la base de datos
require_once("conexion.php");
$link = $mysqli;

//Iniciar Sesión
session_start();

//Validar si se está ingresando con sesi車n correctamente
if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.html"
</script>';
}


$alias = $_POST["alias"]; 
//$imagen = $_FILES['imagen']['name'];
$imagen = $_POST["imagen"];
$codigo = $_POST['codigo'];

    if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
    } else {
    	
    }

$post = (isset($_POST['imagen']) && !empty($_POST['imagen'])) &&
        (isset($_POST['codigo']) && !empty($_POST['codigo'])) &&
        (isset($_POST['alias']) && !empty($_POST['alias']));
        

if($post)
{

//comprobamos si ha ocurrido un error.
//if ($_FILES["imagen"]["error"] > 0){
	//echo "ha ocurrido un error";
//} else {
	//ahora vamos a verificar si el tipo de archivo es un tipo de imagen permitido.
	//y que el tamano del archivo no exceda los 100kb
	//$permitidos = array("image/png");
	//$limite_kb = 180;

	//if (in_array($_FILES['imagen']['type'], $permitidos) && $_FILES['imagen']['size'] <= $limite_kb * 1024){
		//esta es la ruta donde copiaremos la imagen
		//recuerden que deben crear un directorio con este mismo nombre
		//en el mismo lugar donde se encuentra el archivo subir.php
		//$ruta = "img/" . $_FILES['imagen']['name'];
		//comprobamos si este archivo existe para no volverlo a copiar.
		//pero si quieren pueden obviar esto si no es necesario.
		//o pueden darle otro nombre para que no sobreescriba el actual.
		//if (!file_exists($ruta)){
			//aqui movemos el archivo desde la ruta temporal a nuestra ruta
			//usamos la variable $resultado para almacenar el resultado del proceso de mover el archivo
			//almacenara true o false
			//$resultado = @move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
			//if ($resultado){
				//$nombre = $_FILES['imagen']['name'];
				$query = "INSERT INTO t_alias (alias, alias_imagen, codigo)VALUES('".$alias."', '".$imagen."', '".$codigo."')";
				$link->query($query);
				mysqli_close($link);
			echo"<script type=\"text/javascript\">
                        alert(\"Registro Guardado\");
                        window.location=\"formulario_crear_alias.php\"
                        </script>";
			/* } else {
				echo"<script type=\"text/javascript\">
                        alert(\"Error al mover el archivo\");
                        window.location=\"formulario_crear_alias.php\"
                        </script>";
			}
			} else {
			echo"<script type=\"text/javascript\">
                        alert(\"Archivo ya existe\");
                        window.location=\"formulario_crear_alias.php\"
                        </script>";
			}
			} else {
			echo"<script type=\"text/javascript\">
                        alert(\"Tamano de archivo no permitido\");
                        window.location=\"formulario_crear_alias.php\"
                        </script>";
			}
} */

} else {

			echo"<script type=\"text/javascript\">
                        alert(\"Debe completar todos los campos\");
                        window.location=\"formulario_crear_alias.php\"
                        </script>";
} //Cierre del if principal
?>