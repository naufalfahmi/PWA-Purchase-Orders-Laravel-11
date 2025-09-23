@extends('layouts.app')

@section('title', 'Dashboard - Munah - Purchase Orders')
@section('page-title', 'Dashboard')

@section('content')
<div class="p-4 space-y-6">
    <!-- Loading Skeleton (shown on initial load) -->
    <div id="skeletonLoader" class="space-y-6">
        <!-- Stats Cards Skeleton -->
        <div class="grid grid-cols-2 gap-4">
            @for($i = 0; $i < 4; $i++)
                <div class="card p-4 animate-pulse">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="h-4 bg-gray-200 rounded w-16 mb-2"></div>
                            <div class="h-8 bg-gray-200 rounded w-12"></div>
                        </div>
                        <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                    </div>
                </div>
            @endfor
            
            @if(auth()->user()->isOwner())
                @for($i = 0; $i < 2; $i++)
                    <div class="card p-4 animate-pulse">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="h-4 bg-gray-200 rounded w-20 mb-2"></div>
                                <div class="h-8 bg-gray-200 rounded w-12"></div>
                            </div>
                            <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                        </div>
                    </div>
                @endfor
            @endif
        </div>

        <!-- Quick Actions Skeleton -->
        <div class="card p-4 animate-pulse">
            <div class="h-6 bg-gray-200 rounded w-32 mb-4"></div>
            <div class="grid grid-cols-2 gap-3">
                @for($i = 0; $i < 2; $i++)
                    <div class="flex items-center justify-center p-4 bg-gray-100 rounded-lg">
                        <div class="text-center">
                            <div class="w-10 h-10 bg-gray-200 rounded-full mx-auto mb-2"></div>
                            <div class="h-4 bg-gray-200 rounded w-16 mx-auto"></div>
                        </div>
                    </div>
                @endfor
                @if(auth()->user()->isOwner())
                <div class="flex items-center justify-center p-4 bg-gray-100 rounded-lg">
                    <div class="text-center">
                        <div class="w-10 h-10 bg-gray-200 rounded-full mx-auto mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-16 mx-auto"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent PO Skeleton -->
        <div class="card p-4 animate-pulse">
            <div class="flex items-center justify-between mb-4">
                <div class="h-6 bg-gray-200 rounded w-48"></div>
                <div class="h-4 bg-gray-200 rounded w-20"></div>
            </div>
            <div class="space-y-3">
                @for($i = 0; $i < 3; $i++)
                    <div class="flex items-center justify-between p-3 bg-gray-100 rounded-lg">
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 rounded w-32 mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-48"></div>
                        </div>
                        <div class="w-16 h-6 bg-gray-200 rounded-full"></div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Actual Content (hidden initially) -->
    <div id="dashboardContent" class="hidden">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4">
        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total PO</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pos'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending PO</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_pos'] }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Approved PO</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['approved_pos'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Rejected PO</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['rejected_pos'] }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>

        @if(auth()->user()->isOwner())
        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_products'] }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="card p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('sales-transaction.bulk-create') }}" class="flex items-center justify-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <div class="text-center">
                    <div class="w-10 h-10 bg-blue-600 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-blue-700">Buat PO</span>
                </div>
            </a>

            @if(auth()->user()->isOwner())
            <a href="{{ route('data-barang.create') }}" class="flex items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <div class="text-center">
                    <div class="w-10 h-10 bg-green-600 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-green-700">Tambah Barang</span>
                </div>
            </a>
            @else
            <a href="{{ route('sales-transaction.index') }}" class="flex items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="text-center">
                    <div class="w-10 h-10 bg-gray-600 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Lihat PO</span>
                </div>
            </a>
            @endif

            @if(auth()->user()->isOwner())
            <a href="{{ route('reports.index') }}" class="flex items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <div class="text-center">
                    <div class="w-10 h-10 bg-purple-600 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-purple-700">Laporan</span>
                </div>
            </a>
            @endif
        </div>
    </div>

    <!-- Recent Sales Transactions -->
    <div class="card p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Recent Purchase Orders</h2>
            <a href="{{ route('sales-transaction.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                Lihat Semua
            </a>
        </div>
        
        @if($recentPOs->count() > 0)
            <div class="space-y-3">
                @foreach($recentPOs as $po)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $po->po_number }}</p>
                            <p class="text-sm text-gray-600">{{ $po->total_items }} items • {{ $po->sales->name ?? 'N/A' }} • Rp {{ number_format($po->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $po->approval_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($po->approval_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($po->approval_status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500">Belum ada Purchase Order</p>
                <a href="{{ route('sales-transaction.bulk-create') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Buat PO pertama
                </a>
            </div>
        @endif
    </div>
    </div> <!-- Close dashboardContent div -->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const skeletonLoader = document.getElementById('skeletonLoader');
    const dashboardContent = document.getElementById('dashboardContent');

    // Show skeleton loading on initial page load with 1-second delay
    setTimeout(() => {
        skeletonLoader.classList.add('hidden');
        dashboardContent.classList.remove('hidden');
    }, 1000);
});
</script>
@endsection
