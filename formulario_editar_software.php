<?php  
session_start();
$tienellave = ($_SESSION['tipo']==1);
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

$id_sg = $_GET['gps'];

		$preguntar = mysqli_query($link, "select * from t_software_general where id_sg='$id_sg'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$etiqueta = $respuesta['etiqueta'];
		$id_marca = $respuesta['id_marca'];

		$preguntard = mysqli_query($link, "select marca from t_marca where id_marca='$id_marca'");   
		$respuestad = mysqli_fetch_array($preguntard);
		$fabricante = $respuestad['marca'];


?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>PNTM Principal</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="fondoresponsive.css">
  <script src="/css/jquery.min.js"></script>
  <script src="/css/popper.min.js"></script>
  <script src="/css/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <script src="css/jquery-ui.js"></script>
  <link rel="stylesheet" href="css/jquery-ui.css" /> 
            <style>
		.button {
		  background-color: #0080FF;
		  border: none;
		  color: white;
		  padding: 15px 32px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 18px;
		  margin: 4px 2px;
		  cursor: pointer;
		  width: 70px;
                  text-transform: uppercase;
                  letter-spacing: 2px;
                  border-radius: 10px;
                  transition: all 300ms;
		}

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
<script type="text/javascript">
$(document).ready(function () {
   (function($) {
       $('#FiltrarContenido').keyup(function () {
            var ValorBusqueda = new RegExp($(this).val(), 'i');
            $('.BusquedaRapida tr').hide();
             $('.BusquedaRapida tr').filter(function () {
                return ValorBusqueda.test($(this).text());
              }).show();
                })
      }(jQuery));
});
</script>
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
        <a class="nav-link" href="formulario_crear_software.php"><span class="icon icon-undo2"></span> Software</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><span class="icon icon-enter"></span> Cerrar Sesi&oacute;n</a>
      </li>  
    </ul>
  </div>  
</nav>
<br>

<div class="container">
    
	  <h3>Usuario: <?php echo $lognombre; ?> </h3><br>

<h3>Editar Software</h3><br>

      <div class="row">

        <div class="col-md-6">
		<form action="editar_software.php" method="post">
		  <div class="form-group">
		    <label for="software">Software</label>
		    <input type="text" class="form-control" name="software" id="software" aria-describedby="softwareHelp" value="<?php echo $etiqueta;?>">
		    <small id="softwareHelp" class="form-text text-muted">Recuerde que este apartado es únicamente para corregir errores de escritura.</small>
		  </div>
		  <div class="form-group">
		    <label>Frabricante:</label>
		    <input type="text" id="search_marca" name="search_marca" value="<?php echo $fabricante;?>" autocomplete="off" class="form-control input-lg" />
		  </div>
  		    <input type="hidden" id="idsoftware" name="idsoftware" value="<?php echo $id_sg;?>">
		  <button type="submit" class="btn btn-dark"><span class="icon icon-floppy-disk"> Guardar</span></button>
		</form>
        </div>
        <div class="col-md-6">
<div class="d-none d-sm-none d-md-block"><img src="img/edicion-06.png "width="600" height="600"></div>
        </div>

      </div>

</div>
<script>
  $(document).ready(function(){
      
    $('#search_marca').autocomplete({
      source: "fetch_marca.php",
      minLength: 1,
      select: function(event, ui)
      {
        $('#search_marca').val(ui.item.value);
      }
    }).data('ui-autocomplete')._renderItem = function(ul, item){
      return $("<li class='ui-autocomplete-row'></li>")
        .data("item.autocomplete", item)
        .append(item.label)
        .appendTo(ul);
    };

  });
</script>
</body>
</html>

