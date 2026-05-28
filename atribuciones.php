<?php

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
    <div class="col-md-6">
<h2>Atribuciones</h2>

<p>
  Todas las imagenes incluidas en este sitio, fueron aportados por el portal www.freepik.es y sus colaboradores externos.<br>
  <br>
  <i>racool-studio</i><br>
  <i>macrovector</i><br>
  <i>katemangostar</i><br>
  <i>brgfx</i><br>
  <i>slidesgo</i><br>
  <i>iconicbestiary</i><br>
  <i>rawpixel</i><br>
  <i>pikisuperstar</i><br>
  <i>stories</i><br>
  <i>user14579558</i><br>
  <i>upklyak</i><br>
  <i>icomoon</i><br>
  <i>vectorpouch</i><br>
  <i>phcvector</i><br>
  <i>gstudioimagen</i><br>
  <i>ibrandify</i><br>
  <i>omelapics</i><br>
  <i>dgim-studio</i><br>
  <i>graphiqaStock</i><br>
  <i>pch.vector</i><br>
  <i>myriammira</i><br>
  <i>pikisuperstar</i><br>
  <i>pressfoto</i><br>
  <i>vectorjuice</i><br>
  <i>wayhomestudio</i><br>
  <i>racool_studio</i><br>
</p>



    </div>
        <div class="col-md-6">
<div class="d-none d-sm-none d-md-block"><img src="img/atribuciones.png" width="600" height="600"></div>
        </div>
 </div>   
</div>

</body>
</html>