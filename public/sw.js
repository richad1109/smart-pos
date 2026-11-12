const CACHE_NAME = 'smartpos-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
    // Pass-through fetch
    event.respondWith(fetch(event.request).catch(() => caches.match(event.request)));
});
