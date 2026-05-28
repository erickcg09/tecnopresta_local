<?php
session_start();

// Validación de sesión más segura
if (!isset($_SESSION['tipo']) || !in_array($_SESSION['tipo'], [1, 2, 3, 4])) {
    header('Location: index.html');
    exit();
}

// Conexión a la base de datos
require_once("conexion.php");
$link = $mysqli;

// Verificar conexión
if (mysqli_connect_errno()) {
    error_log("Error de conexión a MySQL: " . mysqli_connect_error());
    die("Error en el sistema. Por favor intente más tarde.");
}

// Configurar charset
if (!mysqli_set_charset($link, "utf8")) {
    error_log("Error cargando el conjunto de caracteres utf8");
}

// Validar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    die("Método no permitido");
}

// Validar y sanitizar inputs
$clase = trim($_POST['clase'] ?? '');
$imagen = $_FILES['imagen'] ?? null;

// Validaciones básicas
if (empty($clase)) {
    $_SESSION['error'] = "Debe ingresar una clase de activo";
    header('Location: formulario_crear_activo_general.php');
    exit();
}

if (!$imagen || $imagen['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "Error al subir la imagen";
    header('Location: formulario_crear_activo_general.php');
    exit();
}

// Validar tipo y tamaño de imagen
$permitidos = ['image/png' => 'png'];
$limite_kb = 180;
$extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);

// 
if (!in_array($imagen['type'], array_keys($permitidos))) {
    $_SESSION['error'] = "Solo se permiten imágenes PNG";
    header('Location: formulario_crear_activo_general.php');
    exit();
}

if ($imagen['size'] > $limite_kb * 1024) {
    $_SESSION['error'] = "El tamaño de la imagen excede el límite de " . $limite_kb . "KB";
    header('Location: formulario_crear_activo_general.php');
    exit();
}

// Verificar si la clase ya existe
$stmt = $link->prepare("SELECT id_ag FROM t_activo_general WHERE clase = ?");
$stmt->bind_param("s", $clase);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $_SESSION['error'] = "La clase de activo ya existe en la base de datos";
    header('Location: formulario_crear_activo_general.php');
    exit();
}
$stmt->close();

// Generar nombre único para la imagen
$nombre_unico = uniqid() . '.' . $permitidos[$imagen['type']];
$ruta = "img/" . $nombre_unico;

// Mover archivo
if (!move_uploaded_file($imagen['tmp_name'], $ruta)) {
    error_log("Error al mover el archivo subido: " . $imagen['name']);
    $_SESSION['error'] = "Error al guardar la imagen";
    header('Location: formulario_crear_activo_general.php');
    exit();
}

// Redimensionar imagen
try {
    $imagen_original = imagecreatefrompng($ruta);
    $ancho_original = imagesx($imagen_original);
    $alto_original = imagesy($imagen_original);

    $alto_final = 80;
    $ancho_final = (int)($alto_final / $alto_original * $ancho_original);

    $imagen_redimensionada = imagecreatetruecolor($ancho_final, $alto_final);

    imagealphablending($imagen_redimensionada, false);
    imagesavealpha($imagen_redimensionada, true);
    $transparent = imagecolorallocatealpha($imagen_redimensionada, 255, 255, 255, 127);
    imagefilledrectangle($imagen_redimensionada, 0, 0, $ancho_final, $alto_final, $transparent);

    imagecopyresampled(
        $imagen_redimensionada, $imagen_original,
        0, 0, 0, 0,
        $ancho_final, $alto_final,
        $ancho_original, $alto_original
    );

    imagepng($imagen_redimensionada, $ruta, 9);

    imagedestroy($imagen_original);
    imagedestroy($imagen_redimensionada);
} catch (Exception $e) {
    error_log("Error al redimensionar imagen: " . $e->getMessage());
    // Continúa aunque falle el redimensionado
}

// Insertar en base de datos
$stmt = $link->prepare("INSERT INTO t_activo_general (clase, imagen) VALUES (?, ?)");
$stmt->bind_param("ss", $clase, $nombre_unico);

if ($stmt->execute()) {
    $_SESSION['success'] = "Registro guardado correctamente";
} else {
    error_log("Error al insertar en BD: " . $stmt->error);
    $_SESSION['error'] = "Error al guardar el registro";

    if (file_exists($ruta)) {
        unlink($ruta);
    }
}

$stmt->close();
mysqli_close($link);

header('Location: formulario_crear_activo_general.php');
exit();
?>
