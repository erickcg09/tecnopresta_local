<?php
session_start();

// Verificar permisos del usuario
$tienellave = in_array($_SESSION['tipo'], [1]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}

require_once("conexion.php");
$link = $mysqli;

// Verificar conexión a la base de datos
if (mysqli_connect_errno()) {
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

// Variables de sesión
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre']; // Responsable del proceso
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];

// Variables fijas y recibidas
$titulo = "Informe de Redistribución de Activos"; // Título fijo
$descripcion = "Equipo tecnológico"; // Descripción fija
$observaciones = "La redistribución se realiza con el objetivo de optimizar los recursos disponibles en la institución, trasladando los activos necesarios al centro destino para mejorar su operación y garantizar un uso eficiente de los bienes.";

// Recibir los valores del formulario
$id_placas = isset($_POST['id_placas']) ? $_POST['id_placas'] : '';
$codigo_origen = isset($_POST['cod_o']) ? $_POST['cod_o'] : '';
$codigo_destino = isset($_POST['cod_d']) ? $_POST['cod_d'] : '';
$fondos_id = isset($_POST['fondos']) ? $_POST['fondos'] : '';

// Verificar que el valor de id_placas no esté vacío
if (empty($id_placas)) {
    echo '<script language="javascript">
    alert("No se han seleccionado activos.");
    window.location.href = "in_formulario_redistribuir.php";
    </script>';
    exit();
}

// Procesar las placas como una cadena separada por comas
$id_placas_array = explode(',', $id_placas); // Convertir a array
$id_placas_str = implode(',', $id_placas_array); // Garantizar formato separado por comas

// Consultar el nombre de los fondos
$query_fondos = "SELECT fondos FROM t_fondos WHERE id_fondos = ?";
$stmt_fondos = $link->prepare($query_fondos);
$stmt_fondos->bind_param("i", $fondos_id);
$stmt_fondos->execute();
$result_fondos = $stmt_fondos->get_result();

if ($result_fondos->num_rows > 0) {
    $row_fondos = $result_fondos->fetch_assoc();
    $fondos_nombre = $row_fondos['fondos'];
} else {
    $fondos_nombre = "No especificado";
}

// Obtener fecha y hora actual
$fecha_hora = date("Y-m-d H:i:s");

// Preparar la consulta de inserción en la tabla para recrear un acta de redistribución
$query_insert = "
    INSERT INTO redistribucion_activos (
        titulo, fecha_hora, codigo_origen, codigo_destino, fondos, responsable, id_placa, descripcion, estado, codigo_anterior, nuevo_codigo, observaciones
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt_insert = $link->prepare($query_insert);

// Valores para el registro
$estado = "Usado"; // Estado genérico
$codigo_anterior = $codigo_origen;
$nuevo_codigo = $codigo_destino;

// Asociar los parámetros
$stmt_insert->bind_param(
    "ssssssssssss",
    $titulo,
    $fecha_hora,
    $codigo_origen,
    $codigo_destino,
    $fondos_nombre,
    $lognombre,
    $id_placas_str,
    $descripcion,
    $estado,
    $codigo_anterior,
    $nuevo_codigo,
    $observaciones
);

// Ejecutar la consulta de inserción
if ($stmt_insert->execute()) {
    // Obtener el ID autoincremental del registro recién creado
    $id_redistribucion = $link->insert_id;

    // Confirmar el registro exitoso
    echo '<script language="javascript">
    alert("Redistribución registrada exitosamente con ID: ' . $id_redistribucion . '");
    </script>';
} else {
    echo '<script language="javascript">
    alert("Error al registrar la redistribución: ' . $stmt_insert->error . '");
    window.location.href = "in_formulario_redistribuir.php";
    </script>';
    exit();
}

// Cerrar la declaración de inserción
$stmt_insert->close();

// Actualizar el código de las placas en `t_placa`
foreach ($id_placas_array as $id_placa) {
    // Escapar el ID y códigos
    $id_placa = mysqli_real_escape_string($link, $id_placa);
    $codigo_origen = mysqli_real_escape_string($link, $codigo_origen);
    $codigo_destino = mysqli_real_escape_string($link, $codigo_destino);

    // Consulta para actualizar el código de las placas
    $sql_update = "UPDATE t_placa
                   SET codigo = ?
                   WHERE id_placa = ? AND codigo = ?";

    // Preparar la declaración
    if ($stmt_update = mysqli_prepare($link, $sql_update)) {
        // Vincular los parámetros
        mysqli_stmt_bind_param($stmt_update, "sis", $codigo_destino, $id_placa, $codigo_origen);

        // Ejecutar la consulta
        if (mysqli_stmt_execute($stmt_update)) {
            // Comprobamos si se actualizó alguna fila
            if (mysqli_stmt_affected_rows($stmt_update) > 0) {
                //echo "La placa con ID $id_placa ha sido actualizada correctamente.<br>";
            } else {
                echo "No se encontró la placa con ID $id_placa o no se realizó la actualización.<br>";
            }
        } else {
            echo "Error al ejecutar la consulta para la placa $id_placa.<br>";
        }

        // Cerrar la declaración
        mysqli_stmt_close($stmt_update);
    } else {
        echo "Error al preparar la consulta para la placa $id_placa.<br>";
    }
}

// Cerrar conexiones
$stmt_fondos->close();
$link->close();

?>

<script type="text/javascript">
    // Redirige usando JavaScript
    const idRedistribucion = "<?php echo $id_redistribucion; ?>"; // Pasas la variable PHP al JavaScript
    window.location.href = "in_visualizar_acta_redistribucion.php?id_redistribucion=" + encodeURIComponent(idRedistribucion);
</script>
<?php
exit(); // Asegúrate de salir después de redirigir
?>

