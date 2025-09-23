/**
 * Offline Redirect Manager - Simple offline detection and redirect
 */
class OfflineRedirectManager {
    constructor() {
        this.init();
    }

    init() {
        // Check if we're already offline
        if (!navigator.onLine) {
            this.redirectToOffline();
            return;
        }

        // Listen for offline event
        window.addEventListener('offline', () => {
            // console.log('Network offline detected');
            this.redirectToOffline();
        });

        // Listen for online event
        window.addEventListener('online', () => {
            // console.log('Network online detected');
            this.redirectFromOffline();
        });

        // Periodic check for offline status
        setInterval(() => {
            if (!navigator.onLine && window.location.pathname !== '/offline.html') {
                this.redirectToOffline();
            }
        }, 5000);
    }

    redirectToOffline() {
        // Only redirect if we're not already on offline page
        if (window.location.pathname !== '/offline.html') {
            console.log('Redirecting to offline page');
            window.location.href = '/offline.html';
        }
    }

    redirectFromOffline() {
        // If we're on offline page and back online, redirect to home
        if (window.location.pathname === '/offline.html') {
            console.log('Redirecting from offline page to home');
            window.location.href = '/';
        }
    }
}

// Auto-initialize
window.addEventListener('load', () => {
    new OfflineRedirectManager();
});
