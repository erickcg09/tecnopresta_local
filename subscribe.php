<?php
$subscription = json_decode(file_get_contents('php://input'), true);

if ($subscription) {
    echo "Datos recibidos: ";
    var_dump($subscription);  // Verifica los datos recibidos
} else {
    echo "No se recibieron datos correctamente.";
    exit;
}

$endpoint = $subscription['endpoint'];
$p256dh = $subscription['keys']['p256dh'];
$auth = $subscription['keys']['auth'];

require_once("conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
    exit;
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
} else {
    // Verificar si ya existe una suscripción con el mismo endpoint
    $stmt = $link->prepare("SELECT id FROM subscriptions WHERE endpoint = ?");
    $stmt->bind_param('s', $endpoint);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Si ya existe, actualiza los valores de p256dh y auth
        $stmt->close();
        $stmt = $link->prepare("UPDATE subscriptions SET p256dh = ?, auth = ? WHERE endpoint = ?");
        $stmt->bind_param('sss', $p256dh, $auth, $endpoint);
    } else {
        // Si no existe, inserta una nueva suscripción
        $stmt->close();
        $stmt = $link->prepare("INSERT INTO subscriptions (endpoint, p256dh, auth) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $endpoint, $p256dh, $auth);
    }

    if ($stmt->execute()) {
        echo "Suscripción guardada correctamente!";
    } else {
        echo "Error al guardar la suscripción.";
    }
    $stmt->close();
}

$link->close();
?>


