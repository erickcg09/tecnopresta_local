<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos, si usted es prestador o inventariador, contacte a su director(a) para que le cree los permisos respectivos en Modulo de Inventarios/Mantenimiento/Permisos")
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
  <title>TecnoPresta Inventario</title>
  <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
  <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
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
					<img src="iconos/i1.png" alt="i1">
				</div>
				<div class="vi_right">
					<p class="title">Agregar placa del activo</p>
					<p class="content">Agregue las placas y n&uacute;meros de serie de los activos, encontrar&aacute; que los perfiles de activos m&aacute;s utilizados en el MEP ya han sido creados, si no existe el indicado puede crearlo y colaborar con la comunidad</p>
										<a href="formulario_busqueda_creacion_activo.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i2.png" alt="i2">
				</div>
				<div class="vi_right">
					<p class="title">Importar Placas y Seriales por Lotes</p>
					<p class="content">Ingrese su inventario de activos de forma masiva mediante una inserci&oacute;n directa a la base de datos, ahorre tiempo con la plantilla csv</p>
										<a href="formulario_importar_lotes.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i5.png" alt="i5">
				</div>
				<div class="vi_right">
					<p class="title">Estado del Activo</p>
					<p class="content">Controle el estado f&iacute;sico de los activos de manera eficiente, pase de un estado a otro en poco tiempo, seleccione uno, algunos o todos los elementos, y actualícelos al instante con un solo click.</p>
										<a href="formulario_estado_de_los_activos.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i6.png" alt="i6">
				</div>
				<div class="vi_right">
					<p class="title">Sacar de Inventario</p>
					<p class="content">No es funcional tener activos dañados o en estado de obsolescencia dentro del inventario de activos, sobre todo si est&aacute;n destinados al pr&eacute;stamo</p>
										<a href="formulario_dar_baja.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i7.png" alt="i7">
				</div>
				<div class="vi_right">
					<p class="title">Activos Destinados a Prestarse</p>
					<p class="content">Existen en el inventario activos que no son destinados al pr&eacute;stamo, ya sea porqu&eacute;
 son de uso exclusivo de un departamento o son activos que brindan servicios a m&uacute;ltiples usuarios como un router, servidores entre otros</p>
										<a href="formulario_activo_destinado_prestamo.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i8.png" alt="i8">
				</div>
				<div class="vi_right">
					<p class="title">Asignar Alias y Numeración a los Activos</p>
					<p class="content">Asignar alias es sin&oacute;nimo de crear un grupo con activos que permita administrar de forma m&aacute;s facil el pr&eacute;stamo, por ejemplo un laboratorio port&aacute;til el cual est&aacute; conformado por varios dispositivos</p>
										<a href="formulario_agregar_alias.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i9.png" alt="i9">
				</div>
				<div class="vi_right">
					<p class="title">Ubicación de Activo</p>
					<p class="content">Tener localizado los activos es vital para un funcionamiento &oacute;ptimo, en est&eacute; apartado se hace referencia al lugar de resguardo del activo</p>
										<a href="#" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			
			
		</div>
		<div class="view_wrap grid-view" style="display: none;">
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i1.png" alt="i1">
				</div>
				<div class="vi_right">
					<p class="title">Agregar placa del activo</p>
					<p class="content">Agregue las placas y n&uacute;meros de serie de los activos, encontrar&aacute; que los perfiles de activos m&aacute;s utilizados en el MEP ya han sido creados, si no existe el indicado puede crearlo y colaborar con la comunidad<br><br></p>
										<a href="formulario_busqueda_creacion_activo.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i2.png" alt="i2">
				</div>
				<div class="vi_right">
					<p class="title">Importar Placas y Seriales por Lotes</p>
					<p class="content">Ingrese su inventario de activos de forma masiva mediante una inserci&oacute;n directa a la base de datos, ahorre tiempo con la plantilla csv<br><br><br><br><br></p>
										<a href="formulario_importar_lotes.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i5.png" alt="i5">
				</div>
				<div class="vi_right">
					<p class="title">Estado del Activo</p>
					<p class="content">Controle el estado f&iacute;sico de los activos de manera eficiente, pase de un estado a otro en poco tiempo, seleccione uno, algunos o todos los elementos, y actualícelos al instante con un solo click.<br><br><br><br></p>
										<a href="formulario_estado_de_los_activos.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i6.png" alt="i6">
				</div>
				<div class="vi_right">
					<p class="title">Sacar de Inventario</p>
					<p class="content">No es funcional tener activos dañados o en estado de obsolescencia dentro del inventario de activos, sobre todo si est&aacute;n destinados al pr&eacute;stamo<br><br><br></p>
										<a href="formulario_dar_baja.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i7.png" alt="i7">
				</div>
				<div class="vi_right">
					<p class="title">Activos Destinados a Prestarse</p>
					<p class="content">Existen en el inventario activos que no son destinados al pr&eacute;stamo, ya sea porqu&eacute;
 son de uso exclusivo de un departamento o son activos que brindan servicios a m&uacute;ltiples usuarios como un router, servidores entre otros</p>
										<a href="formulario_activo_destinado_prestamo.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i8.png" alt="i8">
				</div>
				<div class="vi_right">
					<p class="title">Asignar Alias y Numeración a los Activos</p>
					<p class="content">Asignar alias es sin&oacute;nimo de crear un grupo con activos que permita administrar de forma m&aacute;s facil el pr&eacute;stamo, por ejemplo un laboratorio port&aacute;til el cual est&aacute; conformado por varios dispositivos</p>
										<a href="formulario_agregar_alias.php" class="btn">
	                    Acceder
                        </a>
				</div>
			</div>
			<div class="view_item">
				<div class="vi_left">
					<img src="iconos/i9.png" alt="i9">
				</div>
				<div class="vi_right">
					<p class="title">Ubicación de Activo</p>
					<p class="content">Tener localizado los activos es vital para un funcionamiento &oacute;ptimo, en est&eacute; apartado se hace referencia al lugar de resguardo del activo<br><br><br><br><br><br></p>
										<a href="#" class="btn">
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
        <script>
            document.addEventListener("DOMContentLoaded", function(){
                // Invocamos cada 5 segundos ;)
                const milisegundos = 5 *1000;
                setInterval(function(){
                    // No esperamos la respuesta de la petición 
                    fetch("refrescar.php");
                },milisegundos);
            });
        </script>
</body>
</html>