<?php
setcookie('saludo-correo','si',time()+365*24*60*60,'/');
header('Location:tesaludo.php');
?>