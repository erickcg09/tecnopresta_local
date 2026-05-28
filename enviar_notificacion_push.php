<?php
require 'vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

require_once("conexion.php"); // base de datos

// Configuraci車n VAPID con las claves generadas
$auth = [
    'VAPID' => [
        'subject' => 'mailto:sandro.yee@gmail.com',
        'publicKey' => 'BJG5s-2sldzC64tugMXk6xorDxrIcmCMT2J7Wyoq3dFtFshEJ3UvdtUYJ8vGRHl9pepQBZDiaVnH5KDWhlpHvuY',
        'privateKey' => 'HlS0UII8msn5-wokq7_ISOj-CZYTApv6FhF1L6KPVyU',
    ],
];

// Conexi車n a la base de datos y obtener las suscripciones
$link = $mysqli;
if (!$link->set_charset("utf8")) {
    die("Error al cargar el conjunto de caracteres utf8: " . $link->error);
}

$result = $link->query("SELECT * FROM subscriptions");

$webPush = new WebPush($auth);

// Recorre cada suscripci車n y env赤a una notificaci車n
while ($row = $result->fetch_assoc()) {
    $subscription = Subscription::create([
        'endpoint' => $row['endpoint'],
        'keys' => [
            'p256dh' => $row['p256dh'],
            'auth' => $row['auth'],
        ],
    ]);

    $webPush->queueNotification(
        $subscription,
        json_encode([
            'title' => 'TecnoPresta',
            'body' => 'Hola Mundo!'
        ])
    );
}

// Enviar todas las notificaciones en cola
foreach ($webPush->flush() as $report) {
    $endpoint = $report->getEndpoint();
    if ($report->isSuccess()) {
        echo "Mensaje enviado con 谷xito a {$endpoint}.<br>";
    } else {
        echo "Error al enviar mensaje a {$endpoint}: {$report->getReason()}<br>";
    }
}

$link->close();
?>


