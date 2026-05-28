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
    $string = $_SESSION['funcionario'];
    $resultado = json_decode($string, true);

    $nom = $resultado['Nombre'];
    $ap1 = $resultado['Apellido1'];
    $ap2 = $resultado['Apellido2'];
    $cedula = $resultado['EMPCED'];
    $dependencia = $resultado['Dependencia'];
    $correomep = $resultado['Correo_Electronico_Oficial'];
    $codigo = $resultado['CentrosEducativosDondeTrabaja'];
    $nombre = $nom . " " . $ap1 . " " . $ap2;
    $direccionregional = $resultado['NombreRegional'];
    $circuito = $resultado['Circuito'];

    // Utiliza sentencias preparadas para prevenir ataques de inyecci車n SQL
    $query = "SELECT id_lista_blanca FROM t_lista_blanca WHERE cedula = ? AND codigo = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "ss", $cedula, $codigo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $check_user = mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($check_user > 0) {
        // consulta
        $sql = "SELECT id_rol FROM t_lista_blanca WHERE cedula = ? AND codigo = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $cedula, $codigo);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $tipo);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Actualizar variables de sesi車n
        $_SESSION['cedula'] = $cedula;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['codigo'] = $codigo;
        $_SESSION['tipo'] = $tipo;
        $_SESSION['dependencia'] = $dependencia;
        $_SESSION['correomep'] = $correomep;
        $_SESSION['direccionreg'] = $direccionregional;
        $_SESSION['circuito'] = $circuito;

        // Cerrar la conexi車n a la base de datos
        mysqli_close($link);

        // Redirigir al nuevo archivo
        header("Location: inventario_mantenimiento.php");
        exit();
    } else {
        // Si no se encuentra en la lista blanca
        $tipo = 5;
        $_SESSION['cedula'] = $cedula;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['codigo'] = $codigo;
        $_SESSION['tipo'] = $tipo;
        $_SESSION['dependencia'] = $dependencia;
        $_SESSION['correomep'] = $correomep;
        $_SESSION['direccionreg'] = $direccionregional;
        $_SESSION['circuito'] = $circuito;

        // Redirigir al nuevo archivo
        header("Location: inventario_mantenimiento.php");
        exit();
    }
} else {
    echo "Error al cargar variables de sesi車n";
    exit();
}
?>

