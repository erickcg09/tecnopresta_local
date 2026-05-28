<?php
session_start();
if (!$_SESSION) {
    echo '<script language = javascript>
    alert("Usuario no autenticado")
    self.location = "index.html"
    </script>';
    exit(); // Detener la ejecución si el usuario no está autenticado
}

require_once("conexion.php");
$link = $mysqli;

// Verificar conexión a la base de datos
if (mysqli_connect_errno()) {
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
    exit();
}

// Establecer el conjunto de caracteres a UTF-8
if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

// Verificar si todos los campos requeridos están presentes y no están vacíos
$post = (isset($_POST['clase']) && !empty($_POST['clase'])) &&
        (isset($_POST['marca']) && !empty($_POST['marca'])) &&
        (isset($_POST['modelo']) && !empty($_POST['modelo'])) &&
        (isset($_POST['color']) && !empty($_POST['color']));

if ($post) {
    // Asignar valores a las variables
    $id_ag = $_POST['clase'];
    $id_marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $id_color = $_POST['color'];

    // Verificar si el modelo ya existe en la base de datos
    $miconsulta = "SELECT id_activo FROM t_activo WHERE modelo='$modelo' AND id_marca='$id_marca' AND id_color='$id_color'";
    $mirespuesta = $link->query($miconsulta);

    if ($mirespuesta->num_rows >= 1) {
        // Si el modelo ya existe, mostrar alerta y redirigir
        echo '<script language = javascript>
        alert("El modelo que intenta registrar ya existe")
        self.location = "formulario_agregar_activo.php"
        </script>';
    } else {
        // Si el modelo no existe, insertar el nuevo registro
        $consulta = "INSERT INTO t_activo (id_ag, id_marca, modelo, id_color) VALUES ('$id_ag', '$id_marca', '$modelo', '$id_color')";
        if ($link->query($consulta)) {
            // Si la inserción es exitosa, mostrar alerta y redirigir
            echo '<script language = javascript>
            alert("Guardado correctamente")
            self.location = "formulario_agregar_activo.php"
            </script>';
        } else {
            // Si hay un error en la inserción, mostrar el error
            echo "Error al guardar el registro: " . $link->error;
        }
    }
} else {
    // Si faltan campos, mostrar alerta y redirigir
    echo "<script type=\"text/javascript\">
    alert(\"Debe completar todos los campos\");
    window.location=\"formulario_agregar_activo.php\"
    </script>";
}

// Cerrar la conexión a la base de datos
mysqli_close($link);
?>
