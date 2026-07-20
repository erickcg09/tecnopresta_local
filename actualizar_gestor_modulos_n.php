<?php
/**
 * ============================================================
 * ENDPOINT: Gestor de Modulos - Accion (CRUD)
 * ============================================================
 * Proposito: Procesa las operaciones CRUD sobre subsistemas y
 * modulos provenientes del formulario gestor_modulos_n.php
 *
 * Acciones soportadas:
 *   - crear_subsistema / editar_subsistema
 *   - toggle_subsistema (activar/desactivar)
 *   - crear_modulo / editar_modulo
 *   - toggle_modulo (activar/desactivar con validacion)
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
    echo json_encode(['success' => false, 'message' => 'Sesion invalida']);
    exit;
}

if (!esUsuarioRoot()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if (!$action) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Peticion invalida: se requiere accion']);
    exit;
}

/**
 * Sanitiza el nombre para usarlo como nombre de archivo SVG.
 */
function slugifyNombre($nombre) {
    $slug = strtolower(trim($nombre));
    $slug = preg_replace('/[^a-z0-9\s\-]/u', '', $slug);
    $slug = preg_replace('/\s+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug ?: 'archivo';
}

/**
 * Procesa la subida del archivo SVG y retorna la ruta guardada, o la existente si no se subio.
 * @param string $nombreFormulario Nombre usado para generar el slug del archivo
 * @param string $directorio Ruta relativa del directorio (ej. 'assets/img/modulos')
 * @param string|null $rutaExistente Ruta actual en BD por si no se sube archivo nuevo
 * @return string|null
 */
function procesarSubidaSVG($nombreFormulario, $directorio, $rutaExistente = null) {
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

    $dir = __DIR__ . '/' . $directorio;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $filename = slugifyNombre($nombreFormulario) . '.svg';
    $destino = $dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destino)) {
        throw new Exception('Error al guardar el archivo SVG');
    }

    return $directorio . '/' . $filename;
}

