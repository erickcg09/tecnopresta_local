<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "formulario_menu_inventario.html"
</script>';
}
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];
$logtipo = $_SESSION['tipo'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Software</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <script src="/css/jquery.min.js"></script>
  <script src="css/jquery-ui.js"></script>
  <link rel="stylesheet" href="css/jquery-ui.css" />
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
            
	<script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
	<link rel="stylesheet" href="menu/styles.css">
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
        <a class="nav-link" href="formulario_menu_inventario.html"><span class="icon icon-undo2"></span> Principal</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><span class="icon icon-enter"></span> Cerrar Sesi&oacute;n</a>
      </li>  
    </ul>
  </div>  
</nav>
<br>

<div class="container">
  <div class="row">
    <div class="col">


	  <h3>Usuario: <?php echo $lognombre ." ". $logcodigo; ?> </h3>
	<br>  
<div class="wrapper">
	<div class="links">
		<ul>
			<li data-view="list-view" class="li-list active">
			<i class="fas fa-th-list"></i>
			Lista</li>
			<li data-view="grid-view" class="li-grid">
			<i class="fas fa-th-large"></i>
			Bloque</li>
		</ul>
	</div>
	<div class="view_main">
		<div class="view_wrap list-view" style="display: block;">
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/s1.png" alt="s1">
				</div>
				<div class="vi_right">
					<p class="title">Agregar Licencia</p>
					<p class="content">Agrege las licencias de los progrmas adquiridos en la instituci&oacute;n, controle fechas de caducidad y activaci&oacute;n</p>
										<a href="formulario_software.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/s2.png" alt="s2">
				</div>
				<div class="vi_right">
					<p class="title">Ligar Licencias al PC</p>
					<p class="content">Asocie las licencias adquiridas a los dispositivos y controle el inventario de licencias</p>
										<a href="formulario_seleccionar_software.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/s3.png" alt="s3">
				</div>
				<div class="vi_right">
					<p class="title">Desligar Licencias del PC</p>
					<p class="content">Desvincule licencias de dispositivos dados de baja que desee utilizar en otros equipos</p>
										<a href="formulario_seleccionar_desvincular.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/s4.png" alt="s4">
				</div>
				<div class="vi_right">
					<p class="title">Consultar Vigencia de Licencia</p>
					<p class="content">Consulte mediante un calendario la fecha de activaci&oacute;n y caducidad de las licencias</p>
										<a href="formulario_preguntar_software.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>

		</div>
		<div class="view_wrap grid-view" style="display: none;">
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/s1.png" alt="s1">
				</div>
				<div class="vi_right">
					<p class="title">Agregar Licencia</p>
					<p class="content">Agrege las licencias de los progrmas adquiridos en la instituci&oacute;n, controle fechas de caducidad y activaci&oacute;n</p>
										<a href="formulario_software.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/s2.png" alt="s2">
				</div>
				<div class="vi_right">
					<p class="title">Ligar Licencias al PC</p>
					<p class="content">Asocie las licencias adquiridas a los dispositivos y controle el inventario de licencias<br><br></p>
										<a href="formulario_seleccionar_software.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/s3.png" alt="s3">
				</div>
				<div class="vi_right">
					<p class="title">Desligar Licencias del PC</p>
					<p class="content">Desvincule licencias de dispositivos dados de baja que desee utilizar en otros equipos</p>
										<a href="formulario_seleccionar_desvincular.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/s4.png" alt="s4">
				</div>
				<div class="vi_right">
					<p class="title">Consultar Vigencia de Licencia</p>
					<p class="content">Consulte mediante un calendario la fecha de activaci&oacute;n y caducidad de las licencias</p>
										<a href="formulario_preguntar_software.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>

		</div>
	</div>
</div>

    </div>
  </div>
</div>
<script src="menu/scripts.js"></script>
</body>
</html>