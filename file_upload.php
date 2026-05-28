<?php 
session_start();
require_once("conexion.php");
$link = $mysqli;
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];
$myDate = date("d-m-y h:i:s");
if(!empty($_FILES)){     
    $upload_dir = "uploads/";
    $fileName = $myDate.$_FILES['file']['name'];
    $uploaded_file = $upload_dir.$fileName;    
    if(move_uploaded_file($_FILES['file']['tmp_name'],$uploaded_file)){
        
		$mysql_insert = "INSERT INTO uploads (file_name, codigo, upload_time)VALUES('".$fileName."','".$logcodigo."','".date("Y-m-d H:i:s")."')";
		mysqli_query($link, $mysql_insert) or die("database error:". mysqli_error($link));
    }   
}
