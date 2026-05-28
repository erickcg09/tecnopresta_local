<?php
session_start();
$tienellave = ($_SESSION['tipo']==1);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
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
$idcaso = $_GET['gps'];
//$idcaso="6";

		$preguntar = mysqli_query($link, "select tipo from t_chat_soporte where id_caso='$idcaso'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$bandera = $respuesta['tipo'];
		
		$preguntar2 = mysqli_query($link, "select problema from soporte where id='$idcaso'");   
		$respuesta2 = mysqli_fetch_array($preguntar2);
		$problema = $respuesta2['problema'];

$query = "SELECT * FROM t_chat_soporte WHERE id_caso = '".$idcaso."' order by id ASC";
$result = mysqli_query($link, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat de Soporte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
</head>

<style>
    a.nav-link {
        color: gray;
        font-size: 18px;
        padding: 0;
    }

    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid #e84118;
        padding: 2px;
        flex: none;
    }

    input:focus {
        outline: 0px !important;
        box-shadow: none !important;
    }

    .card-text {
        border: 2px solid #ddd;
        border-radius: 8px;
    }
</style>

<body class="bg-secondary">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">TecnoPresta</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
          <div class="navbar-nav">
            <a class="nav-link" aria-current="page" href="miscasos.php">Regresar</a>
            <a class="nav-link" href="gameover.php">Cerrar sesi&oacute;n</a>
          </div>
        </div>
      </div>
    </nav>



    
    <div class="container mt-4">
        <div class="card mx-auto" style="max-width:400px">
            <div class="card-header bg-transparent">
                <div class="navbar navbar-expand p-0">
                    <ul class="navbar-nav me-auto align-items-center">
                        <li class="nav-item">
                            <a href="#!" class="nav-link">
                                <div class="position-relative"
                                    style="width:50px; height: 50px; border-radius: 50%; border: 2px solid #e84118; padding: 2px">
                                    <img src="svg/soport.png" 
                                        class="img-fluid rounded-circle" alt=""> <!-- Foto del perfil -->
                                    <span
                                        class="position-absolute bottom-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
                                        <span class="visually-hidden">New alerts</span>
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#!" class="nav-link"><?php echo $problema; ?></a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Estilo de mensaje del soportista -->
            <div class="card-body p-4" style="height: 500px; overflow: auto;">
     <?php            
     while($row = mysqli_fetch_array($result))
     {   
     ?>   
         <?php
         if ($row["tipo"] == $bandera) {
         ?>   


                <div class="d-flex align-items-baseline mb-4">
                    <div class="position-relative avatar">
                        <img src="svg/e1.png"
                            class="img-fluid rounded-circle" alt=""> <!-- Foto del avatar que inicia -->
                        <span
                            class="position-absolute bottom-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    </div>
                    <div class="pe-2">
                        <div>
                            <div class="card card-text d-inline-block p-2 px-3 m-1"><?php echo $row["mensaje"]; ?>
                            </div> <!-- Mensaje del que inicia -->
                        </div>
                        <div>
                            <div class="small"><?php echo $row["fecha"]; ?></div> <!-- Fecha de envio del mensaje -->
                        </div>
                    </div>
                </div>            
          
         <?php     
         }else{
         ?>   
                <!-- Estilo de mensaje del usuario -->
                <div class="d-flex align-items-baseline text-end justify-content-end mb-4"> <!-- Inicia segundo usuario alineado a la izquierda  -->
                    <div class="pe-2">
                        <div>
                            <div class="card card-text d-inline-block p-2 px-3 m-1"><?php echo $row["mensaje"]; ?>
                            </div>  <!-- Mensaje del segundo -->
                        </div>
                        <div>
                            <div class="small"><?php echo $row["fecha"]; ?></div>
                        </div>
                    </div>
                    <div class="position-relative avatar">
                        <img src="svg/e2.png"
                            class="img-fluid rounded-circle" alt=""> <!-- Foto avatar del segundo a la izquierda -->
                        <span
                            class="position-absolute bottom-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    </div>
                </div>
          

         <?php              
         }
         ?>
         
     <?php
     }
     ?>
            </div>  
            <!-- Fin de seccion de chats -->

            <div class="card-footer bg-white position-absolute w-100 bottom-0 m-0 p-1">
                <form action="guardar_msn_chat_s.php" method="post">
                <div class="input-group">
                    <input type="text" name="mensajito" class="form-control border-0" placeholder="Escriba el mensaje ..." required>
                    <input type="hidden" name="idcaso" value="<?php echo $idcaso;?>">
                    <div class="input-group-text bg-transparent border-0">
                        <button type="submit" class="btn btn-light text-secondary">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
            <!-- tostada con la informacion a los funcionarios -->
<div aria-live="polite" aria-atomic="true" class="position-relative">
 <div class="toast-container position-absolute top-0 start-0 p-3">
  <div class="toast show">
    <div class="toast-header">
      <strong class="me-auto">TecnoPresta</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body">
      <p>Estimados funcionarios, lo que se escribe en este foro es de dominio público y está enfocado en solicitar, ayudar y aportar conocimiento.</p>
    </div>
  </div>
 </div>
</div>

</body>

</html>