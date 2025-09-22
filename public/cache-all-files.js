// Script untuk memaksa cache semua file
console.log('🚀 Starting manual cache process...');

const filesToCache = [
  '/css/inter-fonts.css',
  '/css/mobile-fallback.css',
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
  '/fonts/inter-700.woff2'
];

async function cacheAllFiles() {
  try {
    // Open cache
    const cache = await caches.open('admin-pwa-v5');
    console.log('✅ Opened cache: admin-pwa-v5');
    
    // Cache each file
    for (const url of filesToCache) {
      try {
        console.log(`📥 Caching: ${url}`);
        const response = await fetch(url);
        
        if (response.ok) {
          await cache.put(url, response);
          console.log(`✅ Cached: ${url}`);
        } else {
          console.log(`❌ Failed to fetch: ${url} (${response.status})`);
        }
      } catch (error) {
        console.log(`❌ Error caching ${url}:`, error.message);
      }
    }
    
    // Verify cache
    const keys = await cache.keys();
    console.log(`🎉 Cache complete! ${keys.length} files cached`);
    
    // List cached files
    console.log('📋 Cached files:');
    keys.forEach(key => console.log(`  • ${key.url}`));
    
    // Test cache retrieval
    console.log('🧪 Testing cache retrieval...');
    for (const url of filesToCache.slice(0, 3)) { // Test first 3 files
      const cachedResponse = await cache.match(url);
      if (cachedResponse) {
        console.log(`✅ Retrieved from cache: ${url}`);
      } else {
        console.log(`❌ Not found in cache: ${url}`);
      }
    }
    
  } catch (error) {
    console.error('❌ Cache process failed:', error);
  }
}

// Run the cache process
cacheAllFiles();
