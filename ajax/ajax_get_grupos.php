<?php
// ajax/ajax_get_grupos.php
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

// Verificar que el usuario esté autenticado
if(!isset($_SESSION['cedula']) || empty($_SESSION['cedula'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada']);
    exit;
}

// Verificar que se recibió el ID del soportista
if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de soportista no proporcionado']);
    exit;
}

$id_soportista = intval($_GET['id']);

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
        echo json_encode(['success' => false, 'message' => 'Acceso denegado. Solo coordinadores pueden ver esta información']);
        exit;
    }
    
} catch(Exception $e) {
    error_log("Error en ajax_get_grupos.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al verificar permisos']);
    exit;
}

// Obtener los grupos del soportista (solo los activos)
$sql_grupos = "SELECT grupo FROM soportistas_clasificaciones 
               WHERE id_soportista = ? AND activo = 1 
               ORDER BY grupo ASC";
$stmt_grupos = mysqli_prepare($link, $sql_grupos);
mysqli_stmt_bind_param($stmt_grupos, "i", $id_soportista);
mysqli_stmt_execute($stmt_grupos);
$result_grupos = mysqli_stmt_get_result($stmt_grupos);

$grupos = [];
while($row = mysqli_fetch_assoc($result_grupos)) {
    $grupos[] = intval($row['grupo']);
}
mysqli_stmt_close($stmt_grupos);

// Devolver la respuesta
echo json_encode([
    'success' => true,
    'grupos' => $grupos,
    'id_soportista' => $id_soportista
]);

mysqli_close($link);
?>