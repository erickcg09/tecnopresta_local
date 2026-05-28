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
$logusuario = $_SESSION['cedula'];
$logcodigo = $_SESSION['codigo'];

if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}

$post = (isset($_POST['cbx_placa']) && !empty($_POST['cbx_placa']));



// Inicia detecci&oacute;n de campos vacios y el pase de variables 


if($post)
{
    
    $funcionario = $_POST['funcionario']." ".$logusuario." ".$logcodigo;
    $correo = $_POST['correo'];
    $institucion = $_POST['institucion'];
    $codigo = $_POST['codigo'];
    $fecha= $_POST['fecha'];
    $estatus = $_POST['estatus'];
    $id_activo = $_POST['cbx_activo'];
    $placa = $_POST['cbx_placa'];
    $problema = $_POST['problema'];
    $dre = $_POST['dre'];
    $circuito = $_POST['circuito'];
    $tomado = "No";
    $ahora = date("Y-m-d");
$consulta=mysqli_query($link,"SELECT Ta.modelo, Ta.id_ag, Tm.marca, Ta.modelo, Tc.color, Tag.clase
		 FROM t_activo Ta
		 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 INNER JOIN t_activo_general Tag ON Ta.id_ag = Tag.id_ag
		 WHERE Ta.id_activo = '".$id_activo."' 
		 ORDER BY Ta.modelo ASC") or die(mysqli_error($link));
	        
	while ($activos=mysqli_fetch_array($consulta)) {
	    $etiqueta=$activos['clase']." ".$activos['modelo']." ".$activos['marca']." "."color ".$activos['color'];

	}
    	
    $sql = "INSERT INTO soporte (`id`, `funcionario`, `placa`, `problema`, `fecha`, `estatus`, `codigo`, `correo`, `institucion`, `tomado`, `dre`, `circuito`, `descriactivo`)
    VALUES (NULL, '$funcionario', '$placa', '$problema', '$ahora', '$estatus', '$codigo', '$correo', '$institucion', '$tomado', '$dre', '$circuito', '$etiqueta')";
    
    if (mysqli_query($link, $sql)) {
      echo "New record created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . mysqli_error($link);
    }
    
    mysqli_close($link);
    
    
    echo '<script language = javascript>
                    alert("Guardado")
                    self.location = "plataforma_clientes.php"
                    </script>';
} else {

    echo '<script language = javascript>
                    alert("Debe seleccionar la placa del activo")
                    self.location = "plataforma_clientes.php"
                    </script>';
} //Cierre del if principal
?>