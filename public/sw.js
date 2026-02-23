self.addEventListener('install', (e) => {
    console.log('[Service Worker] Kuruldu');
});

self.addEventListener('fetch', (e) => {
    // Şimdilik sadece ağdan çekiyoruz, ileri seviyede offline çalışması için cache eklenebilir.
    e.respondWith(fetch(e.request));
});
