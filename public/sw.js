const CACHE_NAME = 'planner-v1';
const STATIC_ASSETS = [
    '/planner',
    '/css/planner.css',
    '/js/calendar.js',
    '/js/tasks.js',
    '/js/notes.js',
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png'
];

// Kurulum: statik dosyaları cache'e al
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

// Aktivasyon: eski cache'leri temizle
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// Fetch stratejisi
self.addEventListener('fetch', event => {
    const url = event.request.url;

    // API istekleri — sadece network
    if (url.includes('/tasks') || url.includes('/notes') ||
        url.includes('/login') || url.includes('/logout')) {
        event.respondWith(
            fetch(event.request).catch(() =>
                new Response(JSON.stringify({ error: 'Çevrimdışısın' }), {
                    headers: { 'Content-Type': 'application/json' }
                })
            )
        );
        return;
    }

    // Statik dosyalar — network önce, sonra cache
    event.respondWith(
        fetch(event.request)
            .then(response => {
                const clone = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                return response;
            })
            .catch(() => caches.match(event.request))
    );
});
