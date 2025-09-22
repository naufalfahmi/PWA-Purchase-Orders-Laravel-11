// Script untuk register service worker secara manual
console.log('🚀 Registering Service Worker...');

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then(registration => {
            console.log('✅ Service Worker registered successfully:', registration.scope);
            console.log('Registration:', registration);
            
            // Listen for updates
            registration.addEventListener('updatefound', () => {
                console.log('🔄 Service Worker update found');
                const newWorker = registration.installing;
                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed') {
                        if (navigator.serviceWorker.controller) {
                            console.log('♻️ New Service Worker available, refresh to update');
                        } else {
                            console.log('🎉 Service Worker ready for offline use');
                        }
                    }
                });
            });
            
            // Check if already controlling
            if (navigator.serviceWorker.controller) {
                console.log('✅ Service Worker is controlling the page');
            } else {
                console.log('⏳ Service Worker registered but not yet controlling');
            }
            
            return registration;
        })
        .catch(error => {
            console.error('❌ Service Worker registration failed:', error);
        });
} else {
    console.error('❌ Service Worker not supported');
}
