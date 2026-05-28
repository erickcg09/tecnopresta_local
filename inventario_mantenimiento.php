<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4 or $_SESSION['tipo']==7);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos, si es inventariador o prestador, pida a su Director(a) que le cree el acceso, o escriba a TecnoPresta@mep.go.cr")
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
  <title>TecnoPresta Mantenimiento</title>
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
      </li> 
      <li class="nav-item">
        <a class="nav-link" href="formulario_crear_roles.php"><span class="icon-address-book"></span> Permisos de Usuario</a>
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


	  <h3>USUARIO: <?php echo $lognombre ." ". $logcodigo; ?> </h3>
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
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Agregar y Editar Activo General</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_activo_general.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Agregar y Editar Marca</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_marca.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Agregar y Editar Color</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_color.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Agregar y Editar Software</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_software.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Agregar y Editar Tipo Licencia</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_tipo_licencia.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Agregar y Editar Característica</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_caracteristica_software.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Comprobaci&oacute;n y Edici&oacute;n de Licencia</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_demostrar_serial.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Origen de los fondos de adquisici&oacute;n</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_tipo_fondos.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Crear y Eliminar Alias o Grupos</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_alias.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Editar Alias y Numeraci&oacute;n de los Activos</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_editar_alias_activo.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Editar Placa, Serie</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_editar_placa.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Editar Origen presupuestario de Activo</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_cambiar_origen_presupuestario_a_los_activos.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Editar Cuestionario de Revisi&oacute;n</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_editor_cuestionario.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
		</div>
		<div class="view_wrap grid-view" style="display: none;">
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Agregar y Editar Activo General</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_activo_general.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Agregar y Editar Marca</p>
					<p class="content">Agrege, edite y elimine<br><br></p>
										<a href="formulario_crear_marca.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Agregar y Editar Color</p>
					<p class="content">Agrege, edite y elimine<br><br></p>
										<a href="formulario_crear_color.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Agregar y Editar Software</p>
					<p class="content">Agrege, edite y elimine<br><br></p>
										<a href="formulario_crear_software.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Agregar Tipo Licencia</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_tipo_licencia.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Agregar y Editar Característica</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_caracteristica_software.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Comprobaci&oacute;n y Edici&oacute;n de Licencia</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_demostrar_serial.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Origen de los fondos de adquisici&oacute;n</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_tipo_fondos.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Alias o Grupos(ejm. laboratorios port&aacute;til)</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_crear_alias.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Editar Alias y Numeraci&oacute;n de los Activos</p>
					<p class="content">Agrege, edite y elimine</p>
										<a href="formulario_editar_alias_activo.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Editar Placa, Serie</p>
					<p class="content">Agrege, edite y elimine<br><br></p>
										<a href="formulario_editar_placa.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Editar Origen presupuestario de Activo</p>
					<p class="content">Agrege, edite y elimine<br><br></p>
										<a href="formulario_cambiar_origen_presupuestario_a_los_activos.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/m.png" alt="m">
				</div>
				<div class="vi_right">
					<p class="title">Editar Cuestionario de Revisi&oacute;n</p>
					<p class="content">Agrege, edite y elimine<br><br></p>
										<a href="formulario_editor_cuestionario.php" class="btn">
	                    Acceder
                        </a>
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

