<?php
session_start();
if (!isset($_SESSION['tipo']) || !in_array($_SESSION['tipo'], [1])) {
    header('Location: formulario_corregir_modelo.php');
    exit('No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador.');
}
require_once("conexion.php");
$link = $mysqli;

// Establecer charset UTF-8
if (!$link->set_charset("utf8")) {
    error_log("Error cargando el conjunto de caracteres utf8: " . $link->error);
    die("Ha ocurrido un problema de configuración de la base de datos, por favor intente más tarde.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_activo'])) {
    $id_activo = intval($_POST['id_activo']);

    $query = "SELECT 
                Ta.modelo, 
                Tg.clase, 
                Tm.marca, 
                Tc.color 
              FROM t_activo Ta
              INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag
              INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca
              INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
              WHERE Ta.id_activo = ?";

    if ($stmt = $link->prepare($query)) {
        $stmt->bind_param("i", $id_activo);
        $stmt->execute();
        $stmt->bind_result($modelo, $clase, $marca, $color);
        
        if ($stmt->fetch()) {
            echo json_encode([
                "success" => true,
                "modelo" => $modelo,
                "clase" => $clase,
                "marca" => $marca,
                "color" => $color,
            ]);
        } else {
            echo json_encode(["success" => false]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Error en la consulta."]);
    }

    $link->close();
}
?>
