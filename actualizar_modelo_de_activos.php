<?php
session_start();

// Verificación de permisos
if (!isset($_SESSION['tipo']) || !in_array($_SESSION['tipo'], [1, 2, 3, 4])) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}

require_once("conexion.php");
$link = $mysqli;

// Verificar conexión y conjunto de caracteres
if ($link->connect_error) {
    die("Error de conexión a MySQL: " . $link->connect_error);
}
if (!$link->set_charset("utf8")) {
    die("Error cargando el conjunto de caracteres utf8");
}

// Validación de variables de sesión
$logusuario = $_SESSION['cedula'] ?? '';
$lognombre = $_SESSION['nombre'] ?? '';
$logtipo = $_SESSION['tipo'] ?? '';
$logcodigo = $_SESSION['codigo'] ?? '';

// Validar selección de modelos y placas
if (empty($_POST['idsplacas'])) {
    echo '<script language="javascript">
    alert("No hay ningún activo seleccionado");
    window.location.href = "formulario_corregir_modelo.php";
    </script>';
    exit();
}

if (empty($_POST['modelos_select'])) {
    echo '<script language="javascript">
    alert("No se seleccionó un modelo");
    window.location.href = "formulario_corregir_modelo.php";
    </script>';
    exit();
}

$modelo = $_POST['modelos_select'];

// Preparar la consulta de actualización
$query = "UPDATE t_placa SET id_activo = ? WHERE id_placa = ?";
$stmt = $link->prepare($query);

if (!$stmt) {
    die("Error preparando la consulta: " . $link->error);
}

foreach ($_POST['idsplacas'] as $idplaca) {
    // Validar que los valores no estén vacíos
    if (!empty($modelo) && !empty($idplaca)) {
        $stmt->bind_param('ii', $modelo, $idplaca);
        
        // Ejecutar la consulta
        if (!$stmt->execute()) {
            echo '<script language="javascript">
            alert("Error al actualizar el activo con id de placa ' . $idplaca . '");
            </script>';
        }
    }
}

// Cerrar la conexión
$stmt->close();
$link->close();

// Redireccionar con mensaje de éxito
echo '<script language="javascript">
alert("Cambios realizados");
window.location.href = "formulario_corregir_modelo.php";
</script>';
?>
