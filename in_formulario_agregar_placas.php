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
$activo = $_GET['idx']; 





$preguntar = mysqli_query($link,"SELECT Ta.id_activo, Tag.clase, Tag.imagen, Ta.modelo, Tm.marca, Tc.color
		 FROM t_activo Ta
		 INNER JOIN t_activo_general Tag ON Ta.id_ag = Tag.id_ag
		 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color 
		 WHERE Ta.id_activo = '".$activo."'
		 ORDER BY Tag.clase ASC") or die(mysqli_error($link));  
        $respuesta = mysqli_fetch_array($preguntar);
        $clase = $respuesta['clase'];
        $marca = $respuesta['marca'];
	    $modelo = $respuesta['modelo'];
	    $color = $respuesta['color'];
	    $imagen = $respuesta['imagen'];
?>
<html lang="es">
	<head>
		<title>Agregar placas o seriales</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="/css/bootstrap.min.css" />
		<link rel="stylesheet" href="fondoresponsive.css">
		<script src="js/jquery-3.5.1.min.js"></script>
		<script src="/css/bootstrap.min.js"></script>
		<script src="/css/jquery.min.js"></script>
		<link rel="stylesheet" href="alertifyjs/css/alertify.css">
		<link rel="stylesheet" href="alertifyjs/css/themes/default.css">
                <link rel="stylesheet" type="text/css" href="/css/style.css">
                <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">
                <link rel="stylesheet" type="text/css" href="/css/pimg.css">
		<script src="alertifyjs/alertify.js"></script> 
		<style type="text/css">
			.row { margin: 10px 0; }
			.row div[class*='col'] { padding: 10px; background: white; text-align: center; border: 1px solid;}

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
        <a class="nav-link" href="in_formulario_agregar_equipo.php"><span class="icon icon-undo2"></span> Seleccionar Activo</a>
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
            <div class="col-xl">
                <h3 align="center"><b>Agregar serial y placa para el dispositivo: <font size="5" color="#0000ff"><?php echo $clase . " " . $marca . " " . $modelo . " " . $color?></b></font></h3><a href="ayuda.html#sp">[ <span class="icon icon-lifebuoy"></span> Ayuda ]</a> <a>&nbsp;&nbsp;</a> <a href="contactenos.php?rep=Error en Agregar serial y placa para el dispositivo">[ <span class="icon icon-envelop"></span> Reportar Incidencia / Error]</a><br><br>
<div align="center"><img class="redimension" src="img/<?php echo $imagen;?> "></div>
             <form id="frminformacion">
                <label><b>Ingrese el serial</b></label>  
                <input type="text" name="serial" id="serial" class="form-control" placeholder="Escriba aqui el serial" pattern=".{5,50}" required onkeypress="return event.charCode != 39"/><br /> 
                <label><b>Ingrese la placa</b></label>  
                <input type="text" name="placa" id="placa" class="form-control" placeholder="Escriba aqui la placa" pattern=".{5,50}" required onkeypress="return event.charCode != 39"/><br />
	<div class="input-group mb-3">
		  <div class="input-group-prepend">
		    <button class="btn btn-outline-secondary" type="button">Origen presupuestario con el cual se adquiere el activo</button>
		  </div>
      <select class="custom-select" id="fondos" name="fondos" aria-label="Example select with button addon" required>
        <?php 
          $querz = $link -> query ("SELECT * FROM t_fondos");
          while ($valorez = mysqli_fetch_array($querz)) {
            echo '<option value="'.$valorez['id_fondos'].'">'.$valorez['fondos'].'</option>';
          }
        ?>
      </select>
		</div>
		<input type="hidden" name="codigo" value="<?php echo $logcodigo; ?>">
		<input type="hidden" name="idactivo" value="<?php echo $activo; ?>">
                <button id="enviar" type="button" class="btn btn-dark btn-lg btn-block"> <span class="icon icon-floppy-disk"></span> Guardar</button>
             </form>
             </div>
             <div class="col-xl">
                <h3 align="center">Visualizador de seriales y placas en el sistema</h3><br /><br />      
                <div id="serialList" class="container bg-light shadow" style="height:75px; overflow-y: scroll;"></div><br>
                <div id="placaList" class="container bg-light shadow" style="height:75px; overflow-y: scroll;"></div>
             </div>     
           </div> 
<div class="col-sm-6" id="mensaje" style="background-image: url('ico/llamado2.png'); background-repeat: no-repeat fixed; background-position: center top; backgroudn-size: contain; padding: 20px; height: 112px; text-align: center; font-size: 20px;"></div>        
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
        <script>
            document.addEventListener("DOMContentLoaded", function(){
                // Invocamos cada 10 min ;)
                const milisegundos = 600 *1000;
                setInterval(function(){
                    // No esperamos la respuesta de la petición porque no nos importa
                    fetch("refrescar.php");
                },milisegundos);
            });
        </script>
</body>
</html>
 <script>  
 $(document).ready(function(){  
      $('#serial').keyup(function(){  
           var query = $(this).val();  
           if(query != '')  
           {  
                $.ajax({  
                     url:"b_serial.php",  
                     method:"POST",  
                     data:{query:query},  
                     success:function(data)  
                     {  
                          $('#serialList').fadeIn();  
                          $('#serialList').html(data);  
                     }  
                });  
           }  
      });  
        
 });  
 </script>  
<script>  
 $(document).ready(function(){  
      $('#placa').keyup(function(){  
           var query = $(this).val();  
           if(query != '')  
           {  
                $.ajax({  
                     url:"b_placa.php",  
                     method:"POST",  
                     data:{query:query},  
                     success:function(data)  
                     {  
                          $('#placaList').fadeIn();  
                          $('#placaList').html(data);  
                     }  
                });  
           }  
      });  
    
 });  
 </script>  

<script>
$(document).ready(function(){
  $("#enviar").click(function(){
    var formulario = $("#frminformacion").serializeArray();
    $.ajax({
      type: "POST",
      dataType: 'json',
      url: "guardarsp.php",
      data: formulario,
    }).done(function(respuesta){
      $("#frminformacion").trigger('reset');
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