try {
    $conexionBD = BD::crearInstancia();

    switch ($action) {

        // ============================================================
        // CREAR SUBSISTEMA
        // ============================================================
        case 'crear_subsistema':
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $orden = isset($_POST['orden']) ? (int)$_POST['orden'] : 0;
            $color = isset($_POST['color']) && $_POST['color'] !== '' ? trim($_POST['color']) : null;

            if (empty($nombre)) {
                echo json_encode(['success' => false, 'message' => 'El nombre del subsistema es obligatorio']);
                exit;
            }

            $imagen = null;
            try {
                $imagen = procesarSubidaSVG($nombre, 'assets/img/subsistemas');
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }

            $sql = "INSERT INTO subsistemas (nombre, descripcion, imagen, orden, color, eliminado, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, 0, NOW(), NOW())";
            $stmt = $conexionBD->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $imagen, $orden, $color]);

            echo json_encode([
                'success' => true,
                'message' => 'Subsistema creado correctamente',
                'data' => ['id' => (int)$conexionBD->lastInsertId()]
            ]);
            break;

        // ============================================================
        // EDITAR SUBSISTEMA
        // ============================================================
        case 'editar_subsistema':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $orden = isset($_POST['orden']) ? (int)$_POST['orden'] : 0;
            $color = isset($_POST['color']) && $_POST['color'] !== '' ? trim($_POST['color']) : null;

            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de subsistema invalido']);
                exit;
            }
            if (empty($nombre)) {
                echo json_encode(['success' => false, 'message' => 'El nombre del subsistema es obligatorio']);
                exit;
            }

            // Obtener imagen actual
            $stmtImg = $conexionBD->prepare("SELECT imagen FROM subsistemas WHERE id = ?");
            $stmtImg->execute([$id]);
            $fila = $stmtImg->fetch(PDO::FETCH_ASSOC);
            $imagenActual = $fila ? $fila['imagen'] : null;

            try {
                $imagen = procesarSubidaSVG($nombre, 'assets/img/subsistemas', $imagenActual);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }

            $sql = "UPDATE subsistemas SET nombre = ?, descripcion = ?, imagen = ?, orden = ?, color = ?, updated_at = NOW()
                    WHERE id = ?";
            $stmt = $conexionBD->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $imagen, $orden, $color, $id]);

            echo json_encode([
                'success' => true,
                'message' => 'Subsistema actualizado correctamente'
            ]);
            break;

        // ============================================================
        // TOGGLE SUBSISTEMA (activar/desactivar)
        // ============================================================
        case 'toggle_subsistema':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $activo = isset($_POST['active']) ? filter_var($_POST['active'], FILTER_VALIDATE_BOOLEAN) : false;

            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de subsistema invalido']);
                exit;
            }

            if (!$activo) {
                $sqlCheck = "SELECT COUNT(*) AS total FROM modulos WHERE subsistema_id = ? AND eliminado = 0";
                $stmtCheck = $conexionBD->prepare($sqlCheck);
                $stmtCheck->execute([$id]);
                $resultado = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ((int)$resultado['total'] > 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'No se puede desactivar el subsistema porque tiene ' . (int)$resultado['total'] . ' modulo(s) activo(s). Desactivelos primero.',
                        'code' => 'HAS_ACTIVE_MODULES'
                    ]);
                    exit;
                }
            }

            $nuevoEstado = $activo ? 0 : 1;
            $sql = "UPDATE subsistemas SET eliminado = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conexionBD->prepare($sql);
            $stmt->execute([$nuevoEstado, $id]);

            $mensaje = $activo ? 'Subsistema reactivado correctamente' : 'Subsistema desactivado correctamente';
            echo json_encode(['success' => true, 'message' => $mensaje]);
            break;

        // ============================================================
        // CREAR MODULO
        // ============================================================
        case 'crear_modulo':
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $subsistema_id = isset($_POST['subsistema_id']) ? (int)$_POST['subsistema_id'] : 0;
            $ruta_base = trim($_POST['ruta_base'] ?? '');
            $orden = isset($_POST['orden']) ? (int)$_POST['orden'] : 0;
            $color = isset($_POST['color']) && $_POST['color'] !== '' ? trim($_POST['color']) : null;

            if (empty($nombre)) {
                echo json_encode(['success' => false, 'message' => 'El nombre del modulo es obligatorio']);
                exit;
            }
            if ($subsistema_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Debe seleccionar un subsistema']);
                exit;
            }

            $imagen = null;
            try {
                $imagen = procesarSubidaSVG($nombre, 'assets/img/modulos');
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }

            $sql = "INSERT INTO modulos (nombre, descripcion, subsistema_id, imagen, ruta_base, orden, color, eliminado, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW(), NOW())";
            $stmt = $conexionBD->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $subsistema_id, $imagen, $ruta_base, $orden, $color]);

            echo json_encode([
                'success' => true,
                'message' => 'Modulo creado correctamente',
                'data' => ['id' => (int)$conexionBD->lastInsertId()]
            ]);
            break;

        // ============================================================
        // EDITAR MODULO
        // ============================================================
        case 'editar_modulo':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $subsistema_id = isset($_POST['subsistema_id']) ? (int)$_POST['subsistema_id'] : 0;
            $ruta_base = trim($_POST['ruta_base'] ?? '');
            $orden = isset($_POST['orden']) ? (int)$_POST['orden'] : 0;
            $color = isset($_POST['color']) && $_POST['color'] !== '' ? trim($_POST['color']) : null;

            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de modulo invalido']);
                exit;
            }
            if (empty($nombre)) {
                echo json_encode(['success' => false, 'message' => 'El nombre del modulo es obligatorio']);
                exit;
            }
            if ($subsistema_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Debe seleccionar un subsistema']);
                exit;
            }

            // Obtener imagen actual
            $stmtImg = $conexionBD->prepare("SELECT imagen FROM modulos WHERE id = ?");
            $stmtImg->execute([$id]);
            $fila = $stmtImg->fetch(PDO::FETCH_ASSOC);
            $imagenActual = $fila ? $fila['imagen'] : null;

            try {
                $imagen = procesarSubidaSVG($nombre, 'assets/img/modulos', $imagenActual);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }

            $sql = "UPDATE modulos SET nombre = ?, descripcion = ?, subsistema_id = ?, ruta_base = ?, imagen = ?, orden = ?, color = ?, updated_at = NOW()
                    WHERE id = ?";
            $stmt = $conexionBD->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $subsistema_id, $ruta_base, $imagen, $orden, $color, $id]);

            echo json_encode([
                'success' => true,
                'message' => 'Modulo actualizado correctamente'
            ]);
            break;

        // ============================================================
        // TOGGLE MODULO (activar/desactivar con validacion)
        // ============================================================
        case 'toggle_modulo':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $activo = isset($_POST['active']) ? filter_var($_POST['active'], FILTER_VALIDATE_BOOLEAN) : false;

            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de modulo invalido']);
                exit;
            }

            if (!$activo) {
                $sqlCheck = "SELECT COUNT(*) AS total FROM formularios WHERE modulo_id = ? AND eliminado = 0";
                $stmtCheck = $conexionBD->prepare($sqlCheck);
                $stmtCheck->execute([$id]);
                $resultado = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ((int)$resultado['total'] > 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'No se puede desactivar el modulo porque tiene ' . (int)$resultado['total'] . ' formulario(s) activo(s). Desactivelos primero.',
                        'code' => 'HAS_ACTIVE_FORMS'
                    ]);
                    exit;
                }
            }

            $nuevoEstado = $activo ? 0 : 1;
            $sql = "UPDATE modulos SET eliminado = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conexionBD->prepare($sql);
            $stmt->execute([$nuevoEstado, $id]);

            $mensaje = $activo ? 'Modulo reactivado correctamente' : 'Modulo desactivado correctamente';
            echo json_encode(['success' => true, 'message' => $mensaje]);
            break;

        // ============================================================
        // ACCION NO RECONOCIDA
        // ============================================================
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Accion no reconocida: ' . $action
            ]);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
