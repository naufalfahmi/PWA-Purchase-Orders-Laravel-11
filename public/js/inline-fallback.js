/**
 * Inline Fallback Manager - Inline critical CSS for offline mode
 */
class InlineFallbackManager {
    constructor() {
        this.criticalCSS = `
            /* Critical CSS for offline mode */
            body { font-family: system-ui, -apple-system, sans-serif; margin: 0; padding: 0; }
            .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
            .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
            .btn-primary { background: #2563eb; color: white; }
            .btn-secondary { background: #6b7280; color: white; }
            .card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px; margin: 10px 0; }
            .form-group { margin: 15px 0; }
            .form-control { width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px; }
            .table { width: 100%; border-collapse: collapse; }
            .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
            .table th { background: #f9fafb; font-weight: 600; }
            .alert { padding: 12px 16px; border-radius: 4px; margin: 10px 0; }
            .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
            .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
            .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
            .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .mb-4 { margin-bottom: 1rem; }
            .mt-4 { margin-top: 1rem; }
            .p-4 { padding: 1rem; }
            .hidden { display: none; }
            .block { display: block; }
            .inline-block { display: inline-block; }
            .flex { display: flex; }
            .items-center { align-items: center; }
            .justify-between { justify-content: space-between; }
            .gap-4 { gap: 1rem; }
            .w-full { width: 100%; }
            .h-full { height: 100%; }
            .min-h-screen { min-height: 100vh; }
            .bg-gray-50 { background-color: #f9fafb; }
            .bg-white { background-color: white; }
            .text-gray-900 { color: #111827; }
            .text-gray-600 { color: #4b5563; }
            .text-gray-500 { color: #6b7280; }
            .border { border: 1px solid #e5e7eb; }
            .rounded { border-radius: 4px; }
            .shadow { box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
            .offline-indicator { 
                position: fixed; 
                top: 0; 
                left: 0; 
                right: 0; 
                background: #f59e0b; 
                color: white; 
                padding: 8px; 
                text-align: center; 
                font-size: 14px; 
                z-index: 9999; 
            }
        `;
        
        this.init();
    }

    init() {
        // Inject critical CSS
        this.injectCriticalCSS();
        
        // Setup offline detection
        this.setupOfflineDetection();
        
        // Preload critical assets
        this.preloadCriticalAssets();
    }

    injectCriticalCSS() {
        // Remove existing critical CSS
        const existing = document.getElementById('critical-css');
        if (existing) existing.remove();

        // Create style element
        const style = document.createElement('style');
        style.id = 'critical-css';
        style.textContent = this.criticalCSS;
        document.head.appendChild(style);
    }

    setupOfflineDetection() {
        // Listen for online/offline events
        window.addEventListener('online', () => {
            this.hideOfflineIndicator();
        });

        window.addEventListener('offline', () => {
            this.showOfflineIndicator();
        });

        // Check initial state
        if (!navigator.onLine) {
            this.showOfflineIndicator();
        }
    }

    showOfflineIndicator() {
        // Remove existing indicator
        const existing = document.getElementById('offline-indicator');
        if (existing) existing.remove();

        // Create offline indicator
        const indicator = document.createElement('div');
        indicator.id = 'offline-indicator';
        indicator.className = 'offline-indicator';
        indicator.textContent = '📱 You are offline - Some features may be limited';
        
        document.body.insertBefore(indicator, document.body.firstChild);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            this.hideOfflineIndicator();
        }, 5000);
    }

    hideOfflineIndicator() {
        const indicator = document.getElementById('offline-indicator');
        if (indicator) {
            indicator.style.opacity = '0';
            indicator.style.transform = 'translateY(-100%)';
            indicator.style.transition = 'all 0.3s ease';
            setTimeout(() => indicator.remove(), 300);
        }
    }

    async preloadCriticalAssets() {
        // Dynamic asset detection - try to find current build assets
        const criticalAssets = [];
        
        // Try to find CSS assets
        const cssLinks = document.querySelectorAll('link[href*="/build/assets/"]');
        cssLinks.forEach(link => {
            if (link.href.includes('.css')) {
                criticalAssets.push(link.href.replace(window.location.origin, ''));
            }
        });
        
        // Try to find JS assets
        const jsScripts = document.querySelectorAll('script[src*="/build/assets/"]');
        jsScripts.forEach(script => {
            if (script.src.includes('.js')) {
                criticalAssets.push(script.src.replace(window.location.origin, ''));
            }
        });
        
        // Fallback to known assets if none found
        if (criticalAssets.length === 0) {
            // Try to fetch manifest to get current assets
            try {
                const manifestResponse = await fetch('/build/manifest.json');
                if (manifestResponse.ok) {
                    const manifest = await manifestResponse.json();
                    if (manifest['resources/css/app.css']?.file) {
                        criticalAssets.push('/build/assets/' + manifest['resources/css/app.css'].file);
                    }
                    if (manifest['resources/js/app.js']?.file) {
                        criticalAssets.push('/build/assets/' + manifest['resources/js/app.js'].file);
                    }
                }
            } catch (e) {
        // console.log('Could not fetch manifest, using fallback assets');
            }
            
            // Add static fallbacks
            criticalAssets.push(
                '/css/inter-fonts.css',
                '/css/mobile-fallback.css',
                '/css/production-fallback.css'
            );
        }

        for (const asset of criticalAssets) {
            try {
                // Create preload link
                const link = document.createElement('link');
                link.rel = 'preload';
                link.href = asset;
                
                if (asset.includes('.css')) {
                    link.as = 'style';
                } else if (asset.includes('.js')) {
                    link.as = 'script';
                }
                
                document.head.appendChild(link);
                
                // Remove after preload
                setTimeout(() => {
                    if (link.parentNode) {
                        link.parentNode.removeChild(link);
                    }
                }, 1000);
                
            } catch (error) {
                // Silent error handling
            }
        }
    }
}

// Auto-initialize
window.addEventListener('load', () => {
    new InlineFallbackManager();
});
