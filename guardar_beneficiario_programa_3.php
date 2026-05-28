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

$post = (isset($_POST['nivel']) && !empty($_POST['nivel']));



// Inicia detecci&oacute;n de campos vacios y el pase de variables 


if($post)
{
    
    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $apellidop = $_POST['apellidop'];
    $apellidom = $_POST['apellidom'];
    $sexo= $_POST['sexo'];
    $nivel = $_POST['nivel'];
    $codigo = $_POST['codigo'];
    $periodo = $_POST['periodo'];
    $estatus = $_POST['estatus'];
    $institucion = $_POST['institucion'];
    $regional = $_POST['regional'];
    $circuito = $_POST['circuito'];


    	
    $sql = "INSERT INTO beneficiarios_programa_3 (`id`, `cedula`, `nombre`, `apellidop`, `apellidom`, `nivel`, `sexo`, `codigo`, `periodo`, `entregado`, `institucion`, `regional`, `circuito`)
    VALUES (NULL, '$cedula', '$nombre', '$apellidop', '$apellidom', '$nivel', '$sexo', '$codigo', '$periodo', '$estatus', '$institucion','$regional','$circuito')";
    
    if (mysqli_query($link, $sql)) {
      
    } else {
      echo "Error: " . $sql . "<br>" . mysqli_error($link);
    }
    
    mysqli_close($link);
    
    
    echo '<script language = javascript>
                    alert("Guardado")
                    self.location = "beneficiarios_programa_3.php"
                    </script>';
} else {

    echo '<script language = javascript>
                    alert("Debe seleccionar un nivel")
                    self.location = "beneficiarios_programa_3.php"
                    </script>';
} //Cierre del if principal
?>