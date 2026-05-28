<?php

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



        $post = (isset($_POST['cedula']) && !empty($_POST['cedula'])) &&
                (isset($_POST['nombre']) && !empty($_POST['nombre'])) &&
                (isset($_POST['codigop']) && !empty($_POST['codigop']));
        
        if($post)
        {
        
        $rol = 2;
        $cedula = "0".$_POST['cedula'];
        $nombre = $_POST['nombre'];
        $codigop = $_POST['codigop'];

$cadena_devuelta = strtolower($nombre);
$searchString = " ";
$replaceString = "";
$originalString = $cedula; 
 
$outputString = str_replace($searchString, $replaceString, $originalString); 


        
        	          $miconsulta = "select id_lista_temporal from t_lista_temporal where cedula='$outputString' AND codigo='$codigop'";
                          $mirespuesta = $link->query($miconsulta);
        
        		  if($mirespuesta->num_rows >= 1){
        
        			echo '<script language = javascript>
        		        alert("El usuario ya fue reportado")
        		        self.location = "solicitud_admin.html"
        		        </script>';
        	          } else {
        
        		        $consulta = "INSERT INTO t_lista_temporal (cedula,nombre,codigo,id_rol)VALUES('".$outputString."','".$cadena_devuelta."','".$codigop."','".$rol."')";
        		        $link->query($consulta);
        		        
        			echo '<script language = javascript>
        		        alert("Muchas gracias, datos enviados")
        		        self.location = "solicitud_admin.html"
        		        </script>';
          
        		  }
	}




?>
