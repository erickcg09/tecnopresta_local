<?php 
session_start();
if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.html"
</script>';
}
//select.php  
if(isset($_POST["beneficiario_id"]))
{
 $output = '';
 //conexion a la base de datos
 require_once("conexion.php");
 $link = $mysqli;
 $logcorreo = $_SESSION['correomep'];
 $id_beneficiario=$_POST["beneficiario_id"];
 $year = date('Y');
		$preguntar = mysqli_query($link, "select placa from activos_beneficiarios_programa_3 where id_benef='$id_beneficiario' AND periodo='$year'");   
		$respuesta = mysqli_fetch_array($preguntar);
		$placa = $respuesta['placa'];

     $output .= '
          <div class="mb-3">
            <input type="hidden" name="idbeneficiario" value="'.$id_beneficiario.'">
            <label for="recipient-name" class="col-form-label">Placa:</label>
            <input type="text" name="placa" class="form-control" required>
          </div>
          <div class="mb-3">

            <label for="recipient-name" class="col-form-label">Serial:</label>
            <input type="text" name="serial" class="form-control" required>
          </div>
          <div class="mb-3"> 
          

            <div class="input-group date" id="datepicker">
 <table class="table">
  <tbody>
    <tr>
      <td><center>Inicio</center></td>
      <td><center>Fin</center></td>
    </tr>
    <tr>
      <td>            <input type="date" name="fechai"
                   
                   min="2022-01-01" max="2030-12-21" class="form-control"></td>
      <td>            <input type="date" name="fechaf"
                   
                   min="2022-01-01" max="2030-12-21" class="form-control"></td>
    </tr>
  </tbody>
</table>
          </div>        

     ';

     $output .= '
     <p class="h5">Equipo adjudicado al estudiante</p>
        <table class="table">
        
        	<h2></h2>
        	<th scope="col">Placa</th>
        	<th scope="col"></th>
        	<th scope="col"></th>
                <tbody>  
     ';    
         $miquery=mysqli_query($link,"SELECT id, placa, serial
		 FROM activos_beneficiarios_programa_3
		 WHERE id_benef='$id_beneficiario' AND periodo='$year'
		 ORDER BY placa ASC") or die(mysqli_error($link));


	while ($row=mysqli_fetch_array($miquery)) {     
        $cplaca=$row['placa'];
        $cserial=$row['serial'];
        $id=$row['id'];
     $output .= '                
                <tr>
                    <td>'.$cplaca.'<td>
                    <td><a href="formulario_editar_activo_bp3.php?gps='.$id.'"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
  <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
</svg> editar</a></td>
                    <td><a href="formulario_devolver_activo_bp3.php?gps='.$id.'"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-repeat" viewBox="0 0 16 16">
  <path d="M11 5.466V4H5a4 4 0 0 0-3.584 5.777.5.5 0 1 1-.896.446A5 5 0 0 1 5 3h6V1.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384l-2.36 1.966a.25.25 0 0 1-.41-.192Zm3.81.086a.5.5 0 0 1 .67.225A5 5 0 0 1 11 13H5v1.466a.25.25 0 0 1-.41.192l-2.36-1.966a.25.25 0 0 1 0-.384l2.36-1.966a.25.25 0 0 1 .41.192V12h6a4 4 0 0 0 3.585-5.777.5.5 0 0 1 .225-.67Z"/>
</svg> devolver</a></td>
                </tr>
     ';

    }
    	mysqli_close($link);
     $output .= '
        </tbody>
        </table>
     ';



    echo $output;

    
    
    
    
} // cierre del if
?>