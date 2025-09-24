const CACHE_VERSION = "v2";
const CACHE_NAME = `revi3-cache-${CACHE_VERSION}`;
const APP_SHELL = [
        "/",
        "/manifest.json",
        "/css/app.css",
        "/themes/default/css/custom.css",
        "/js/app.js",
        "/images/theme/theme-favicon-logo.png",
        "/images/theme/theme-logo.png",
];
const NETWORK_ONLY_PATTERNS = [/\/api\//];

self.addEventListener("install", (event) => {
        event.waitUntil(
                caches.open(CACHE_NAME).then((cache) => cache.addAll(APP_SHELL))
        );
        self.skipWaiting();
});

self.addEventListener("activate", (event) => {
        event.waitUntil(
                caches.keys().then((cacheNames) =>
                        Promise.all(
                                cacheNames
                                        .filter((cacheName) => cacheName !== CACHE_NAME)
                                        .map((cacheName) => caches.delete(cacheName))
                        )
                )
        );
        self.clients.claim();
});

self.addEventListener("fetch", (event) => {
        if (event.request.method !== "GET") {
                return;
        }

        const requestUrl = new URL(event.request.url);

        if (NETWORK_ONLY_PATTERNS.some((pattern) => pattern.test(requestUrl.pathname))) {
                return;
        }

        if (requestUrl.pathname === "/js/app.js" || requestUrl.pathname === "/css/app.css") {
                event.respondWith(
                        fetch(event.request)
                                .then((response) => {
                                        const responseClone = response.clone();
                                        caches
                                                .open(CACHE_NAME)
                                                .then((cache) => cache.put(event.request, responseClone));
                                        return response;
                                })
                                .catch(() => caches.match(event.request))
                );
                return;
        }

        event.respondWith(
                caches.match(event.request).then((cachedResponse) => {
                        if (cachedResponse) {
                                return cachedResponse;
                        }

                        return fetch(event.request)
                                .then((networkResponse) => {
                                        if (
                                                !networkResponse ||
                                                networkResponse.status !== 200 ||
                                                networkResponse.type !== "basic"
                                        ) {
                                                return networkResponse;
                                        }

                                        const responseClone = networkResponse.clone();
                                        caches
                                                .open(CACHE_NAME)
                                                .then((cache) => cache.put(event.request, responseClone));
                                        return networkResponse;
                                })
                                .catch(() => caches.match(event.request));
                })
        );
});
