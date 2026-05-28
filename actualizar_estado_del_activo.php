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

$login_usuario = $_SESSION['username'];


if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {
    	
}


// sql para actualizar





if(empty($_POST['idsplacas']))
{
	  echo '<script language = javascript>
  alert("No hay ningun activo seleccionado")
  self.location = "formulario_filtrar_activos_codigo.php"
  </script>';
}
else {
		
	foreach($_POST['idsplacas'] as $idplaca)
	{

		$id_estado = $_POST['estado'.$idplaca];
		// sql para actualizar

		$update = "UPDATE t_placa SET id_estado = '".$id_estado."' WHERE id_placa = '".$idplaca."'";
		$link->query($update);  

		if (mysqli_query($link, $update)) {
		    
		} else {
		    echo "Error al actualizar registro: " . mysqli_error($link);
		}
	} // Cierre del foreach
} // Cierre IF principal
mysqli_close($link);


echo '<script language = javascript>
  alert("Cambios realizados")
  self.location = "formulario_filtrar_activos_codigo.php"
  </script>';
?>
