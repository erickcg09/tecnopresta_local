<?php 
session_start();
if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.html"
</script>';
}
//select.php  
if(isset($_POST["soporte_id"]))
{
 $output = '';
 //conexion a la base de datos
 require_once("conexion.php");
 $link = $mysqli;
 if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}
 $logcorreo = $_SESSION['correomep'];
 $id_soporte=$_POST["soporte_id"];
		$preguntar = mysqli_query($link, "select correo, placa from soporte where id='$id_soporte'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$correo = $respuesta['correo'];
        $placa = $respuesta['placa'];
     $output .= '
          <div class="mb-3">
            <input type="hidden" name="correo" value="'.$correo.'">
            <input type="hidden" name="correosoportista" value="'.$logcorreo.'">
            <label for="recipient-name" class="col-form-label">Caso del activo:</label>
            <input type="text" name="asunto" value="'.$placa.'" class="form-control" id="recipient-name">
          </div>
          <div class="mb-3">
            <label for="message-text" class="col-form-label">Mensaje:</label>
            <textarea class="form-control" name="mensaje" id="message-text"></textarea>
          </div>
          <div class="mb-3">
            <label for="file" class="form-label">Adjuntar Archivo</label>
            <input type="file" class="form-control" id="file" name="file">
          </div>
     ';

    echo $output;
}
?>