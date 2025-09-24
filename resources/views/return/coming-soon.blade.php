@extends('layouts.app')

@section('title', 'Return - Coming Soon - Munah - Purchase Orders')
@section('page-title', 'Purchase Return')

@section('content')
<div class="min-h-screen bg-gray-50">

    <!-- Content -->
    <div class="p-4">
        <!-- Skeleton Loading -->
        <div id="skeletonLoader" class="space-y-4">
            <!-- Skeleton Card 1 -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm animate-pulse">
                <div class="bg-gradient-to-r from-gray-200 to-gray-300 rounded-t-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-300 rounded-lg"></div>
                            <div>
                                <div class="h-5 bg-gray-300 rounded w-32 mb-2"></div>
                                <div class="h-4 bg-gray-300 rounded w-24"></div>
                            </div>
                        </div>
                        <div class="h-6 bg-gray-300 rounded-full w-16"></div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-300 rounded w-16"></div>
                            <div class="h-4 bg-gray-300 rounded w-24"></div>
                            <div class="h-4 bg-gray-300 rounded w-20"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-300 rounded w-16"></div>
                            <div class="h-4 bg-gray-300 rounded w-20"></div>
                            <div class="h-4 bg-gray-300 rounded w-24"></div>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <div class="h-4 bg-gray-300 rounded w-32"></div>
                        <div class="h-4 bg-gray-300 rounded w-20"></div>
                    </div>
                </div>
            </div>

            <!-- Skeleton Card 2 -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm animate-pulse">
                <div class="bg-gradient-to-r from-gray-200 to-gray-300 rounded-t-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-300 rounded-lg"></div>
                            <div>
                                <div class="h-5 bg-gray-300 rounded w-28 mb-2"></div>
                                <div class="h-4 bg-gray-300 rounded w-20"></div>
                            </div>
                        </div>
                        <div class="h-6 bg-gray-300 rounded-full w-20"></div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-300 rounded w-18"></div>
                            <div class="h-4 bg-gray-300 rounded w-22"></div>
                            <div class="h-4 bg-gray-300 rounded w-16"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-300 rounded w-14"></div>
                            <div class="h-4 bg-gray-300 rounded w-18"></div>
                            <div class="h-4 bg-gray-300 rounded w-26"></div>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <div class="h-4 bg-gray-300 rounded w-28"></div>
                        <div class="h-4 bg-gray-300 rounded w-16"></div>
                    </div>
                </div>
            </div>

            <!-- Skeleton Card 3 -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm animate-pulse">
                <div class="bg-gradient-to-r from-gray-200 to-gray-300 rounded-t-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-300 rounded-lg"></div>
                            <div>
                                <div class="h-5 bg-gray-300 rounded w-30 mb-2"></div>
                                <div class="h-4 bg-gray-300 rounded w-22"></div>
                            </div>
                        </div>
                        <div class="h-6 bg-gray-300 rounded-full w-18"></div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-300 rounded w-20"></div>
                            <div class="h-4 bg-gray-300 rounded w-26"></div>
                            <div class="h-4 bg-gray-300 rounded w-18"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-300 rounded w-16"></div>
                            <div class="h-4 bg-gray-300 rounded w-24"></div>
                            <div class="h-4 bg-gray-300 rounded w-20"></div>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <div class="h-4 bg-gray-300 rounded w-30"></div>
                        <div class="h-4 bg-gray-300 rounded w-18"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coming Soon Content (Hidden initially) -->
        <div id="comingSoonContent" class="hidden">
            <!-- Simple Coming Soon Placeholder -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-8 text-center">
                <div class="mb-6">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m5 14v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2h12a2 2 0 002-2z"></path>
                        </svg>
                    </div>
                </div>
                
                <h2 class="text-2xl font-semibold text-gray-700 mb-2">Coming Soon</h2>
                <p class="text-gray-500 mb-6">Fitur Return sedang dalam pengembangan</p>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">Coming Soon</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const skeletonLoader = document.getElementById('skeletonLoader');
    const comingSoonContent = document.getElementById('comingSoonContent');
    
    // Show skeleton loading for 2 seconds
    setTimeout(() => {
        // Fade out skeleton
        skeletonLoader.style.opacity = '0';
        skeletonLoader.style.transition = 'opacity 0.5s ease-out';
        
        setTimeout(() => {
            skeletonLoader.classList.add('hidden');
            comingSoonContent.classList.remove('hidden');
            
            // Fade in content
            comingSoonContent.style.opacity = '0';
            comingSoonContent.style.transition = 'opacity 0.5s ease-in';
            
            setTimeout(() => {
                comingSoonContent.style.opacity = '1';
            }, 50);
        }, 500);
    }, 2000);
});
</script>
@endsection
