<?php  
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "inventario_reporte.php"
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
    $cedulalog = $_SESSION['cedula'];
    $nombrelog = $_SESSION['nombre'];
    $codigolog = $_SESSION['codigo'];
    $tipolog = $_SESSION['tipo'];
    $dependencialog = $_SESSION['dependencia'];
    $correolog = $_SESSION['correomep'];
    $regionallog = $_SESSION['direccionreg'];
    $circuitolog = $_SESSION['circuito'];


$queryX = "select id_confirmacion FROM t_confirmacion_entrega_fonatel WHERE codigo_i='$codigolog'";
$resultX = mysqli_query($link,$queryX);
$check_user = mysqli_num_rows($resultX);
        
if($check_user>0){
          echo '<script language = javascript>
          alert("Su instituci\u00f3 ya realiz\u00f3 el reporte gracias")
          self.location = "inventario_reporte.php"
          </script>';
} else {   
          echo '<script language = javascript>
          alert("El presente reporte contiene los activos reportados por parte de la empresa")
          </script>';
}
 ?>
<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="js/jquery.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Ocultar objetos CSS -->
    <link REL=stylesheet href="css/check_oculto.css" type="text/css">
    <title>Confirmar recibo de activos FONATEL</title>
    <style>
        p {
      font-size: 20px;
      line-height: 1.5;
      color: #666;
    }
  </style>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">TecnoPresta</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
          <div class="navbar-nav">
            <a class="nav-link active" aria-current="page" href="inventario_reporte.php">Regresar</a>
          </div>
        </div>
      </div>
    </nav>     
   
   <div class="container">

        <form action="guardar_confirmacion_fonatel.php" method="post">
        <div class="table-responsive">
        <table class="table">
        <div class="w3-container w3-blue">
        	<h2>Lista de Equipos entregados por parte de la empresa PC Central a la instituci&oacute;n: <?php echo $dependencialog;?></h2><br>
		<p>Estimado (a) funcionario, a continuaci&oacute;n se le presentan la lista de activos del programa FONATEL, que la empresa PC Central reporta haber entregado a su representada, 
                   por favor verifique que las Placas y Series fis&iacute;cas impresas en los equipos correspondan al listado aqu&iacute; presentado</p>
        </div>
            <th></th>
        	<th>Placa</th>
        	<th>Serie</th>
		<th colspan="2">Declaro</th>
        	<?php
        	
        	$consulta=mysqli_query($link,"select * from t_entrega_fonatel where codigo = '".$codigolog."' ORDER BY placa ASC, serie ASC") or
              	die(mysqli_error($link));
        	while ($equiposf=mysqli_fetch_array($consulta)) { ?>
        	<tr>
        	    <td><input type="checkbox" class="custom-checkbox-input" name="idsentregas[]" value="<?php echo $equiposf['id_entrega']?>" checked/></td>
        		<td><?php echo $equiposf['placa']?></td>
        		<td><?php echo $equiposf['serie']?></td>
        		<td><input type="radio" name="tipo<?php echo $equiposf['id_entrega']; ?>" value="0" checked> No</td>
        		<td><input type="radio" name="tipo<?php echo $equiposf['id_entrega']; ?>" value="1"> Si</td>
        	</tr>
        	<?php } 
        	
        	$sql = "SELECT id_entrega FROM t_entrega_fonatel where codigo = $codigolog";
            if ($result=mysqli_query($link,$sql)) {
                $rowcount=mysqli_num_rows($result);
            }
        	?>
        	<tr><td colspan="4">TOTAL de Activos: <b><?php echo $rowcount;?></b></td></tr>
        	<?php
        	mysqli_close($link);	
        	?>
        </table>
        </div>
        <input type="hidden" name="funcionario" value="<?php echo $nombrelog?>">
        <input type="hidden" name="cedula_funcionario" value="<?php echo $cedulalog?>">
        <input type="hidden" name="institucion" value="<?php echo $dependencialog?>">
        <input type="hidden" name="codigo_institucion" value="<?php echo $codigolog?>">
        <input type="hidden" name="direccion_reg" value="<?php echo $regionallog?>">       
        <input type="hidden" name="circuito" value="<?php echo $circuitolog?>">

        <div class="mb-3">
          <label class="form-label">Al finalizar la revisi&oacute;n del listado anterior, se informa que:</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="completo" value="1" onchange="habilitar(this.value);" checked>
          <label class="form-check-label" for="flexRadioDefault2">
            La entrega es CORRECTA (toda la informaci&oacute;n: cantidad de activos, placa y serie)
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="completo" value="0" onchange="habilitar(this.value);">
          <label class="form-check-label" for="flexRadioDefault1">
            La entrega es INCORRECTA (existen diferencias en la cantidad recibida y/o placas y series difieren de las recibidas)
          </label>
        </div>
        <div class="form-outline">
          <textarea class="form-control" name="comentario" id="segundo" rows="2" disabled></textarea>
          <label class="form-label" for="textAreaExample3">Por favor describa las incongruencias encontradas</label>
        </div>

        <div class="d-grid gap-2">
                <input type="submit" name="btnReportar" class="btn btn-primary btn-lg" value="Enviar el Informe" onclick="confirmarEnvio(event)" enabled/> 
                </form>
        </div>
               
   </div><br>
    <div class="container">
        <div class="p-3 mb-2 bg-light text-dark"><h4><center>Ministerio de Educaci&oacute;n P&uacute;blica &#169; 2023</center></h4></div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
    <script>
      $(document).ready(function () {  
        //Detectar click en el checkbox superior de la lista
        $('#selectall').on('click', function () {
          //verificar el estado de ese checkbox si esta marcado o no
          var checked_status = this.checked;
 
          /*
           * asignarle ese estatus a cada uno de los checkbox
           * que tengan la clase "selectall"
          */
          $(".selectall").each(function () {
            this.checked = checked_status;
          });
        });
      });
    </script>
    	<script>

		function habilitar(value)

		{

			if(value=="1" || value==true)

			{

				// habilitamos

				document.getElementById("segundo").disabled=true;

			}else if(value!="1" || value==false){

				// deshabilitamos

				document.getElementById("segundo").disabled=false;

			}

		}

	</script>
	<script>
    function confirmarEnvio(event) {
        event.preventDefault(); // Evita que el formulario se envíe automáticamente

        if (confirm("¿Estás seguro de enviar los datos?")) {
            document.querySelector("form").submit();
        }
    }
</script>
  </body>
</html>