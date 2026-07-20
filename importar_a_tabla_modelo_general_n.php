<?php
if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
/*
if (!isset($_SESSION['tipo']) || !in_array($_SESSION['tipo'], [1])) {
    header('Location: formulario_corregir_modelo.php');
    exit('No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador.');
}
    */
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

require_once __DIR__ . '/usuarioAzure.php';
$usuario_azure = obtenerUsuarioSesion();

if (!$usuario_azure) {
    header("Location: index.html");
    exit();
}

// Verificar si se recibieron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_modelo = intval($_POST['id_activo']); // Campo ID del activo
    $modelo = trim($_POST['descripcion']);   // Campo descripción del activo
    $id_fondos = intval($_POST['fondos']);   // Campo select de fondos

    // Validar que los campos no estén vacíos
    if (!empty($id_modelo) && !empty($modelo) && $id_fondos > 0) {
        // Comprobar si el modelo ya existe en la tabla para evitar duplicados (modelo)
        $query_check_modelo = "SELECT COUNT(*) AS total FROM t_modelos_sugeridos WHERE modelo = ?";
        $stmt_check_modelo = $link->prepare($query_check_modelo);
        $stmt_check_modelo->bind_param("s", $modelo);
        $stmt_check_modelo->execute();
        $stmt_check_modelo->bind_result($total_modelo);
        $stmt_check_modelo->fetch();
        $stmt_check_modelo->close();

        if ($total_modelo > 0) {
            echo "<script>
                    alert('El modelo ya existe en la base de datos. No se realizará la importación.');
                    window.history.back();
                  </script>";
            exit();
        }

        // Comprobar si el id_modelo ya existe en la tabla para evitar duplicados (id_modelo)
        $query_check_id_modelo = "SELECT COUNT(*) AS total FROM t_modelos_sugeridos WHERE id_modelo = ?";
        $stmt_check_id_modelo = $link->prepare($query_check_id_modelo);
        $stmt_check_id_modelo->bind_param("i", $id_modelo);
        $stmt_check_id_modelo->execute();
        $stmt_check_id_modelo->bind_result($total_id_modelo);
        $stmt_check_id_modelo->fetch();
        $stmt_check_id_modelo->close();

        if ($total_id_modelo > 0) {
            echo "<script>
                    alert('El ID del modelo ya existe en la base de datos. No se realizará la importación.');
                    window.history.back();
                  </script>";
            exit();
        }

        // Si no existen duplicados, proceder con la inserción
        $query_insert = "INSERT INTO t_modelos_sugeridos (id_modelo, modelo, id_fondos) VALUES (?, ?, ?)";
        $stmt_insert = $link->prepare($query_insert);
        $stmt_insert->bind_param("isi", $id_modelo, $modelo, $id_fondos);

        if ($stmt_insert->execute()) {
            echo "<script>
                    alert('El modelo fue importado exitosamente.');
                    window.location.href = 'formulario_importar_modelo_general_n.php'; // Redirige a la página 
                  </script>";
        } else {
            echo "<script>
                    alert('Hubo un error al importar el modelo. Inténtelo de nuevo.');
                    window.history.back();
                  </script>";
        }

        $stmt_insert->close();
    } else {
        echo "<script>
                alert('Por favor, complete todos los campos del formulario.');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('Acceso no permitido.');
            window.history.back();
          </script>";
}

$link->close();
?>
