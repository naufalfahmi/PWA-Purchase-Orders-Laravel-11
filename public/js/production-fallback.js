/**
 * Production Fallback JavaScript
 * Memastikan fungsionalitas tetap berjalan jika Vite assets tidak tersedia
 */

// Check if main app is loaded
if (typeof window.appLoaded === 'undefined') {
    console.log('Loading production fallback JavaScript...');
    
    // Basic DOM utilities
    window.DOMUtils = {
        ready: function(fn) {
            if (document.readyState !== 'loading') {
                fn();
            } else {
                document.addEventListener('DOMContentLoaded', fn);
            }
        },
        
        query: function(selector) {
            return document.querySelector(selector);
        },
        
        queryAll: function(selector) {
            return document.querySelectorAll(selector);
        },
        
        addClass: function(element, className) {
            if (element && element.classList) {
                element.classList.add(className);
            }
        },
        
        removeClass: function(element, className) {
            if (element && element.classList) {
                element.classList.remove(className);
            }
        },
        
        toggleClass: function(element, className) {
            if (element && element.classList) {
                element.classList.toggle(className);
            }
        }
    };
    
    // Basic AJAX utilities
    window.AjaxUtils = {
        post: function(url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            callback(null, response);
                        } catch (e) {
                            callback(null, xhr.responseText);
                        }
                    } else {
                        callback(new Error('Request failed: ' + xhr.status), null);
                    }
                }
            };
            
            xhr.send(JSON.stringify(data));
        },
        
        get: function(url, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            callback(null, response);
                        } catch (e) {
                            callback(null, xhr.responseText);
                        }
                    } else {
                        callback(new Error('Request failed: ' + xhr.status), null);
                    }
                }
            };
            
            xhr.send();
        }
    };
    
    // Basic form utilities
    window.FormUtils = {
        serialize: function(form) {
            const formData = new FormData(form);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            return data;
        },
        
        validate: function(form, rules) {
            let isValid = true;
            const errors = {};
            
            for (let field in rules) {
                const input = form.querySelector(`[name="${field}"]`);
                if (input) {
                    const value = input.value.trim();
                    const rule = rules[field];
                    
                    if (rule.required && !value) {
                        errors[field] = rule.message || `${field} is required`;
                        isValid = false;
                    } else if (rule.min && value.length < rule.min) {
                        errors[field] = rule.message || `${field} must be at least ${rule.min} characters`;
                        isValid = false;
                    }
                }
            }
            
            return { isValid, errors };
        }
    };
    
    // Basic notification utilities
    window.NotificationUtils = {
        show: function(message, type = 'info', duration = 3000) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${this.getTypeClass(type)}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, duration);
        },
        
        getTypeClass: function(type) {
            const classes = {
                'success': 'bg-green-100 text-green-800 border border-green-200',
                'error': 'bg-red-100 text-red-800 border border-red-200',
                'warning': 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                'info': 'bg-blue-100 text-blue-800 border border-blue-200'
            };
            return classes[type] || classes['info'];
        }
    };
    
    // Basic modal utilities
    window.ModalUtils = {
        show: function(content, options = {}) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
                    ${content}
                    ${options.showClose !== false ? '<button onclick="ModalUtils.hide(this)" class="mt-4 w-full bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">Close</button>' : ''}
                </div>
            `;
            
            document.body.appendChild(modal);
            return modal;
        },
        
        hide: function(button) {
            const modal = button.closest('.fixed.inset-0');
            if (modal && modal.parentNode) {
                modal.parentNode.removeChild(modal);
            }
        }
    };
    
    // Basic loading utilities
    window.LoadingUtils = {
        show: function(element) {
            if (element) {
                element.style.opacity = '0.5';
                element.style.pointerEvents = 'none';
            }
        },
        
        hide: function(element) {
            if (element) {
                element.style.opacity = '1';
                element.style.pointerEvents = 'auto';
            }
        }
    };
    
    // Initialize basic functionality when DOM is ready
    DOMUtils.ready(function() {
        // Initialize any basic functionality here
        console.log('Production fallback JavaScript loaded');
        
        // Add basic event listeners for common interactions
        const buttons = document.querySelectorAll('button[type="submit"]');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('form');
                if (form) {
                    LoadingUtils.show(form);
                }
            });
        });
        
        // Add basic form validation
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.style.borderColor = '#ef4444';
                        isValid = false;
                    } else {
                        field.style.borderColor = '#d1d5db';
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    NotificationUtils.show('Please fill in all required fields', 'error');
                }
            });
        });
    });
    
    // Mark as loaded
    window.appLoaded = true;
}
