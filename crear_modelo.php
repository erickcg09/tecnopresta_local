<?php
session_start();

// ✅ CORRECCIÓN: Verificar sesión correctamente
if (!isset($_SESSION['tipo']) || !in_array($_SESSION['tipo'], [1, 2, 3, 4])) {
    echo '<script>
        alert("No tienes permisos para realizar esta acción");
        window.location.href = "formulario_menu_inventario.html";
    </script>';
    exit;
}

require_once("conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
    exit;
}

mysqli_set_charset($link, "utf8");

// ✅ CORRECCIÓN: Debug para ver qué datos llegan
error_log("Datos recibidos en crear_modelo.php:");
error_log("id_ag: " . ($_POST['id_ag'] ?? 'NO RECIBIDO'));
error_log("id_marca: " . ($_POST['id_marca'] ?? 'NO RECIBIDO'));
error_log("id_color: " . ($_POST['id_color'] ?? 'NO RECIBIDO'));
error_log("modelo: " . ($_POST['modelo'] ?? 'NO RECIBIDO'));

// ✅ CORRECCIÓN: Verificar campos requeridos de forma más robusta
$campos_requeridos = ['id_ag', 'id_marca', 'id_color'];
$campos_faltantes = [];

foreach ($campos_requeridos as $campo) {
    if (empty($_POST[$campo]) || $_POST[$campo] === '0') {
        $campos_faltantes[] = $campo;
    }
}

// El campo 'modelo' es especial porque viene del campo de texto
if (empty($_POST['modelo'])) {
    $campos_faltantes[] = 'modelo';
}

if (empty($campos_faltantes)) {
    // Sanitizar datos
    $id_ag    = intval($_POST['id_ag']);
    $id_marca = intval($_POST['id_marca']);
    $id_color = intval($_POST['id_color']);
    $modelo   = trim($link->real_escape_string($_POST['modelo']));

    // Verificar si ya existe
    $stmt = $link->prepare("SELECT id_activo FROM t_activo WHERE modelo = ? AND id_marca = ? AND id_color = ? AND id_ag = ?");
    $stmt->bind_param("siii", $modelo, $id_marca, $id_color, $id_ag);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows >= 1) {
        $stmt->close();
        echo '<script>
            alert("El modelo que intenta registrar ya existe");
            window.location.href = "formulario_busqueda_creacion_activo.php";
        </script>';
    } else {
        // Insertar nuevo modelo
        $stmt_insert = $link->prepare("INSERT INTO t_activo (id_ag, id_marca, modelo, id_color) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("iisi", $id_ag, $id_marca, $modelo, $id_color);

        if ($stmt_insert->execute()) {
            $nuevo_id = $stmt_insert->insert_id;
            $stmt_insert->close();
            $stmt->close();
            
            echo '<script>
                alert("Modelo guardado correctamente con ID: ' . $nuevo_id . '");
                window.location.href = "formulario_busqueda_creacion_activo.php";
            </script>';
        } else {
            error_log("Error en inserción: " . $stmt_insert->error);
            $stmt_insert->close();
            $stmt->close();
            
            echo '<script>
                alert("Error al guardar el registro: ' . $stmt_insert->error . '");
                window.location.href = "formulario_busqueda_creacion_activo.php";
            </script>';
        }
    }
} else {
    echo '<script>
        alert("Debe completar todos los campos. Faltan: ' . implode(', ', $campos_faltantes) . '");
        window.location.href = "formulario_busqueda_creacion_activo.php";
    </script>';
}

$link->close();
?>