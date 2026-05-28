<?php
require_once 'bd.php';
//session_start();
header('Content-Type: application/json');

session_start();
$funcionario = $_SESSION['funcionario'];
echo json_encode([
    "debug_sesion" => $_SESSION
]);

/*
if (!isset($_SESSION['funcionario'])) {
    echo json_encode(["error" => "Sesión no válida"]);
    //exit();
}
*/
/*if (!isset($_SESSION['funcionario']) || empty($_SESSION['funcionario'])) {
    echo json_encode(["error" => "Sesión no válida"]);
    //exit();
}*/

echo "<pre>";
print_r($_SESSION);
//print_r($funcionario);
echo "</pre>";

try {

    $conexionBD = BD::crearInstancia();
    //$funcionario = $_SESSION['funcionario'];

    // =========================
    // VALIDAR USUARIO
    // =========================
    $sql = "SELECT * FROM usuarios WHERE id = ? LIMIT 1";
    $consulta = $conexionBD->prepare($sql);
    $consulta->execute([$funcionario]);
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $sql = "INSERT INTO usuarios (id) VALUES (?)";
        $consulta = $conexionBD->prepare($sql);
        $consulta->execute([$funcionario]);

        $usuario = ['id' => $funcionario]; // ✔ CORRECTO
    }

    // =========================
    // PERMISOS
    // =========================
    $sql = "SELECT
                s.nombre AS subsistema,
                m.id AS modulo_id,
                m.nombre AS modulo,
                m.ruta_base
            FROM usuarios u
            JOIN usuarios_roles ur ON ur.usuario_id = u.id
            JOIN t_roles r ON r.id_rol = ur.rol_id
            JOIN subsistemas s ON s.id = ur.subsistema_id
            JOIN roles_permisos rp ON rp.rol_id = r.id_rol
            JOIN permisos p ON p.id = rp.permiso_id
            JOIN modulos m ON m.id = p.modulo_id AND m.eliminado = 0
            WHERE u.id = ?";

    $consulta = $conexionBD->prepare($sql);
    $consulta->execute([$funcionario]);
    $permisos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // =========================
    // AGRUPAR
    // =========================
    $menu = [];

    foreach ($permisos as $row) {
        $subsistema = $row['subsistema'];

        if (!isset($menu[$subsistema])) {
            $menu[$subsistema] = [
                "nombre" => $subsistema,
                "modulos" => []
            ];
        }

        $menu[$subsistema]["modulos"][] = [
            "id" => $row['modulo_id'],
            "nombre" => $row['modulo'],
            "ruta" => $row['ruta_base']
        ];
    }

    echo json_encode(array_values($menu));

} catch (Exception $e) {
    echo json_encode([
        "error" => $e->getMessage()
    ]);
}