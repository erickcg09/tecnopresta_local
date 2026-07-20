<?php
// ajax/ajax_actualizar_grupos.php
session_start();

require_once("../conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

if (!mysqli_set_charset($link, "utf8")) {
    echo json_encode(['success' => false, 'message' => 'Error al configurar caracteres']);
    exit;
}

date_default_timezone_set('America/Costa_Rica');

// Verificar que el usuario esté autenticado
if(!isset($_SESSION['cedula']) || empty($_SESSION['cedula'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada']);
    exit;
}

// Verificar que se recibió el JSON
$input = json_decode(file_get_contents('php://input'), true);
if(!$input) {
    echo json_encode(['success' => false, 'message' => 'Datos no válidos']);
    exit;
}

// Verificar que se recibió el ID del soportista y los grupos
if(!isset($input['id_soportista']) || empty($input['id_soportista'])) {
    echo json_encode(['success' => false, 'message' => 'ID de soportista no proporcionado']);
    exit;
}

if(!isset($input['grupos']) || empty($input['grupos']) || !is_array($input['grupos'])) {
    echo json_encode(['success' => false, 'message' => 'Debe seleccionar un grupo para el soportista']);
    exit;
}

$id_soportista = intval($input['id_soportista']);
$grupos = $input['grupos'];

// Validar que solo se haya enviado un grupo (selección única)
if(count($grupos) !== 1) {
    echo json_encode(['success' => false, 'message' => 'Solo puede seleccionar un grupo por soportista']);
    exit;
}

$grupo = intval($grupos[0]);

// Validar que el grupo sea válido (1,2,3,4)
if(!in_array($grupo, [1,2,3,4])) {
    echo json_encode(['success' => false, 'message' => 'Grupo no válido']);
    exit;
}

// Verificar que el usuario logueado sea coordinador
$logusuario = $_SESSION['cedula'];
$logcorreo = $_SESSION['correomep'];
$es_coordinador = false;

try {
    // Obtener el soportista por correo
    $sql = "SELECT id_soportista FROM soportistas WHERE correo = ? OR cedula = ? LIMIT 1";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $logcorreo, $logusuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $soportista = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if($soportista) {
        $id_soportista_logueado = $soportista['id_soportista'];
        
        // Verificar si es coordinador
        $sql_grupo = "SELECT grupo FROM soportistas_clasificaciones 
                      WHERE id_soportista = ? AND grupo = 4 AND activo = 1 LIMIT 1";
        $stmt_grupo = mysqli_prepare($link, $sql_grupo);
        mysqli_stmt_bind_param($stmt_grupo, "i", $id_soportista_logueado);
        mysqli_stmt_execute($stmt_grupo);
        $result_grupo = mysqli_stmt_get_result($stmt_grupo);
        
        if(mysqli_fetch_assoc($result_grupo)) {
            $es_coordinador = true;
        }
        mysqli_stmt_close($stmt_grupo);
    }
    
    if(!$es_coordinador) {
        echo json_encode(['success' => false, 'message' => 'Acceso denegado. Solo coordinadores pueden realizar esta acción']);
        exit;
    }
    
} catch(Exception $e) {
    error_log("Error en ajax_actualizar_grupos.php - permisos: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al verificar permisos']);
    exit;
}

// Verificar que el soportista existe
$sql_check = "SELECT id_soportista FROM soportistas WHERE id_soportista = ?";
$stmt_check = mysqli_prepare($link, $sql_check);
mysqli_stmt_bind_param($stmt_check, "i", $id_soportista);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_store_result($stmt_check);

if(mysqli_stmt_num_rows($stmt_check) === 0) {
    mysqli_stmt_close($stmt_check);
    echo json_encode(['success' => false, 'message' => 'El soportista no existe']);
    exit;
}
mysqli_stmt_close($stmt_check);

// Actualizar las clasificaciones
$fecha_actual = date('Y-m-d H:i:s');

// Iniciar transacción
mysqli_begin_transaction($link);

try {
    // Desactivar todos los grupos actuales del soportista
    $sql_desactivar = "UPDATE soportistas_clasificaciones 
                       SET activo = 0, updated_at = ? 
                       WHERE id_soportista = ?";
    $stmt_desactivar = mysqli_prepare($link, $sql_desactivar);
    mysqli_stmt_bind_param($stmt_desactivar, "si", $fecha_actual, $id_soportista);
    mysqli_stmt_execute($stmt_desactivar);
    mysqli_stmt_close($stmt_desactivar);
    
    // Verificar si ya existe una clasificación con este grupo
    $sql_verificar = "SELECT id_clas FROM soportistas_clasificaciones 
                      WHERE id_soportista = ? AND grupo = ?";
    $stmt_verificar = mysqli_prepare($link, $sql_verificar);
    mysqli_stmt_bind_param($stmt_verificar, "ii", $id_soportista, $grupo);
    mysqli_stmt_execute($stmt_verificar);
    mysqli_stmt_store_result($stmt_verificar);
    
    if(mysqli_stmt_num_rows($stmt_verificar) > 0) {
        // Si existe, reactivarlo
        $sql_activar = "UPDATE soportistas_clasificaciones 
                        SET activo = 1, updated_at = ? 
                        WHERE id_soportista = ? AND grupo = ?";
        $stmt_activar = mysqli_prepare($link, $sql_activar);
        mysqli_stmt_bind_param($stmt_activar, "sii", $fecha_actual, $id_soportista, $grupo);
        mysqli_stmt_execute($stmt_activar);
        mysqli_stmt_close($stmt_activar);
    } else {
        // Si no existe, insertar nuevo registro
        $sql_insertar = "INSERT INTO soportistas_clasificaciones (id_soportista, grupo, activo, created_at, updated_at) 
                         VALUES (?, ?, 1, ?, ?)";
        $stmt_insertar = mysqli_prepare($link, $sql_insertar);
        mysqli_stmt_bind_param($stmt_insertar, "iiss", $id_soportista, $grupo, $fecha_actual, $fecha_actual);
        mysqli_stmt_execute($stmt_insertar);
        mysqli_stmt_close($stmt_insertar);
    }
    mysqli_stmt_close($stmt_verificar);
    
    // Confirmar transacción
    mysqli_commit($link);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Clasificación actualizada correctamente',
        'grupo' => $grupo
    ]);
    
} catch(Exception $e) {
    // Revertir cambios en caso de error
    mysqli_rollback($link);
    error_log("Error en ajax_actualizar_grupos.php - actualización: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la clasificación: ' . $e->getMessage()]);
}

mysqli_close($link);
?>