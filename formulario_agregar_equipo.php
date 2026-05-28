<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador")
self.location = "formulario_menu_inventario.html"
</script>';
}


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
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
//$buscar = $_POST['buscar'];


?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta// Agregar Nuevos Activos</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">


  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="fondoresponsive.css">
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  
  <link rel="stylesheet" href="alertifyjs/css/alertify.css">
  <link rel="stylesheet" href="alertifyjs/css/themes/bootstrap.css">
  <script src="alertifyjs/alertify.js"></script> 
  <script language="javascript" src="js/utf8.js"></script>
<script language="javascript">
function contar() {
var checkboxes = formulario.idactivo; //Array que contiene los checkbox
var cont = 0; //Variable que lleva la cuenta de los checkbox pulsados
	for (var x=0; x < checkboxes.length; x++) {
		if (checkboxes[x].checked) {
			cont = cont + 1;
		}
	}
		document.getElementById("conteo").value = cont;
}
</script>
            <style>
		.button {
		  background-color: #0174DF;
		  border: none;
		  color: white;
		  padding: 15px 32px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 18px;
		  margin: 4px 2px;
		  cursor: pointer;
		  width: 200px;
                 text-transform: uppercase;
                 letter-spacing: 2px;
                 border-radius: 10px;
                 transition: all 300ms;
		}
		.button:hover{
		   text-decoration: none;
                }
	     </style>
</head>
<body>

<nav class="navbar navbar-expand-md bg-dark navbar-dark">
  <img src="img/logomep2020.png" width="45" height="30" alt="" loading="lazy"><a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="inventario_activo.php"><span class="icon icon-undo2"></span> Inventario</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><span class="icon icon-enter"></span> Cerrar Sesi&oacute;n</a>
      </li>  
    </ul>
  </div>
</nav>
<br>

<div class="container">

	  <strong><h3>USUARIO REGISTRADO: <?php echo $lognombre; ?> </h3></strong>

<br>
<h3>Agregar Nuevos Activos</h3><br>
<font size="5" color="#0000ff"> <b>Activos Preconstruidos:</b> Antes de crear un nuevo activo, por favor busca en nuestro inventario existente. Puedes buscar por tipo, modelo, color o marca. Si no encuentras el activo que buscas, procede a crearlo. </font><br>
<br>
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text" id="inputGroup-sizing-default">Buscar</span>
      </div>
      <input 
        type="text" 
        name="caja_busqueda" 
        id="caja_busqueda" 
        class="form-control" 
        aria-label="Sizing example input" 
        aria-describedby="inputGroup-sizing-default"
      >
      <a>&nbsp</a>
      <a href="formulario_agregar_activo.php" class="btn btn-dark">
        <span class="icon icon-plus"> Construir Modelo General</span>
      </a>
    </div>




<div id="datos">

</div>


</div>




  <script>
    function typeWriterEffect(element, text, speed) {
      let i = 0;
      element.setAttribute("placeholder", ""); // Limpiamos el placeholder inicial

      function type() {
        if (i < text.length) {
          // Añadimos la siguiente letra al placeholder
          element.setAttribute("placeholder", element.getAttribute("placeholder") + text.charAt(i));
          i++;
          // Llamamos a la función de nuevo después de un tiempo
          setTimeout(type, speed);
        }
      }

      // Iniciamos el efecto
      type();
    }

    // Seleccionamos el input
    const placeholderElement = document.getElementById("caja_busqueda");

    // Texto que se escribirá automáticamente
    const placeholderText = "Ingrese el modelo, marca o descripción...";

    // Velocidad de escritura (en milisegundos)
    const typingSpeed = 100; // 100 ms por letra

    // Aplicamos el efecto
    typeWriterEffect(placeholderElement, placeholderText, typingSpeed);
  </script>
    <script>
    function keepSessionAlive() {
        setInterval(function() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "keep_alive.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Opcional: Mantener la sesion activa
                    // console.log("Session kept alive");
                }
            };
            xhr.send();
        }, 300000); // 300000 milisegundos = 5 minutos
    }
    
    // Iniciar la función cuando la página cargue
    window.onload = keepSessionAlive;
    </script>
<script>
function keepSessionAlive() {
    setInterval(function() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "keep_alive.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Opcional: Mantener la sesion activa
                // console.log("Session kept alive");
            }
        };
        xhr.send();
    }, 300000); // 300000 milisegundos = 5 minutos
}

// Iniciar la función cuando la página cargue
window.onload = keepSessionAlive;
</script>
  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/fajaxbuscarx.js"></script> <!-- main.js -->


  
</body>
</html>