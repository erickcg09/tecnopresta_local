<?php
/**
 * ============================================================
 * ENDPOINT: Gestor de Formularios - Accion (CRUD)
 * ============================================================
 * Proposito: Procesa las operaciones CRUD sobre formularios
 * y sus permisos desde el formulario gestor_formularios_n.php
 *
 * Acciones soportadas:
 *   - crear:  INSERT formulario + INSERT permisos
 *   - editar: UPDATE formulario + reemplazar permisos
 *   - toggle: activar/desactivar formulario
 *
 * Seguridad:
 *   - Valida sesion Azure
 *   - Solo Root
 *   - Prepared statements
 *   - Transacciones en operaciones multi-tabla
 * ============================================================
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/usuarioAzure.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/sql/bd.php';

$usuario_azure = obtenerUsuarioSesion();
if (!$usuario_azure) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sesión inválida']);
    exit;
}

if (!esUsuarioRoot()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
if (!$action) {
    $json = json_decode(file_get_contents('php://input'), true);
    if ($json && isset($json['action'])) {
        $action = $json['action'];
        $_POST = array_merge($_POST, $json);
    }
}

if (!$action) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Petición inválida: se requiere acción']);
    exit;
}

/**
 * Convierte el nombre de un modulo a formato camelCase para usarlo como subcarpeta.
 * Ejemplos:
 *   "Catálogos"             → "catalogos"
 *   "Centros Educativos"    → "centrosEducativos"
 *   "Cordinación de Campo"  → "cordinacionCampo"
 */
function nombreModuloACarpeta($nombre) {
    $nombre = trim($nombre);
    if (empty($nombre)) return 'sinModulo';

    // Eliminar acentos y caracteres especiales
    $nombre = str_replace(
        ['Á','É','Í','Ó','Ú','á','é','í','ó','ú','ñ','Ñ','ü','Ü'],
        ['A','E','I','O','U','a','e','i','o','u','n','N','u','U'],
        $nombre
    );
    $nombre = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nombre);

    // Eliminar preposiciones y articulos comunes
    $nombre = preg_replace('/\b(de|del|la|las|los|el|un|una|y|del)\b/i', '', $nombre);

    // Dividir en palabras, descartando vacias
    $palabras = array_filter(preg_split('/\s+/', $nombre));

    if (empty($palabras)) return 'sinModulo';

    // Construir camelCase
    $resultado = '';
    $primera = true;
    foreach ($palabras as $palabra) {
        $palabra = strtolower($palabra);
        if ($primera) {
            $resultado .= $palabra;
            $primera = false;
        } else {
            $resultado .= ucfirst($palabra);
        }
    }

    return $resultado ?: 'sinModulo';
}

/**
 * Sanitiza el nombre del formulario para usarlo como nombre de archivo SVG.
 */
function slugifyNombreFormulario($nombre) {
    $slug = strtolower(trim($nombre));
    $slug = preg_replace('/[^a-z0-9\s\-]/u', '', $slug);
    $slug = preg_replace('/\s+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug ?: 'formulario';
}

/**
 * Procesa la subida del archivo SVG y retorna la ruta guardada, o null si no se subio.
 * Si se provee $nombreModulo, guarda el archivo en una subcarpeta con el nombre del modulo.
 */
function procesarSubidaSVG($nombreFormulario, $rutaExistente = null, $nombreModulo = null) {
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
        return $rutaExistente;
    }

    $file = $_FILES['imagen'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error al subir el archivo (codigo: ' . $file['error'] . ')');
    }

    if ($file['size'] > 512000) {
        throw new Exception('El archivo SVG no debe superar los 500KB');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'svg') {
        throw new Exception('Solo se permiten archivos SVG');
    }

    $mime = mime_content_type($file['tmp_name']);
    if ($mime !== 'image/svg+xml' && $mime !== 'text/plain' && $mime !== 'text/xml') {
        throw new Exception('El archivo no parece ser un SVG valido');
    }

    $dirBase = __DIR__ . '/assets/img/formularios';

    if ($nombreModulo) {
        $subcarpeta = nombreModuloACarpeta($nombreModulo);
        $dir = $dirBase . '/' . $subcarpeta;
    } else {
        $dir = $dirBase;
    }

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $filename = slugifyNombreFormulario($nombreFormulario) . '.svg';
    $destino = $dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destino)) {
        throw new Exception('Error al guardar el archivo SVG');
    }

    $rutaRelativa = 'assets/img/formularios';
    if ($nombreModulo) {
        $rutaRelativa .= '/' . nombreModuloACarpeta($nombreModulo);
    }

    return $rutaRelativa . '/' . $filename;
}

