<?php

session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
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
$logcodigo = $_SESSION['codigo'];

$activo = $_GET['varactivo'];
$placa = $_GET['varplaca'];
$serie = $_GET['varserie'];
$encargado = $_GET['varencargado'];
$cedula = $_GET['varcedula'];
$email = $_GET['varemail'];
$telefono = $_GET['vartelefono'];
$estudiante = $_GET['varestudiante'];
$idest = $_GET['varidest'];
$direccion = $_GET['vardireccion'];
$insti = $_GET['varinsti'];
$codigo = $_GET['varcodigo'];
$fechai = $_GET['varfechai'];
$fechaf = $_GET['varfechaf'];

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <b> Boleta de Compromiso</b> </title>
    <link rel="shortcut icon" href="ico/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <script src="boleta/jspdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script src="boleta/app.js"></script>
    <script src="/css/jquery.min.js"></script>
    <script src="/css/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>

<nav class="navbar navbar-expand-md bg-dark navbar-dark">
  <img src="img/logomep2020.png" width="45" height="30" alt="" loading="lazy"><a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="#.php"><span class="icon icon-undo2"></span> Reportes</a>
      </li> 
      <li class="nav-item">
        <a class="nav-link" href="guardar_compromiso_firmado.php"><span class="icon icon-dropbox"></span> Almacenar Contratos Firmados</a>
      </li> 
    </ul>
  </div>  
</nav>
<br>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h3>  <b>  Refrendo de Compromiso de Contrato Préstamo de dispositivos tecnol&oacute;gicos fuera del centro educativo </b></h3>
                <hr>
                <form id="form">
                    <div class="mb-3">
                       
                            <input type="hidden" class="form-control" id="activo"  value="<?php echo $activo;?>">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            
                            <input type="hidden" class="form-control" id="placa"  value="<?php echo $placa;?>">
                        </div>
                        <div class="col-md-6">
                            
                            <input type="hidden" class="form-control" id="serie"  value="<?php echo $serie;?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            
                            <input type="hidden" class="form-control" id="encargado" value="<?php echo $encargado;?>">
                        </div>
                        <div class="col-md-6">
                            
                            <input type="hidden" class="form-control" id="cedula"  value="<?php echo $cedula;?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            
                            <input type="hidden" class="form-control" id="email"  value="<?php echo $email;?>">
                        </div>
                        <div class="col-md-6">
                            
                            <input type="hidden" class="form-control" id="telefono"  value="<?php echo $telefono;?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            
                            <input type="hidden" class="form-control" id="estudiante"  value="<?php echo $estudiante;?>">
                        </div>
                        <div class="col-md-6">
                            
                            <input type="hidden" class="form-control" id="idest"  value="<?php echo $idest;?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        
                        <input type="hidden" class="form-control" id="direccion"  value="<?php echo $direccion;?>">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            
                            <input type="hidden" class="form-control" id="insti"  value="<?php echo $insti;?>">
                        </div>
                        <div class="col-md-6">
                           
                            <input type="hidden" class="form-control" id="codigo"  value="<?php echo $codigo;?>">
                        </div>
                        <div class="col-md-6">
                           
                            <input type="hidden" class="form-control" id="fechai"  value="<?php echo $fechai;?>">
                        </div>
                        <div class="col-md-6">
                           
                            <input type="hidden" class="form-control" id="fechaf"  value="<?php echo $fechaf;?>">
                        </div>
                        <div class="col-md-6">
                           
                            <input type="hidden" class="form-control" id="separador"  value="hasta">
                        </div>
                        

                    <span class="d-block pb-2">Firma digital aqui &#8595; (con el puntero del rat&oacute;n y el bot&oacute;n izquierdo presionado trace su firma)</span>
                    <div class="signature mb-2" style="width: 100%; height: 200px;">
                        <canvas id="signature-canvas"
                            style="border: 1px dashed red; width: 100%; height: 200px;"></canvas>
                    </div>

                    <button type="submit" class="btn btn-dark mb-4">Generar PDF del Contrato</button>
                    <img src="pdficon.PNG" style="max-width:100%;width:auto;height:auto;"/>
                    <h6>    Localice el Archivo del Contrato en su carpeta predeterminada de <b>Descargas</b> </h6>
                    <img src="flecha.png" style="max-width:100%;width:auto;height:auto;"/>
                     
                </form>
            </div>
        </div>
    </div>

</body>

</html>