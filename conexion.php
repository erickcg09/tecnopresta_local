<?php


$config = include 'config.php';
$mysqli = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);

if ($mysqli->connect_errno) {
    // Registrar el error en un archivo de registro o redirigir a una página de error
    exit('Error de conexión a la base de datos');
}
?>
