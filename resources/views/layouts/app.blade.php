<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin PWA')</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Admin PWA">
    
    <!-- PWA Icons -->
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Local Fonts -->
    <link rel="stylesheet" href="{{ asset('css/inter-fonts.css') }}">
    
    <!-- Fallback CSS untuk mobile -->
    <link rel="stylesheet" href="{{ asset('css/mobile-fallback.css') }}" media="screen and (max-width: 768px)">
    
    <style>
        /* Fallback CSS untuk mobile - memastikan styling tetap berfungsi */
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f9fafb;
            color: #111827;
            line-height: 1.5;
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        .min-h-screen {
            min-height: 100vh;
        }
        
        .bg-gray-50 {
            background-color: #f9fafb;
        }
        
        .bg-white {
            background-color: #ffffff;
        }
        
        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        
        .border-b {
            border-bottom-width: 1px;
        }
        
        .border-gray-200 {
            border-color: #e5e7eb;
        }
        
        .sticky {
            position: sticky;
        }
        
        .top-0 {
            top: 0;
        }
        
        .z-40 {
            z-index: 40;
        }
        
        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .py-3 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
        
        .flex {
            display: flex;
        }
        
        .items-center {
            align-items: center;
        }
        
        .justify-between {
            justify-content: space-between;
        }
        
        .text-lg {
            font-size: 1.125rem;
        }
        
        .font-semibold {
            font-weight: 600;
        }
        
        .text-gray-900 {
            color: #111827;
        }
        
        .space-x-2 > * + * {
            margin-left: 0.5rem;
        }
        
        .text-sm {
            font-size: 0.875rem;
        }
        
        .text-gray-600 {
            color: #4b5563;
        }
        
        .text-gray-500 {
            color: #6b7280;
        }
        
        .hover\:text-gray-700:hover {
            color: #374151;
        }
        
        .w-5 {
            width: 1.25rem;
        }
        
        .h-5 {
            height: 1.25rem;
        }
        
        .flex-1 {
            flex: 1 1 0%;
        }
        
        .pb-20 {
            padding-bottom: 5rem;
        }
        
        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #ffffff;
            border-top: 1px solid #e5e7eb;
            z-index: 50;
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        .grid {
            display: grid;
        }
        
        .grid-cols-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
        
        .grid-cols-5 {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
        
        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 4px;
            text-decoration: none;
            color: #6b7280;
            transition: color 0.2s;
            font-size: 10px;
        }
        
        .mobile-nav-item:hover,
        .mobile-nav-item.active {
            color: #2563eb;
        }
        
        .mobile-nav-item svg {
            margin-bottom: 2px;
        }
        
        .w-6 {
            width: 1.5rem;
        }
        
        .h-6 {
            height: 1.5rem;
        }
        
        .mb-1 {
            margin-bottom: 0.25rem;
        }

        /* Desktop warning */
        .desktop-warning {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: #92400e;
            padding: 8px 16px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            border-bottom: 1px solid #d97706;
        }

        .desktop-warning .close-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #92400e;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }
        
        /* Success/Error Messages */
        .fixed {
            position: fixed;
        }
        
        .top-4 {
            top: 1rem;
        }
        
        .right-4 {
            right: 1rem;
        }
        
        .bg-green-500 {
            background-color: #10b981;
        }
        
        .bg-red-500 {
            background-color: #ef4444;
        }
        
        .text-white {
            color: #ffffff;
        }
        
        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        
        .rounded-lg {
            border-radius: 0.5rem;
        }
        
        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .z-50 {
            z-index: 50;
        }
        
        /* Button styles */
        button {
            background: none;
            border: none;
            cursor: pointer;
        }
        
        /* Form styles */
        form {
            display: inline;
        }
        
        /* SVG styles */
        svg {
            fill: none;
            stroke: currentColor;
        }
        
        /* Responsive */
        @media (max-width: 640px) {
            .px-4 {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            
            .text-lg {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Desktop Warning Banner -->
    @if(session('mobile_warning'))
    <div class="desktop-warning" id="desktopWarning">
        <span>📱 Aplikasi ini dirancang untuk mobile. Untuk pengalaman terbaik, gunakan smartphone.</span>
        <button class="close-btn" onclick="closeDesktopWarning()">&times;</button>
    </div>
    @endif

    <div id="app" class="flex flex-col min-h-screen" @if(session('mobile_warning')) style="margin-top: 40px;" @endif>
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
            <div class="px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <h1 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Admin PWA')</h1>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="text-right">
                            <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                            @if(Auth::user()->isSales())
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full ml-2">Sales</span>
                            @elseif(Auth::user()->isOwner())
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full ml-2">Owner</span>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 pb-20">
            @yield('content')
        </main>

        <!-- Bottom Navigation -->
        <nav class="mobile-nav fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
            <div class="grid {{ Auth::user()->isOwner() ? 'grid-cols-5' : 'grid-cols-4' }}">
                <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('sales-transaction.index') }}" class="mobile-nav-item {{ request()->routeIs('sales-transaction.*') ? 'active' : '' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>PO</span>
                </a>
                
                @if(Auth::user()->isOwner())
                <a href="{{ route('reports.index') }}" class="mobile-nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Laporan</span>
                </a>
                @endif
                
                <a href="{{ route('data-barang.index') }}" class="mobile-nav-item {{ request()->routeIs('data-barang.*') ? 'active' : '' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>Barang</span>
                </a>
                
                <a href="{{ route('profile') }}" class="mobile-nav-item {{ request()->routeIs('profile*') ? 'active' : '' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profile</span>
                </a>
            </div>
        </nav>
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

    <!-- Desktop Warning Script -->
    <script>
        function closeDesktopWarning() {
            const warning = document.getElementById('desktopWarning');
            const app = document.getElementById('app');
            if (warning) {
                warning.remove();
                if (app) {
                    app.style.marginTop = '0';
                }
            }
        }
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('✅ ServiceWorker registered: ', registration.scope);

                        // Optional: auto detect update
                        registration.onupdatefound = () => {
                            const installingWorker = registration.installing;
                            installingWorker.onstatechange = () => {
                                if (installingWorker.state === 'installed') {
                                    if (navigator.serviceWorker.controller) {
                                        console.log('♻️ New ServiceWorker available, refresh to update.');
                                    } else {
                                        console.log('🎉 ServiceWorker ready for offline use.');
                                    }
                                }
                            };
                        };
                    })
                    .catch(error => {
                        console.error('❌ ServiceWorker registration failed:', error);
                    });
            });
        }
    </script>

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

    {{-- Inline Fallback Script - Critical CSS and offline handling --}}
    <script src="{{ asset('js/inline-fallback.js') }}"></script>
    
    {{-- Register Service Worker with Fallback --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw-simple.js')
                    .then(registration => {
                        // Service Worker registered successfully
                        console.log('SW registered:', registration.scope);
                    })
                    .catch(error => {
                        // Service Worker failed, use fallback
                        console.log('SW failed, using fallback');
                        window.fallbackMode = true;
                    });
            });
        } else {
            // No Service Worker support, use fallback
            window.fallbackMode = true;
        }
    </script>

    {{-- View-level pushed scripts (e.g., Select2) --}}
    @stack('scripts')
</body>
</html>
