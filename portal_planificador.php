<?php
    session_start();

require_once("conexion_var.php");
$link = mysqli_connect($servername, $username, $password, $dbname);

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}

	if(isset($_SESSION['funcionario'])){
		
	    
		$string = $_SESSION['funcionario'];
        $resultado = json_decode($string, true);


        $nom = $resultado['Nombre'];
        $ap1 = $resultado['Apellido1'];
        $ap2 = $resultado['Apellido2'];
        $cedula = $resultado['EMPCED'];
        $dependencia = $resultado['Dependencia'];
        $correomep = $resultado['Correo_Electronico_Oficial'];        
        $codigo = $resultado['CentrosEducativosDondeTrabaja'];
        $nombre = $nom." ".$ap1." ".$ap2;
        $direccionregional = $resultado['NombreRegional'];
        $circuito = $resultado['Circuito'];
        
        $query = "select id_lista_blanca from t_lista_blanca where cedula='$cedula' AND codigo='$codigo'";
        $resulta = mysqli_query($link,$query);
        $check_user = mysqli_num_rows($resulta);
        
        if($check_user>0){
        
            // consulta
            $sql = "SELECT id_rol FROM t_lista_blanca WHERE cedula='$cedula' AND codigo='$codigo'";
            $result = mysqli_query($link,$sql);
            
            // Array asociativo
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $tipo = $row["id_rol"];
    
            // Free result set
            mysqli_free_result($result);
            
            mysqli_close($link);        
            
            
            $_SESSION['cedula']=$cedula;
            $_SESSION['nombre']=$nombre;
            $_SESSION['codigo']=$codigo;
            $_SESSION['tipo']=$tipo;
            $_SESSION['dependencia']=$dependencia;
            $_SESSION['correomep']=$correomep;
            $_SESSION['direccionreg']=$direccionregional;
            $_SESSION['circuito']=$circuito;
            
                header("Location: informe_calendario.php");
        } else {
            $tipo = 5;
            $_SESSION['cedula']=$cedula;
            $_SESSION['nombre']=$nombre;
            $_SESSION['codigo']=$codigo;
            $_SESSION['tipo']=$tipo;
            $_SESSION['dependencia']=$dependencia;
            $_SESSION['correomep']=$correomep;
            $_SESSION['direccionreg']=$direccionregional;
            $_SESSION['circuito']=$circuito;
            
                header("Location: informe_calendario.php");
        }
	}else{
	    echo "Error al cargar variables de sesion";
	}


?>