try {
    $conexionBD = BD::crearInstancia();

    switch ($action) {

        // ============================================================
        // CREAR FORMULARIO
        // ============================================================
        case 'crear':
            $modulo_id = isset($_POST['modulo_id']) ? (int)$_POST['modulo_id'] : 0;
            $modulo_nombre = trim($_POST['modulo_nombre'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $ruta = isset($_POST['ruta']) && $_POST['ruta'] !== '' ? trim($_POST['ruta']) : null;
            $orden = isset($_POST['orden']) ? (int)$_POST['orden'] : 0;
            $color = isset($_POST['color']) && $_POST['color'] !== '' ? trim($_POST['color']) : null;
            $acciones = isset($_POST['acciones']) ? json_decode($_POST['acciones'], true) : [];

            if (empty($nombre)) {
                echo json_encode(['success' => false, 'message' => 'El nombre del formulario es obligatorio']);
                exit;
            }
            if ($modulo_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Debe seleccionar un modulo']);
                exit;
            }

            // Procesar SVG
            $imagen = null;
            try {
                $imagen = procesarSubidaSVG($nombre, null, $modulo_nombre);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }

            $conexionBD->beginTransaction();

            $sql = "INSERT INTO formularios (modulo_id, nombre, descripcion, ruta, imagen, orden, color, eliminado, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW(), NOW())";
            $stmt = $conexionBD->prepare($sql);
            $stmt->execute([$modulo_id, $nombre, $descripcion, $ruta, $imagen, $orden, $color]);
            $formulario_id = (int)$conexionBD->lastInsertId();

            if (!empty($acciones)) {
                $sqlPermiso = "INSERT INTO permisos (formulario_id, accion_id) VALUES (?, ?)";
                $stmtPerm = $conexionBD->prepare($sqlPermiso);
                foreach ($acciones as $accion_id) {
                    $aid = (int)$accion_id;
                    if ($aid > 0) {
                        $stmtPerm->execute([$formulario_id, $aid]);
                    }
                }
            }

            $conexionBD->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Formulario creado correctamente',
                'data' => ['id' => $formulario_id]
            ]);
            break;

        // ============================================================
        // EDITAR FORMULARIO
        // ============================================================
        case 'editar':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $modulo_id = isset($_POST['modulo_id']) ? (int)$_POST['modulo_id'] : 0;
            $modulo_nombre = trim($_POST['modulo_nombre'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $ruta = isset($_POST['ruta']) && $_POST['ruta'] !== '' ? trim($_POST['ruta']) : null;
            $orden = isset($_POST['orden']) ? (int)$_POST['orden'] : 0;
            $color = isset($_POST['color']) && $_POST['color'] !== '' ? trim($_POST['color']) : null;
            $acciones = isset($_POST['acciones']) ? json_decode($_POST['acciones'], true) : [];

            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de formulario invalido']);
                exit;
            }
            if (empty($nombre)) {
                echo json_encode(['success' => false, 'message' => 'El nombre del formulario es obligatorio']);
                exit;
            }
            if ($modulo_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Debe seleccionar un modulo']);
                exit;
            }

            // Obtener imagen actual del formulario
            $stmtImg = $conexionBD->prepare("SELECT imagen FROM formularios WHERE id = ?");
            $stmtImg->execute([$id]);
            $imagenActual = $stmtImg->fetchColumn();

            // Procesar SVG (si no se sube archivo, conserva la imagen actual)
            try {
                $imagen = procesarSubidaSVG($nombre, $imagenActual, $modulo_nombre);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }

            $conexionBD->beginTransaction();

            $sql = "UPDATE formularios SET modulo_id = ?, nombre = ?, descripcion = ?, ruta = ?, imagen = ?, orden = ?, color = ?, updated_at = NOW()
                    WHERE id = ?";
            $stmt = $conexionBD->prepare($sql);
            $stmt->execute([$modulo_id, $nombre, $descripcion, $ruta, $imagen, $orden, $color, $id]);

            // Obtener permisos actuales del formulario
            $stmtActual = $conexionBD->prepare("SELECT id, accion_id FROM permisos WHERE formulario_id = ?");
            $stmtActual->execute([$id]);
            $permisosActuales = $stmtActual->fetchAll(PDO::FETCH_ASSOC);

            // Indexar: accion_id => permiso_id
            $actualMap = [];
            foreach ($permisosActuales as $p) {
                $actualMap[(int)$p['accion_id']] = (int)$p['id'];
            }

            $nuevasAcciones = [];
            foreach ($acciones as $accion_id) {
                $aid = (int)$accion_id;
                if ($aid > 0) {
                    $nuevasAcciones[] = $aid;
                }
            }

            // Eliminar permisos que ya no estan en el nuevo set
            foreach ($actualMap as $accion_id => $permiso_id) {
                if (!in_array($accion_id, $nuevasAcciones)) {
                    // Primero: eliminar asignaciones a roles
                    $stmtDelRP = $conexionBD->prepare("DELETE FROM roles_permisos WHERE permiso_id = ?");
                    $stmtDelRP->execute([$permiso_id]);
                    // Luego: eliminar el permiso
                    $stmtDelPerm = $conexionBD->prepare("DELETE FROM permisos WHERE id = ?");
                    $stmtDelPerm->execute([$permiso_id]);
                }
            }

            // Agregar permisos nuevos que no existen en BD
            $stmtInsertPerm = $conexionBD->prepare("INSERT IGNORE INTO permisos (formulario_id, accion_id) VALUES (?, ?)");
            foreach ($nuevasAcciones as $accion_id) {
                if (!isset($actualMap[$accion_id])) {
                    $stmtInsertPerm->execute([$id, $accion_id]);
                }
            }

            $conexionBD->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Formulario actualizado correctamente'
            ]);
            break;

        // ============================================================
        // TOGGLE FORMULARIO (activar/desactivar)
        // ============================================================
        case 'toggle':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $activo = isset($_POST['active']) ? filter_var($_POST['active'], FILTER_VALIDATE_BOOLEAN) : false;

            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de formulario invalido']);
                exit;
            }

            $nuevoEstado = $activo ? 0 : 1;
            $sql = "UPDATE formularios SET eliminado = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conexionBD->prepare($sql);
            $stmt->execute([$nuevoEstado, $id]);

            $mensaje = $activo ? 'Formulario reactivado correctamente' : 'Formulario desactivado correctamente';
            echo json_encode(['success' => true, 'message' => $mensaje]);
            break;

        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Accion no reconocida: ' . $action
            ]);
            break;
    }

} catch (Exception $e) {
    if (isset($conexionBD) && $conexionBD->inTransaction()) {
        $conexionBD->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
