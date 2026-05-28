<?php
session_start();
$tienellave = ($_SESSION['tipo'] == 1); // Permitir root solamente
if ($tienellave == false) {
    echo '<script language = javascript>
    alert("No tienes permisos")
    self.location = "formulario_menu_principal.html"
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
$xeliminar = $_GET['id']; 


		// sql para eliminar
		$sql = "DELETE FROM t_lista_blanca WHERE id_lista_blanca=$xeliminar";

		if (mysqli_query($link, $sql)) {
		    
		} else {
		    echo "Error al eliminar registro: " . mysqli_error($link);
		}
		// Redireccion al index 
	    echo '<script language = javascript>
        alert("Eliminado correctamente")
        self.location = "formulario_administracion_permisos.php"
        </script>';
		exit();
?> 