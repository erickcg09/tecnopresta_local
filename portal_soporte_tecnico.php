<?php
session_start();

require_once("conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

if (isset($_SESSION['funcionario'])) {
    // Validar y limpiar los datos de sesión
    $string = $_SESSION['funcionario'];
    $resultado = json_decode($string, true);

    $nom = mysqli_real_escape_string($link, $resultado['Nombre']);
    $ap1 = mysqli_real_escape_string($link, $resultado['Apellido1']);
    $ap2 = mysqli_real_escape_string($link, $resultado['Apellido2']);
    $cedula = mysqli_real_escape_string($link, $resultado['EMPCED']);
    $dependencia = mysqli_real_escape_string($link, $resultado['Dependencia']);
    $correomep = mysqli_real_escape_string($link, $resultado['Correo_Electronico_Oficial']);
    $codigo = mysqli_real_escape_string($link, $resultado['CentrosEducativosDondeTrabaja']);
    $nombre = $nom . " " . $ap1 . " " . $ap2;
    $direccionregional = mysqli_real_escape_string($link, $resultado['NombreRegional']);
    $circuito = mysqli_real_escape_string($link, $resultado['Circuito']);

    // Consultar la existencia del usuario en la lista blanca
    $query = "SELECT id_lista_blanca FROM t_lista_blanca WHERE cedula=? AND codigo=?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "ss", $cedula, $codigo);
    mysqli_stmt_execute($stmt);
    $resulta = mysqli_stmt_get_result($stmt);

    if (!$resulta) {
        die('Error en la consulta: ' . mysqli_error($link));
    }

    $check_user = mysqli_num_rows($resulta);

    if ($check_user > 0) {
        // Obtener el tipo de usuario de la lista blanca
        $sql = "SELECT id_rol FROM t_lista_blanca WHERE cedula=? AND codigo=?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $cedula, $codigo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            die('Error en la consulta: ' . mysqli_error($link));
        }

        // Obtener el tipo de usuario
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $tipo = $row["id_rol"];

        // Liberar el conjunto de resultados
        mysqli_free_result($result);

        mysqli_close($link);

        // Establecer las variables de sesión de forma segura
        $_SESSION['cedula'] = $cedula;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['codigo'] = $codigo;
        $_SESSION['tipo'] = $tipo;
        $_SESSION['dependencia'] = $dependencia;
        $_SESSION['correomep'] = $correomep;
        $_SESSION['direccionreg'] = $direccionregional;
        $_SESSION['circuito'] = $circuito;

        // Redirigir a la página de plataforma_clientes.php
        header("Location: plataforma_clientes.php");
        exit();
    } else {
        $tipo = 5;

        // Establecer las variables de sesión de forma segura
        $_SESSION['cedula'] = $cedula;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['codigo'] = $codigo;
        $_SESSION['tipo'] = $tipo;
        $_SESSION['dependencia'] = $dependencia;
        $_SESSION['correomep'] = $correomep;
        $_SESSION['direccionreg'] = $direccionregional;
        $_SESSION['circuito'] = $circuito;

        // Redirigir a la página de plataforma_clientes.php
        header("Location: plataforma_clientes.php");
        exit();
    }
} else {
    echo "Error al cargar variables de sesión";
    exit();
}
?>
