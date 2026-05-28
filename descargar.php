<?php
// Validar sesión o permisos aquí (ej: if (!isset($_SESSION['usuario'])) { die(); })
$archivo = $_GET['archivo'] ?? '';
$ruta_segura = 'manuales/'; // Ajusta la ruta

// Lista blanca de archivos permitidos
$archivos_permitidos = [
    'Registro de Involucrados en el Proyecto.pdf',
    'Circulartecnopresta.pdf'
];

if (in_array($archivo, $archivos_permitidos)) {
    $ruta_completa = $ruta_segura . $archivo;
    
    if (file_exists($ruta_completa)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($ruta_completa) . '"');
        header('Content-Length: ' . filesize($ruta_completa));  // Línea corregida
        readfile($ruta_completa);
        exit;
    }
}

// Si falla, redirige o muestra error
header("HTTP/1.0 404 Not Found");
echo "Archivo no encontrado.";
?>