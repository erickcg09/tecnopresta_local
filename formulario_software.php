<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4);
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

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Registrar Licencia</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <script src="css/jquery.min.js"></script>
  <script src="css/jquery-ui.js"></script>
  <link rel="stylesheet" href="css/jquery-ui.css" />
  <script src="/css/bootstrap.min.js"></script>
  <script language="javascript" src="js/utf8.js"></script>
  <link rel="stylesheet" href="alertifyjs/css/alertify.css">
  <link rel="stylesheet" href="alertifyjs/css/themes/default.css">
  <script src="alertifyjs/alertify.js"></script> 
  <script src="gijgo/gijgo.min.js" type="text/javascript"></script>
  <link href="gijgo/gijgo.min.css" rel="stylesheet" type="text/css" />
  <script src="gijgo/messages.es-es.js" type="text/javascript"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">
    <style type="text/css">
      .ui-autocomplete-row
      {
        padding:8px;
        background-color: #f4f4f4;
        border-bottom:1px solid #ccc;
        font-weight:bold;
      }
      .ui-autocomplete-row:hover
      {
        background-color: #ddd;
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
        <a class="nav-link" href="inventario_software.php"> <span class="icon icon-undo2"></span> Inventario</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><span class="icon icon-enter"></span> Cerrar Sesi&oacute;n</a>
      </li>  
    </ul>
  </div>
</nav>
<br>

<div class="container">
      <h3>Registro de Licencias Adquiridas</h3><a href="ayuda.html#s">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a><a>&nbsp;&nbsp;</a> <a href="contactenos.php?rep=Error en Registro de Licencias Adquiridas">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error ]</a><br><br>
      <div class="row">

        <div class="col-md-6">
		<form id="frminformacion" action="guardar_software.php" method="POST">
		  <div class="form-group">
		    <label>Nombre del software:</label>
			  <input type="text" id="search_data" name="search_data" placeholder="Escriba el nombre del software" autocomplete="off" class="form-control input-lg" autofocus required/>
		  </div>
		  <div class="form-group">
		    <label>Código o Serial de licencia:</label>
		    <input type="text" class="form-control" id="licencia" name="licencia" placeholder="Licencia" required>
		  </div>
		<div class="input-group mb-3">
		  <div class="input-group-prepend">
		    <button class="btn btn-outline-secondary" type="button">Seleccione el tipo de licencia</button>
		  </div>
      <select class="custom-select" id="selecctipo" name="selecctipo" aria-label="Example select with button addon" required>
        <option value="0">Seleccione..</option>
        <?php 
          $query = $link -> query ("SELECT * FROM t_tipolicencia");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores['id_tipolicencia'].'">'.$valores['tipo'].'</option>';
          }
        ?>
      </select>
		</div>
		<div class="input-group mb-3">
		  <div class="input-group-prepend">
		    <button class="btn btn-outline-secondary" type="button">Seleccione la caracteristica del software</button>
		  </div>
      <select class="custom-select" id="caracteristica" name="caracteristica" aria-label="Example select with button addon" required>
        <option value="0">Seleccione..</option>
        <?php 
          $querx = $link -> query ("SELECT * FROM t_caracteristica_software");
          while ($valorex = mysqli_fetch_array($querx)) {
            echo '<option value="'.$valorex['id_cs'].'">'.$valorex['caracteristica'].'</option>';
          }
        ?>
      </select>
		</div>
		<div class="form-group">
             	    <label for="ceal">Cantidad de equipos permitidos a instalar:</label><input type="number" id="ceal" name="ceal" min="1" class="form-control input-lg" required>
		</div>
		<div>
                  <label>Fecha de activación de la licencia:</label><input id="datepicker" name="factivacion" width="234" required/>
		</div>
 <br>
		<div class="input-group mb-3">
		  <div class="input-group-prepend">
		    <span class="input-group-text">Vigencia de la licencia</span>
		  </div>
		  <input type="text" id="vigencia" name="vigencia" class="form-control" aria-label="Amount (to the nearest dollar)" required onkeypress="return valideKey(event);">
		  <div class="input-group-append">
		    <span class="input-group-text">Meses</span>
		  </div>
		</div>
