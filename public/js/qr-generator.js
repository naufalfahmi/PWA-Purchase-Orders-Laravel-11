/**
 * QR Code Generator - Local Implementation
 * Uses the downloaded QRCode.js library
 */

class LocalQRGenerator {
    constructor() {
        this.isLoaded = false;
        this.loadQRCodeLibrary();
    }

    loadQRCodeLibrary() {
        // Check if QRCode is already loaded
        if (typeof QRCode !== 'undefined') {
            this.isLoaded = true;
            return Promise.resolve();
        }

        // Load from local file
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = '/libs/qrcode.min.js';
            script.onload = () => {
                this.isLoaded = true;
                resolve();
            };
            script.onerror = () => {
                reject(new Error('Failed to load QRCode library'));
            };
            document.head.appendChild(script);
        });
    }

    async generateQRCode(elementId, data, options = {}) {
        try {
            await this.loadQRCodeLibrary();
            
            const defaultOptions = {
                text: data,
                width: 128,
                height: 128,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            };

            const finalOptions = { ...defaultOptions, ...options };
            
            // Clear existing content
            const element = document.getElementById(elementId);
            if (element) {
                element.innerHTML = '';
                new QRCode(element, finalOptions);
            }
            
        } catch (error) {
            console.error('Error generating QR code:', error);
            // Fallback to text or placeholder
            const element = document.getElementById(elementId);
            if (element) {
                element.innerHTML = `<div style="width: 128px; height: 128px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; font-size: 12px; text-align: center; background: #f9f9f9;">QR Code<br/>Error</div>`;
            }
        }
    }

    async generateQRCodeImg(imgElementId, data, options = {}) {
        try {
            await this.loadQRCodeLibrary();
            
            const defaultOptions = {
                text: data,
                width: 128,
                height: 128,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            };

            const finalOptions = { ...defaultOptions, ...options };
            
            // Create a temporary div for QR generation
            const tempDiv = document.createElement('div');
            tempDiv.style.display = 'none';
            document.body.appendChild(tempDiv);
            
            new QRCode(tempDiv, finalOptions);
            
            // Get the canvas element from the QR code
            const canvas = tempDiv.querySelector('canvas');
            if (canvas) {
                // Convert canvas to data URL
                const dataURL = canvas.toDataURL('image/png');
                
                // Set the image source
                const imgElement = document.getElementById(imgElementId);
                if (imgElement) {
                    imgElement.src = dataURL;
                    imgElement.alt = 'QR Code';
                }
            }
            
            // Clean up
            document.body.removeChild(tempDiv);
            
        } catch (error) {
            console.error('Error generating QR code image:', error);
            // Fallback
            const imgElement = document.getElementById(imgElementId);
            if (imgElement) {
                imgElement.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTI4IiBoZWlnaHQ9IjEyOCIgdmlld0JveD0iMCAwIDEyOCAxMjgiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjgiIGhlaWdodD0iMTI4IiBmaWxsPSIjZjlmOWY5Ii8+Cjx0ZXh0IHg9IjY0IiB5PSI2NCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEwIiBmaWxsPSIjNjY2IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+UVLigJxDb2RlPC90ZXh0Pgo8dGV4dCB4PSI2NCIgeT0iODAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSI4IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5FcnJvcjwvdGV4dD4KPC9zdmc+';
                imgElement.alt = 'QR Code Error';
            }
        }
    }
}

// Global instance
window.qrGenerator = new LocalQRGenerator();

// Helper function for easy use
window.generateQRCode = function(elementId, data, options) {
    return window.qrGenerator.generateQRCode(elementId, data, options);
};

window.generateQRCodeImg = function(imgElementId, data, options) {
    return window.qrGenerator.generateQRCodeImg(imgElementId, data, options);
};
