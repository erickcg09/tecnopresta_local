<?php
/**
 * ============================================================
 * ENDPOINT: Gestor de Modulos - Datos
 * ============================================================
 * Proposito: Retorna JSON con lista de subsistemas y modulos
 * para el formulario gestor_modulos_n.php
 * 
 * Seguridad:
 * - Valida sesion Azure
 * - Solo accesible por usuario Root
 * - Prepared statements en consultas SQL
 * ============================================================
 */

// Configurar respuesta como JSON
header('Content-Type: application/json; charset=utf-8');

// Validar sesion y acceso
require_once __DIR__ . '/../usuarioAzure.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/bd.php';

$usuario_azure = obtenerUsuarioSesion();
if (!$usuario_azure) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Sesion invalida'
    ]);
    exit;
}

// Solo Root puede acceder a este endpoint
if (!esUsuarioRoot()) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Acceso no autorizado'
    ]);
    exit;
}

try {
    $conexionBD = BD::crearInstancia();

    // Consultar subsistemas (todos, activos e inactivos, para gestion)
    $sqlSubsistemas = "
        SELECT
            id,
            nombre,
            descripcion,
            imagen,
            orden,
            color,
            eliminado,
            created_at,
            updated_at
        FROM subsistemas
        ORDER BY orden ASC, nombre ASC
    ";
    $stmtSub = $conexionBD->prepare($sqlSubsistemas);
    $stmtSub->execute();
    $subsistemas = $stmtSub->fetchAll(PDO::FETCH_ASSOC);

    // Consultar modulos con JOIN a subsistemas para obtener el nombre
    $sqlModulos = "
        SELECT
            m.id,
            m.nombre,
            m.descripcion,
            m.subsistema_id,
            s.nombre AS subsistema_nombre,
            m.ruta_base,
            m.imagen,
            m.orden,
            m.color,
            m.eliminado,
            m.created_at,
            m.updated_at
        FROM modulos m
        INNER JOIN subsistemas s ON s.id = m.subsistema_id
        ORDER BY m.subsistema_id ASC, m.orden ASC, m.nombre ASC
    ";
    $stmtMod = $conexionBD->prepare($sqlModulos);
    $stmtMod->execute();
    $modulos = $stmtMod->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'subsistemas' => $subsistemas,
        'modulos' => $modulos
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
