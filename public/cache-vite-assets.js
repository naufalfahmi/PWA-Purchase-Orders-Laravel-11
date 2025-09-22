// Script khusus untuk cache Vite assets
console.log('🎨 Caching Vite assets...');

async function cacheViteAssets() {
    try {
        const cache = await caches.open('admin-pwa-v6');
        
        const viteAssets = [
            '/build/assets/app-D5mplNn1.css',
            '/build/assets/app-BIUlJ5IE.js'
        ];
        
        let cached = 0;
        let failed = 0;
        
        for (const url of viteAssets) {
            try {
                console.log(`📥 Caching Vite asset: ${url}`);
                const response = await fetch(url);
                
                if (response.ok) {
                    await cache.put(url, response);
                    cached++;
                    console.log(`✅ Cached: ${url}`);
                } else {
                    failed++;
                    console.log(`❌ Failed: ${url} (${response.status})`);
                }
            } catch (error) {
                failed++;
                console.log(`❌ Error caching ${url}:`, error.message);
            }
        }
        
        console.log(`🎉 Vite assets cached! ${cached} cached, ${failed} failed`);
        
        // Verify cache
        const keys = await cache.keys();
        console.log(`📊 Total cached files: ${keys.length}`);
        
        // Test cache retrieval
        for (const url of viteAssets) {
            const cachedResponse = await cache.match(url);
            if (cachedResponse) {
                console.log(`✅ Verified: ${url} available in cache`);
            } else {
                console.log(`❌ Missing: ${url} not in cache`);
            }
        }
        
    } catch (error) {
        console.error('❌ Error caching Vite assets:', error);
    }
}

// Run the cache process
cacheViteAssets();
