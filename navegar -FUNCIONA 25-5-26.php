<?php
/**
 * ============================================================
 * NAVEGADOR CENTRAL DE FORMULARIOS (DISPATCHER)
 * ============================================================
 * 
 * Este archivo:
 * ✔ Recibe el nombre del archivo desde URL
 * ✔ Valida seguridad
 * ✔ Valida sesión
 * ✔ Valida permisos
 * ✔ Carga el archivo real
 * 
 * NO depende de rutas físicas complejas
 * Utiliza le Sistema en Producción, NO lo rompe
 */

//session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// require_once 'usuarioAzure.php';
//require_once 'sql/bd.php';
require 'usuarioAzure.php';
require 'sql/bd.php';



/**
 * ============================================================
 * 1. VALIDAR SESIÓN
 * ============================================================
 */
$usuario = obtenerUsuarioSesion();

 /*  echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';
    exit;
*/
if (!$usuario) {
    die("Sesión inválida");
}

$esRoot = $_SESSION['Root'] ?? false; // Verificar si el usuario es Root

/**
 * ============================================================
 * 2. OBTENER RUTA DESDE URL
 * ============================================================
 */
$ruta = $_GET['ruta'] ?? null; //ruta=nombreArchivo.php 

if (!$ruta) {
    die("Ruta no especificada");
}

/**
 * VALIDAR CARACTERES PERMITIDOS
 */
if (!preg_match('/^[a-zA-Z0-9_\-\.\/=]+$/', $ruta)) { // Solo permite letras, números, guiones, guiones bajos, puntos y barras
    die("Ruta inválida");
}

/**
 * ============================================================
 * 3. SEGURIDAD: LIMPIAR RUTA
 * ============================================================
 * basename evita ataques tipo:  ../../archivo.php */
$ruta = basename($ruta); 

/**
 * ============================================================
 * 4. VALIDAR EXTENSIÓN (Lista BLANCA SEGURA)
 * ============================================================
 */

// 🔍 Obtener extensión real del archivo
$extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));

// 🔐 Lista blanca de extensiones permitidas
$extensionesPermitidas = ['php', 'html'];

// Si no está en la lista → bloquear
if (!in_array($extension, $extensionesPermitidas)) {
    die("Extensión no permitida");
}

/**
 * ============================================================
 * 5. VALIDAR QUE EL ARCHIVO EXISTA
 * ============================================================
 */
//$archivo = __DIR__ . '/' . $ruta;
$baseDir = realpath(__DIR__); // Evita problemas con rutas relativas
$archivo = realpath($baseDir . '/' . $ruta); 

if (!$archivo || strpos($archivo, $baseDir) !== 0) { // Verifica que el archivo esté dentro del directorio base
    http_response_code(403);
    die("Ruta inválida");
}


if (!file_exists($archivo)) {
    http_response_code(404);
    die("Archivo no encontrado");
}

/**
 * ========= 6. VALIDAR PERMISOS DESDE BD =======
 * Los usuarios Root tienen Acceso Total
 */
if (!$esRoot) {
    
    $db = BD::crearInstancia();

    $sql = "SELECT COUNT(*) 
        FROM usuarios u
        INNER JOIN usuarios_roles ur ON ur.usuario_id = u.id
        INNER JOIN roles_permisos rp ON rp.rol_id = ur.rol_id
        INNER JOIN permisos p ON p.id = rp.permiso_id
        INNER JOIN formularios f ON f.id = p.formulario_id
        WHERE u.cedula = ?
        AND f.ruta = ?
        AND f.eliminado = 0";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        $usuario['cedula'],
        $ruta
    ]);

    $tienePermiso = $stmt->fetchColumn(); // Si es > 0, tiene permiso

    // Si no tiene permiso → bloquear acceso
    if (!$tienePermiso) {
        http_response_code(403);
        die("Acceso denegado");
    }

}

/** Para evitar que ingresen directarmente con la ruta del archivo */
/**
 * ============================================================
 * 7. ACCESO CONTROLADO
 * ============================================================
 */

// 🔐 Define constante para evitar acceso directo
define('ACCESO_SEGURO', true);

/* DEBO COLOCAR ESTO EN CADA ARCHIVO
<?php
// 🔐 BLOQUEA ACCESO DIRECTO
if (!defined('ACCESO_SEGURO')) {
    die("Acceso directo no permitido");
}
?>

*/


/**
 * ============================================================
 * 8. CARGAR ARCHIVO REAL
 * ============================================================
 */
require_once $archivo;