<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Only - Admin PWA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center">
            <!-- Mobile Icon -->
            <div class="w-20 h-20 bg-blue-600 rounded-full mx-auto mb-6 flex items-center justify-center">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            
            <!-- Title -->
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Admin PWA Mobile</h1>
            
            <!-- Description -->
            <p class="text-gray-600 mb-6 leading-relaxed">
                Aplikasi ini dirancang khusus untuk perangkat mobile. Untuk pengalaman terbaik, silakan akses menggunakan smartphone atau tablet.
            </p>
            
            <!-- QR Code Placeholder -->
            <div class="bg-gray-100 rounded-lg p-4 mb-6">
                <div class="w-32 h-32 bg-white rounded border-2 border-dashed border-gray-300 mx-auto flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4M4 4h4m12 0h4"></path>
                        </svg>
                        <p class="text-xs text-gray-500">QR Code</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Scan untuk akses mobile</p>
            </div>
            
            <!-- Instructions -->
            <div class="space-y-3 text-left">
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-xs font-semibold text-blue-600">1</span>
                    </div>
                    <p class="text-sm text-gray-600">Buka browser di smartphone Anda</p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-xs font-semibold text-blue-600">2</span>
                    </div>
                    <p class="text-sm text-gray-600">Akses URL: <span class="font-mono text-blue-600 break-all">{{ request()->url() }}</span></p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-xs font-semibold text-blue-600">3</span>
                    </div>
                    <p class="text-sm text-gray-600">Install sebagai PWA untuk akses offline</p>
                </div>
            </div>
            
            <!-- Copy URL Button -->
            <button onclick="copyUrl()" class="w-full mt-6 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 mb-3">
                📋 Copy URL
            </button>
            
            <!-- Force Mobile Button -->
            <button onclick="forceMobileView()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                Lanjutkan di Desktop (Tidak Disarankan)
            </button>
            
            <!-- Footer -->
            <p class="text-xs text-gray-400 mt-6">
                Admin PWA v1.0 - Mobile First Design
            </p>
        </div>
    </div>

    <script>
        function copyUrl() {
            navigator.clipboard.writeText(window.location.href).then(function() {
                alert('URL berhasil disalin!');
            });
        }

        function forceMobileView() {
            // Redirect dengan query parameter untuk bypass mobile check
            window.location.href = '{{ route("login") }}?force_mobile=1';
        }

        // Register service worker untuk PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');
                        // Force update cache
                        registration.update();
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>
</body>
</html>
