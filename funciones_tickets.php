<?php
// funciones_tickets.php - Funciones reutilizables para el sistema de tickets
require_once("conexion.php");

/**
 * Genera un código único para el ticket
 */
function generarCodigoTicket($tipo, $id) {
    $prefijo = ($tipo == 0) ? 'ADM' : 'TEC';
    return $prefijo . '-' . str_pad($id, 6, '0', STR_PAD_LEFT);
}

/**
 * Inserta un ticket en la base de datos
 */
function insertarTicket($datos) {
    global $mysqli;
    
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    
    // INSERT sin codigo_tkt (el campo debe permitir NULL temporalmente)
    $query = "INSERT INTO tickets (
        usuario_id, dre_id, circuito, dependencia, tipo, 
        asunto, descripcion, estado_id, eliminado, 
        created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "iississiiss", 
        $datos['usuario_id'],
        $datos['dre_id'],
        $datos['circuito'],
        $datos['dependencia'],
        $datos['tipo'],
        $datos['asunto'],
        $datos['descripcion'],
        $datos['estado_id'],
        $datos['eliminado'],
        $created_at,
        $updated_at
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $id = mysqli_insert_id($mysqli);
        mysqli_stmt_close($stmt);
        
        // Generar código basado en el tipo y el ID obtenido
        $codigo = generarCodigoTicket($datos['tipo'], $id);
        
        // Actualizar el ticket con el código generado
        $updateQuery = "UPDATE tickets SET codigo_tkt = ? WHERE id = ?";
        $updateStmt = mysqli_prepare($mysqli, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "si", $codigo, $id);
        
        if (mysqli_stmt_execute($updateStmt)) {
            mysqli_stmt_close($updateStmt);
            return $id;
        }
        
        $error = mysqli_stmt_error($updateStmt);
        error_log("Error actualizando codigo_tkt: " . $error);
        mysqli_stmt_close($updateStmt);
        return $id; // Aún así retornamos el ID, el código se puede generar después
    }
    
    $error = mysqli_stmt_error($stmt);
    error_log("Error insertarTicket: " . $error);
    mysqli_stmt_close($stmt);
    return false;
}

/**
 * Actualiza el código del ticket después de insertar
 */
function actualizarCodigoTicket($ticket_id, $codigo) {
    global $mysqli;
    
    $query = "UPDATE tickets SET codigo_tkt = ? WHERE id = ?";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "si", $codigo, $ticket_id);
    
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $resultado;
}

/**
 * Obtiene o crea el usuario en la tabla usuarios
 */
function obtenerOCrearUsuario($cedula, $nombre, $correo) {
    global $mysqli;
    
    // Buscar usuario existente
    $query = "SELECT id FROM usuarios WHERE cedula = ?";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "s", $cedula);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if ($usuario) {
        return $usuario['id'];
    }
    
    // Crear nuevo usuario
    $query = "INSERT INTO usuarios (nombre, correo, cedula, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "sss", $nombre, $correo, $cedula);
    
    if (mysqli_stmt_execute($stmt)) {
        $id = mysqli_insert_id($mysqli);
        mysqli_stmt_close($stmt);
        return $id;
    }
    
    mysqli_stmt_close($stmt);
    return false;
}

/**
 * Obtiene el ID real de la regional a partir del código
 */
function obtenerIdRegional($codigoRegional) {
    global $mysqli;
    
    $query = "SELECT id FROM regionales WHERE codigo = ? LIMIT 1";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "s", $codigoRegional);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $regional = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return $regional ? $regional['id'] : null;
}

/**
 * Verifica si un activo tiene tickets abiertos
 */
function verificarActivosConTicketsAbiertos($idsPlaca) {
    if (empty($idsPlaca)) return [];
    
    global $mysqli;
    
    $placeholders = implode(',', array_fill(0, count($idsPlaca), '?'));
    $query = "SELECT DISTINCT ta.id_placa, t.id as ticket_id, t.codigo_tkt, t.asunto, t.created_at 
              FROM tickets_activos ta
              JOIN tickets t ON ta.ticket_id = t.id
              WHERE ta.id_placa IN ($placeholders)
                AND t.estado_id = 1
                AND t.tipo = 1
                AND t.eliminado = 0
                AND ta.eliminado = 0";
    
    $stmt = mysqli_prepare($mysqli, $query);
    
    // Bind dinámico de parámetros
    $types = str_repeat('i', count($idsPlaca));
    mysqli_stmt_bind_param($stmt, $types, ...$idsPlaca);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $tickets = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tickets[$row['id_placa']] = [
            'ticket_id' => $row['ticket_id'],
            'codigo_tkt' => $row['codigo_tkt'],
            'asunto' => $row['asunto'],
            'created_at' => $row['created_at']
        ];
    }
    
    mysqli_stmt_close($stmt);
    return $tickets;
}

/**
 * Guarda los activos asociados a un ticket
 */
function guardarActivosTicket($ticket_id, $activos, $observaciones, $descripcionGeneral) {
    global $mysqli;
    
    // Forzar fechas correctas
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    
    $query = "INSERT INTO tickets_activos (
        ticket_id, id_placa, observacion_usuario, funcionando, 
        eliminado, created_at, updated_at
    ) VALUES (?, ?, ?, 0, 0, ?, ?)";
    
    $stmt = mysqli_prepare($mysqli, $query);
    
    foreach ($activos as $id_placa) {
        $observacion = isset($observaciones[$id_placa]) && !empty($observaciones[$id_placa]) 
                       ? $observaciones[$id_placa] 
                       : $descripcionGeneral;
        
        mysqli_stmt_bind_param($stmt, "iisss", $ticket_id, $id_placa, $observacion, $created_at, $updated_at);
        mysqli_stmt_execute($stmt);
    }
    
    mysqli_stmt_close($stmt);
    return true;
}

/**
 * Obtiene los activos de un centro
 */
function getActivosByCentro($codigoCentro) {
    global $mysqli;
    
    // Asegurar charset antes de consultar
    mysqli_set_charset($mysqli, "utf8mb4");
    
    $query = "SELECT 
                p.id_placa,
                p.placa,
                p.serial,
                a.modelo,
                ag.clase AS tipo_activo,
                m.marca AS nombre_marca
            FROM t_placa p
            JOIN t_activo a ON p.id_activo = a.id_activo
            JOIN t_activo_general ag ON a.id_ag = ag.id_ag
            LEFT JOIN t_marca m ON a.id_marca = m.id_marca
            WHERE p.codigo = ?
              AND p.activo = 1
            ORDER BY ag.clase, a.modelo, p.placa";
    
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "s", $codigoCentro);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $activos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $activos[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $activos;
}

/**
 * Obtiene tipos de activos únicos
 */
function getTiposActivos() {
    global $mysqli;
    
    $query = "SELECT DISTINCT clase FROM t_activo_general ORDER BY clase";
    $result = mysqli_query($mysqli, $query);
    
    $tipos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tipos[] = $row['clase'];
    }
    
    return $tipos;
}

/**
 * Obtiene marcas únicas
 */
function getMarcas() {
    global $mysqli;
    
    $query = "SELECT DISTINCT marca FROM t_marca ORDER BY marca";
    $result = mysqli_query($mysqli, $query);
    
    $marcas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $marcas[] = $row['marca'];
    }
    
    return $marcas;
}
?>