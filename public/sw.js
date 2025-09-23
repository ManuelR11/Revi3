const CACHE_NAME = "revi3-cache-v1";
const urlsToCache = [
	"/",
	"/manifest.json",
	"/css/app.css",
	"/themes/default/css/custom.css",
	"/js/app.js",
	"/images/theme/theme-favicon-logo.png",
	"/images/theme/theme-logo.png",
];

self.addEventListener("install", (event) => {
	event.waitUntil(
		caches.open(CACHE_NAME).then((cache) => cache.addAll(urlsToCache))
	);
	self.skipWaiting();
});

self.addEventListener("fetch", (event) => {
	event.respondWith(
		caches
			.match(event.request)
			.then((response) => response || fetch(event.request))
	);
});
