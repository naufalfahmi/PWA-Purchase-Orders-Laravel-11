const CACHE_NAME = 'munah-pwa-v10';
const urlsToCache = [
  // Static assets only - no pages that require auth
  '/build/assets/app-CP0OoLXE.css',
  '/build/assets/app-Dd4mxizw.js',
  '/css/mobile-fallback.css',
  '/css/inter-fonts.css',
  '/js/inline-fallback.js',
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
  '/offline.html'
];

// Install event
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        // Cache resources one by one to handle individual failures
        const cachePromises = urlsToCache.map(url => {
          return cache.add(url).catch(error => {
            console.log(`Failed to cache ${url}:`, error);
            // Continue with other resources even if one fails
            return null;
          });
        });
        
        return Promise.allSettled(cachePromises).then(results => {
          const successful = results.filter(result => result.status === 'fulfilled').length;
          const failed = results.filter(result => result.status === 'rejected').length;
          console.log(`Cached ${successful} resources, ${failed} failed`);
        });
      })
      .then(() => {
        return self.skipWaiting();
      })
      .catch(error => {
        console.log('Cache installation failed:', error);
        // Still skip waiting even if caching failed
        return self.skipWaiting();
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
        // Try cache first for static assets
        const cachedResponse = await caches.match(event.request);
        if (cachedResponse) {
          return cachedResponse;
        }

        // Try network
        const networkResponse = await fetch(event.request);
        
        // Only cache static assets (CSS, JS, fonts, images)
        if (networkResponse && networkResponse.status === 200) {
          const url = event.request.url;
          if (url.includes('.css') || url.includes('.js') || url.includes('.woff2') || 
              url.includes('.png') || url.includes('.jpg') || url.includes('.jpeg') ||
              url.includes('.gif') || url.includes('.svg') || url.includes('.ico')) {
            // Clone response for caching
            const responseToCache = networkResponse.clone();
            
            // Cache the response with error handling
            try {
              const cache = await caches.open(CACHE_NAME);
              await cache.put(event.request, responseToCache);
        // console.log(`Cached new resource: ${url}`);
            } catch (cacheError) {
              console.log(`Failed to cache ${url}:`, cacheError);
              // Continue even if caching fails
            }
          }
        }
        
        return networkResponse;
        
      } catch (error) {
        // For navigation requests, return offline page
        if (event.request.mode === 'navigate') {
          const offlinePage = await caches.match('/offline.html');
          if (offlinePage) {
            return offlinePage;
          }
          
          // Return a custom offline page for authenticated routes
          return new Response(`
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Offline - Munah - Purchase Orders</title>
                <style>
                    body { 
                        font-family: system-ui, sans-serif; 
                        margin: 0; 
                        padding: 20px; 
                        background: #f8fafc;
                        text-align: center;
                    }
                    .container {
                        max-width: 600px;
                        margin: 50px auto;
                        background: white;
                        padding: 40px;
                        border-radius: 12px;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    }
                    .offline-icon {
                        font-size: 64px;
                        margin-bottom: 20px;
                    }
                    h1 { color: #1f2937; margin-bottom: 10px; }
                    p { color: #6b7280; margin-bottom: 30px; }
                    .btn {
                        background: #2563eb;
                        color: white;
                        padding: 12px 24px;
                        border: none;
                        border-radius: 8px;
                        cursor: pointer;
                        text-decoration: none;
                        display: inline-block;
                    }
                    .btn:hover { background: #1d4ed8; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="offline-icon">📱</div>
                    <h1>You are offline</h1>
                    <p>Please check your internet connection and try again.</p>
                    <a href="/" class="btn">Go to Login</a>
                </div>
            </body>
            </html>
          `, {
            status: 200,
            headers: { 'Content-Type': 'text/html' }
          });
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
        
        // Return offline response for other requests
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
