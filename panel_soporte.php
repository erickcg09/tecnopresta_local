<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
</script>';
}
include 'global/config.php';
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
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
$loginstitucion = $_SESSION['dependencia'];
$logcorreo = $_SESSION['correomep'];
$estatus = "Abierto";
$activado = 1;

	$querygeneral = "SELECT id_ag, clase FROM t_activo_general ORDER BY id_ag";
	$resultadog=$link->query($querygeneral);


	$query = "SELECT id_placa, placa, serial
		 FROM t_placa
		 WHERE codigo = '".$logcodigo."' AND activo = '".$activado."'
		 ORDER BY placa ASC";
	$resultado=$link->query($query);

$time = time();
$fecha = date("d-m-Y", $time);

//Preguntar si la consulta viene vacia

	$consulta = "SELECT id
		 FROM soporte
		 WHERE estatus = '".$estatus."' AND cedulatecnico = '".$logusuario."'
		 ORDER BY id ASC";
	$result=$link->query($consulta);
$totalFilas    =    mysqli_num_rows($result); 
if ($totalFilas == 0 ) {
        $luz = "success";
} else {
        $luz = "danger";
}

?>
<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script> 
    <title>Soporte T&eacute;cnico</title>
    
<style type="text/css">
  .pagina {
    padding:8px 16px;
    border:1px solid #ccc;
    color:#333;
    font-weight:bold;
  }
  /* Nuevos estilos agregados */
  .casos-table {
    width: 100%;
    table-layout: fixed;
  }
  .casos-table th, .casos-table td {
    padding: 8px;
    vertical-align: top;
    word-wrap: break-word;
  }
  .casos-table th {
    background-color: #f8f9fa;
    position: sticky;
    top: 0;
  }
  .table-container {
    max-height: 60vh;
    overflow-y: auto;
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
  }
  .problema-col {
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .problema-col:hover {
    white-space: normal;
    overflow: visible;
    position: relative;
    z-index: 100;
    background: white;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
  }
  @media (max-width: 768px) {
    .casos-table {
      font-size: 14px;
    }
    .casos-table th, .casos-table td {
      padding: 6px 4px;
    }
  }
</style>

  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">TecnoPresta</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
          <div class="navbar-nav">
            <a class="nav-link" aria-current="page" href="plataforma_clientes.php">
                <i class="bi bi-arrow-left-circle"></i> Regresar
            </a>
            <a class="nav-link" aria-current="page" href="administracion_plataforma_soporte.php">
                <i class="bi bi-gear"></i> Administrar
            </a>
            <a class="nav-link" href="gameover.php">
                <i class="bi bi-door-open"></i> Cerrar sesi籀n
            </a>
          </div>
        </div>
            <form action="miscasos.php" method="post" class="d-flex">
                <input type="hidden" name="cedula" value="<?php echo $logusuario;?>">
              <button type="submit" class="btn btn-primary position-relative">
                  Estado de los casos que atiendo
                  <span class="position-absolute top-0 start-100 translate-middle p-2 bg-<?php echo $luz; ?> border border-light rounded-circle">
                    <span class="visually-hidden">New alerts</span>
                  </span>
              </button>
            </form>
      </div>
    </nav>
<div class="container">
  <div class="row">
    <div class="col-12">
        
      
        
        
      
    </div>
  </div>
  <div class="row">
<div class="col-12">
<div class="panel-body">


 <div class="container">
  <div class="row">
    <div class="col-12">
        <div class="text-center">
<br>
              <img src="img/centro de soporte5.png" class="img-fluid w-75" alt="...">
        </div>
    </div> 





<?php
$record_per_page = 5;
$pagina = '';
if(isset($_GET["pagina"]))
{
 $pagina = $_GET["pagina"];
}
else
{
 $pagina = 1;
}

$start_from = ($pagina-1)*$record_per_page;


$estatus = "Abierto";
$tomado = "No";
?>

<div class="table-container">
  <table class="table casos-table">
    <thead>
      <tr>
        <th width="15%">Funcionario</th>
        <th width="15%">Instituci&oacute;n</th>
        <th width="20%">Asunto</th>
        <th width="40%">Problema</th>
        <th width="10%">Acci&oacute;n</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $registros=mysqli_query($link,"SELECT id, funcionario, placa, descriactivo, problema, fecha, estatus, codigo, correo, institucion, solucion FROM soporte WHERE estatus='".$estatus."' AND tomado='".$tomado."' order by id DESC") or
        die(mysqli_error($link));
      
      while ($reg=mysqli_fetch_array($registros)) {
        $valor = $reg['id'];
        echo "<tr>";
        echo "<td>".$reg['funcionario']."</td>";  
        echo "<td>".$reg['institucion']."</td>";    
        echo "<td>".$reg['placa']." ".$reg['descriactivo']."</td>";
        echo "<td class='problema-col' title='".htmlspecialchars($reg['problema'])."'>".$reg['problema']."</td>";
        echo "<td class='text-center'>";
        echo "<a href=\"#\" onclick=\"confirmarEnvio('$valor', '{$reg['correo']}')\" class='btn btn-sm btn-primary' title='Tomar caso'>";
        echo "<i class='bi bi-chat-right-text-fill'></i>";
        echo "</a>";  
        echo "</td>";  
        echo "</tr>";  
      }
      ?>
    </tbody>
  </table>
</div>

</div>
</div>
  </div>
</div>
<div class="panel-footer">
  <div class="container">
   
    
  </div>
</div>
    
<script>
    function confirmarEnvio(id_soporte, correosolicitante) {
        // Mostrar un cuadro de confirmaci車n
        if (confirm("\u00BFEst\u00E1 seguro de tomar este caso\u003F")) {
            // Si el usuario confirma, enviar los datos
            enviarDatos(id_soporte, correosolicitante);
        } else {
            // Si el usuario cancela, no hacer nada
            console.log("El usuario cancel車 la acci車n.");
        }
    }
    
    function enviarDatos(id_soporte, correosolicitante) {
        // Crear un objeto FormData para enviar los datos
        var formData = new FormData();
        formData.append('id_soporte', id_soporte);
        formData.append('correosolicitante', correosolicitante);
    
        // Enviar la solicitud AJAX
        fetch('guardar_tomar_caso.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log(data); // Respuesta del servidor
            alert('Caso tomado correctamente');
            // Recargar la p芍gina despu谷s de mostrar el mensaje
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al tomar el caso');
        });
    }   
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

