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

// Ajustar los límites de carga de archivos
ini_set('upload_max_filesize', '50M'); 
ini_set('post_max_size', '55M'); 
ini_set('max_execution_time', '300'); // Tiempo de ejecución en segundos (5 minutos)
ini_set('memory_limit', '256M');

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los valores de los campos del formulario
    $etiqueta = $link->real_escape_string($_POST['etiqueta']);
    $fondos = $link->real_escape_string($_POST['fondos']);
    $codigo = $link->real_escape_string($_POST['codigo']);
    $dependencia = $link->real_escape_string($_POST['dependencia']);
    $regional = $link->real_escape_string($_POST['regional']);
    $circuito = $link->real_escape_string($_POST['circuito']);
    $fechaHora = $link->real_escape_string($_POST['fecha_hora']);

    // Verificar y mover el archivo subido
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
        $archivoNombre = basename($_FILES['archivo']['name']);
        $archivoTmpNombre = $_FILES['archivo']['tmp_name'];
        
        // Generar un nombre único para el archivo
        $archivoNuevoNombre = time() . '_' . $archivoNombre;
        $archivoDestino = 'in_recepcion_reportes/' . $archivoNuevoNombre;

        if (move_uploaded_file($archivoTmpNombre, $archivoDestino)) {
            // Preparar la consulta SQL para insertar los datos
            $sql = "INSERT INTO t_in_reportes_firmados (archivo, etiqueta, id_fondos, codigo, dependencia, regional, circuito, fecha)
                    VALUES ('$archivoDestino', '$etiqueta', '$fondos', '$codigo', '$dependencia', '$regional', '$circuito', '$fechaHora')";

            if ($link->query($sql) === TRUE) {
                echo '<script language="javascript">
                alert("Guardado correctamente");
                window.location.href = "in_formulario_recibir_reportes.php";
                </script>';
                exit();
            } else {
                echo '<script language="javascript">
                alert("Error al almacenar los datos");
                window.location.href = "in_formulario_recibir_reportes.php";
                </script>';
                exit();
            }
        } else {
            echo '<script language="javascript">
            alert("Error al mover el archivo.");
            window.location.href = "in_formulario_recibir_reportes.php";
            </script>';
            exit();
        }
    } else {
        echo '<script language="javascript">
        alert("Error al subir el archivo.");
        window.location.href = "in_formulario_recibir_reportes.php";
        </script>';
        exit();    
    }
} else {
    echo '<script language="javascript">
    alert("No se ha enviado ningún formulario.");
    window.location.href = "in_formulario_recibir_reportes.php";
    </script>';
    exit(); 
}

// Cerrar la conexión
$link->close();
?>