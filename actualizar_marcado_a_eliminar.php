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
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
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

if (empty($_POST['idsplacas'])) {
    echo '<script language="javascript">
    alert("No hay ningún activo seleccionado");
    self.location = "formulario_eliminacion_nocturna.php";
    </script>';
    exit();
} else {
    foreach ($_POST['idsplacas'] as $idplaca) {
        $marcado = 1;
        $update = "UPDATE t_placa SET marcado = '$marcado' WHERE id_placa = '$idplaca'";

        if ($link->query($update) === TRUE) {
            // Consulta para obtener los detalles del registro actualizado
            $query = "SELECT placa, serial, codigo, id_activo FROM t_placa WHERE id_placa = '$idplaca'";
            $result = $link->query($query);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $placa = $row['placa'];
                $serial = $row['serial'];
                $codigo = $row['codigo'];
                $id_activo = $row['id_activo'];

                // Insertar los detalles en la tabla de bitácora
                $insert = "INSERT INTO bitacora_eliminados (id_placa, placa, serial, codigo, id_activo, usuario) VALUES ('$idplaca', '$placa', '$serial', '$codigo', '$id_activo', '$logusuario')";
                if ($link->query($insert) !== TRUE) {
                    echo "Error al insertar en la bitácora: " . $link->error;
                }
            } else {
                echo "Error al obtener detalles del registro: " . $link->error;
            }
        } else {
            echo "Error al actualizar registro: " . $link->error;
        }
    }
}

mysqli_close($link);

echo '<script language="javascript">
alert("Cambios realizados");
self.location = "formulario_eliminacion_nocturna.php";
</script>';
?>