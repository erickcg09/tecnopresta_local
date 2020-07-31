const CACHE_NAME = 'static-cacheTP';
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
        // Not a page navigation, bail.
        return;
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