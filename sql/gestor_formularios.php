<?php
/**
 * ============================================================
 * ENDPOINT: Gestor de Formularios - Datos
 * ============================================================
 * Proposito: Retorna JSON con subsistemas, modulos, formularios,
 * acciones y permisos para el formulario gestor_formularios_n.php
 *
 * Seguridad:
 * - Valida sesion Azure
 * - Solo accesible por usuario Root
 * - Prepared statements en consultas SQL
 * ============================================================
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../usuarioAzure.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/bd.php';

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

try {
    $conexionBD = BD::crearInstancia();

    // Consultar subsistemas activos para los filtros
    $sqlSubsistemas = "
        SELECT id, nombre
        FROM subsistemas
        WHERE eliminado = 0
        ORDER BY nombre ASC
    ";
    $stmtSub = $conexionBD->prepare($sqlSubsistemas);
    $stmtSub->execute();
    $subsistemas = $stmtSub->fetchAll(PDO::FETCH_ASSOC);

    // Consultar modulos activos para los filtros
    $sqlModulos = "
        SELECT id, nombre, subsistema_id
        FROM modulos
        WHERE eliminado = 0
        ORDER BY subsistema_id ASC, nombre ASC
    ";
    $stmtMod = $conexionBD->prepare($sqlModulos);
    $stmtMod->execute();
    $modulos = $stmtMod->fetchAll(PDO::FETCH_ASSOC);

    // Consultar formularios con JOIN a modulos para obtener subsistema_id
    $sqlFormularios = "
        SELECT
            f.id,
            f.modulo_id,
            m.subsistema_id,
            f.nombre,
            f.descripcion,
            f.ruta,
            f.imagen,
            f.orden,
            f.color,
            f.eliminado,
            f.created_at,
            f.updated_at
        FROM formularios f
        INNER JOIN modulos m ON m.id = f.modulo_id
        ORDER BY f.modulo_id ASC, f.orden ASC
    ";
    $stmtForm = $conexionBD->prepare($sqlFormularios);
    $stmtForm->execute();
    $formularios = $stmtForm->fetchAll(PDO::FETCH_ASSOC);

    // Consultar las acciones asignadas a cada formulario (permisos)
    $sqlPermisos = "
        SELECT p.formulario_id, p.accion_id
        FROM permisos p
        ORDER BY p.formulario_id, p.accion_id
    ";
    $stmtPerm = $conexionBD->prepare($sqlPermisos);
    $stmtPerm->execute();
    $permisosRaw = $stmtPerm->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar acciones por formulario para facil consumo en frontend
    $accionesPorFormulario = [];
    foreach ($permisosRaw as $permiso) {
        $fid = (int)$permiso['formulario_id'];
        if (!isset($accionesPorFormulario[$fid])) {
            $accionesPorFormulario[$fid] = [];
        }
        $accionesPorFormulario[$fid][] = (int)$permiso['accion_id'];
    }

    // Adjuntar acciones a cada formulario
    foreach ($formularios as &$formulario) {
        $fid = (int)$formulario['id'];
        $formulario['acciones'] = $accionesPorFormulario[$fid] ?? [];
    }
    unset($formulario);

    // Consultar todas las acciones disponibles
    $sqlAcciones = "
        SELECT id, nombre, descripcion
        FROM acciones
        ORDER BY id ASC
    ";
    $stmtAcc = $conexionBD->prepare($sqlAcciones);
    $stmtAcc->execute();
    $acciones = $stmtAcc->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'subsistemas' => $subsistemas,
        'modulos' => $modulos,
        'formularios' => $formularios,
        'acciones' => $acciones,
        'acciones_por_formulario' => $accionesPorFormulario
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
