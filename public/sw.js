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
  // Add more pages for better offline support
  '/sales-transaction',
  '/sales-transaction/create',
  '/sales-transaction/bulk-create',
  '/reports'
];

// Install event - cache resources
self.addEventListener('install', event => {
  console.log('SW Installing...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache:', CACHE_NAME);
        console.log('Caching URLs:', urlsToCache);
        return cache.addAll(urlsToCache);
      })
      .then(() => {
        console.log('✅ All resources cached successfully');
      })
      .catch(error => {
        console.error('❌ Failed to cache resources:', error);
      })
  );
  // Activate updated SW immediately
  self.skipWaiting();
});

// Listen for skipWaiting message
self.addEventListener('message', event => {
  if (event.data && event.data.action === 'skipWaiting') {
    self.skipWaiting();
  }
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
  // Offline fallback for navigation requests
  if (event.request.mode === 'navigate') {
    event.respondWith(
      (async () => {
        try {
          const cachedPage = await caches.match(event.request);
          if (cachedPage) return cachedPage;
          const networkResponse = await fetch(event.request);
          return networkResponse;
        } catch (error) {
          const offlinePage = await caches.match('/offline.html');
          return offlinePage || new Response('You are offline', { status: 503, headers: { 'Content-Type': 'text/plain' } });
        }
      })()
    );
    return;
  }

  // Handle download requests specially
  if (event.request.url.includes('/test-export-')) {
    event.respondWith(
      fetch(event.request).then(response => {
        // For download requests, ensure proper headers and bypass sandbox
        if (response.ok) {
          const headers = new Headers(response.headers);
          headers.set('Content-Disposition', response.headers.get('Content-Disposition') || 'attachment');
          headers.set('X-Frame-Options', 'SAMEORIGIN');
          headers.set('X-Content-Type-Options', 'nosniff');
          headers.set('Content-Security-Policy', "default-src 'self' 'unsafe-inline' 'unsafe-eval' data: blob:; sandbox allow-downloads allow-same-origin allow-scripts");
          
          return new Response(response.body, {
            status: response.status,
            statusText: response.statusText,
            headers: headers
          });
        }
        return response;
      })
    );
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Return cached version or fetch from network
        if (response) {
          console.log('Serving from cache:', event.request.url);
          return response;
        }
        
        return fetch(event.request).then(response => {
          // Check if we received a valid response
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response;
          }

          // Clone the response
          const responseToCache = response.clone();

          caches.open(CACHE_NAME)
            .then(cache => {
              cache.put(event.request, responseToCache);
              console.log('Cached new resource:', event.request.url);
            });

          return response;
        }).catch(async (error) => {
          console.log('Fetch failed for:', event.request.url, error);
          
          // For CSS/JS files, try to serve from cache even if not exact match
          if (event.request.url.includes('.css') || event.request.url.includes('.js') || event.request.url.includes('.woff2')) {
            const cache = await caches.open(CACHE_NAME);
            const keys = await cache.keys();
            
            // Try to find similar file in cache
            for (const key of keys) {
              if (key.url.includes(event.request.url.split('/').pop())) {
                console.log('Found similar cached file:', key.url);
                return cache.match(key);
              }
            }
          }
          
          // As a safety net for non-navigation requests, try to serve offline page for HTML requests
          if (event.request.headers.get('accept')?.includes('text/html')) {
            const offlinePage = await caches.match('/offline.html');
            if (offlinePage) return offlinePage;
          }
          return new Response('Offline', { status: 503, headers: { 'Content-Type': 'text/plain' } });
        });
      })
      .catch(async (error) => {
        console.log('Cache match failed for:', event.request.url, error);
        
        // Try to serve offline page for HTML requests
        if (event.request.headers.get('accept')?.includes('text/html')) {
          const offlinePage = await caches.match('/offline.html');
          if (offlinePage) return offlinePage;
        }
        return new Response('Offline', { status: 503, headers: { 'Content-Type': 'text/plain' } });
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  // Take control of open clients immediately
  self.clients.claim();
});

// Push notification event
self.addEventListener('push', event => {
  const options = {
    body: event.data ? event.data.text() : 'New notification from Admin PWA',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-72x72.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'explore',
        title: 'View Details',
        icon: '/icons/icon-72x72.png'
      },
      {
        action: 'close',
        title: 'Close',
        icon: '/icons/icon-72x72.png'
      }
    ]
  };

  event.waitUntil(
    self.registration.showNotification('Admin PWA', options)
  );
});

// Notification click event
self.addEventListener('notificationclick', event => {
  event.notification.close();

  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/dashboard')
    );
  }
});
