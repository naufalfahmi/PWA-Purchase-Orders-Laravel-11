<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login - Munah - Purchase Orders')</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Munah - Purchase Orders">
    <meta name="application-name" content="Munah - Purchase Orders">
    <meta name="msapplication-TileTitle" content="Munah - Purchase Orders">
    <meta name="msapplication-tooltip" content="Munah - Purchase Orders">
    
    <!-- PWA Icons -->
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Local Fonts -->
    <link rel="stylesheet" href="{{ asset('css/inter-fonts.css') }}">
    
    <!-- Inline Fallback Script - Critical CSS and offline handling -->
    <script src="{{ asset('js/inline-fallback.js') }}"></script>
    
    <!-- Offline Overlay Script - Add offline indicator to any page -->
    <script src="{{ asset('js/offline-overlay.js') }}"></script>
    
    <!-- Offline Storage Script - Handle offline data storage and sync -->
    <script src="{{ asset('js/offline-storage.js') }}"></script>
    
    <!-- Offline Enhancement Script - Add offline capabilities to existing pages -->
    <script src="{{ asset('js/offline-enhance.js') }}"></script>
    
    <!-- Offline List Manager Script - Display offline data in lists -->
    <script src="{{ asset('js/offline-list-manager.js') }}"></script>
    
    <!-- Register Service Worker (Optional) -->
    <script>
        // Try to register Service Worker for caching, but don't rely on it
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw-simple.js')
                    .then(registration => {
                        console.log('SW registered:', registration.scope);
                    })
                    .catch(error => {
                        console.log('SW failed, using fallback mode:', error);
                    });
            });
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        @yield('content')
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50" id="success-message">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('success-message').remove();
            }, 3000);
        </script>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50" id="error-message">
            {{ session('error') }}
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('error-message').remove();
            }, 3000);
        </script>
    @endif

    @if($errors->any())
        <div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50" id="error-message">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('error-message').remove();
            }, 5000);
        </script>
    @endif
</body>
</html>
