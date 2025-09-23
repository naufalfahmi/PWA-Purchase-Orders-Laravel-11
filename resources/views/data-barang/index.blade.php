@extends('layouts.app')

@section('title', 'Data Barang - Munah - Purchase Orders')
@section('page-title', 'Data Barang')

@section('content')
<div class="p-4 space-y-4">
    <!-- Header Actions -->
    <div class="flex items-center space-x-3">
        <div class="relative flex-1">
            <input 
                type="text" 
                id="searchInput"
                placeholder="Cari barang..." 
                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full"
            >
            <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        
        <button id="filterToggle" class="flex items-center space-x-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex-shrink-0">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700">Filter</span>
        </button>
    </div>

    <!-- Supplier Filter Dropdown (Hidden by default) -->
    <div id="supplierFilterContainer" class="hidden">
        <div class="flex items-center space-x-2">
            <label for="supplierFilter" class="text-sm font-medium text-gray-700">Filter Supplier:</label>
            <select id="supplierFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" style="width: 300px;">
                <option value="">Semua Supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->kode_supplier }} - {{ $supplier->nama_supplier }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Loading Skeleton (shown on initial load) -->
    <div id="skeletonLoader" class="space-y-3">
        @for($i = 0; $i < 5; $i++)
            <div class="card p-4 animate-pulse">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="h-5 bg-gray-200 rounded w-48"></div>
                            <div class="h-6 bg-gray-200 rounded-full w-16"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-200 rounded w-32"></div>
                            <div class="h-4 bg-gray-200 rounded w-24"></div>
                            <div class="h-4 bg-gray-200 rounded w-28"></div>
                            <div class="h-4 bg-gray-200 rounded w-20"></div>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-2 ml-4">
                        <div class="h-4 bg-gray-200 rounded w-12"></div>
                        <div class="h-4 bg-gray-200 rounded w-8"></div>
                        <div class="h-4 bg-gray-200 rounded w-12"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>

    <!-- Products List -->
    <div id="productsList" class="space-y-3 hidden">
        @forelse($products as $product)
            <a href="{{ route('data-barang.show', $product) }}" class="block card p-4 product-item hover:shadow-lg transition-shadow duration-200 cursor-pointer" data-supplier-id="{{ $product->supplier_id }}" data-name="{{ $product->name }}" data-sku="{{ $product->sku }}" data-category="{{ $product->category }}">
                <div class="flex items-start space-x-4">
                    <!-- Product Icon/Image Placeholder -->
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    
                    <!-- Product Information -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $product->stock_status['class'] }} flex-shrink-0 ml-2">
                                {{ $product->stock_status['label'] }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-2">
                            <div>
                                <span class="font-medium text-gray-500">SKU:</span>
                                <span class="block">{{ $product->sku }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Kategori:</span>
                                <span class="block">{{ $product->category }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Harga:</span>
                                <span class="block text-green-600 font-medium">{{ $product->formatted_price }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Stok:</span>
                                <span class="block">
                                    @if($product->stock_quantity && $product->stock_unit)
                                        {{ $product->stock_quantity }} {{ strtolower($product->stock_unit) }}
                                    @else
                                        {{ $product->quantity_per_carton }} pcs
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-start">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="text-sm text-gray-600">
                                    @if($product->supplier)
                                        {{ $product->supplier->nama_supplier }}
                                    @else
                                        Supplier tidak tersedia
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        @if($product->description)
                            <p class="text-gray-500 text-xs mt-2 line-clamp-2">{{ $product->description }}</p>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada Data Barang</h3>
                <p class="text-gray-500 mb-4">Mulai dengan menambahkan barang pertama Anda</p>
                <a href="{{ route('data-barang.create') }}" class="btn-primary">
                    Tambah Barang
                </a>
            </div>
        @endforelse
    </div>

    <!-- Load More Button (Hidden) -->
    <div id="loadMoreContainer" class="text-center mt-6" style="display: none;">
        <button id="loadMoreBtn" class="btn-secondary">
            <span id="loadMoreText">Load More</span>
            <span id="loadMoreSpinner" class="hidden">
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            </span>
        </button>
    </div>

    <!-- No Results Found -->
    <div id="noResultsFound" class="text-center py-12 hidden">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Data Barang Tidak Ditemukan</h3>
        <p class="text-gray-500">Coba ubah kata kunci pencarian atau filter supplier</p>
    </div>

    <!-- End of Results -->
    <div id="endOfResults" class="text-center py-8 text-gray-500 hidden">
        <p>Tidak ada data lagi untuk ditampilkan</p>
    </div>

    <!-- Floating Action Button for Owners -->
    @if(auth()->user()->isOwner())
        <a href="{{ route('data-barang.create') }}" class="fixed bottom-20 right-4 w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center z-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </a>
    @endif
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let isLoading = false;
    let hasMorePages = {{ $products->hasMorePages() ? 'true' : 'false' }};
    let currentSupplierId = '';
    let currentSearchTerm = '';

    const skeletonLoader = document.getElementById('skeletonLoader');
    const productsList = document.getElementById('productsList');
    const loadMoreContainer = document.getElementById('loadMoreContainer');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const loadMoreText = document.getElementById('loadMoreText');
    const loadMoreSpinner = document.getElementById('loadMoreSpinner');
    const endOfResults = document.getElementById('endOfResults');
    const noResultsFound = document.getElementById('noResultsFound');
    const supplierFilter = document.getElementById('supplierFilter');
    const searchInput = document.getElementById('searchInput');
    const filterToggle = document.getElementById('filterToggle');
    const supplierFilterContainer = document.getElementById('supplierFilterContainer');

    // Show skeleton loader with 1-second delay
    function showSkeletonLoader() {
        skeletonLoader.classList.remove('hidden');
        productsList.classList.add('hidden');
        
        setTimeout(() => {
            skeletonLoader.classList.add('hidden');
            productsList.classList.remove('hidden');
        }, 1000);
    }

    // Initial page load with skeleton
    function initialPageLoad() {
        // Show skeleton immediately (it's already visible)
        // Hide products list initially (it's already hidden)
        // Hide no results message initially
        noResultsFound.classList.add('hidden');
        
        setTimeout(() => {
            skeletonLoader.classList.add('hidden');
            productsList.classList.remove('hidden');
        }, 1000);
    }

    // Load more products
    function loadMoreProducts() {
        if (isLoading || !hasMorePages) {
            return;
        }
        
        isLoading = true;
        currentPage++;

        const params = new URLSearchParams({
            page: currentPage
        });

        if (currentSupplierId) {
            params.append('supplier_id', currentSupplierId);
        }

        fetch(`{{ route('data-barang.load-more') }}?${params}`)
            .then(response => response.json())
            .then(data => {
                if (data.products && data.products.length > 0) {
                    data.products.forEach(product => {
                        const productHtml = createProductHtml(product);
                        productsList.insertAdjacentHTML('beforeend', productHtml);
                    });
                }

                hasMorePages = data.hasMorePages;
                
                if (!hasMorePages) {
                    endOfResults.classList.remove('hidden');
                }
            })
            .catch(error => {
                currentPage--; // Revert page increment on error
            })
            .finally(() => {
                isLoading = false;
            });
    }

    // Create product HTML
    function createProductHtml(product) {
        return `
            <div class="card p-4 product-item hover:shadow-lg transition-shadow duration-200" data-supplier-id="${product.supplier_id}" data-name="${product.name || ''}" data-sku="${product.sku || ''}" data-category="${product.category || ''}">
                <div class="flex items-start space-x-4">
                    <!-- Product Icon/Image Placeholder -->
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    
                    <!-- Product Information -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-900 truncate">${product.name}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${product.stock_status.class} flex-shrink-0 ml-2">
                                ${product.stock_status.label}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-2">
                            <div>
                                <span class="font-medium text-gray-500">SKU:</span>
                                <span class="block">${product.sku}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Kategori:</span>
                                <span class="block">${product.category}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Harga:</span>
                                <span class="block text-green-600 font-medium">${product.formatted_price}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Stok:</span>
                                <span class="block">${product.quantity_per_carton} pcs</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="text-sm text-gray-600">${product.supplier ? product.supplier.nama_supplier : 'Supplier tidak tersedia'}</span>
                            </div>
                        </div>
                        
                        ${product.description ? `<p class="text-gray-500 text-xs mt-2 line-clamp-2">${product.description}</p>` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    // Filter products by supplier
    function filterBySupplier(supplierId) {
        currentSupplierId = supplierId;
        currentPage = 1;
        hasMorePages = true;
        
        // Show skeleton loading when filtering
        showSkeletonLoader();
        
        setTimeout(() => {
            // Use the combined filter function that handles both search and supplier
            filterProductsBySearch();

            // Reset pagination for infinite scroll
            endOfResults.classList.add('hidden');
            
            // hasMorePages already initialized above
        }, 1000);
    }

    // Search products with skeleton loading
    function searchProducts(searchTerm) {
        currentSearchTerm = searchTerm.toLowerCase();
        
        // Show skeleton loading when searching
        if (searchTerm.length > 0) {
            showSkeletonLoader();
            
            setTimeout(() => {
                filterProductsBySearch();
            }, 1000);
        } else {
            // If search is empty, show all products immediately
            filterProductsBySearch();
        }
    }

    // Filter products by search term and supplier
    function filterProductsBySearch() {
        const productItems = document.querySelectorAll('.product-item');
        let visibleCount = 0;
        
        productItems.forEach(item => {
            const productName = (item.dataset.name || '').toLowerCase();
            const sku = (item.dataset.sku || '').toLowerCase();
            const category = (item.dataset.category || '').toLowerCase();
            const itemSupplierId = item.dataset.supplierId || '';
            
            // Check search term match across name, SKU, and category
            const searchMatch = currentSearchTerm === '' || 
                               productName.includes(currentSearchTerm) || 
                               sku.includes(currentSearchTerm) ||
                               category.includes(currentSearchTerm);
            
            // Check supplier filter match
            const supplierMatch = currentSupplierId === '' || 
                                 itemSupplierId === currentSupplierId;
            
            // Show item only if both search and supplier filters match
            if (searchMatch && supplierMatch) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0 && (currentSearchTerm !== '' || currentSupplierId !== '')) {
            noResultsFound.classList.remove('hidden');
        } else {
            noResultsFound.classList.add('hidden');
        }
    }

    // Toggle filter visibility
    function toggleFilter() {
        supplierFilterContainer.classList.toggle('hidden');
        
        // Update button appearance
        if (supplierFilterContainer.classList.contains('hidden')) {
            filterToggle.classList.remove('bg-blue-100', 'text-blue-700');
            filterToggle.classList.add('bg-gray-100', 'text-gray-600');
        } else {
            filterToggle.classList.remove('bg-gray-100', 'text-gray-600');
            filterToggle.classList.add('bg-blue-100', 'text-blue-700');
        }
    }

    // Event listeners
    filterToggle.addEventListener('click', toggleFilter);

    searchInput.addEventListener('input', function() {
        searchProducts(this.value);
    });

    // Infinite scroll functionality
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            const scrollPosition = window.innerHeight + window.scrollY;
            const documentHeight = document.body.offsetHeight;
            const threshold = documentHeight - 1000;
            
            if (scrollPosition >= threshold) {
                loadMoreProducts();
            }
        }, 100);
    });

    // Initialize Select2 for supplier filter
    function initializeSelect2() {
        if (typeof $ !== 'undefined') {
            $('#supplierFilter').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Supplier',
                allowClear: true,
                templateResult: function (data) {
                    if (!data.id) { return data.text; }
                    var full = (data.text || '').toString();
                    var parts = full.split(' - ');
                    var code = parts[0] || '';
                    var name = parts.slice(1).join(' - ') || code || full;
                    var html = '<div style="display:flex; align-items:center; justify-content:space-between; width:100%">'
                             +   '<span>' + name + '</span>'
                             +   '<span style="color:#6b7280; font-size:0.875rem;">' + code + '</span>'
                             + '</div>';
                    return $(html);
                },
                templateSelection: function (data) {
                    if (!data.id) { return data.text; }
                    var full = (data.text || '').toString();
                    var parts = full.split(' - ');
                    var name = parts.slice(1).join(' - ') || parts[0] || full;
                    return name;
                }
            }).on('select2:select select2:clear', function (e) {
                // Handle Select2 selection/clear events
                var supplierId = $(this).val();
                filterBySupplier(supplierId);
            });
        }
    }

    // Initialize page load with skeleton
    initialPageLoad();

    // Initialize Select2 after a short delay to ensure DOM is ready
    setTimeout(() => {
        initializeSelect2();
    }, 100);

    // Load more button is always hidden
    loadMoreContainer.style.display = 'none';
});
</script>
@endsection

<!-- Select2 CSS/JS -->
<link href="{{ asset('libs/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('libs/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">
<script src="{{ asset('libs/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('libs/select2.min.js') }}"></script>