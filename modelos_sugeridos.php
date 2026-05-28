<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 2, 3, 4]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];

// Verificar que el parámetro POST está presente y es un número
if (!isset($_POST['id_fondos']) || !is_numeric($_POST['id_fondos'])) {
    echo "Error: id_fondos no es válido.";
    exit();
}
$id_fondos = (int)$_POST['id_fondos']; // Convertir a entero para seguridad

// Consulta para obtener los modelos
$query = "SELECT * FROM t_modelos_sugeridos WHERE id_fondos = $id_fondos";
$result = $link->query($query);

if (!$result) {
    echo "Error en la consulta: " . $link->error;
    exit();
}

// Generar el select HTML
echo '<select class="form-select my-3 w-50" id="modelos_select" name="modelos_select">';
echo '<option value="0">Seleccione un modelo...</option>';
while ($row = $result->fetch_assoc()) {
    echo '<option value="' . htmlspecialchars($row['id_modelo']) . '">' . htmlspecialchars($row['modelo']) . '</option>';
}
echo '</select>';

$link->close();
?>
