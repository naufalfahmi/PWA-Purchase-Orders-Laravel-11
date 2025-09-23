/**
 * Offline Storage Manager - Handle offline data storage and sync
 */
class OfflineStorageManager {
    constructor() {
        this.storageKey = 'offlineData';
        this.syncKey = 'offlineSyncQueue';
        this.init();
    }

    init() {
        // Auto-sync when online
        window.addEventListener('online', () => {
            this.syncAllData();
        });
        
        // Periodic sync check
        setInterval(() => {
            if (navigator.onLine) {
                this.syncAllData();
            }
        }, 30000); // Every 30 seconds
        
        // console.log('Offline Storage Manager initialized');
    }

    // Save data offline
    saveOfflineData(type, data, action = 'create') {
        const offlineData = this.getOfflineData();
        const id = this.generateId();
        
        const offlineItem = {
            id: id,
            type: type,
            action: action,
            data: data,
            timestamp: new Date().toISOString(),
            status: 'offline',
            synced: false
        };
        
        // Add to offline storage
        offlineData.push(offlineItem);
        this.setOfflineData(offlineData);
        
        // Add to sync queue
        this.addToSyncQueue(offlineItem);
        
        // console.log(`Data saved offline: ${type} - ${id}`);
        // console.log('Offline data count:', offlineData.length);
        
        // Trigger list update immediately and after delay
        if (window.offlineListManager) {
            window.offlineListManager.updateOfflineDataInLists();
        }
        
        // Trigger custom event for immediate update
        window.dispatchEvent(new CustomEvent('offlineDataUpdated', {
            detail: { type: type, id: id, action: action }
        }));
        
        setTimeout(() => {
            if (window.offlineListManager) {
                window.offlineListManager.updateOfflineDataInLists();
            }
        }, 500);
        
        return id;
    }

    // Get offline data
    getOfflineData() {
        try {
            const data = localStorage.getItem(this.storageKey);
            return data ? JSON.parse(data) : [];
        } catch (error) {
            console.error('Failed to load offline data:', error);
            return [];
        }
    }

