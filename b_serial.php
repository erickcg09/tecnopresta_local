<?php  
 session_start();
if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.php"
</script>';
}
require_once("conexion.php");


$link = $mysqli;

if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

} // Fin del if
 if(isset($_POST["query"]))  
 {  
      $output = '';  
      $query = "SELECT * FROM t_placa WHERE serial LIKE '%".$_POST["query"]."%'";  
      $result = mysqli_query($link, $query);  
      $output = '<ul class="list-unstyled">';  
      if(mysqli_num_rows($result) > 0)  
      {  
           while($row = mysqli_fetch_array($result))  
           {  
                $output .= '<li>'.$row["serial"].'</li>';  
           }  
      }  
      else  
      {  
           $output .= '<li></li>';  
      }  
      $output .= '</ul>';  
      echo $output;  
 }  
 ?>  
