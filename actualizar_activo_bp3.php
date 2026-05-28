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
date_default_timezone_set('America/Costa_Rica'); //configuro un nuevo timezone 


if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}

$post = (isset($_POST['idabp3']) && !empty($_POST['idabp3']));

if($post)
{
    $placa = $_POST['placa'];
    $serial = $_POST['serial'];
    $id = $_POST['idabp3'];

        	$update = "UPDATE activos_beneficiarios_programa_3 SET placa= '".$placa."', serial= '".$serial."' WHERE id = '".$id."'";
    		$link->query($update);  
    
    		if (mysqli_query($link, $update)) {
    		    
    		} else {
    		    echo "Error al actualizar registro: " . mysqli_error($link);
    		}
    		
    		    echo '<script language = javascript>
                    alert("Actualizado")
                    self.location = "beneficiarios_programa_3.php"
                    </script>';
} else {

    echo '<script language = javascript>
                    alert("No puede haber campos en blanco")
                    self.location = "beneficiarios_programa_3.php"
                    </script>';
} //Cierre del if principal    		
    		
?>