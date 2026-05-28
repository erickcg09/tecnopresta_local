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

$post = (isset($_POST['clase']) && !empty($_POST['clase'])) &&
        (isset($_POST['marca']) && !empty($_POST['marca'])) &&
		(isset($_POST['modelo']) && !empty($_POST['modelo'])) &&
		(isset($_POST['alias']) && !empty($_POST['alias'])) &&
        (isset($_POST['color']) && !empty($_POST['color']));

if (isset($_POST['numero']) && !empty($_POST['numero'])) {
	$numero = $_POST['numero'];
} else {
	$numero=0;
}

if($post)
{

$id_ag = $_POST['clase'];
$id_marca = $_POST['marca'];
$modelo = $_POST['modelo'];
$id_color = $_POST['color'];
$alias_id = $_POST['alias'];

	          $miconsulta = "select id_activo from t_activo where modelo='$modelo'";
                  $mirespuesta = $link->query($miconsulta);

		  if($mirespuesta->num_rows >= 1){

			echo '<script language = javascript>
		        alert("El modelo ya existe en la lista")
		        self.location = "formulario_agregar_activo.php"
		        </script>';
	          } else {

		        $consulta = "INSERT INTO t_activo (id_ag,id_marca,modelo,id_color, alias_id, numero_activo)VALUES('".$id_ag."','".$id_marca."','".$modelo."','".$id_color."', '".$alias_id."', '".$numero."')";
		        $link->query($consulta);
			echo '<script language = javascript>
		        alert("Guardado correctamente")
		        self.location = "formulario_agregar_activo.php"
		        </script>';
		  } // Fin del if interno
  
} else {

			echo"<script type=\"text/javascript\">
                        alert(\"Debe completar todos los campos\");
                        window.location=\"formulario_agregar_activo.php\"
                        </script>";
} //Cierre del if principal
?>
