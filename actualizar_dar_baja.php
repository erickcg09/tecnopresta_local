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

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
$logcorreo = $_SESSION['correomep'];
$fecha = date("d/m/Y");
if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {
    	
}


// sql para actualizar


if(empty($_POST['idsplacas']))
{
	echo 'No hay ningun reistro seleccionado';
}
else {
		
	foreach($_POST['idsplacas'] as $idplaca)
	{

		$activo = $_POST['activado'.$idplaca];
		if ($activo == 1) {
            // Código a ejecutar si $activo es igual a 1
            $prestar = 1;

        } else {
            // Código a ejecutar si $activo no es igual a 1

            $prestar = 0;
        }
		// sql para actualizar

		$update = "UPDATE t_placa SET activo = '".$activo."', prestar = '".$prestar."' WHERE id_placa = '".$idplaca."'";
		$link->query($update); 


		if (mysqli_query($link, $update)) {
		    
    $sql = "INSERT INTO t_log_sacar(id_placa,cedula,nombre,codigo,fecha) VALUES('$idplaca','$logusuario','$lognombre','$logcodigo','$fecha')";
		mysqli_query($link,$sql);
		} else {
		    echo "Error al actualizar registro: " . mysqli_error($link);
		}

	} // Cierre del foreach
} // Cierre IF principal
mysqli_close($link);

echo '<script language = javascript>
  alert("Cambios realizados")
  self.location = "formulario_dar_baja.php"
  </script>';
?>
