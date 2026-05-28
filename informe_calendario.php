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
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
$activado = 1;

$consulta_eventos = "SELECT solicitud_Id, solicitud_nombre_funcionario, solicitud_fechaRetiro, solicitud_fechaDevolucion, solicitud_horaRetiro, solicitud_horaDevolucion FROM t_solicitud WHERE solicitud_codigo_presupuestario = '$logcodigo' AND solicitud_aprobada = '$activado'";
$resultado_eventos = mysqli_query($link, $consulta_eventos);

$color = "#0071c5";
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset='utf-8' />
		<title>Agenda Personal</title>
		<link href='ccss/bootstrap.min.css' rel='stylesheet'>
		<link href='ccss/fullcalendar.min.css' rel='stylesheet' />
		<link href='ccss/fullcalendar.print.min.css' rel='stylesheet' media='print' />
		<link href='ccss/personalizado.css' rel='stylesheet' />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style type="text/css">
body {
    margin: 0px 0px;
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
}
</style>
		<script src='cjs/jquery.min.js'></script>
		<script src='cjs/bootstrap.min.js'></script>
		<script src='cjs/moment.min.js'></script>
		<script src='cjs/fullcalendar.min.js'></script>
		<script src='locale/es.js'></script>
		<script>
			$(document).ready(function() {
				$('#calendar').fullCalendar({
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,agendaWeek,agendaDay'
					},
					defaultDate: Date(),
					navLinks: true, // can click day/week names to navigate views
					editable: true,
					eventLimit: true, // allow "more" link when too many events
					eventClick: function(event) {
						
						$('#visualizar #id').text(event.id);
						$('#visualizar #title').text(event.title);
						$('#visualizar #start').text(event.start.format('DD/MM/YYYY HH:mm:ss'));
						$('#visualizar #end').text(event.end.format('DD/MM/YYYY HH:mm:ss'));
						$('#visualizar').modal('show');
						return false;

					},
					
					selectable: true,
					selectHelper: true,
					select: function(start, end){
						$('#cadastrar #start').val(moment(start).format('DD/MM/YYYY HH:mm:ss'));
						$('#cadastrar #end').val(moment(end).format('DD/MM/YYYY HH:mm:ss'));
						$('#cadastrar').modal('show');						
					},
					events: [
						<?php
							while($registros_eventos = mysqli_fetch_array($resultado_eventos)){
								?>
								{
								id: '<?php echo $registros_eventos['solicitud_Id']; ?>',
								title: '<?php echo $registros_eventos['solicitud_nombre_funcionario']; ?>',
								start: '<?php echo $registros_eventos['solicitud_fechaRetiro']." ".$registros_eventos['solicitud_horaRetiro']; ?>',
								end: '<?php echo $registros_eventos['solicitud_fechaDevolucion']." ".$registros_eventos['solicitud_horaDevolucion']; ?>',
								color: '<?php echo $color; ?>',
								},<?php
							}
						?>
					]
				});
			});
			
			//Mascara para o campo data y hora
			function DataHora(evento, objeto){
				var keypress=(window.event)?event.keyCode:evento.which;
				campo = eval (objeto);
				if (campo.value == '00/00/0000'){
					campo.value=""
				}
			 
				caracteres = '0123456789';
				separacao1 = '/';
				separacao2 = ' ';
				separacao3 = ':';
				conjunto1 = 2;
				conjunto2 = 5;
				conjunto3 = 10;
				conjunto4 = 13;
				conjunto5 = 16;
				if ((caracteres.search(String.fromCharCode (keypress))!=-1) && campo.value.length < (19)){
					if (campo.value.length == conjunto1 )
					campo.value = campo.value + separacao1;
					else if (campo.value.length == conjunto2)
					campo.value = campo.value + separacao1;
					else if (campo.value.length == conjunto3)
					campo.value = campo.value + separacao2;
					else if (campo.value.length == conjunto4)
					campo.value = campo.value + separacao3;
					else if (campo.value.length == conjunto5)
					campo.value = campo.value + separacao3;
				}else{
					event.returnValue = false;
				}
			}
		</script>
</head>
	<body>
	
<nav class="navbar navbar-inverse">
  <div class="container"> 
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <a class="navbar-brand" href="formulario_menu_principal.html">TecnoPresta</a> </div>
    
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li ><a href="formulario_registrar_reservas_alias.php"><i class="fas fa-chalkboard"></i> Reserva de espacios <span class="sr-only">(current)</span></a></li>  
        <li ><a href="formulario_VistaSolicitud.html"><i class="fas fa-door-closed"></i> Cerrar Reporte <span class="sr-only">(current)</span></a></li>
      </ul>
    </div>
    <!-- /.navbar-collapse --> 
  </div>
  <!-- /.container-fluid --> 
</nav>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h4>Informe de Solicitudes de Equipos Aceptados</h4>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
<div class="panel-body">
<!--Inicio elementos contenedor-->






			<div class="page-header">
			<!--	<h1>Consultas</h1> -->
			</div>
			<?php
			if(isset($_SESSION['mensaje'])){
				echo $_SESSION['mensaje'];
				unset($_SESSION['mensaje']);
			}
			?>
		
			<div id='calendar'></div>
		</div>

		<div class="modal fade" id="visualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title text-center">Datos del Evento</h4>
					</div>
					<div class="modal-body">
						<dl class="dl-horizontal">
							<dt>ID Pr&eacute;stamo</dt>
							<dd id="id"></dd>
							<dt>Usuario</dt>
							<dd id="title"></dd>
							<dt>Inicio de Pr&eacute;stamo</dt>
							<dd id="start"></dd>
							<dt>Fin de Pr&eacute;stamo</dt>
							<dd id="end"></dd>
						</dl>
					</div>
				</div>
			</div>
		</div>
		






<!--Fin elementos contenedor-->
</div>
</div>
  </div>
</div>
<div class="panel-footer">
  <div class="container">
    <p></p>
  </div>
</div>
        <script>
            document.addEventListener("DOMContentLoaded", function(){
                // Invocamos cada 5 segundos ;)
                const milisegundos = 5 *1000;
                setInterval(function(){
                    // No esperamos la respuesta de la petición porque no nos importa
                    fetch("refrescar.php");
                },milisegundos);
            });
        </script>
</body>
</html>