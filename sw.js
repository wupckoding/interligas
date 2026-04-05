const CACHE_NAME = 'interliga-v1';
const ASSETS = [
    '/',
    '/interliga/index.php',
    '/interliga/admin.php',
    '/interliga/manifest.json',
    'https://cdn.tailwindcss.com',
    'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap'
];

self.addEventListener('install', function(e) {
    e.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            return cache.addAll(ASSETS).catch(function() {});
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', function(e) {
    e.waitUntil(
        caches.keys().then(function(names) {
            return Promise.all(
                names.filter(function(n) { return n !== CACHE_NAME; })
                     .map(function(n) { return caches.delete(n); })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', function(e) {
    // No cachear llamadas API
    if (e.request.url.includes('api.php')) return;
    
    e.respondWith(
        caches.match(e.request).then(function(cached) {
            var fetched = fetch(e.request).then(function(response) {
                if (response && response.status === 200) {
                    var clone = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) {
                        cache.put(e.request, clone);
                    });
                }
                return response;
            }).catch(function() {
                return cached;
            });
            return cached || fetched;
        })
    );
});
