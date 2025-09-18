@extends('layouts.app')

@section('title', 'Purchase Orders - Admin PWA')
@section('page-title', 'Purchase Orders')

@section('content')
<div class="p-4 space-y-4">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            
            <form method="GET" action="{{ route('sales-transaction.index') }}" class="relative flex items-center" id="searchForm">
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari Purchase Order" 
                    class="pl-10 pr-10 py-2 w-59 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                @if(request('start_date'))
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                @endif
                @if(request('end_date'))
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                @endif
                @if(request('sales_id'))
                    <input type="hidden" name="sales_id" value="{{ request('sales_id') }}">
                @endif
            </form>
        </div>
        
        <a href="{{ route('sales-transaction.bulk-create') }}" class="btn-primary flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span>Buat PO</span>
        </a>
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
</div>


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
        skeletonId = showInfiniteScrollSkeleton();
    }
    
    // Get current filter parameters
    const params = new URLSearchParams();
    params.append('page', currentPage);
    
    // Add any existing filter parameters
    const urlParams = new URLSearchParams(window.location.search);
    ['start_date', 'end_date', 'sales_id', 'approval_status', 'search'].forEach(param => {
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
    });
    }, 2000); // 2-second delay
}

// Filter function
function filterByStatus(status) {
    const url = new URL('{{ route("sales-transaction.index") }}', window.location.origin);
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

// Initial skeleton loading functions
function showInitialSkeletonLoading() {
    const container = document.getElementById('transactions-container');
    if (container) {
        // Store the original content
        const originalContent = container.innerHTML;
        container.setAttribute('data-original-content', originalContent);
        
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
    
    // Show skeleton loading
    showSkeletonLoading();
    
    // Get current filter parameters
    const params = new URLSearchParams();
    params.append('search', searchTerm);
    params.append('page', 1); // Reset to first page
    
    // Add any existing filter parameters
    const urlParams = new URLSearchParams(window.location.search);
    ['approval_status', 'start_date', 'end_date', 'sales_id'].forEach(param => {
        if (urlParams.get(param)) {
            params.append(param, urlParams.get(param));
        }
    });
    
    const url = `{{ route('sales-transaction.load-more') }}?${params.toString()}`;
    
    // Add 2-second delay to see skeleton loading
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
    });
    }, 2000); // 2-second delay
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
});
</script>
@endsection
