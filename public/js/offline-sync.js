/**
 * Offline Sync Manager - Handle data synchronization between offline and online modes
 */
class OfflineSyncManager {
    constructor() {
        this.syncQueue = [];
        this.isOnline = navigator.onLine;
        this.init();
    }

    init() {
        // Load existing sync queue
        this.loadSyncQueue();
        
        // Listen for online/offline events
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.syncData();
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
        });
        
        // Auto-sync every 30 seconds when online
        setInterval(() => {
            if (this.isOnline && this.syncQueue.length > 0) {
                this.syncData();
            }
        }, 30000);
    }

    // Add item to sync queue
    addToSyncQueue(type, data, action = 'create') {
        const syncItem = {
            id: `${type}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
            type: type,
            action: action, // create, update, delete
            data: data,
            timestamp: new Date().toISOString(),
            status: 'pending',
            retryCount: 0
        };
        
        this.syncQueue.push(syncItem);
        this.saveSyncQueue();
        
        console.log(`Added to sync queue: ${syncItem.id}`);
        return syncItem.id;
    }

    // Sync all pending items
    async syncData() {
        if (!this.isOnline || this.syncQueue.length === 0) {
            return;
        }
        
        console.log(`Syncing ${this.syncQueue.length} items...`);
        
        const itemsToSync = this.syncQueue.filter(item => item.status === 'pending');
        
        for (const item of itemsToSync) {
            try {
                await this.syncItem(item);
                item.status = 'synced';
                console.log(`Synced: ${item.id}`);
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
        
        // Update UI
        this.updateSyncUI();
    }

    // Sync individual item
    async syncItem(item) {
        const { type, action, data } = item;
        
        switch (type) {
            case 'sales_transaction':
                return await this.syncSalesTransaction(action, data);
            case 'product':
                return await this.syncProduct(action, data);
            case 'user_activity':
                return await this.syncUserActivity(action, data);
            default:
                throw new Error(`Unknown sync type: ${type}`);
        }
    }

    // Sync sales transaction
    async syncSalesTransaction(action, data) {
        const url = '/api/sales-transaction';
        const method = action === 'create' ? 'POST' : action === 'update' ? 'PUT' : 'DELETE';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return await response.json();
    }

    // Sync product
    async syncProduct(action, data) {
        const url = '/api/products';
        const method = action === 'create' ? 'POST' : action === 'update' ? 'PUT' : 'DELETE';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return await response.json();
    }

    // Sync user activity
    async syncUserActivity(action, data) {
        const url = '/api/user-activity';
        const method = 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return await response.json();
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

    // Update sync UI
    updateSyncUI() {
        const pendingItems = this.syncQueue.filter(item => item.status === 'pending');
        const failedItems = this.syncQueue.filter(item => item.status === 'failed');
        
        // Update sync indicator
        const syncIcon = document.getElementById('syncIcon');
        const syncText = document.getElementById('syncText');
        
        if (syncIcon && syncText) {
            if (pendingItems.length > 0) {
                syncIcon.className = 'sync-icon';
                syncText.textContent = `${pendingItems.length} pending`;
            } else if (failedItems.length > 0) {
                syncIcon.className = 'sync-icon';
                syncIcon.style.background = '#ef4444';
                syncText.textContent = `${failedItems.length} failed`;
            } else {
                syncIcon.className = 'sync-icon synced';
                syncText.textContent = 'Synced';
            }
        }
        
        // Update sync queue display
        this.updateSyncQueueDisplay();
    }

    // Update sync queue display
    updateSyncQueueDisplay() {
        const syncQueue = document.getElementById('syncQueue');
        const syncItems = document.getElementById('syncItems');
        
        if (!syncQueue || !syncItems) return;
        
        const pendingItems = this.syncQueue.filter(item => item.status === 'pending');
        
        if (pendingItems.length > 0) {
            syncQueue.style.display = 'block';
            syncItems.innerHTML = '';
            
            pendingItems.forEach(item => {
                const div = document.createElement('div');
                div.className = 'sync-item';
                div.innerHTML = `
                    <div class="sync-item-name">${item.type} - ${item.action}</div>
                    <div class="sync-item-status">Pending</div>
                `;
                syncItems.appendChild(div);
            });
        } else {
            syncQueue.style.display = 'none';
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

    // Clear failed items
    clearFailedItems() {
        this.syncQueue = this.syncQueue.filter(item => item.status !== 'failed');
        this.saveSyncQueue();
        this.updateSyncUI();
    }

    // Retry failed items
    retryFailedItems() {
        const failedItems = this.syncQueue.filter(item => item.status === 'failed');
        failedItems.forEach(item => {
            item.status = 'pending';
            item.retryCount = 0;
        });
        this.saveSyncQueue();
        this.updateSyncUI();
        
        if (this.isOnline) {
            this.syncData();
        }
    }
}

// Auto-initialize
window.addEventListener('load', () => {
    window.offlineSync = new OfflineSyncManager();
});

// Export for global access
window.OfflineSyncManager = OfflineSyncManager;
