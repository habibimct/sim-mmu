const CACHE_NAME = 'sim-mmu-v6';

const STATIC_ASSETS = [
    '/manifest.json',
    '/login',
    '/guru/dashboard',
    '/guru/attendance',
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                return self.skipWaiting();
            })
            .catch(error => {
                console.warn(
                    '[SIM-MMU PWA] Instalasi Service Worker ditunda karena asset belum dapat diambil:',
                    error
                );

                // Jangan throw error lagi.
                // Service Worker lama tetap dapat digunakan.
            })
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames
                        .filter(name => name !== CACHE_NAME)
                        .map(name => caches.delete(name))
                );
            })
            .then(() => {
                return self.clients.claim();
            })
    );
});

self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') {
        return;
    }

    const request = event.request;

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then(response => {
                    if (response && response.status === 200) {
                        const responseClone = response.clone();

                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, responseClone);
                        });
                    }

                    return response;
                })
                .catch(() => {
                    return caches.match(request).then(cachedResponse => {
                        if (cachedResponse) {
                            return cachedResponse;
                        }

                        return caches.match('/guru/dashboard').then(dashboard => {
                            if (dashboard) {
                                return dashboard;
                            }

                            return new Response(
                                'SIM-MMU sedang offline. Halaman belum tersedia di perangkat.',
                                {
                                    status: 503,
                                    statusText: 'Service Unavailable',
                                    headers: {
                                        'Content-Type': 'text/plain; charset=utf-8',
                                    },
                                }
                            );
                        });
                    });
                })
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Asset statis
    |--------------------------------------------------------------------------
    |
    | CSS, JS, gambar, font, dan asset Vite:
    | Cache First
    |
    */

    if (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font'
    ) {
        event.respondWith(
            caches.match(request).then(cachedResponse => {
                if (cachedResponse) {
                    return cachedResponse;
                }

                return fetch(request).then(response => {
                    if (!response || response.status !== 200) {
                        return response;
                    }

                    const responseClone = response.clone();

                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(request, responseClone);
                    });

                    return response;
                });
            })
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Request lainnya
    |--------------------------------------------------------------------------
    |
    | Untuk sementara:
    | Network First dengan fallback cache.
    |
    */

    event.respondWith(
        fetch(request)
            .then(response => {
                if (
                    response &&
                    response.status === 200 &&
                    response.type === 'basic'
                ) {
                    const responseClone = response.clone();

                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(request, responseClone);
                    });
                }

                return response;
            })
            .catch(() => {
                return caches.match(request).then(cachedResponse => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    return new Response(
                        'SIM-MMU sedang offline dan halaman ini belum tersedia di perangkat.',
                        {
                            status: 503,
                            statusText: 'Service Unavailable',
                            headers: {
                                'Content-Type': 'text/plain; charset=utf-8',
                            },
                        }
                    );
                });
            })
    );
});
