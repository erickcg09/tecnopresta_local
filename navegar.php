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

// IMPORTAR AUTH
require_once __DIR__ . '/auth.php';

// ====  1. VALIDAR SESIÓN ======
validarSesion();

// === 2. OBTENER RUTA DESDE URL ====
$ruta = $_GET['ruta'] ?? null; //ruta=nombreArchivo.php 

// VALIDAR EXISTENCIA DE RUTA
if (!$ruta) {
    http_response_code(400);
    die("Ruta no especificada");
}

// ==== VALIDAR CARACTERES PERMITIDOS ===
if (!preg_match('/^[a-zA-Z0-9_\-\.\/]+$/', $ruta)) { // Solo permite letras, números, guiones, guiones bajos, puntos y barras
    http_response_code(400);
    die("Ruta inválida");
}

// ====  3. SEGURIDAD: LIMPIAR RUTA =====
$ruta = basename($ruta); 

// == 4. VALIDAR EXTENSIÓN (Lista BLANCA SEGURA) === Obtener extensión real del archivo
$extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));

//Lista blanca de extensiones permitidas
$extensionesPermitidas = ['php', 'html'];

// Bloquear extensiones no permitidas
if (!in_array($extension, $extensionesPermitidas, true)) {
    http_response_code(403);
    die("Extensión no permitida");
}

// ===  5. VALIDAR QUE EL ARCHIVO EXISTA ===
// DEFINIR DIRECTORIO BASE
$baseDir = realpath(__DIR__); // Evita problemas con rutas relativas

// OBTENER RUTA REAL
$archivo = realpath($baseDir . '/' . $ruta); 
//$archivo = $baseDir . '/' . $ruta; // No resolver ruta real para evitar problemas con enlaces simbólicos o rutas no existentes

// VALIDAR PATH REAL
if (!$archivo || strpos($archivo, $baseDir) !== 0) { // Verifica que el archivo esté dentro del directorio base
    http_response_code(403);
    die("Ruta inválida");
}

// VALIDAR EXISTENCIA FÍSICA
if (!file_exists($archivo)) {
    http_response_code(403);
    die("Archivo no encontrado");
}

// ==== 6. VALIDAR PERMISOS DESDE BD -- Los usuarios Root tienen Acceso Total =====
validarPermisoRuta($ruta);

// == BLOQUEAR ACCESO DIRECTO ==
define('ACCESO_SEGURO', true);

/* DEBO COLOCAR ESTO EN CADA ARCHIVO
<?php
// BLOQUEA ACCESO DIRECTO
if (!defined('ACCESO_SEGURO')) {
    die("Acceso directo no permitido");
}
?>
*/

 //===  8. CARGAR FORMULARIO -- ARCHIVO REAL ===
require_once $archivo;