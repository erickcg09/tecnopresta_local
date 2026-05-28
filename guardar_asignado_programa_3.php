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

$post = (isset($_POST['idbeneficiario']) && !empty($_POST['idbeneficiario']));



// Inicia detecci&oacute;n de campos vacios y el pase de variables 


if($post)
{
    
    $placa = $_POST['placa'];
    $serial = $_POST['serial'];
    $idbeneficiario = $_POST['idbeneficiario'];
    $fechai = $_POST['fechai'];
    $fechaf = $_POST['fechaf'];
    $year = date('Y');
    $devuelto = "No";
    $asignado ="Asignado";
    	
    	
    $miconsulta = "select id from activos_beneficiarios_programa_3 where
    id_benef='$idbeneficiario'";
    $mirespuesta = $link->query($miconsulta);
    
    if ($mirespuesta->num_rows >= 1) {
        		echo '<script language = javascript>
                alert("No se puede asignar mas de un activo por estudiante")
                self.location = "beneficiarios_programa_3.php"
                </script>';
    }else{

        $sql = "INSERT INTO activos_beneficiarios_programa_3 (`id`, `id_benef`, `placa`, `serial`, `periodo`, `fechai`, `fechaf`, `devuelto`)
        VALUES (NULL, '$idbeneficiario', '$placa', '$serial', '$year', '$fechai', '$fechaf', '$devuelto')";
        
        if (mysqli_query($link, $sql)) {
          
        } else {
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
        
        	$update = "UPDATE beneficiarios_programa_3 SET entregado= '".$asignado."' WHERE id = '".$idbeneficiario."'";
    		$link->query($update);  
    
    		if (mysqli_query($link, $update)) {
    		    
    		} else {
    		    echo "Error al actualizar registro: " . mysqli_error($link);
    		}
        
        mysqli_close($link);
        
        
        echo '<script language = javascript>
                        alert("Guardado")
                        self.location = "beneficiarios_programa_3.php"
                        </script>';
    }
} else {

    echo '<script language = javascript>
                    alert("Algo salio mal, por favor intente de nuevo")
                    self.location = "beneficiarios_programa_3.php"
                    </script>';
} //Cierre del if principal
?>