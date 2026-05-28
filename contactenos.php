<?php
    session_start();
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];
$logcorreo = $_SESSION['correomep'];
$logdireccionreg = $_SESSION['direccionreg'];
$logcircuito = $_SESSION['circuito'];
$soportista = "tecnopresta@mep.go.cr";
$generica = $_GET["rep"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>PNTM Principal</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <script src="/css/jquery.min.js"></script>
  <script src="css/jquery-ui.js"></script>
  <link rel="stylesheet" href="css/jquery-ui.css" />
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
            <style>
        a:link, a:visited, a:active {
            text-decoration:none;
            color: #000000;
        }
         p { color: #000000; 
             font-size: 30px;
        }
#lista3 {
    counter-reset: li; 
    list-style: none; 
    *list-style: decimal; 
    font: 30px; 
    padding: 0;
    margin-bottom: 4em;
    text-shadow: 0 1px 0 rgba(255,255,255,.5);
}

#lista3 ol {
    margin: 0 0 0 2em; 
}

#lista3 li{
    position: relative;
    display: block;
    padding: .4em .4em .4em .8em;
    *padding: .4em;
    margin: .5em 0 .5em 2.5em;
    background: #ddd;
    color: white;
    text-decoration: none;
    transition: all .3s ease-out;   
}

#lista3 li:hover{
    background: #eee;
}   

#lista3 li:before{
    content: counter(li);
    counter-increment: li;
    position: absolute;
    border-radius:100%;
    left: -2.5em;
    top: 50%;
    margin-top: -1em;
    background: black;
    height: 2em;
    width: 2em;
    line-height: 2em;
    text-align: center;
    font-weight: bold;
}

#lista3 li:after{
    position: absolute; 
    content: '';
    border: .5em solid transparent;
    left: -1em;
    top: 50%;
    margin-top: -.5em;
    transition: all .3s ease-out;               
}

#lista3 li:hover:after{
    left: -.5em;
    border-left-color: black;             
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
        <a class="nav-link" href="formulario_menu_principal.html"><span class="icon icon-undo2"></span> Principal</a>
      </li>
    </ul>
  </div>  
</nav>
<br>

<div class="container">


  <div class="row">
    <div class="col-md-7">
<h2>Contactar a Soporte TecnoPresta</h2>
<br>
<form name="solicitar" id="solicitar" action="enviar_notificacion_soporte.php" method="post" enctype="multipart/form-data">
<div class="table-responsive">
<table class="table table-bordered table-dark">
  <tbody>
    <tr>
      <td>Asunto</td>
      <td><input type="text" name="asunto" value="<?php echo $generica?>" size=40></td>
    </tr>
    <tr>
      <td>Funcionario</td>
      <td><input type="text" name="emisor" value="<?php echo $lognombre?>" size=40 ></td>
    </tr>
    <tr>
      <td>Regional</td>
      <td><input type="text" name="regional" value="<?php echo $logdireccionreg?>" size=40 ></td>
    </tr>
    <tr>
      <td>Circuito</td>
      <td><input type="text" name="circuito" value="<?php echo $logcircuito?>" size=10 ></td>
    </tr>
    <tr>
      <td>Correo donde contactarle</td>
      <td><input type="text" name="correo" value="<?php echo $logcorreo?>" size=40 ></td>
    </tr>
    <tr>
      <td>Mensaje</td>
      <td><textarea name="mensaje" rows="4" cols="50" placeholder="Escriba su problema o inquietud"></textarea></td>
    </tr>
    <tr>
        <td>Captura o pdf</td>
        <td><input type="file" class="form-control" name="resume" accept="image/*, .pdf" required></td>
    </tr>
    <input type="hidden" id="receptor" name="receptor" value="<?php echo $soportista?>">
        <input type="hidden" id="cedula" name="cedula" value="<?php echo $logusuario?>">
  </tbody>
</table>
</div>
  <div>
         <button type="submit" class="btn btn-dark"><span class="icon icon-envelop"> Enviar</span></button><br>
  </div>
</form>

    </div>
        <div class="col-md-5">
<div class="d-none d-sm-none d-md-block"><img src="img/contactenos2.png" width="600" height="600"></div>
        </div>
 </div>   
</div>

</body>
</html>