<?php
// ajax/ajax_eliminar_soportista.php
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

// Verificar que se recibió el ID del soportista
if(!isset($input['id_soportista']) || empty($input['id_soportista'])) {
    echo json_encode(['success' => false, 'message' => 'ID de soportista no proporcionado']);
    exit;
}

$id_soportista = intval($input['id_soportista']);

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
        echo json_encode(['success' => false, 'message' => 'Acceso denegado. Solo coordinadores pueden eliminar soportistas']);
        exit;
    }
    
} catch(Exception $e) {
    error_log("Error en ajax_eliminar_soportista.php - permisos: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al verificar permisos']);
    exit;
}

// Verificar que el soportista existe
$sql_check = "SELECT id_soportista, imagen FROM soportistas WHERE id_soportista = ?";
$stmt_check = mysqli_prepare($link, $sql_check);
mysqli_stmt_bind_param($stmt_check, "i", $id_soportista);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$soportista_data = mysqli_fetch_assoc($result_check);

if(!$soportista_data) {
    mysqli_stmt_close($stmt_check);
    echo json_encode(['success' => false, 'message' => 'El soportista no existe']);
    exit;
}
mysqli_stmt_close($stmt_check);

// Iniciar transacción
mysqli_begin_transaction($link);

try {
    // Eliminar la imagen asociada si existe
    if($soportista_data['imagen'] && file_exists("../soportistas/" . $soportista_data['imagen'])) {
        unlink("../soportistas/" . $soportista_data['imagen']);
    }
    
    // Eliminar el soportista (las clasificaciones se eliminarán automáticamente por ON DELETE CASCADE)
    $sql_delete = "DELETE FROM soportistas WHERE id_soportista = ?";
    $stmt_delete = mysqli_prepare($link, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $id_soportista);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);
    
    // Confirmar transacción
    mysqli_commit($link);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Soportista eliminado correctamente'
    ]);
    
} catch(Exception $e) {
    // Revertir cambios en caso de error
    mysqli_rollback($link);
    error_log("Error en ajax_eliminar_soportista.php - eliminación: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el soportista: ' . $e->getMessage()]);
}

mysqli_close($link);
?>