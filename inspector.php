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
$logcodigo = $_SESSION['codigo'];
$logcorreo = $_SESSION['correomep'];
$logdireccionreg = $_SESSION['direccionreg'];
$logcircuito = $_SESSION['circuito'];
$logclase = $_SESSION['clase'];
$logespecialidad = $_SESSION['especialidad'];
$logdependencia = $_SESSION['dependencia'];


//if(!$_COOKIE['saludo-correo']){
//  header('Location:crearcookie.php');
//}else if($_COOKIE['saludo-correo'] == 'si'){
  header('Location:perfil_usuario.php');
//}


?>