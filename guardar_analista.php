<?php
require_once("conexion.php"); // Incluye la conexión a la base de datos
$link = $mysqli;

// Verificar si la conexión a MySQL fue exitosa
if (mysqli_connect_errno()) {
    die("Error de conexión a MySQL: " . mysqli_connect_error());
}

// Configurar el conjunto de caracteres a UTF-8
if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link));
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar los datos
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $cedula = trim($_POST['cedula']); // No usar htmlspecialchars aquí para no alterar los ceros
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $id_regional = intval($_POST['id_regional']);
    $kilometros = floatval($_POST['kilometros']);
    $mantenimiento = intval($_POST['mantenimiento']);
    $redes = intval($_POST['redes']);
    $configuracion = intval($_POST['configuracion']);

    // Validaciones adicionales
    $errores = [];
    
    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del correo electrónico no es válido";
    }
    
    // Validar que la cédula solo contenga números (incluyendo ceros al inicio)
    if (!preg_match('/^[0-9]+$/', $cedula)) {
        $errores[] = "La cédula debe contener solo números";
    }
    
    // Validar longitud mínima de cédula (por ejemplo, al menos 9 caracteres)
    if (strlen($cedula) < 9) {
        $errores[] = "La cédula debe tener al menos 9 dígitos";
    }
    
    // Si hay errores, mostrarlos
    if (!empty($errores)) {
        echo "<script>alert('Error: " . implode("\\n", $errores) . "'); window.history.back();</script>";
        mysqli_close($link);
        exit();
    }

    // Verificar si ya existe un registro con la misma cédula o correo
    $sql_verificar = "SELECT cedula, correo_analista FROM t_analista WHERE cedula = ? OR correo_analista = ?";
    $stmt_verificar = $link->prepare($sql_verificar);
    if ($stmt_verificar) {
        $stmt_verificar->bind_param("ss", $cedula, $email);
        $stmt_verificar->execute();
        $stmt_verificar->store_result();

        if ($stmt_verificar->num_rows > 0) {
            // Obtener los datos del registro existente
            $stmt_verificar->bind_result($cedula_existente, $email_existente);
            $stmt_verificar->fetch();
            
            $mensaje_error = "Error: ";
            if ($cedula_existente === $cedula) {
                $mensaje_error .= "Ya existe un analista con la cédula proporcionada. ";
            }
            if ($email_existente === $email) {
                $mensaje_error .= "Ya existe un analista con el correo electrónico proporcionado.";
            }
            
            echo "<script>alert('" . $mensaje_error . "'); window.history.back();</script>";
            $stmt_verificar->close();
            mysqli_close($link);
            exit();
        }
        $stmt_verificar->close();
    } else {
        echo "<script>alert('Error al preparar la consulta de verificación: " . $link->error . "'); window.history.back();</script>";
        mysqli_close($link);
        exit();
    }

    // Procesar la imagen
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        // Validar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
        $tipoArchivo = $_FILES['foto']['type'];
        
        if (!in_array($tipoArchivo, $tiposPermitidos)) {
            echo "<script>alert('Error: Solo se permiten archivos JPG, PNG o GIF.'); window.history.back();</script>";
            mysqli_close($link);
            exit();
        }
        
        // Validar tamaño de archivo (máximo 2MB)
        if ($_FILES['foto']['size'] > 2097152) {
            echo "<script>alert('Error: La imagen no debe exceder los 2MB.'); window.history.back();</script>";
            mysqli_close($link);
            exit();
        }
        
        $carpetaDestino = 'img_ingenieros/';
        
        // Crear directorio si no existe
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }
        
        $nombreArchivo = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', basename($_FILES['foto']['name']));
        $rutaCompleta = $carpetaDestino . $nombreArchivo;

        // Mover la imagen a la carpeta de destino
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaCompleta)) {
            // Insertar los datos en la base de datos
            $sql = "INSERT INTO t_analista (nombre, id_regional, foto, mantenimiento, redes, configuracion, kilometros, cedula, correo_analista)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $link->prepare($sql);
            if ($stmt) {
                // CORRECCIÓN: Cambiamos el tipo de datos para cédula a string
                $stmt->bind_param(
                    "sisiiidss", // Tipos corregidos: s=string, i=integer, d=double
                    $nombre,
                    $id_regional,
                    $rutaCompleta,
                    $mantenimiento,
                    $redes,
                    $configuracion,
                    $kilometros,
                    $cedula,       // Ahora como string
                    $email         // Como string
                );

                if ($stmt->execute()) {
                    // Redirigir o mostrar un mensaje de éxito
                    echo "<script>alert('Analista registrado correctamente.'); window.location.href = 'formulario_agregar_analistas.php';</script>";
                } else {
                    // Eliminar la imagen subida si hay error en la base de datos
                    if (file_exists($rutaCompleta)) {
                        unlink($rutaCompleta);
                    }
                    echo "<script>alert('Error al guardar los datos en la base de datos: " . addslashes($stmt->error) . "'); window.history.back();</script>";
                }
                $stmt->close();
            } else {
                // Eliminar la imagen subida si hay error en la preparación
                if (file_exists($rutaCompleta)) {
                    unlink($rutaCompleta);
                }
                echo "<script>alert('Error al preparar la consulta SQL: " . addslashes($link->error) . "'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Error al subir la imagen.'); window.history.back();</script>";
        }
    } else {
        $error_mensaje = "Error: No se ha subido ninguna imagen.";
        if (isset($_FILES['foto'])) {
            switch ($_FILES['foto']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error_mensaje = "Error: La imagen es demasiado grande.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_mensaje = "Error: La imagen se subió parcialmente.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_mensaje = "Error: No se ha seleccionado ninguna imagen.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error_mensaje = "Error: No existe directorio temporal.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error_mensaje = "Error: No se pudo escribir en el disco.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $error_mensaje = "Error: Subida detenida por extensión.";
                    break;
            }
        }
        echo "<script>alert('$error_mensaje'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Método no permitido.'); window.history.back();</script>";
}

// Cerrar la conexión a la base de datos
mysqli_close($link);
?>