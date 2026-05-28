<?php
// Iniciar la sesión
session_start();
$tienellave = ($_SESSION['tipo'] == 1); // Permitir root solamente
if ($tienellave == false) {
    echo '<script language = javascript>
    alert("No tienes permisos")
    self.location = "formulario_menu_principal.html"
    </script>';
}

// Incluir el archivo de conexión a la base de datos
require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}
if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

// Verificar si el método de solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar y sanitizar los datos del formulario
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $cedula = mysqli_real_escape_string($link, $_POST['cedula']);
    $codigo_presupuestario = mysqli_real_escape_string($link, $_POST['codigo_presupuestario']);
    $rol = mysqli_real_escape_string($link, $_POST['rol']);

    // Validar que los campos no estén vacíos
    if (empty($email) || empty($cedula) || empty($codigo_presupuestario) || empty($rol)) {
        die("Todos los campos son obligatorios.");
    }

    // Preparar la consulta SQL para insertar los datos en la tabla t_lista_blanca
    $query = "INSERT INTO t_lista_blanca (cedula, nombre, codigo, id_rol) 
              VALUES ('$cedula', '$email', '$codigo_presupuestario', '$rol')";

    // Ejecutar la consulta
    if (mysqli_query($link, $query)) {
        echo '<script language="javascript">
        alert("Registro guardado exitosamente.");
        self.location = "formulario_crear_usuario_sistema.php"; // Redirigir al formulario o a otra página
        </script>';
    } else {
        echo "Error al guardar el registro: " . mysqli_error($link);
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($link);
} else {
    echo "Acceso no permitido.";
}
?>