/**
 * Offline Overlay Manager - Add offline indicator and sync capability to any page
 */
class OfflineOverlayManager {
    constructor() {
        this.isOnline = navigator.onLine;
        this.syncQueue = [];
        this.hideTimeout = null;
        this.init();
    }

    init() {
        // Load existing sync queue
        this.loadSyncQueue();
        
        // Create offline indicator (hidden by default). Comment out to fully disable
        // this.createOfflineIndicator();
        
        // Listen for online/offline events
        window.addEventListener('online', () => {
            const wasOffline = !this.isOnline;
            this.isOnline = true;
            if (wasOffline) {
                this.updateIndicator();
            }
            this.syncData();
        });
        
        window.addEventListener('offline', () => {
            const wasOnline = this.isOnline;
            this.isOnline = false;
            if (wasOnline) {
                this.updateIndicator();
            }
        });
        
        // Auto-sync every 30 seconds when online
        setInterval(() => {
            if (this.isOnline && this.syncQueue.length > 0) {
                this.syncData();
            }
        }, 30000);
        
        // Initial update (don't show indicator on page load)
        // this.updateIndicator();
    }

    // Create offline indicator overlay
    createOfflineIndicator() {
        // Remove existing indicator
        const existing = document.getElementById('offline-overlay-indicator');
        if (existing) existing.remove();
        
        // Create simple indicator
        const indicator = document.createElement('div');
        indicator.id = 'offline-overlay-indicator';
        indicator.innerHTML = `
            <div class="offline-indicator-content">
                <div class="offline-status">
                    <div class="offline-dot" id="offlineDot"></div>
                    <div class="offline-text" id="offlineText">Offline</div>
                </div>
            </div>
        `;
        
        // Add styles
        const style = document.createElement('style');
        style.textContent = `
            #offline-overlay-indicator {
                position: fixed;
                top: 20px;
                right: 20px;
                background: rgba(0,0,0,0.8);
                color: white;
                z-index: 9999;
                font-family: system-ui, sans-serif;
                border-radius: 20px;
                padding: 8px 16px;
                backdrop-filter: blur(10px);
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                display: none;
                opacity: 0;
            }
            
            .offline-indicator-content {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .offline-status {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .offline-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #ef4444;
                animation: pulse 2s infinite;
            }
            
            .offline-dot.online {
                background: #10b981;
                animation: none;
            }
            
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
            
            .offline-text {
                font-size: 12px;
                font-weight: 500;
            }
        `;
        
        document.head.appendChild(style);
        document.body.insertBefore(indicator, document.body.firstChild);
    }

    // Update indicator
    updateIndicator() {
        const indicator = document.getElementById('offline-overlay-indicator');
        const dot = document.getElementById('offlineDot');
        const text = document.getElementById('offlineText');
        
        if (!indicator || !dot || !text) return;
        
        if (this.isOnline) {
            dot.className = 'offline-dot online';
            text.textContent = 'Online';
        } else {
            dot.className = 'offline-dot';
            text.textContent = 'Offline';
        }
        
        // Show indicator and auto-hide after 2 seconds
        indicator.style.display = 'block';
        indicator.style.opacity = '1';
        
        // Clear existing timeout
        if (this.hideTimeout) {
            clearTimeout(this.hideTimeout);
        }
        
        // Auto-hide after 2 seconds
        this.hideTimeout = setTimeout(() => {
            indicator.style.opacity = '0';
            setTimeout(() => {
                indicator.style.display = 'none';
            }, 300); // Wait for fade out animation
        }, 2000);
    }


    // Add item to sync queue
    addToSyncQueue(type, data, action = 'create') {
        const syncItem = {
            id: `${type}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
            type: type,
            action: action,
            data: data,
            timestamp: new Date().toISOString(),
            status: 'pending',
            retryCount: 0
        };
        
        this.syncQueue.push(syncItem);
        this.saveSyncQueue();
        // this.updateIndicator();
        
        // console.log(`Added to sync queue: ${syncItem.id}`);
        return syncItem.id;
    }

    // Sync data
    async syncData() {
        if (!this.isOnline || this.syncQueue.length === 0) {
            return;
        }
        
        // console.log(`Syncing ${this.syncQueue.length} items...`);
        
        const itemsToSync = this.syncQueue.filter(item => item.status === 'pending');
        
        for (const item of itemsToSync) {
            try {
                await this.syncItem(item);
                item.status = 'synced';
                // console.log(`Synced: ${item.id}`);
            } catch (error) {
                item.retryCount++;
                if (item.retryCount >= 3) {
                    item.status = 'failed';
                    console.error(`Failed to sync after 3 retries: ${item.id}`, error);
                } else {
                    console.warn(`Sync failed, will retry: ${item.id}`, error);
                }
            }
        }
        
        // Remove synced items
        this.syncQueue = this.syncQueue.filter(item => item.status !== 'synced');
        this.saveSyncQueue();
        this.updateIndicator();
    }

    // Sync individual item
    async syncItem(item) {
        const { type, action, data } = item;
        
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        // In real implementation, make actual API calls
        // console.log(`Syncing ${type} ${action}:`, data);
    }

    // Load sync queue from localStorage
    loadSyncQueue() {
        try {
            const stored = localStorage.getItem('offlineSyncQueue');
            this.syncQueue = stored ? JSON.parse(stored) : [];
        } catch (error) {
            console.error('Failed to load sync queue:', error);
            this.syncQueue = [];
        }
    }

    // Save sync queue to localStorage
    saveSyncQueue() {
        try {
            localStorage.setItem('offlineSyncQueue', JSON.stringify(this.syncQueue));
        } catch (error) {
            console.error('Failed to save sync queue:', error);
        }
    }

    // Get sync status
    getSyncStatus() {
        return {
            isOnline: this.isOnline,
            pendingCount: this.syncQueue.filter(item => item.status === 'pending').length,
            failedCount: this.syncQueue.filter(item => item.status === 'failed').length,
            totalCount: this.syncQueue.length
        };
    }
}

// Auto-initialize
window.addEventListener('load', () => {
    window.offlineOverlay = new OfflineOverlayManager();
});

// Export for global access
window.OfflineOverlayManager = OfflineOverlayManager;
