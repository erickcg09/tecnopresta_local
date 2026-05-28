<?php

$ruta_imagen = 'borrador/rectangulo.png';
 
//Cargo en memoria la imagen que quiero redimensionar
$imagen_original = imagecreatefrompng($ruta_imagen);
 
//Obtengo el ancho de la imagen quecargue
$ancho_original = imagesx($imagen_original);
 
//Obtengo el alto de la imagen que cargue
$alto_original = imagesy($imagen_original);
 
//SI QUEREMOS UN ANCHO FINAL FIJO, calculamos el ALTO de forma proporcionada
//$ancho_final = 250;
 
//Ancho final en pixeles
//$alto_final = ($ancho_final / $ancho_original) * $alto_original;
 
//SI CONOCEMOS UN ALTO FINAL FIJO, calculamos el ANCHO de forma proporcionada
 
//Para usar este caso, comentar las dos lineas anteriores, y descomentar las dos siguientes a este comentario
 
$alto_final = 80; //Alto final en pixeles
$ancho_final = ($alto_final / $alto_original) * $ancho_original;
 
//Creo una imagen vacia, con el alto y el ancho que tendrá la imagen redimensionada
$imagen_redimensionada = imagecreatetruecolor($ancho_final, $alto_final);
 
//Copio la imagen original con las nuevas dimensiones a la imagen en blanco que creamos en la linea anterior
imagecopyresampled($imagen_redimensionada, $imagen_original, 0, 0, 0, 0, $ancho_final, $alto_final, $ancho_original, $alto_original);
 
//Guardo la imagen ya redimensionada
imagepng($imagen_redimensionada, 'borrador/rectangulo.png');
 
//Libero recursos, destruyendo las imágenes que estaban en memoria
imagedestroy($imagen_original);
 
imagedestroy($imagen_redimensionada);
 

                
?>

