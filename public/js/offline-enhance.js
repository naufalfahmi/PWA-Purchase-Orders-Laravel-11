/**
 * Offline Enhancement - Add offline capabilities to existing pages
 */
class OfflineEnhancer {
    constructor() {
        this.init();
    }

    init() {
        // Wait for offline overlay to be ready
        this.waitForOfflineOverlay().then(() => {
            this.enhanceForms();
            this.enhanceButtons();
            this.enhanceTables();
            this.addOfflineCapabilities();
        });
    }

    // Wait for offline overlay to be ready
    async waitForOfflineOverlay() {
        return new Promise((resolve) => {
            const checkOverlay = () => {
                if (window.offlineOverlay) {
                    resolve();
                } else {
                    setTimeout(checkOverlay, 100);
                }
            };
            checkOverlay();
        });
    }

    // Enhance forms for offline capability
    enhanceForms() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            // Add offline data attribute
            form.setAttribute('data-offline-capable', 'true');
            
            // Add offline submit handler
            form.addEventListener('submit', (e) => {
                this.handleFormSubmit(e, form);
            });
        });
    }

    // Enhance buttons for offline capability
    enhanceButtons() {
        const buttons = document.querySelectorAll('button[type="submit"], .btn-primary, .btn-success');
        buttons.forEach(button => {
            // Add offline data attribute
            button.setAttribute('data-offline-capable', 'true');
            
            // Add offline click handler
            button.addEventListener('click', (e) => {
                this.handleButtonClick(e, button);
            });
        });
    }

    // Enhance tables for offline capability
    enhanceTables() {
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            // Add offline data attribute
            table.setAttribute('data-offline-capable', 'true');
        });
    }

    // Add offline capabilities to page
    addOfflineCapabilities() {
        // Add offline data attributes to main content
        const mainContent = document.querySelector('main, .main-content, .container');
        if (mainContent) {
            mainContent.setAttribute('data-offline-capable', 'true');
        }
        
        // Add offline data attributes to navigation
        const nav = document.querySelector('nav, .navigation, .navbar');
        if (nav) {
            nav.setAttribute('data-offline-capable', 'true');
        }
        
        // Add offline data attributes to cards
        const cards = document.querySelectorAll('.card, .panel, .widget');
        cards.forEach(card => {
            card.setAttribute('data-offline-capable', 'true');
        });
    }

    // Handle form submit
    handleFormSubmit(event, form) {
        if (!navigator.onLine) {
            event.preventDefault();
            
            // Get form data (preserve multiple products/items)
            const data = this.serializeForm(form);
            
            // Determine form type based on URL
            const formType = this.getFormType(form);
            
            // Save to offline storage
            if (window.offlineStorage) {
                const offlineId = window.offlineStorage.saveOfflineData(formType, data, 'create');
                console.log('Form submitted offline:', offlineId);
                
                // Show success message
                this.showOfflineMessage('✅ Data berhasil disimpan offline! Akan di-sync otomatis saat online.');
                
                // Update offline list if available
                this.updateOfflineList(formType);
                
                // Reset form
                form.reset();
                
                // Redirect immediately to appropriate page
                this.redirectAfterOfflineSubmit(formType);
            } else {
                console.error('Offline storage not available');
                this.showOfflineMessage('Error: Offline storage tidak tersedia');
            }
        }
    }

    // Convert form to object while preserving arrays and grouped products
    serializeForm(form) {
        const formData = new FormData(form);
        const result = {};
        const products = {};
        
        for (const [rawKey, rawValue] of formData.entries()) {
            const value = typeof rawValue === 'string' ? rawValue.trim() : rawValue;
            
            // Match products[0][field] style
            const match = rawKey.match(/^products\[(\d+)\]\[(.+)\]$/);
            if (match) {
                const index = parseInt(match[1], 10);
                const field = match[2];
                if (!products[index]) products[index] = {};
                products[index][field] = value;
                continue;
            }
            
            // Handle array fields like name[]
            if (rawKey.endsWith('[]')) {
                const key = rawKey.slice(0, -2);
                if (!Array.isArray(result[key])) result[key] = [];
                result[key].push(value);
                continue;
            }
            
            // Preserve multiple same-name fields by turning into array
            if (Object.prototype.hasOwnProperty.call(result, rawKey)) {
                if (!Array.isArray(result[rawKey])) {
                    result[rawKey] = [result[rawKey]];
                }
                result[rawKey].push(value);
            } else {
                result[rawKey] = value;
            }
        }
        
        // Normalize products map into array if any
        const productIndexes = Object.keys(products)
            .map(i => parseInt(i, 10))
            .filter(n => !Number.isNaN(n))
            .sort((a, b) => a - b);
        if (productIndexes.length > 0) {
            result.products = productIndexes.map(i => products[i]);
        }
        
        return result;
    }

    // Handle button click
    handleButtonClick(event, button) {
        // Only handle button clicks that are not form submit buttons
        if (!navigator.onLine && button.type !== 'submit') {
            event.preventDefault();
            
            // Get button context
            const context = this.getButtonContext(button);
            
            // Save to offline storage
            if (window.offlineStorage) {
                const offlineId = window.offlineStorage.saveOfflineData('button_action', {
                    button: button.textContent.trim(),
                    context: context,
                    timestamp: new Date().toISOString()
                }, 'create');
                
                console.log('Button action saved offline:', offlineId);
                
                // Show success message
                this.showOfflineMessage('Action berhasil disimpan offline! Akan di-sync otomatis saat online.');
            } else {
                console.error('Offline storage not available');
                this.showOfflineMessage('Error: Offline storage tidak tersedia');
            }
        }
    }

    // Get form type based on URL
    getFormType(form) {
        const url = form.action || window.location.pathname;
        
        // Check if this is a PO form specifically
        if (url.includes('purchase-order') || url.includes('po') || 
            (url.includes('sales-transaction') && form.querySelector('input[name*="po_number"]'))) {
            return 'purchase_order';
        } else if (url.includes('sales-transaction')) {
            return 'sales_transaction';
        } else if (url.includes('data-barang') || url.includes('products')) {
            return 'product';
        } else {
            return 'form_submit';
        }
    }

    // Redirect after offline submit
    redirectAfterOfflineSubmit(formType) {
        let redirectUrl = '/';
        
        switch (formType) {
            case 'sales_transaction':
                redirectUrl = '/sales-transaction';
                break;
            case 'purchase_order':
                redirectUrl = '/sales-transaction';
                break;
            case 'product':
                redirectUrl = '/data-barang';
                break;
            default:
                redirectUrl = '/dashboard';
        }
        
        console.log(`Redirecting to: ${redirectUrl}`);
        window.location.href = redirectUrl;
    }

    // Get button context
    getButtonContext(button) {
        const form = button.closest('form');
        const table = button.closest('table');
        const card = button.closest('.card, .panel, .widget');
        
        return {
            form: form ? form.action || 'unknown' : null,
            table: table ? 'table' : null,
            card: card ? card.className : null,
            page: window.location.pathname
        };
    }

    // Show offline message
    showOfflineMessage(message) {
        // Remove existing message
        const existing = document.getElementById('offline-message');
        if (existing) existing.remove();
        
        // Create message
        const messageEl = document.createElement('div');
        messageEl.id = 'offline-message';
        messageEl.className = 'offline-message';
        messageEl.innerHTML = `
            <div class="offline-message-content">
                <span class="offline-message-icon">📱</span>
                <span class="offline-message-text">${message}</span>
                <button class="offline-message-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;
        
        // Add styles
        if (!document.getElementById('offline-message-styles')) {
            const style = document.createElement('style');
            style.id = 'offline-message-styles';
            style.textContent = `
                .offline-message {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    background: #f59e0b;
                    color: white;
                    padding: 12px 16px;
                    border-radius: 8px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    z-index: 10000;
                    font-family: system-ui, sans-serif;
                    max-width: 300px;
                    animation: slideInUp 0.3s ease;
                }
                
                @keyframes slideInUp {
                    from { transform: translateY(100%); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }
                
                .offline-message-content {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                .offline-message-icon {
                    font-size: 16px;
                }
                
                .offline-message-text {
                    flex: 1;
                    font-size: 14px;
                }
                
                .offline-message-close {
                    background: none;
                    border: none;
                    color: white;
                    font-size: 18px;
                    cursor: pointer;
                    padding: 0;
                    width: 20px;
                    height: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
            `;
            document.head.appendChild(style);
        }
        
        document.body.appendChild(messageEl);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.remove();
            }
        }, 5000);
    }

    // Update offline list after data is saved
    updateOfflineList(formType) {
        // Check if we're on a page that has offline list functionality
        if (typeof updatePOList === 'function') {
            console.log('Updating PO list after offline insert');
            updatePOList();
        } else if (typeof updateOfflineList === 'function') {
            console.log('Updating offline list after offline insert');
            updateOfflineList();
        } else if (typeof refreshData === 'function') {
            console.log('Refreshing data after offline insert');
            refreshData();
        } else {
            console.log('No offline list update function found');
        }
        
        // Trigger custom event for other components to listen
        window.dispatchEvent(new CustomEvent('offlineDataUpdated', {
            detail: { formType: formType }
        }));
    }
}

// Auto-initialize
window.addEventListener('load', () => {
    window.offlineEnhancer = new OfflineEnhancer();
});

// Export for global access
window.OfflineEnhancer = OfflineEnhancer;
