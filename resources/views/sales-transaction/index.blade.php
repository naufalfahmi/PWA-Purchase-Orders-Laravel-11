@extends('layouts.app')

@section('title', 'Purchase Orders - Admin PWA')
@section('page-title', 'Purchase Orders')


@section('content')
<div class="p-4 space-y-4">
    <!-- Header Actions -->
    <div class="flex items-center space-x-3">
        <div class="relative flex-1">
            <form method="GET" action="{{ route('sales-transaction.index') }}" class="relative flex items-center" id="searchForm">
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari Purchase Order" 
                    class="pl-10 pr-10 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    id="searchInput"
                >
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                @if(request('search'))
                <button type="button" onclick="clearSearch()" class="absolute right-3 top-2.5 w-4 h-4 text-gray-400 hover:text-gray-600">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                @endif
                <!-- Preserve other filter parameters -->
                @if(request('approval_status'))
                    <input type="hidden" name="approval_status" value="{{ request('approval_status') }}">
                @endif
                @if(request('toko'))
                    <input type="hidden" name="toko" value="{{ request('toko') }}">
                @endif
                @if(request('start_date'))
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                @endif
                @if(request('end_date'))
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                @endif
                @if(request('supplier_id'))
                    <input type="hidden" name="supplier_id" value="{{ request('supplier_id') }}">
                @endif
                @if(request('sales_id'))
                    <input type="hidden" name="sales_id" value="{{ request('sales_id') }}">
                @endif
            </form>
        </div>
        
        <button id="filterToggle" class="flex items-center space-x-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex-shrink-0">
            <svg id="filterIcon" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
            </svg>
            <span id="filterText" class="text-sm font-medium text-gray-700">Filter</span>
            <span id="activeFilterCount" class="hidden bg-blue-500 text-white text-xs rounded-full px-2 py-0.5 ml-1">0</span>
        </button>
    </div>

    <!-- Filter Options (Hidden by default) -->
    <div id="additionalFilterContainer" class="hidden">
        <div class="bg-white rounded-lg shadow-lg border border-gray-200">
            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200">
                <button onclick="switchFilterTab('toko')" id="tabToko" class="flex-1 px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-b-2 border-transparent transition-colors filter-tab active">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Toko</span>
                    </div>
                </button>
                <button onclick="switchFilterTab('tanggal')" id="tabTanggal" class="flex-1 px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-b-2 border-transparent transition-colors filter-tab">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Tanggal</span>
                    </div>
                </button>
                <button onclick="switchFilterTab('supplier')" id="tabSupplier" class="flex-1 px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-b-2 border-transparent transition-colors filter-tab">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Supplier</span>
                    </div>
                </button>
            </div>
            
            <!-- Tab Content -->
            <div class="p-4">
                <!-- Toko Tab Content -->
                <div id="contentToko" class="filter-tab-content">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Toko</label>
                            <select id="tokoFilter" class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                <option value="">Semua Toko</option>
                                @if(isset($orderAccOptions))
                                    @foreach($orderAccOptions as $toko)
                                        <option value="{{ $toko->name }}" {{ request('toko') == $toko->name ? 'selected' : '' }}>{{ $toko->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Tanggal Tab Content -->
                <div id="contentTanggal" class="filter-tab-content hidden">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengiriman Mulai</label>
                            <input type="date" id="startDateFilter" class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white" value="{{ request('start_date') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengiriman Akhir</label>
                            <input type="date" id="endDateFilter" class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white" value="{{ request('end_date') }}">
                        </div>
                    </div>
                </div>
                
                <!-- Supplier Tab Content -->
                <div id="contentSupplier" class="filter-tab-content hidden">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Supplier</label>
                            <select id="supplierFilter" class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                <option value="">Semua Supplier</option>
                                @if(isset($supplierList) && $supplierList->count() > 0)
                                    @foreach($supplierList as $supplier)
                                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->nama_supplier }}</option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Data supplier tidak tersedia</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filter Actions -->
            <div class="flex space-x-3 p-4 bg-gray-50 border-t border-gray-200">
                <button onclick="clearFilters()" class="flex-1 px-4 py-3 text-base font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg transition-colors">
                    Reset
                </button>
                <button onclick="applyAdditionalFilters()" class="flex-1 px-4 py-3 text-base font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    Terapkan
                </button>
            </div>
        </div>
    </div>

    <!-- Status Filter -->
    <div class="flex space-x-2 overflow-x-auto pb-2">
        <button onclick="filterByStatus('')" class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors {{ request('approval_status') === null ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Semua
        </button>
        <button onclick="filterByStatus('pending')" class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors {{ request('approval_status') === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Pending
        </button>
        <button onclick="filterByStatus('approved')" class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors {{ request('approval_status') === 'approved' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Approved
        </button>
        <button onclick="filterByStatus('rejected')" class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors {{ request('approval_status') === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Rejected
        </button>
    </div>

    <!-- Purchase Orders List -->
    <div id="transactions-container" class="space-y-3">
        <!-- Initial skeleton loading will be shown here by JavaScript -->
        @include('sales-transaction.partials.po-list', ['poList' => $poList])
    </div>

    <!-- Loading Indicator -->
    <div id="loading-indicator" class="text-center py-4 hidden">
        <div class="inline-flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-600">Loading more PO...</span>
        </div>
    </div>

    <!-- End of Results -->
    <div id="end-of-results" class="text-center py-4 hidden">
        <p class="text-gray-500 text-sm">No more PO to load</p>
    </div>

    <!-- Floating Action Button -->
    <a href="{{ route('sales-transaction.bulk-create') }}" class="fixed bottom-20 right-4 w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center z-50">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
    </a>
</div>

<style>
/* Prevent scroll when loading */
body.loading {
    overflow: hidden;
}

/* Filter active state */
.filter-active {
    background-color: #3b82f6 !important;
    color: white !important;
}

.filter-active svg {
    color: white !important;
}

.filter-active #filterText {
    color: white !important;
}

/* Tab Navigation Styles */
.filter-tab {
    position: relative;
    cursor: pointer;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}

.filter-tab:hover {
    background-color: #f8fafc;
}

.filter-tab.active {
    color: #3b82f6 !important;
    border-bottom-color: #3b82f6 !important;
    background-color: #f8fafc;
}

.filter-tab.active svg {
    color: #3b82f6 !important;
}

/* Ensure tab buttons are clickable */
.filter-tab {
    pointer-events: auto;
    cursor: pointer;
}

.filter-tab * {
    pointer-events: auto;
}

/* Make sure tab content is clickable */
.filter-tab-content {
    pointer-events: auto;
    animation: fadeIn 0.2s ease-in-out;
}

/* Filter container animations */
#additionalFilterContainer {
    transition: all 0.3s ease-in-out;
}

#additionalFilterContainer.hidden {
    opacity: 0;
    transform: translateY(-10px);
    pointer-events: none;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mobile-optimized filter styles */
@media (max-width: 768px) {
    #additionalFilterContainer {
        margin: 0 -1rem;
        border-radius: 0;
    }
    
    #additionalFilterContainer .bg-gray-50 {
        border-radius: 0;
        padding: 1rem;
    }
    
    /* Larger touch targets for mobile */
    #additionalFilterContainer select,
    #additionalFilterContainer input[type="date"] {
        min-height: 48px;
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    #additionalFilterContainer button {
        min-height: 48px;
        font-size: 16px;
    }
    
    /* Better spacing for mobile */
    #additionalFilterContainer .space-y-4 > * + * {
        margin-top: 1rem;
    }
    
    /* Mobile tab optimization */
    .filter-tab {
        min-height: 48px;
        padding: 12px 16px;
        font-size: 14px;
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
    }
    
    .filter-tab:active {
        background-color: #e5e7eb;
        transform: scale(0.98);
    }
}

/* Ensure date inputs are clickable */
input[type="date"] {
    cursor: pointer;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    padding: 4px;
    margin-left: 4px;
}


/* Skeleton loading styles */
.skeleton-item {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
</style>

<script>
let currentPage = 1;
let isLoading = false;
let hasMorePages = true;

// Infinite scroll functionality
function initInfiniteScroll() {
    const loadingIndicator = document.getElementById('loading-indicator');
    const endOfResults = document.getElementById('end-of-results');
    
    // Throttle scroll events to prevent excessive calls
    let scrollTimeout;
    
    window.addEventListener('scroll', function() {
        if (isLoading || !hasMorePages) return;
        
        // Clear previous timeout
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        
        // Throttle scroll events
        scrollTimeout = setTimeout(function() {
            const scrollPosition = window.innerHeight + window.scrollY;
            const documentHeight = document.body.offsetHeight;
            const threshold = documentHeight - 500;
            
            // Check if user has scrolled near the bottom (more conservative threshold)
            if (scrollPosition >= threshold) {
                loadMorePO();
            }
        }, 100); // 100ms throttle
    });
}

function loadMorePO() {
    if (isLoading || !hasMorePages) {
        return;
    }
    
    isLoading = true;
    currentPage++;
    
    const loadingIndicator = document.getElementById('loading-indicator');
    const endOfResults = document.getElementById('end-of-results');
    
    // Only show loading indicator if there's actually data to load
    let skeletonId = null;
    if (hasMorePages) {
        loadingIndicator.classList.remove('hidden');
        // Prevent scroll during infinite scroll loading
        preventScroll();
        skeletonId = showInfiniteScrollSkeleton();
    }
    
    // Get current filter parameters
    const params = new URLSearchParams();
    params.append('page', currentPage);
    
    // Add any existing filter parameters
    const urlParams = new URLSearchParams(window.location.search);
    ['start_date', 'end_date', 'sales_id', 'approval_status', 'search', 'toko', 'supplier_id'].forEach(param => {
        if (urlParams.get(param)) {
            params.append(param, urlParams.get(param));
        }
    });
    
    // Add 2-second delay to see skeleton loading
    setTimeout(() => {
        fetch(`{{ route('sales-transaction.load-more') }}?${params.toString()}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Remove skeleton if it exists
        if (skeletonId) {
            const skeletonElement = document.getElementById(skeletonId);
            if (skeletonElement) {
                skeletonElement.remove();
            }
        }
        
        if (data.html && data.html.trim() !== '') {
            // Append new PO items to container
            const container = document.getElementById('transactions-container');
            container.insertAdjacentHTML('beforeend', data.html);
        }
        
        hasMorePages = data.hasMore;
        
        if (!hasMorePages) {
            endOfResults.classList.remove('hidden');
        }
        loadingIndicator.classList.add('hidden');
    })
    .catch(error => {
        // Remove skeleton if it exists
        if (skeletonId) {
            const skeletonElement = document.getElementById(skeletonId);
            if (skeletonElement) {
                skeletonElement.remove();
            }
        }
        
        currentPage--; // Revert page increment on error
        hasMorePages = false; // Stop trying to load more on error
    })
    .finally(() => {
        isLoading = false;
        loadingIndicator.classList.add('hidden');
        enableScroll();
    });
    }, 500); // 500ms delay
}

// Filter function
function filterByStatus(status) {
    const url = new URL('{{ route("sales-transaction.index") }}', window.location.origin);
    
    // Preserve existing filter parameters
    const urlParams = new URLSearchParams(window.location.search);
    ['search', 'toko', 'start_date', 'end_date', 'supplier_id'].forEach(param => {
        if (urlParams.get(param)) {
            url.searchParams.set(param, urlParams.get(param));
        }
    });
    
    // Set or remove approval status
    if (status) {
        url.searchParams.set('approval_status', status);
    } else {
        url.searchParams.delete('approval_status');
    }
    
    window.location.href = url.toString();
}


// Clear search function
function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    searchInput.value = '';
    performSearch();
}

// Skeleton loading functions
function showSkeletonLoading() {
    const container = document.getElementById('transactions-container');
    container.innerHTML = `
        <div class="space-y-4">
            ${Array(3).fill(0).map(() => `
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
            `).join('')}
        </div>
    `;
}

function hideSkeletonLoading() {
    // Skeleton will be replaced by actual content
}

function showInfiniteScrollSkeleton() {
    const container = document.getElementById('transactions-container');
    const skeletonId = 'infinite-scroll-skeleton-' + Date.now();
    const skeleton = `
        <div id="${skeletonId}" class="bg-white border border-gray-200 rounded-lg shadow-sm animate-pulse">
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
    `;
    container.insertAdjacentHTML('beforeend', skeleton);
    return skeletonId;
}

// Prevent scroll during loading
function preventScroll() {
    document.body.classList.add('loading');
}

// Enable scroll after loading
function enableScroll() {
    document.body.classList.remove('loading');
}

// Initial skeleton loading functions
function showInitialSkeletonLoading() {
    const container = document.getElementById('transactions-container');
    if (container) {
        // Store the original content
        const originalContent = container.innerHTML;
        container.setAttribute('data-original-content', originalContent);
        
        // Prevent scroll during loading
        preventScroll();
        
        // Show skeleton loading
        container.innerHTML = `
            <div class="space-y-4">
                ${Array(4).fill(0).map(() => `
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
                `).join('')}
            </div>
        `;
    }
}

function hideInitialSkeletonLoading() {
    const container = document.getElementById('transactions-container');
    if (container) {
        const originalContent = container.getAttribute('data-original-content');
        if (originalContent) {
            container.innerHTML = originalContent;
            container.removeAttribute('data-original-content');
        }
    }
    // Enable scroll after loading
    enableScroll();
}




// Auto-search with debounce using AJAX
let searchTimeout;
let isSearching = false;

function autoSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        performSearch();
    }, 500); // 500ms delay
}

function performSearch() {
    if (isSearching) return;
    
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.trim();
    
    // Show loading indicator and skeleton
    const loadingIndicator = document.getElementById('loading-indicator');
    const endOfResults = document.getElementById('end-of-results');
    const container = document.getElementById('transactions-container');
    
    isSearching = true;
    loadingIndicator.classList.remove('hidden');
    endOfResults.classList.add('hidden');
    
    // Prevent scroll and show skeleton loading
    preventScroll();
    showSkeletonLoading();
    
    // Get current filter parameters
    const params = new URLSearchParams();
    params.append('search', searchTerm);
    params.append('page', 1); // Reset to first page
    
    // Add any existing filter parameters
    const urlParams = new URLSearchParams(window.location.search);
    ['approval_status', 'start_date', 'end_date', 'sales_id', 'toko', 'supplier_id'].forEach(param => {
        if (urlParams.get(param)) {
            params.append(param, urlParams.get(param));
        }
    });
    
    const url = `{{ route('sales-transaction.load-more') }}?${params.toString()}`;
    
    // Add short delay to show loading overlay
    setTimeout(() => {
        fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
    .then(response => {
        
        if (!response.ok) {
            // Try to get the response text to see what's being returned
            return response.text().then(text => {
                throw new Error(`Network response was not ok: ${response.status} - ${text.substring(0, 200)}`);
            });
        }
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                
                // Check if it's an HTML error page
                if (text.includes('<!DOCTYPE html>') || text.includes('<html')) {
                }
                
                throw new Error('Response is not JSON. Content-Type: ' + contentType + '. Response: ' + text.substring(0, 200));
            });
        }
        
        return response.json();
    })
    .then(data => {
        // Replace container content with search results
        container.innerHTML = data.html || '<div class="text-center py-8"><p class="text-gray-500">Tidak ada hasil ditemukan</p></div>';
        
        // Update pagination state
        hasMorePages = data.hasMore;
        currentPage = 1; // Reset to first page
        
        if (!hasMorePages) {
            endOfResults.classList.remove('hidden');
        }
        
        // Update URL without reload
        const newUrl = new URL(window.location);
        if (searchTerm) {
            newUrl.searchParams.set('search', searchTerm);
        } else {
            newUrl.searchParams.delete('search');
        }
        window.history.pushState({}, '', newUrl);
    })
    .catch(error => {
        container.innerHTML = '<div class="text-center py-8"><p class="text-red-500">Error saat mencari data: ' + error.message + '</p></div>';
    })
    .finally(() => {
        isSearching = false;
        loadingIndicator.classList.add('hidden');
        enableScroll();
    });
    }, 500); // 500ms delay
}

// Auto-submit search form on Enter key and auto-search on input
document.addEventListener('DOMContentLoaded', function() {
    // Show initial skeleton loading
    showInitialSkeletonLoading();
    
    // Hide skeleton and show real content after a short delay
    setTimeout(() => {
        hideInitialSkeletonLoading();
    }, 2000); // 2 second delay for initial load
    
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Auto-search as you type
        searchInput.addEventListener('input', function() {
            autoSearch();
        });
        
        // Submit immediately on Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                performSearch();
            }
        });
    }
    
    // Check if we have initial data and set hasMorePages accordingly
    const container = document.getElementById('transactions-container');
    const initialItems = container.querySelectorAll('.po-item');
    const emptyState = container.querySelector('.text-center.py-8');
    
    // Wait for skeleton to be replaced first, then check items
    setTimeout(() => {
        // Wait for skeleton replacement to complete
        setTimeout(() => {
            const finalItems = container.querySelectorAll('.po-item');
            const finalEmptyState = container.querySelector('.text-center.py-8');
            
            // If there's an empty state or no items at all, no more pages
            if (finalEmptyState || finalItems.length === 0) {
                hasMorePages = false;
                document.getElementById('end-of-results').classList.remove('hidden');
            } else {
                // Always assume there might be more pages unless we know for sure there aren't
                hasMorePages = true;
            }
        }, 100); // Wait for skeleton replacement to complete
    }, 2000); // Wait for skeleton to be hidden first
    
    initInfiniteScroll();
    
    // Filter functionality
    const filterToggle = document.getElementById('filterToggle');
    const additionalFilterContainer = document.getElementById('additionalFilterContainer');
    const tokoFilter = document.getElementById('tokoFilter');
    const startDateFilter = document.getElementById('startDateFilter');
    const endDateFilter = document.getElementById('endDateFilter');
    const supplierFilter = document.getElementById('supplierFilter');

    // Toggle additional filter visibility
    function toggleAdditionalFilter() {
        additionalFilterContainer.classList.toggle('hidden');
        updateFilterStatus();
    }
    
    // Close filter form with animation
    function closeFilterForm() {
        if (additionalFilterContainer && !additionalFilterContainer.classList.contains('hidden')) {
            additionalFilterContainer.classList.add('hidden');
        }
    }

    // Update filter button status based on active filters
    function updateFilterStatus() {
        const filterToggle = document.getElementById('filterToggle');
        const filterIcon = document.getElementById('filterIcon');
        const filterText = document.getElementById('filterText');
        const activeFilterCount = document.getElementById('activeFilterCount');
        
        let activeCount = 0;
        
        // Check if any filters are active
        if (tokoFilter && tokoFilter.value) activeCount++;
        if (startDateFilter && startDateFilter.value) activeCount++;
        if (endDateFilter && endDateFilter.value) activeCount++;
        if (supplierFilter && supplierFilter.value) activeCount++;
        
        if (activeCount > 0) {
            filterToggle.classList.add('filter-active');
            filterText.textContent = 'Filtered';
            activeFilterCount.textContent = activeCount;
            activeFilterCount.classList.remove('hidden');
        } else {
            filterToggle.classList.remove('filter-active');
            filterText.textContent = 'Filter';
            activeFilterCount.classList.add('hidden');
        }
    }

    // Switch filter tab
    function switchFilterTab(tabName) {
        // Remove active class from all tabs
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Hide all tab contents
        document.querySelectorAll('.filter-tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Show selected tab content
        const selectedTab = document.getElementById(`tab${tabName.charAt(0).toUpperCase() + tabName.slice(1)}`);
        const selectedContent = document.getElementById(`content${tabName.charAt(0).toUpperCase() + tabName.slice(1)}`);
        
        if (selectedTab && selectedContent) {
            selectedTab.classList.add('active');
            selectedContent.classList.remove('hidden');
        }
    }

    // Apply additional filters
    function applyAdditionalFilters() {
        // Close filter form first
        closeFilterForm();
        
        // Prevent scroll before redirect
        preventScroll();
        
        const url = new URL('{{ route("sales-transaction.index") }}', window.location.origin);
        
        // Preserve existing search and approval status
        const urlParams = new URLSearchParams(window.location.search);
        ['search', 'approval_status'].forEach(param => {
            if (urlParams.get(param)) {
                url.searchParams.set(param, urlParams.get(param));
            }
        });
        
        // Add new filter values
        if (tokoFilter && tokoFilter.value) {
            url.searchParams.set('toko', tokoFilter.value);
        }
        if (startDateFilter && startDateFilter.value) {
            url.searchParams.set('start_date', startDateFilter.value);
        }
        if (endDateFilter && endDateFilter.value) {
            url.searchParams.set('end_date', endDateFilter.value);
        }
        if (supplierFilter && supplierFilter.value) {
            url.searchParams.set('supplier_id', supplierFilter.value);
        }
        
        // Small delay before redirect
        setTimeout(() => {
            window.location.href = url.toString();
        }, 100);
    }

    // Clear all filters
    function clearFilters() {
        // Close filter form first
        closeFilterForm();
        
        // Get fresh references to filter elements
        const tokoFilterEl = document.getElementById('tokoFilter');
        const startDateFilterEl = document.getElementById('startDateFilter');
        const endDateFilterEl = document.getElementById('endDateFilter');
        const supplierFilterEl = document.getElementById('supplierFilter');
        
        // Clear all filter values
        if (tokoFilterEl) {
            tokoFilterEl.value = '';
        }
        
        if (startDateFilterEl) {
            startDateFilterEl.value = '';
        }
        
        if (endDateFilterEl) {
            endDateFilterEl.value = '';
        }
        
        if (supplierFilterEl) {
            supplierFilterEl.value = '';
        }
        
        // Update filter status
        updateFilterStatus();
        
        // Redirect to clean URL
        const url = new URL('{{ route("sales-transaction.index") }}', window.location.origin);
        
        // Preserve existing search and approval status
        const urlParams = new URLSearchParams(window.location.search);
        ['search', 'approval_status'].forEach(param => {
            if (urlParams.get(param)) {
                url.searchParams.set(param, urlParams.get(param));
            }
        });
        
        window.location.href = url.toString();
    }
    
    // Make functions globally accessible
    window.clearFilters = clearFilters;
    window.applyAdditionalFilters = applyAdditionalFilters;

    // Event listeners
    if (filterToggle) {
        filterToggle.addEventListener('click', toggleAdditionalFilter);
    }

    if (tokoFilter) {
        tokoFilter.addEventListener('change', updateFilterStatus);
    }

    if (startDateFilter) {
        startDateFilter.addEventListener('change', updateFilterStatus);
    }

    if (endDateFilter) {
        endDateFilter.addEventListener('change', updateFilterStatus);
    }

    if (supplierFilter) {
        supplierFilter.addEventListener('change', updateFilterStatus);
    }

    // Initialize filter status on page load
    updateFilterStatus();
    
    // Add event listener for reset button as backup
    const resetButton = document.querySelector('button[onclick="clearFilters()"]');
    if (resetButton) {
        resetButton.addEventListener('click', function(e) {
            e.preventDefault();
            clearFilters();
        });
    }
    
    // Add event listener for apply button as backup
    const applyButton = document.querySelector('button[onclick="applyAdditionalFilters()"]');
    if (applyButton) {
        applyButton.addEventListener('click', function(e) {
            e.preventDefault();
            applyAdditionalFilters();
        });
    }
    
    // Add click event listeners for tabs as backup
    const tabToko = document.getElementById('tabToko');
    const tabTanggal = document.getElementById('tabTanggal');
    const tabSupplier = document.getElementById('tabSupplier');
    
    if (tabToko) {
        tabToko.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            switchFilterTab('toko');
        });
    }
    
    if (tabTanggal) {
        tabTanggal.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            switchFilterTab('tanggal');
        });
    }
    
    if (tabSupplier) {
        tabSupplier.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            switchFilterTab('supplier');
        });
    }
    
});
</script>

@endsection
