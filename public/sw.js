const CACHE_NAME = 'admin-pwa-v3';
const urlsToCache = [
  '/',
  '/login',
  '/dashboard',
  '/purchase-order',
  '/data-barang',
  '/profile',
  '/offline.html',
  '/build/assets/app-D5mplNn1.css',
  '/build/assets/app-BIUlJ5IE.js',
  '/css/mobile-fallback.css',
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png'
];

// Install event - cache resources
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
  // Activate updated SW immediately
  self.skipWaiting();
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
            });

          return response;
        });
      })
      .catch(async () => {
        // As a safety net for non-navigation requests, try to serve offline page for HTML requests
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