// Iniciar la funci車n cuando la p芍gina cargue
window.onload = keepSessionAlive;
</script>

  </body>
  
  <br>
  <br>
  <br>
  <br>
  <br>
  
  
  
<footer class="bg-light text-center text-lg-start">
  <!-- Grid container -->
  <div class="container p-4">
    <!--Grid row-->
    <div class="row">
      <!--Grid column-->
      <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
        <h5 class="text-uppercase">Objetivos del Centro de Soporte</h5>

        <p>
          El Centro de Soporte es un canal o mesa de ayuda que resuelve problemas variados a funcionarios del MEP. El servicio que se encarga de responder una variedad de incidencias, por ejemplo, dudas sobre programas (software) o recibir solicitudes de reparaci&oacute;n de hadware, as&iacute; como atenci&oacute;n y apoyo de programas y proyectos espec&iacute;ficos. 
        </p>
      </div>
      <!--Grid column-->

      <!--Grid column-->
      <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
        <h5 class="text-uppercase">Alcances del Centro de Soporte</h5>

        <p>
          El Centro de Soporte trabaja con un equipo base de funcionarios(as) capacitados. Se brinda soporte a programas y proyectos espec&iacute;ficos. Sus alcances estan limitados a soporte de software y atenci&oacute;n de consultas generales en tiempo real. Esperamos poder servirles en la mayor&iacute;a de casos posibles.
        </p>
      </div>
      <!--Grid column-->
    </div>
    <!--Grid row-->
  </div>
  <!-- Grid container -->

 
  <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
     TecnoPresta es realizado por gente MEP, para la gente del MEP.
     </div>
  
</footer>