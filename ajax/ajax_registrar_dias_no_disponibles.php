<?php
// ajax/ajax_registrar_dias_no_disponibles.php
session_start();

require_once("../conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . mysqli_connect_error()]);
    exit;
}

date_default_timezone_set('America/Costa_Rica');

// Verificar sesión
if(!isset($_SESSION['cedula'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada']);
    exit;
}

// Verificar que se recibieron datos por POST
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id_soportista = $_POST['id_soportista'] ?? 0;
$fecha_desde = $_POST['fecha_desde'] ?? '';
$fecha_hasta = $_POST['fecha_hasta'] ?? '';
$id_motivo = $_POST['id_motivo'] ?? 0;

// Debug: registrar datos recibidos
error_log("Datos recibidos - id_soportista: $id_soportista, desde: $fecha_desde, hasta: $fecha_hasta, motivo: $id_motivo");

// Validaciones
if(!$id_soportista) {
    echo json_encode(['success' => false, 'message' => 'ID de soportista no válido']);
    exit;
}

if(!$fecha_desde || !$fecha_hasta) {
    echo json_encode(['success' => false, 'message' => 'Las fechas son requeridas']);
    exit;
}

if(!$id_motivo) {
    echo json_encode(['success' => false, 'message' => 'Debe seleccionar un motivo']);
    exit;
}

// Verificar que el usuario sea el soportista
$logcorreo = $_SESSION['correomep'];
$logcedula = $_SESSION['cedula'];

$sql_check = "SELECT id_soportista FROM soportistas WHERE id_soportista = ? AND (correo = ? OR cedula = ?)";
$stmt_check = mysqli_prepare($link, $sql_check);
mysqli_stmt_bind_param($stmt_check, "iss", $id_soportista, $logcorreo, $logcedula);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_store_result($stmt_check);

if(mysqli_stmt_num_rows($stmt_check) === 0) {
    mysqli_stmt_close($stmt_check);
    echo json_encode(['success' => false, 'message' => 'No tiene permiso para registrar días para este soportista']);
    exit;
}
mysqli_stmt_close($stmt_check);

// Generar array de fechas
$fechas = [];
$current = strtotime($fecha_desde);
$end = strtotime($fecha_hasta);

if($current === false || $end === false) {
    echo json_encode(['success' => false, 'message' => 'Formato de fecha inválido']);
    exit;
}

while($current <= $end) {
    $fechas[] = date('Y-m-d', $current);
    $current = strtotime('+1 day', $current);
}

if(empty($fechas)) {
    echo json_encode(['success' => false, 'message' => 'No se generaron fechas válidas']);
    exit;
}

$fecha_actual = date('Y-m-d H:i:s');
$registrados = 0;
$duplicados = 0;
$errores = 0;

foreach($fechas as $fecha) {
    // Verificar si ya existe
    $sql_exists = "SELECT id_dnod FROM dias_no_disponibles 
                   WHERE id_soportista = ? AND fecha = ?";
    $stmt_exists = mysqli_prepare($link, $sql_exists);
    mysqli_stmt_bind_param($stmt_exists, "is", $id_soportista, $fecha);
    mysqli_stmt_execute($stmt_exists);
    mysqli_stmt_store_result($stmt_exists);
    
    if(mysqli_stmt_num_rows($stmt_exists) === 0) {
        mysqli_stmt_close($stmt_exists);
        
        // Insertar
        $sql_insert = "INSERT INTO dias_no_disponibles (id_soportista, fecha, id_motivo, created_at, updated_at, aprobado) 
                       VALUES (?, ?, ?, ?, ?, 0)";
        $stmt_insert = mysqli_prepare($link, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "isiss", $id_soportista, $fecha, $id_motivo, $fecha_actual, $fecha_actual);
        
        if(mysqli_stmt_execute($stmt_insert)) {
            $registrados++;
        } else {
            $errores++;
            error_log("Error al insertar fecha $fecha: " . mysqli_error($link));
        }
        mysqli_stmt_close($stmt_insert);
    } else {
        mysqli_stmt_close($stmt_exists);
        $duplicados++;
    }
}

$mensaje = "Se registraron $registrados días no disponibles";
if($duplicados > 0) {
    $mensaje .= ". $duplicados días ya estaban registrados";
}
if($errores > 0) {
    $mensaje .= ". $errores días no se pudieron registrar";
}

echo json_encode(['success' => true, 'message' => $mensaje]);
?>