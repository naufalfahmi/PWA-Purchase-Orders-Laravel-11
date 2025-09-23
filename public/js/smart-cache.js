/**
 * Smart Cache Manager - Otomatis update cache saat ada asset baru
 */
class SmartCacheManager {
    constructor() {
        this.cacheName = 'munah-pwa-v10';
        this.isRunning = false;
        this.lastCheck = localStorage.getItem('lastCacheCheck') || 0;
        this.checkInterval = 5 * 60 * 1000; // Check every 5 minutes
    }

    async init() {
        // Smart Cache Manager initialized
        
        // Check on page load
        await this.checkAndUpdate();
        
        // Check periodically
        setInterval(() => {
            this.checkAndUpdate();
        }, this.checkInterval);
        
        // Check when page becomes visible
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.checkAndUpdate();
            }
        });
    }

    async checkAndUpdate() {
        if (this.isRunning) return;
        
        try {
            const needsUpdate = await this.checkIfUpdateNeeded();
            
            if (needsUpdate) {
                // Update needed, starting smart cache
                await this.smartUpdate();
            } else {
        // console.log('✅ Cache is up to date');
            }
            
            // Update last check time
            this.lastCheck = Date.now();
            localStorage.setItem('lastCacheCheck', this.lastCheck);
            
        } catch (error) {
            console.error('❌ Smart cache check failed:', error);
        }
    }

    async checkIfUpdateNeeded() {
        try {
            // Check if Cache API is available
            if (!('caches' in window)) {
        // console.log('⚠️ Cache API not available, skipping cache check');
                return false;
            }
            
            // Check if we have the latest assets
            const latestAssets = [
                '/build/assets/app-CP0OoLXE.css',
                '/build/assets/app-CeXcbV7U.js'
            ];
            
            // Check if assets exist
            for (const asset of latestAssets) {
                try {
                    const response = await fetch(asset, { method: 'HEAD' });
                    if (!response.ok) {
        // console.log(`❌ Asset not found: ${asset}`);
                        return true;
                    }
                } catch (error) {
                    // console.log(`❌ Error checking asset: ${asset}`);
                    return true;
                }
            }
            
            // Check cache version
            const cache = await caches.open(this.cacheName);
            const keys = await cache.keys();
            
            // If cache is empty, need update
            if (keys.length < 5) {
        // console.log('📦 Cache is empty, need update');
                return true;
            }
            
            // Check if we have the latest assets in cache
            const cachedUrls = keys.map(key => key.url);
            const hasLatestAssets = latestAssets.every(asset => 
                cachedUrls.some(url => url.includes(asset))
            );
            
            if (!hasLatestAssets) {
        // console.log('🔄 Cache missing latest assets, need update');
                return true;
            }
            
            // Check if cache is too old (older than 1 hour)
            const cacheAge = Date.now() - this.lastCheck;
            if (cacheAge > 60 * 60 * 1000) {
        // console.log('⏰ Cache is old, need update');
                return true;
            }
            
            return false;
            
        } catch (error) {
            console.error('❌ Error checking update status:', error);
            return true;
        }
    }

    async smartUpdate() {
        this.isRunning = true;
        
        try {
        // console.log('🔄 Starting smart update...');
            
            // Show subtle notification
            this.showUpdateNotification();
            
            // Clear old caches
            await this.clearOldCaches();
            
            // Cache new assets
            await this.cacheNewAssets();
            
            // Update Service Worker
            await this.updateServiceWorker();
            
        // console.log('✅ Smart update completed');
            
        } catch (error) {
            console.error('❌ Smart update failed:', error);
        } finally {
            this.isRunning = false;
        }
    }

    async clearOldCaches() {
        try {
            if (!('caches' in window)) {
        // console.log('⚠️ Cache API not available, skipping cache clear');
                return;
            }
            
            const cacheNames = await caches.keys();
            const oldCaches = cacheNames.filter(name => 
                name.startsWith('munah-pwa-') && name !== this.cacheName
            );
            
            await Promise.all(oldCaches.map(name => {
        // console.log(`   Deleting old cache: ${name}`);
                return caches.delete(name);
            }));
            
        } catch (error) {
            console.error('❌ Error clearing old caches:', error);
        }
    }

    async cacheNewAssets() {
        try {
            if (!('caches' in window)) {
        // console.log('⚠️ Cache API not available, skipping asset caching');
                return;
            }
            
            const cache = await caches.open(this.cacheName);
            const assetsToCache = [
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
                '/fonts/inter-700.woff2',
                '/build/assets/app-CP0OoLXE.css',
                '/build/assets/app-CeXcbV7U.js'
            ];
            
            let cached = 0;
            for (const asset of assetsToCache) {
                try {
                    const response = await fetch(asset);
                    if (response.ok) {
                        await cache.put(asset, response);
                        cached++;
        // console.log(`✅ Cached: ${asset}`);
                    }
                } catch (error) {
                    // console.log(`❌ Failed: ${asset}`);
                }
            }
            
        // console.log(`🎉 Cached ${cached}/${assetsToCache.length} assets`);
            
        } catch (error) {
            console.error('❌ Error caching assets:', error);
        }
    }

    async updateServiceWorker() {
        try {
            if ('serviceWorker' in navigator) {
                const registration = await navigator.serviceWorker.getRegistration();
                
                if (registration) {
                    if (registration.waiting) {
        // console.log('✅ New Service Worker waiting, activating...');
                        registration.waiting.postMessage({ action: 'skipWaiting' });
                        
                        // Wait for activation
                        await new Promise(resolve => {
                            const newWorker = registration.waiting;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'activated') {
                                    resolve();
                                }
                            });
                        });
                        
        // console.log('✅ Service Worker updated');
                    }
                }
            }
        } catch (error) {
            console.error('❌ Error updating Service Worker:', error);
        }
    }

    showUpdateNotification() {
        // Remove existing notification
        const existing = document.getElementById('smart-cache-notification');
        if (existing) existing.remove();
        
        const notificationHTML = `
            <div id="smart-cache-notification" style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: #10b981;
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 9999;
                font-family: system-ui, sans-serif;
                font-size: 14px;
                animation: slideIn 0.3s ease;
            ">
                🔄 Updating offline mode...
            </div>
            <style>
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            </style>
        `;
        
        document.body.insertAdjacentHTML('beforeend', notificationHTML);
        
        // Auto-hide after 3 seconds
        setTimeout(() => {
            const notification = document.getElementById('smart-cache-notification');
            if (notification) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                notification.style.transition = 'all 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, 3000);
    }
}

// Global instance
window.smartCacheManager = new SmartCacheManager();

// Auto-start
window.addEventListener('load', () => {
    window.smartCacheManager.init();
});
