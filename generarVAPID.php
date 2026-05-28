<?php
require 'vendor/autoload.php';

use Minishlink\WebPush\VAPID;

try {
    $vapidKeys = VAPID::createVapidKeys();
    echo "Clave Pública: " . $vapidKeys['publicKey'] . "<br>";
    echo "Clave Privada: " . $vapidKeys['privateKey'] . "<br>";
} catch (Exception $e) {
    echo "Error al generar claves VAPID: " . $e->getMessage();
}
?>
