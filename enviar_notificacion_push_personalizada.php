<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_inventario.html";
    </script>';
    exit();
}
require 'vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

require_once("conexion.php"); // Conexión a la base de datos

// Configuración VAPID
$auth = [
    'VAPID' => [
        'subject' => 'mailto:sandro.yee@gmail.com',
        'publicKey' => 'BJG5s-2sldzC64tugMXk6xorDxrIcmCMT2J7Wyoq3dFtFshEJ3UvdtUYJ8vGRHl9pepQBZDiaVnH5KDWhlpHvuY',
        'privateKey' => 'HlS0UII8msn5-wokq7_ISOj-CZYTApv6FhF1L6KPVyU',
    ],
];

// Obtener datos del formulario
$titulo = $_POST['titulo'] ?? 'TecnoPresta';
$mensaje = $_POST['mensaje'] ?? 'Hola Mundo!';

// Conexión a la base de datos y obtener las suscripciones
$link = $mysqli;
if (!$link->set_charset("utf8")) {
    die("Error al cargar el conjunto de caracteres utf8: " . $link->error);
}

$result = $link->query("SELECT * FROM subscriptions");
$webPush = new WebPush($auth);

$errores = []; // Array para almacenar errores

// Recorre cada suscripción y envía una notificación
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
            'title' => $titulo,
            'body' => $mensaje
        ])
    );
}

// Enviar todas las notificaciones en cola y manejar errores
foreach ($webPush->flush() as $report) {
    $endpoint = $report->getEndpoint();
    if (!$report->isSuccess()) {
        // Si es un error 410 (suscripción expirada), elimina la suscripción
        if ($report->getResponse()->getStatusCode() === 410) {
            $stmt = $link->prepare("DELETE FROM subscriptions WHERE endpoint = ?");
            $stmt->bind_param("s", $endpoint);
            $stmt->execute();
            $stmt->close();
        } else {
            $errores[] = "Error al enviar mensaje a {$endpoint}: {$report->getReason()}";
        }
    }
}

// Cerrar la conexión
$link->close();

// Redireccionar con un mensaje de éxito o error
if (empty($errores)) {
    header("Location: comunicacion_con_suscriptores.php?status=success");
} else {
    // Opcional: puedes pasar el mensaje de error a través de la URL si es breve
    header("Location: comunicacion_con_suscriptores.php?status=error&msg=" . urlencode(implode(", ", $errores)));
}
exit();
?>

