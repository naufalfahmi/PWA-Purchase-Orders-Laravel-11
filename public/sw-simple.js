const CACHE_NAME = 'admin-pwa-v7';
const urlsToCache = [
  '/',
  '/login',
  '/dashboard',
  '/purchase-order',
  '/data-barang',
  '/profile',
  '/offline.html',
  '/build/assets/app-CR2TckGB.css',
  '/build/assets/app-D2c31c7y.js',
  '/css/mobile-fallback.css',
  '/css/inter-fonts.css',
  '/js/qr-generator.js',
  '/js/smart-cache.js',
  '/libs/jquery-3.6.0.min.js',
  '/libs/select2.min.css',
  '/libs/select2-bootstrap-5-theme.min.css',
  '/libs/select2.min.js',
  '/libs/chart.min.js',
  '/libs/qrcode.min.js',
  '/fonts/inter-300.woff2',
  '/fonts/inter-400.woff2',
  '/fonts/inter-500.woff2',
  '/fonts/inter-600.woff2',
  '/fonts/inter-700.woff2',
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  '/sales-transaction',
  '/sales-transaction/create',
  '/sales-transaction/bulk-create',
  '/reports'
];

// Install event
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
      .then(() => {
        return self.skipWaiting();
      })
      .catch(() => {
        // Silent error handling
      })
  );
});

// Activate event
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => {
      return self.clients.claim();
    })
  );
});

// Fetch event
self.addEventListener('fetch', event => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  event.respondWith(
    (async () => {
      try {
        // Try cache first
        const cachedResponse = await caches.match(event.request);
        if (cachedResponse) {
          return cachedResponse;
        }

        // Try network
        const networkResponse = await fetch(event.request);
        
        // Check if response is valid
        if (networkResponse && networkResponse.status === 200) {
          // Clone response for caching
          const responseToCache = networkResponse.clone();
          
          // Cache the response
          const cache = await caches.open(CACHE_NAME);
          await cache.put(event.request, responseToCache);
        }
        
        return networkResponse;
        
      } catch (error) {
        // For navigation requests, return offline page
        if (event.request.mode === 'navigate') {
          const offlinePage = await caches.match('/offline.html');
          if (offlinePage) {
            return offlinePage;
          }
        }
        
        // For CSS/JS files, try to find similar cached file
        if (event.request.url.includes('.css') || event.request.url.includes('.js')) {
          const cache = await caches.open(CACHE_NAME);
          const keys = await cache.keys();
          
          // Try to find similar file in cache
          for (const key of keys) {
            if (key.url.includes(event.request.url.split('/').pop())) {
              return await cache.match(key);
            }
          }
        }
        
        // Return offline response
        return new Response('Offline', { 
          status: 503, 
          statusText: 'Service Unavailable',
          headers: { 'Content-Type': 'text/plain' }
        });
      }
    })()
  );
});

// Listen for skipWaiting message
self.addEventListener('message', event => {
  if (event.data && event.data.action === 'skipWaiting') {
    self.skipWaiting();
  }
});