    // Set offline data
    setOfflineData(data) {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(data));
        } catch (error) {
            console.error('Failed to save offline data:', error);
        }
    }

    // Generate unique ID
    generateId() {
        return 'offline_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    // Add to sync queue
    addToSyncQueue(item) {
        try {
            const queue = JSON.parse(localStorage.getItem(this.syncKey) || '[]');
            queue.push(item);
            localStorage.setItem(this.syncKey, JSON.stringify(queue));
        } catch (error) {
            console.error('Failed to add to sync queue:', error);
        }
    }

    // Get sync queue
    getSyncQueue() {
        try {
            const queue = localStorage.getItem(this.syncKey);
            return queue ? JSON.parse(queue) : [];
        } catch (error) {
            console.error('Failed to load sync queue:', error);
            return [];
        }
    }

    // Clear sync queue
    clearSyncQueue() {
        try {
            localStorage.setItem(this.syncKey, '[]');
        } catch (error) {
            console.error('Failed to clear sync queue:', error);
        }
    }

    // Sync all data
    async syncAllData() {
        if (!navigator.onLine) return;
        
        const queue = this.getSyncQueue();
        const unsyncedItems = queue.filter(item => !item.synced);
        
        if (unsyncedItems.length === 0) return;
        
        // Notify UI sync is starting
        try {
            window.dispatchEvent(new CustomEvent('offlineSyncStart', {
                detail: { total: unsyncedItems.length }
            }));
        } catch (e) {}

        // console.log(`Syncing ${unsyncedItems.length} items...`);
        
        const syncedItems = [];
        const initialPendingCount = unsyncedItems.length;
        
        for (const item of unsyncedItems) {
            try {
                await this.syncItem(item);
                item.synced = true;
                item.syncedAt = new Date().toISOString();
                syncedItems.push(item.id);
                // console.log(`Synced: ${item.type} - ${item.id}`);
            } catch (error) {
                console.error(`Failed to sync ${item.type} - ${item.id}:`, error);
                item.syncError = error.message;
            }
        }
        
        // Update sync queue
        this.updateSyncQueue(queue);
        
        // Update offline data
        this.updateOfflineData(queue);
        
        // Remove synced items from offline storage
        if (syncedItems.length > 0) {
            this.removeSyncedItems(syncedItems);
        }
        
        // Dispatch completion event only when there are actually synced items
        if (syncedItems.length > 0) {
            try {
                window.dispatchEvent(new CustomEvent('offlineDataSynced', {
                    detail: { syncedIds: syncedItems }
                }));
            } catch (e) {}
        }
        
        // Save last sync time
        localStorage.setItem('lastSyncTime', new Date().toISOString());
        
        // Trigger list update
        if (window.offlineListManager) {
            window.offlineListManager.updateOfflineDataInLists();
        }

        // Notify UI sync is finished
        try {
            window.dispatchEvent(new CustomEvent('offlineSyncEnd', {
                detail: { synced: syncedItems.length, remaining: this.getSyncQueue().filter(i => !i.synced).length }
            }));
        } catch (e) {}
    }

    // Sync individual item
    async syncItem(item) {
        const { type, action, data } = item;
        
        switch (type) {
            case 'sales_transaction':
                return await this.syncSalesTransaction(action, data);
            case 'purchase_order':
                return await this.syncPurchaseOrder(action, data);
            case 'product':
                return await this.syncProduct(action, data);
            default:
                throw new Error(`Unknown sync type: ${type}`);
        }
    }

    // Sync sales transaction
    async syncSalesTransaction(action, data) {
        const url = '/api/sales-transaction';
        const method = action === 'create' ? 'POST' : action === 'update' ? 'PUT' : 'DELETE';
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP ${response.status}: ${response.statusText} - ${errorText}`);
        }
        
        return await response.json();
    }

    // Sync purchase order
    async syncPurchaseOrder(action, data) {
        const url = '/api/purchase-order';
        const method = action === 'create' ? 'POST' : action === 'update' ? 'PUT' : 'DELETE';
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP ${response.status}: ${response.statusText} - ${errorText}`);
        }
        
        return await response.json();
    }

    // Sync product
    async syncProduct(action, data) {
        const url = '/api/products';
        const method = action === 'create' ? 'POST' : action === 'update' ? 'PUT' : 'DELETE';
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP ${response.status}: ${response.statusText} - ${errorText}`);
        }
        
        return await response.json();
    }

    // Update sync queue
    updateSyncQueue(queue) {
        try {
            localStorage.setItem(this.syncKey, JSON.stringify(queue));
        } catch (error) {
            console.error('Failed to update sync queue:', error);
        }
    }

    // Update offline data
    updateOfflineData(queue) {
        try {
            const offlineData = this.getOfflineData();
            
            // Update offline data with sync status
            queue.forEach(queueItem => {
                const offlineItem = offlineData.find(item => item.id === queueItem.id);
                if (offlineItem) {
                    offlineItem.synced = queueItem.synced;
                    offlineItem.syncedAt = queueItem.syncedAt;
                    offlineItem.syncError = queueItem.syncError;
                }
            });
            
            this.setOfflineData(offlineData);
        } catch (error) {
            console.error('Failed to update offline data:', error);
        }
    }

    // Get data by type
    getDataByType(type) {
        const offlineData = this.getOfflineData();
        return offlineData.filter(item => item.type === type);
    }

    // Get unsynced data
    getUnsyncedData() {
        const offlineData = this.getOfflineData();
        return offlineData.filter(item => !item.synced);
    }

    // Get sync status
    getSyncStatus() {
        const offlineData = this.getOfflineData();
        const unsynced = offlineData.filter(item => !item.synced);
        const synced = offlineData.filter(item => item.synced);
        const failed = offlineData.filter(item => item.syncError);
        
        return {
            total: offlineData.length,
            unsynced: unsynced.length,
            synced: synced.length,
            failed: failed.length
        };
    }

    // Clear synced data
    clearSyncedData() {
        const offlineData = this.getOfflineData();
        const unsyncedData = offlineData.filter(item => !item.synced);
        this.setOfflineData(unsyncedData);
        
        const queue = this.getSyncQueue();
        const unsyncedQueue = queue.filter(item => !item.synced);
        this.updateSyncQueue(unsyncedQueue);
    }

    // Remove synced items from offline storage
    removeSyncedItems(syncedItemIds) {
        try {
            const offlineData = this.getOfflineData();
            const filteredData = offlineData.filter(item => !syncedItemIds.includes(item.id));
            this.setOfflineData(filteredData);
            // console.log(`Removed ${syncedItemIds.length} synced items from offline storage`);
        } catch (error) {
            console.error('Failed to remove synced items:', error);
        }
    }

    // Clear all data
    clearAllData() {
        this.setOfflineData([]);
        this.clearSyncQueue();
    }

    // Reset sync total (clear synced data only)
    resetSyncTotal() {
        const offlineData = this.getOfflineData();
        const unsyncedData = offlineData.filter(item => !item.synced);
        this.setOfflineData(unsyncedData);
        
        const queue = this.getSyncQueue();
        const unsyncedQueue = queue.filter(item => !item.synced);
        this.updateSyncQueue(unsyncedQueue);
        
        // console.log('Sync total reset - cleared all synced data');
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    try {
        window.offlineStorage = new OfflineStorageManager();
        // console.log('Offline Storage Manager created successfully');
    } catch (error) {
        console.error('Failed to initialize Offline Storage Manager:', error);
        // Create a fallback object
        window.offlineStorage = {
            saveOfflineData: () => {/* console.log('Offline storage not available') */},
            getSyncStatus: () => ({ total: 0, unsynced: 0, synced: 0, failed: 0 }),
            syncAllData: () => Promise.resolve(),
            clearSyncedData: () => {/* console.log('Offline storage not available') */},
            clearAllData: () => {/* console.log('Offline storage not available') */}
        };
    }
});

// Also try on window load as backup
window.addEventListener('load', () => {
    if (!window.offlineStorage) {
        try {
            window.offlineStorage = new OfflineStorageManager();
            // console.log('Offline Storage Manager created on window load');
        } catch (error) {
            console.error('Failed to initialize Offline Storage Manager on window load:', error);
        }
    }
});

// Export for global access
window.OfflineStorageManager = OfflineStorageManager;
