/**
 * Offline List Manager - Display offline data in lists with sync indicators
 */
class OfflineListManager {
    constructor() {
        this.init();
    }

    init() {
        // Wait for offline storage to be ready
        this.waitForOfflineStorage().then(() => {
            this.enhanceLists();
            this.updateOfflineDataInLists();
            
            // Update lists every 5 seconds
            setInterval(() => {
                this.updateOfflineDataInLists();
            }, 5000);
        });
    }

    // Wait for offline storage to be ready
    async waitForOfflineStorage() {
        return new Promise((resolve) => {
            const checkStorage = () => {
                if (window.offlineStorage) {
                    resolve();
                } else {
                    setTimeout(checkStorage, 100);
                }
            };
            checkStorage();
        });
    }

    // Enhance lists to show offline data
    enhanceLists() {
        // Find tables that might contain PO data
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            this.enhanceTable(table);
        });

        // Find lists that might contain PO data
        const lists = document.querySelectorAll('.list-group, .data-list, .item-list');
        lists.forEach(list => {
            this.enhanceList(list);
        });
    }

    // Enhance table to show offline data
    enhanceTable(table) {
        if (table.hasAttribute('data-offline-enhanced')) return;
        
        table.setAttribute('data-offline-enhanced', 'true');
        
        // Add offline data to table
        this.addOfflineDataToTable(table);
    }

    // Enhance list to show offline data
    enhanceList(list) {
        if (list.hasAttribute('data-offline-enhanced')) return;
        
        list.setAttribute('data-offline-enhanced', 'true');
        
        // Add offline data to list
        this.addOfflineDataToList(list);
    }

    // Add offline data to table
    addOfflineDataToTable(table) {
        if (!window.offlineStorage) return;

        const offlineData = window.offlineStorage.getDataByType('purchase_order');
        const unsyncedData = offlineData.filter(item => !item.synced);

        if (unsyncedData.length === 0) return;

        // Find table body
        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        // Remove existing offline rows first
        const existingOfflineRows = tbody.querySelectorAll('.offline-row');
        existingOfflineRows.forEach(row => row.remove());

        // Add offline data rows
        unsyncedData.forEach(item => {
            const row = this.createOfflineTableRow(item);
            tbody.insertBefore(row, tbody.firstChild);
        });

        // console.log(`Added ${unsyncedData.length} offline PO items to table`);
    }

    // Add offline data to list
    addOfflineDataToList(list) {
        if (!window.offlineStorage) return;

        const offlineData = window.offlineStorage.getDataByType('purchase_order');
        const unsyncedData = offlineData.filter(item => !item.synced);

        if (unsyncedData.length === 0) return;

        // Add offline data items
        unsyncedData.forEach(item => {
            const listItem = this.createOfflineListItem(item);
            list.insertBefore(listItem, list.firstChild);
        });
    }

    // Create offline table row
    createOfflineTableRow(item) {
        const row = document.createElement('tr');
        row.className = 'offline-row';
        row.setAttribute('data-offline-id', item.id);
        
        const data = item.data;
        
        row.innerHTML = `
            <td>
                <span class="offline-indicator" title="Data offline - belum di-sync">
                    📱
                </span>
                ${data.po_number || data.supplier_name || 'Offline Data'}
            </td>
            <td>${data.supplier_name || '-'}</td>
            <td>${data.total_amount ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.total_amount) : '-'}</td>
            <td>
                <span class="badge badge-warning">Offline</span>
            </td>
            <td>
                <small class="text-muted">${new Date(item.timestamp).toLocaleString('id-ID')}</small>
            </td>
        `;

        // Add styles for offline row
        if (!document.getElementById('offline-row-styles')) {
            const style = document.createElement('style');
            style.id = 'offline-row-styles';
            style.textContent = `
                .offline-row {
                    background-color: #fef3c7 !important;
                    border-left: 4px solid #f59e0b;
                }
                
                .offline-indicator {
                    font-size: 14px;
                    margin-right: 8px;
                }
                
                .badge-warning {
                    background-color: #f59e0b;
                    color: white;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 12px;
                }
            `;
            document.head.appendChild(style);
        }

        return row;
    }

    // Create offline list item
    createOfflineListItem(item) {
        const listItem = document.createElement('div');
        listItem.className = 'offline-item list-group-item';
        listItem.setAttribute('data-offline-id', item.id);
        
        const data = item.data;
        
        listItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="offline-indicator" title="Data offline - belum di-sync">
                        📱
                    </span>
                    <strong>${data.po_number || data.supplier_name || 'Offline Data'}</strong>
                    <br>
                    <small class="text-muted">${data.supplier_name || '-'} - ${data.total_amount ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.total_amount) : '-'}</small>
                </div>
                <div>
                    <span class="badge badge-warning">Offline</span>
                    <br>
                    <small class="text-muted">${new Date(item.timestamp).toLocaleString('id-ID')}</small>
                </div>
            </div>
        `;

        // Add styles for offline item
        if (!document.getElementById('offline-item-styles')) {
            const style = document.createElement('style');
            style.id = 'offline-item-styles';
            style.textContent = `
                .offline-item {
                    background-color: #fef3c7 !important;
                    border-left: 4px solid #f59e0b;
                }
                
                .offline-indicator {
                    font-size: 14px;
                    margin-right: 8px;
                }
                
                .badge-warning {
                    background-color: #f59e0b;
                    color: white;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 12px;
                }
            `;
            document.head.appendChild(style);
        }

        return listItem;
    }

    // Update offline data in all lists
    updateOfflineDataInLists() {
        if (!window.offlineStorage) return;

        // Remove existing offline rows/items
        this.removeExistingOfflineData();

        // Add updated offline data
        this.enhanceLists();
    }

    // Remove existing offline data
    removeExistingOfflineData() {
        // Remove offline table rows
        const offlineRows = document.querySelectorAll('.offline-row');
        offlineRows.forEach(row => row.remove());

        // Remove offline list items
        const offlineItems = document.querySelectorAll('.offline-item');
        offlineItems.forEach(item => item.remove());
    }

    // Get offline data count
    getOfflineDataCount() {
        if (!window.offlineStorage) return 0;
        
        const offlineData = window.offlineStorage.getDataByType('purchase_order');
        return offlineData.filter(item => !item.synced).length;
    }

    // Get offline data for specific type
    getOfflineDataByType(type) {
        if (!window.offlineStorage) return [];
        
        return window.offlineStorage.getDataByType(type);
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    window.offlineListManager = new OfflineListManager();
});

// Export for global access
window.OfflineListManager = OfflineListManager;
