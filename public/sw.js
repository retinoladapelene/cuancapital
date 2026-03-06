const CACHE_NAME = 'cuan-cashbook-v3'; // Bumped version to bypass aggressive caching

// Assets utamanya di-cache saat SW install
const CORE_ASSETS = [
    '/assets/css/cashbook-core.css',
    '/assets/js/cashbook/core.js',
    '/assets/js/cashbook/ui.js',
    '/assets/js/cashbook/domUtils.js',
    '/assets/js/cashbook/virtualList.js',
    'https://cdn.jsdelivr.net/npm/chart.js'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(CORE_ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.filter(name => name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        })
    );
});

self.addEventListener('fetch', event => {
    // Hanya GET request
    if (event.request.method !== 'GET') return;

    // Jika user merefresh paksa (bypass cache dari browser)
    if (event.request.cache === 'only-if-cached' && event.request.mode !== 'same-origin') return;

    const url = new URL(event.request.url);

    // Stale-While-Revalidate untuk assets
    if (
        url.pathname.startsWith('/assets/css/') ||
        url.pathname.startsWith('/assets/js/') ||
        url.host.includes('cdnjs.cloudflare.com') ||
        url.host.includes('fonts.googleapis.com') ||
        url.host.includes('fonts.gstatic.com')
    ) {
        event.respondWith(
            caches.open(CACHE_NAME).then(cache => {
                return cache.match(event.request).then(cachedResponse => {
                    const fetchPromise = fetch(event.request).then(networkResponse => {
                        if (networkResponse && networkResponse.status === 200) {
                            cache.put(event.request, networkResponse.clone());
                        }
                        return networkResponse;
                    }).catch(() => {
                        // Offline fallback
                    });

                    // Return cached response immediately if available, while network fetches in background
                    return cachedResponse || fetchPromise;
                });
            })
        );
    }
});
