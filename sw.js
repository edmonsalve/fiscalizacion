const CACHE_NAME = 'fiscalizacion-shell-v1';
const APP_SHELL = [
  '/fiscalizacion/',
  '/fiscalizacion/index.php',
  '/fiscalizacion/manifest.json',
  '/fiscalizacion/assets/icons/icon-192.png',
  '/fiscalizacion/assets/icons/icon-512.png',
  '/fiscalizacion/api/logoPET.png',
  '/fiscalizacion/api/dcode.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(APP_SHELL))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key !== CACHE_NAME)
          .map((key) => caches.delete(key))
      )
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const requestUrl = new URL(event.request.url);

  if (event.request.method !== 'GET') {
    return;
  }

  if (requestUrl.pathname.startsWith('/fiscalizacion/api/')) {
    event.respondWith(
      fetch(event.request).catch(() => {
        return new Response(
          JSON.stringify({
            message: 'Sin conexión. No fue posible consultar el servicio.'
          }),
          {
            status: 503,
            headers: {
              'Content-Type': 'application/json; charset=utf-8'
            }
          }
        );
      })
    );
    return;
  }

  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      return (
        cachedResponse ||
        fetch(event.request).then((networkResponse) => {
          const responseClone = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, responseClone));
          return networkResponse;
        })
      );
    })
  );
});
