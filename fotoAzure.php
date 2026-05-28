<?php

/** ===== INICIAR SESIÓN =====*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/** === RESPUESTA JSON ==== */
header('Content-Type: application/json');

/** =====  VALIDAR MÉTODO POST =======*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido"
    ]);

    exit;
}

/** ==== LEER DATOS JSON ==== */
$data = json_decode(
    file_get_contents("php://input"),
    true
);

/** ==== VALIDAR FOTO ====  */
if ( !isset($data['fotoPerfil']) || empty($data['fotoPerfil'])) {

    echo json_encode([
        "ok" => false,
        "mensaje" => "No se recibió foto"
    ]);

    exit;
}

/** ==== VALIDAR SESIÓN FUNCIONARIO ==== */
if (!isset($_SESSION['funcionario'])) {

    echo json_encode([
        "ok" => false,
        "mensaje" => "Sesión funcionario no existe"
    ]);

    exit;
}

/** ===== GUARDAR FOTO EN SESSION ==== */
$_SESSION['funcionario']['fotoPerfil'] =
    $data['fotoPerfil'];

/** ==== RESPUESTA EXITOSA ==== */
echo json_encode([
    "ok" => true,
    "mensaje" => "Foto guardada correctamente"
]);