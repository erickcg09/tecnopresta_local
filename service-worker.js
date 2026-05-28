const CACHE_NAME = 'static-cacheTPv2';
const FILES_TO_CACHE = ['./', './sinconexion.html'];

self.addEventListener('install', function(evt) {
    console.log('TecnoPresta instalado!');
    evt.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[ServiceWorker] Pre-caching la página offline: sinconexion.html');
            return cache.addAll(FILES_TO_CACHE);
        })
    );
});

self.addEventListener("activate", event => {
    console.log('TecnoPresta activado!');
    event.waitUntil(
        caches.keys().then((keyList) => {
            return Promise.all(keyList.map((key) => {
                if (key !== CACHE_NAME) {
                    console.log('[ServiceWorker] Removing old cache', key);
                    return caches.delete(key);
                }
            }));
        })
    );
});

self.addEventListener('fetch', function(event) {
    console.log('Fetch!', event.request);
    if (event.request.mode !== 'navigate') {
        return; // No es una navegación de página
    }
    event.respondWith(
        fetch(event.request)
            .catch(() => {
                return caches.open(CACHE_NAME)
                    .then((cache) => {
                        return cache.match('./sinconexion.html');
                    });
            })
    );
});

// Manejar notificaciones push
self.addEventListener('push', function(event) {
    console.log('Push recibido:', event);

    if (event.data) {
        const data = event.data.json();
        console.log("Datos de la notificación:", data);

        // Mostrar la notificación
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: 'icon.png', // el archivo `icon.png` esta en el mismo directorio.
            badge: 'badge.png' // Opcional: imagen pequeña que aparece en la esquina de la notificación no la he puesto
        });
    } else {
        console.log('Push sin datos');

        // Mostrar una notificación por defecto
        self.registration.showNotification("Notificación", {
            body: "Tienes una nueva notificación.",
            icon: 'icon.png'
        });
    }
});

// Desuscripción de una suscripción existente
self.addEventListener('pushsubscriptionchange', function(event) {
    console.log('Evento de cambio de suscripción recibido');

    event.waitUntil(
        self.registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlB64ToUint8Array('BJG5s-2sldzC64tugMXk6xorDxrIcmCMT2J7Wyoq3dFtFshEJ3UvdtUYJ8vGRHl9pepQBZDiaVnH5KDWhlpHvuY')
        }).then(function(newSubscription) {
            console.log('Nueva suscripción:', newSubscription);

            // Enviar la nueva suscripción al servidor
            fetch('/subscribe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(newSubscription)
            });
        }).catch(function(error) {
            console.error('Error al re-suscribir:', error);
        })
    );
});

// Función de utilidad para convertir la clave VAPID de URL Base64 a Uint8Array
function urlB64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}