<br>
		<div class="input-group mb-3">
		  <div class="input-group-prepend">
		    <span class="input-group-text" id="inputGroup-sizing-default">Número de contratación / C&oacute;digo presupuestario (corto) de la instituci&oacute;n</span>
		  </div>
		  <input type="text" id="contratacion" name="contratacion" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
		</div><br>

	<div class="input-group mb-3">
		  <div class="input-group-prepend">
		    <button class="btn btn-outline-secondary" type="button">Origen presupuestario con el cual se adquiere la licencia</button>
		  </div>
      <select class="custom-select" id="fondos" name="fondos" aria-label="Example select with button addon" required>
        <option value="0">Seleccione..</option>
        <?php 
          $querz = $link -> query ("SELECT * FROM t_fondos");
          while ($valorez = mysqli_fetch_array($querz)) {
            echo '<option value="'.$valorez['id_fondos'].'">'.$valorez['fondos'].'</option>';
          }
        ?>
      </select>
		</div>
<br>

		<div class="custom-control custom-switch">
		  <input type="checkbox" class="custom-control-input" id="asociar" name="asociar" value="Si">
		  <label class="custom-control-label" for="asociar">Deseo asociar la (s) licencia a un (os) equipo (s)</label>
		</div>

 <br>

		  <button type="submit" id="guardar" name="guardar" class="btn btn-dark btn-lg btn-block"> <span class="icon icon-floppy-disk"></span> Guardar</button><br>
		</form>
        </div>
        <div class="col-md-6">
<div class="d-none d-sm-none d-md-block"><img src="img/crearsoftware.png "width="600" height="600"></div>
        </div>
   </div>
</div>
<script>
function notificacion(){
        //una notificación normal
      alertify.log("Esto es una notificación cualquiera."); 
      return false;
}
                   
function ok(){
        //una notificación correcta
      alertify.success("Guardado!, desea agregar otro"); 
      return false;
}
                   
function error(){
        //una notificación de error
      alertify.error("Algo no está bien"); 
      return false; 
}
</script>
</body>
</html>
<script>
  $(document).ready(function(){
      
    $('#search_data').autocomplete({
      source: "fetch.php",
      minLength: 1,
      select: function(event, ui)
      {
        $('#search_data').val(ui.item.value);
      }
    }).data('ui-autocomplete')._renderItem = function(ul, item){
      return $("<li class='ui-autocomplete-row'></li>")
        .data("item.autocomplete", item)
        .append(item.label)
        .appendTo(ul);
    };

  });
</script>
<script>
  $(document).ready(function(){
      
    $('#search_data2').autocomplete({
      source: "fetch2.php",
      minLength: 1,
      select: function(event, ui)
      {
        $('#search_data2').val(ui.item.value);
      }
    }).data('ui-autocomplete')._renderItem = function(ul, item){
      return $("<li class='ui-autocomplete-row'></li>")
        .data("item.autocomplete", item)
        .append(item.label)
        .appendTo(ul);
    };

  });
</script>
<script>
   $(function(){
   $(".op").click(function(){
        if($(this).val()=='T'){
         $("#search_data2").removeAttr('disabled');
         $("#search_data2").focus();
        }else{
        $("#search_data2").attr('disabled','disabled');
        }
   })
})
</script>
<script>
        $('#datepicker').datepicker({
            locale: 'es-es',
            format: 'dd mm yyyy',
            uiLibrary: 'bootstrap4'
        });
</script>

<script type="text/javascript">
		function valideKey(evt){
			
			
			var code = (evt.which) ? evt.which : evt.keyCode;
			
			if(code==8) { // backspace.
			  return true;
			} else if(code>=48 && code<=57) { // numeros.
			  return true;
			} else{ // otros.
			  return false;
			}
		}
</script>
