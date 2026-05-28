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

$gps = $_GET['gps'];

		$miconsulta = mysqli_query($link, "select * from t_software where id_software='$gps'");   
		$mirespuesta = mysqli_fetch_array($miconsulta);
		$id_software = $mirespuesta['id_software'];
		$licencia = $mirespuesta['licencia'];
		$factivacion = $mirespuesta['factivacion'];
		$ceal = $mirespuesta['ceal'];
		$vigencia = $mirespuesta['vigencia'];
		$idsg = $mirespuesta['id_sg'];

		$sql = "SELECT COUNT(*) total FROM t_licencia where id_software='$gps'";
		$result = mysqli_query($link, $sql);
		$fila = mysqli_fetch_assoc($result);
		$numero=$fila['total'];
		
		$micsql = mysqli_query($link, "select etiqueta from t_software_general where id_sg='$idsg'");   
		$respuestamic = mysqli_fetch_array($micsql);		
        $etiqueta = $respuestamic['etiqueta'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>PNTM Principal</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <script src="js/jquery-3.5.1.min.js"></script>
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
        <a class="nav-link" href="inventario_software.php"><span class="icon icon-undo2"></span> Inventario</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><span class="icon icon-enter"></span> Cerrar Sesi&oacute;n</a>
      </li>  
    </ul>
  </div>
</nav>
<br>

<div class="container">
      <h3>Asignar Licencia al PC</h3>
      <br />
      <br />
      <p><b>Software: </b> <?php echo $etiqueta;?></p>
      <div class="row">

        <div class="col-md-6">
		<table class="table table-borderless">

		  <tbody>
		    <tr>
		      <th scope="row">Número de Licencia</th>
		      <td><?php echo $licencia;?></td>
		    </tr>
		    <tr>
		      <th scope="row">Fecha de venciento</th>
		      <td><?php echo date("d-m-Y",strtotime($factivacion."+ $vigencia month")); ?></td>
		    </tr>
		    <tr>
		      <th scope="row">Cantidad de Equipos Permitidos</th>
		      <td colspan="2"><?php echo $ceal;?></td>
		    </tr>
		    <tr>
		      <th scope="row">Cantidad en Equipos Instalados</th>
		      <td colspan="2"><?php echo $numero;?></td>
		    </tr>
		  </tbody>
		</table>
<br>
		<form id="frmasignar">
		  <div class="form-group">
		    <label>Placa de la computadora:</label>
			  <input type="text" id="buscar_placa" name="buscar_placa" placeholder="Escriba la placa de la computadora" autocomplete="off" class="form-control input-lg" autofocus required/>
			  <input type="hidden" name="id_software" value="<?php echo $gps;?>">
			  <input type="hidden" name="permitidos" value="<?php echo $ceal;?>">
                          <input type="hidden" name="instalados" value="<?php echo $numero;?>">
		  </div><br>
		  <button type="button" id="guardar" name="guardar" class="btn btn-dark btn-lg btn-block" > <span class="icon icon-floppy-disk"></span> Guardar</button><br>
		</form><br>
	<div id="mensaje" style="background-image: url('ico/llamado2.png'); background-repeat: no-repeat fixed; background-position: center top; backgroudn-size: contain; padding: 20px; height: 112px; text-align: center; font-size: 20px;"></div> 
        </div>
        <div class="col-md-6" style="background-image: url('img/asignarlic.png'); background-repeat: no-repeat fixed; background-size: 100% 100%;">
	
        </div>
</div>
<script>
function notificacion(){
        //una notificación normal
      alertify.log("Importante."); 
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
      
    $('#buscar_placa').autocomplete({
      source: "find_placa.php",
      minLength: 1,
      select: function(event, ui)
      {
        $('#buscar_placa').val(ui.item.value);
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
  $("#guardar").click(function(){
    var formulario = $("#frmasignar").serializeArray();
    $.ajax({
      type: "POST",
      dataType: 'json',
      url: "guardarsepc.php",
      data: formulario,
    }).done(function(respuesta){
      $("#frmasignar").trigger('reset');
      $("#mensaje").html(respuesta.mensaje);
      setTimeout(() => {  location.reload(true); }, 5000);
    });
  });
  
    function limpiarformulario(formulario){
  
  $(formulario).find('input').each(function() {
      switch(this.type) {
        case 'text':
                $(this).val('');
              break;
          }
      });
  
      $(formulario).find('select').each(function() {
          $("#"+this.id + " option[value=0]").attr("selected",true);
  
    });
  }
});
</script>


