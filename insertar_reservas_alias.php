<?php
// Conectar a la base de datos
require_once("conexion.php");
$link = $mysqli;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y recibir datos del formulario
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;
    $alias_id = $_POST['alias'] ?? null;
    $dias_seleccionados = $_POST['dias'] ?? [];
    $hora_inicio = $_POST['hora_inicio'] ?? null;
    $hora_fin = $_POST['hora_fin'] ?? null;
    $codigo = $_POST['codigo'] ?? null;

    // Validar los datos obligatorios
    if (!$fecha_inicio || !$fecha_fin || !$alias_id || !$hora_inicio || !$hora_fin || !$codigo || empty($dias_seleccionados)) {
        header("Location: formulario_registrar_reservas_alias.php?error=datos_incompletos");
        exit;
    }

    // Obtener el valor del campo alias desde la tabla t_alias
    $query_alias = "SELECT alias FROM t_alias WHERE alias_id = ?";
    $stmt_alias = $link->prepare($query_alias);

    if (!$stmt_alias) {
        header("Location: formulario_registrar_reservas_alias.php?error=error_preparacion_alias");
        exit;
    }

    $stmt_alias->bind_param("i", $alias_id);
    $stmt_alias->execute();
    $result_alias = $stmt_alias->get_result();
    $row_alias = $result_alias->fetch_assoc();

    if (!$row_alias) {
        header("Location: formulario_registrar_reservas_alias.php?error=alias_no_encontrado&alias_id=$alias_id");
        exit;
    }

    $alias = $row_alias['alias'];

    // Generar la camada
    $camada = $alias . '-' . $fecha_inicio . '-' . $fecha_fin . '-' . implode('-', $dias_seleccionados);

    // Crear un mapa para los días
    $mapa_dias = ['Mon' => 'L', 'Tue' => 'K', 'Wed' => 'M', 'Thu' => 'J', 'Fri' => 'V', 'Sat' => 'S', 'Sun' => 'D'];

    // Iterar sobre las fechas
    $fecha_inicio_obj = new DateTime($fecha_inicio);
    $fecha_fin_obj = new DateTime($fecha_fin);
    $fecha_fin_obj->modify('+1 day'); // Incluir la fecha final en el intervalo

    // Validar que no existan conflictos antes de insertar
    while ($fecha_inicio_obj < $fecha_fin_obj) {
        $dia_actual = $fecha_inicio_obj->format('D'); // Extrae el día (Mon, Tue, etc.)
        $dia_convertido = $mapa_dias[$dia_actual] ?? null;

        if ($dia_convertido && in_array($dia_convertido, $dias_seleccionados)) {
            // La fecha actual es válida para inserción
            $fecha_unitaria = $fecha_inicio_obj->format('Y-m-d');

            // Verificar si existe un conflicto de horarios para el alias
            $query_verificar = "SELECT COUNT(*) as total FROM t_reservas 
                               WHERE id_alias = ? AND fecha_unitaria = ? 
                               AND ((hora_inicio < ? AND hora_fin > ?) 
                               OR (hora_inicio < ? AND hora_fin > ?) 
                               OR (hora_inicio >= ? AND hora_fin <= ?))";
            $stmt_verificar = $link->prepare($query_verificar);

            if (!$stmt_verificar) {
                header("Location: formulario_registrar_reservas_alias.php?error=error_preparacion_verificacion");
                exit;
            }

            $stmt_verificar->bind_param("ssssssss", 
                $alias_id, 
                $fecha_unitaria, 
                $hora_fin, $hora_inicio, 
                $hora_inicio, $hora_fin, 
                $hora_inicio, $hora_fin
            );
            $stmt_verificar->execute();
            $result_verificar = $stmt_verificar->get_result();
            $row_verificar = $result_verificar->fetch_assoc();

            if ($row_verificar['total'] > 0) {
                header("Location: formulario_registrar_reservas_alias.php?error=conflicto_horarios&fecha=$fecha_unitaria");
                exit;
            }
        }

        // Avanzar al siguiente día
        $fecha_inicio_obj->modify('+1 day');
    }

    // Si no hay conflictos, proceder con las inserciones
    $fecha_inicio_obj = new DateTime($fecha_inicio);
    while ($fecha_inicio_obj < $fecha_fin_obj) {
        $dia_actual = $fecha_inicio_obj->format('D');
        $dia_convertido = $mapa_dias[$dia_actual] ?? null;

        if ($dia_convertido && in_array($dia_convertido, $dias_seleccionados)) {
            $fecha_unitaria = $fecha_inicio_obj->format('Y-m-d');

            $query = "INSERT INTO t_reservas 
                      (fecha_inicio, fecha_fin, hora_inicio, hora_fin, codigo, id_alias, camada, fecha_unitaria) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $link->prepare($query);

            if (!$stmt) {
                header("Location: formulario_registrar_reservas_alias.php?error=error_preparacion_insercion");
                exit;
            }

            $stmt->bind_param(
                "ssssssss",
                $fecha_inicio,
                $fecha_fin,
                $hora_inicio,
                $hora_fin,
                $codigo,
                $alias_id,
                $camada,
                $fecha_unitaria
            );

            if (!$stmt->execute()) {
                header("Location: formulario_registrar_reservas_alias.php?error=error_ejecucion_insercion");
                exit;
            }
        }

        $fecha_inicio_obj->modify('+1 day');
    }

    header("Location: formulario_registrar_reservas_alias.php?success=true");
    exit;
} else {
    header("Location: formulario_registrar_reservas_alias.php?error=metodo_incorrecto");
    exit;
}
?>


