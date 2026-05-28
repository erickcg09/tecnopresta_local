<?php
session_start();
if (!isset($_SESSION['tipo']) || !in_array($_SESSION['tipo'], [1, 2, 3, 4])) {
    header('Location: formulario_menu_inventario.html');
    exit('No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador.');
}

require_once("conexion.php");
$link = $mysqli;

// Verificar conexión a la base de datos
if ($link->connect_error) {
    error_log("Error de conexión a MySQL: " . $link->connect_error);
    die("Ha ocurrido un problema de conexión, por favor intente más tarde.");
}

// Establecer charset UTF-8
if (!$link->set_charset("utf8")) {
    error_log("Error cargando el conjunto de caracteres utf8: " . $link->error);
    die("Ha ocurrido un problema de configuración de la base de datos, por favor intente más tarde.");
}

// Construcción de la consulta base
$query = "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tg.imagen
          FROM t_activo Ta
          INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
          INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
          INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag";

// Si hay una consulta de búsqueda, modificar la consulta
if (isset($_POST['consulta'])) {
    $q = $link->real_escape_string($_POST['consulta']);
    $query .= " WHERE Tg.clase LIKE ? OR Tm.marca LIKE ? OR Ta.modelo LIKE ?";
}

// Añadir ordenamiento final a la consulta
$query .= " ORDER BY Tg.clase ASC";

$stmt = $link->prepare($query);

if (!$stmt) {
    error_log("Error en la preparación de la consulta: " . $link->error);
    die("Ha ocurrido un problema con la base de datos, por favor intente más tarde.");
}

// Si hay una consulta de búsqueda, pasar los parámetros
if (isset($_POST['consulta'])) {
    $q_param = '%' . $q . '%';
    $stmt->bind_param('sss', $q_param, $q_param, $q_param);
}

// Ejecutar la consulta
if (!$stmt->execute()) {
    error_log("Error en la ejecución de la consulta: " . $stmt->error);
    die("Ha ocurrido un problema al ejecutar la consulta, por favor intente más tarde.");
}

$resultado = $stmt->get_result();

// Iniciar el buffer de salida para capturar el HTML generado
ob_start();

if ($resultado->num_rows > 0) {
    ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Activo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Color</th>
                <th>Imagen</th>
                <th>Agregar</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($fila['id_activo']) ?></td>
                <td><?= htmlspecialchars($fila['clase']) ?></td>
                <td><?= htmlspecialchars($fila['marca']) ?></td>
                <td><?= htmlspecialchars($fila['modelo']) ?></td>
                <td><?= htmlspecialchars($fila['color']) ?></td>
                <td><img src="/img/<?= htmlspecialchars($fila['imagen']) ?>" width="70" class="img-fluid img-thumbnail"></td>
                <td><a class="btn btn-dark" href="formulario_agregar_placas.php?idx=<?= htmlspecialchars($fila['id_activo']) ?>" role="button"><span class="icon icon-plus"> Placa y Serie</span></a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php
} else {
    echo "No hay activos.";
}

// Capturar el contenido del buffer de salida
$salida = ob_get_clean();

// Cerrar el statement y la conexión a la base de datos
$stmt->close();
$link->close();

// Mostrar la salida final
header('Content-Type: text/html; charset=utf-8');
echo $salida;
?>