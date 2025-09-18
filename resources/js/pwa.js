// Register Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

// Install prompt
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent Chrome 67 and earlier from automatically showing the prompt
    e.preventDefault();
    // Stash the event so it can be triggered later
    deferredPrompt = e;
    
    // Show install button or banner
    showInstallBanner();
});

function showInstallBanner() {
    // Create install banner if it doesn't exist
    if (!document.getElementById('install-banner')) {
        const banner = document.createElement('div');
        banner.id = 'install-banner';
        banner.className = 'fixed bottom-20 left-4 right-4 bg-primary-600 text-white p-4 rounded-lg shadow-lg z-40';
        banner.innerHTML = `
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold">Install Admin PWA</h3>
                    <p class="text-sm opacity-90">Install app for better experience</p>
                </div>
                <div class="flex space-x-2">
                    <button id="install-btn" class="bg-white text-primary-600 px-3 py-1 rounded text-sm font-medium">
                        Install
                    </button>
                    <button id="dismiss-btn" class="text-white opacity-75 hover:opacity-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(banner);
        
        // Install button click
        document.getElementById('install-btn').addEventListener('click', () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                    }
                    deferredPrompt = null;
                    banner.remove();
                });
            }
        });
        
        // Dismiss button click
        document.getElementById('dismiss-btn').addEventListener('click', () => {
            banner.remove();
        });
        
        // Auto dismiss after 10 seconds
        setTimeout(() => {
            if (banner.parentNode) {
                banner.remove();
            }
        }, 10000);
    }
}

// Handle app installed
window.addEventListener('appinstalled', (evt) => {
    console.log('App was installed');
    const banner = document.getElementById('install-banner');
    if (banner) {
        banner.remove();
    }
});

// Online/Offline status
function updateOnlineStatus() {
    const status = document.getElementById('connection-status');
    if (navigator.onLine) {
        if (status) status.remove();
    } else {
        if (!status) {
            const offlineBanner = document.createElement('div');
            offlineBanner.id = 'connection-status';
            offlineBanner.className = 'fixed top-0 left-0 right-0 bg-red-600 text-white p-2 text-center z-50';
            offlineBanner.innerHTML = 'No internet connection. Some features may be limited.';
            document.body.appendChild(offlineBanner);
        }
    }
}

window.addEventListener('online', updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);

// Initialize
updateOnlineStatus();

// Touch feedback for mobile
document.addEventListener('touchstart', function() {}, true);

// Prevent zoom on double tap
let lastTouchEnd = 0;
document.addEventListener('touchend', function (event) {
    const now = (new Date()).getTime();
    if (now - lastTouchEnd <= 300) {
        event.preventDefault();
    }
    lastTouchEnd = now;
}, false);
