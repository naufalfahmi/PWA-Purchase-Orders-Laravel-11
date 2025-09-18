@extends('layouts.app')

@section('title', 'Laporan PO')
@section('page-title', 'Laporan PO')

@section('content')
<!-- Loading Skeleton -->
<div id="loadingSkeleton" class="p-3 space-y-4">
    <!-- Summary Cards Skeleton -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="animate-pulse">
                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-6 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="animate-pulse">
                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-6 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="animate-pulse">
                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-6 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="animate-pulse">
                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-6 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>
    </div>

    <!-- Filter Form Skeleton -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="animate-pulse">
            <div class="h-6 bg-gray-200 rounded w-1/4 mb-4"></div>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="h-4 bg-gray-200 rounded w-1/3 mb-2"></div>
                        <div class="h-10 bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 bg-gray-200 rounded w-1/3 mb-2"></div>
                        <div class="h-10 bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="h-4 bg-gray-200 rounded w-1/3 mb-2"></div>
                        <div class="h-10 bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 bg-gray-200 rounded w-1/3 mb-2"></div>
                        <div class="h-10 bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div>
                    <div class="h-4 bg-gray-200 rounded w-1/4 mb-2"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 bg-gray-200 rounded w-1/4 mb-2"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 bg-gray-200 rounded w-1/4 mb-2"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
                <div class="flex gap-2">
                    <div class="h-10 bg-gray-200 rounded flex-1"></div>
                    <div class="h-10 bg-gray-200 rounded w-20"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Section Skeleton -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="animate-pulse">
            <div class="h-6 bg-gray-200 rounded w-1/3 mb-4"></div>
            <div class="grid grid-cols-2 gap-3">
                <div class="h-12 bg-gray-200 rounded"></div>
                <div class="h-12 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Charts Skeleton -->
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="animate-pulse">
                <div class="h-6 bg-gray-200 rounded w-1/4 mb-4"></div>
                <div class="h-64 bg-gray-200 rounded"></div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="animate-pulse">
                <div class="h-6 bg-gray-200 rounded w-1/4 mb-4"></div>
                <div class="h-64 bg-gray-200 rounded"></div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="animate-pulse">
                    <div class="h-6 bg-gray-200 rounded w-1/3 mb-4"></div>
                    <div class="h-64 bg-gray-200 rounded"></div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="animate-pulse">
                    <div class="h-6 bg-gray-200 rounded w-1/3 mb-4"></div>
                    <div class="h-64 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actual Content (Hidden Initially) -->
<div id="actualContent" class="p-3 space-y-4" style="display: none;">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Laporan</h3>
        
        <form method="GET" action="{{ route('reports.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 gap-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transaksi Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transaksi Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pengiriman Mulai</label>
                        <input type="date" name="delivery_start_date" value="{{ request('delivery_start_date') }}" 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pengiriman Akhir</label>
                        <input type="date" name="delivery_end_date" value="{{ request('delivery_end_date') }}" 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <select name="supplier_id" id="supplierSelect" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->kode_supplier }} - {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Approval</label>
                    <select name="approval_status" id="statusSelect" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        @foreach($approvalStatuses as $value => $label)
                            <option value="{{ $value }}" {{ request('approval_status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor PO</label>
                    <input type="text" name="po_number" value="{{ request('po_number') }}" 
                           placeholder="Cari berdasarkan nomor PO..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    Filter
                </button>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="text-center">
                <div class="p-2 bg-blue-100 rounded-lg inline-block mb-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-600">Total Transaksi</p>
                <p class="text-lg font-semibold text-gray-900">{{ number_format($summary['total_transactions']) }}</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="text-center">
                <div class="p-2 bg-green-100 rounded-lg inline-block mb-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-600">Total Nilai</p>
                <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="text-center">
                <div class="p-2 bg-purple-100 rounded-lg inline-block mb-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-600">Total Quantity</p>
                <p class="text-lg font-semibold text-gray-900">{{ number_format($summary['total_quantity']) }}</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <div class="text-center">
                <div class="p-2 bg-orange-100 rounded-lg inline-block mb-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-600">Supplier</p>
                <p class="text-lg font-semibold text-gray-900">{{ $summary['supplier_counts']->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Download Laporan</h3>
        
        @if(request()->hasAny(['start_date', 'end_date', 'delivery_start_date', 'delivery_end_date', 'supplier_id', 'approval_status', 'po_number']))
        <div class="mb-3 p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-800">
            <p class="font-medium">Filter Aktif:</p>
            @if(request('start_date') || request('end_date'))
                <p>• Tanggal Transaksi: {{ request('start_date') ? Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : 'Awal' }} - {{ request('end_date') ? Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : 'Akhir' }}</p>
                @endif
            @if(request('delivery_start_date') || request('delivery_end_date'))
                <p>• Tanggal Pengiriman: {{ request('delivery_start_date') ? Carbon\Carbon::parse(request('delivery_start_date'))->format('d/m/Y') : 'Awal' }} - {{ request('delivery_end_date') ? Carbon\Carbon::parse(request('delivery_end_date'))->format('d/m/Y') : 'Akhir' }}</p>
                @endif
                @if(request('supplier_id'))
                <p>• Supplier: {{ $suppliers->where('id', request('supplier_id'))->first()->nama_supplier ?? 'Tidak ditemukan' }}</p>
                @endif
                @if(request('approval_status'))
                <p>• Status: {{ ucfirst(request('approval_status')) }}</p>
                @endif
                @if(request('po_number'))
                <p>• Nomor PO: {{ request('po_number') }}</p>
                @endif
        </div>
                @endif
        
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('reports.export.excel', request()->query()) }}" 
                        class="w-full px-3 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center justify-center text-sm text-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel
            </a>
            
            <a href="{{ route('reports.export.pdf', request()->query()) }}" 
                        class="w-full px-3 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 flex items-center justify-center text-sm text-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    PDF
            </a>
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Distribusi Status PO</h3>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-400 rounded-full mr-1"></div>
                        <span>Pending</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-1"></div>
                        <span>Approved</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-1"></div>
                        <span>Rejected</span>
                    </div>
                </div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Supplier</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="supplierChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Trend Bulanan</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- New Charts Section -->
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Amount (Top 10)</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="salesAmountChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Amount Trend</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="monthlyAmountChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Products (Transaksi)</h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kategori Terlaris</h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="topCategoriesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
// Loading skeleton control
document.addEventListener('DOMContentLoaded', function() {
    // Show loading skeleton for 1 second
    setTimeout(function() {
        const loadingSkeleton = document.getElementById('loadingSkeleton');
        const actualContent = document.getElementById('actualContent');
        
        if (loadingSkeleton && actualContent) {
            // Fade out loading skeleton
            loadingSkeleton.style.opacity = '0';
            loadingSkeleton.style.transition = 'opacity 0.3s ease-out';
            
            // Show actual content
            actualContent.style.display = 'block';
            actualContent.style.opacity = '0';
            actualContent.style.transition = 'opacity 0.3s ease-in';
            
            // Fade in actual content
            setTimeout(function() {
                actualContent.style.opacity = '1';
                loadingSkeleton.style.display = 'none';
                
                // Initialize charts after content is visible
                initializeCharts();
            }, 300);
        }
    }, 1000);
});
</script>

<script>
    // Chart data from PHP
    const chartData = {
        statusData: @json($summary['status_counts']),
        supplierData: @json($summary['supplier_counts']->take(5)),
        monthlyData: @json($monthlyData),
        salesAmountData: @json($salesAmountData),
        monthlyAmountData: @json($monthlyAmountData),
        topProductsData: @json($topProductsData),
        topCategoriesData: @json($topCategoriesData)
    };
    

    // Store chart instances
    let statusChart = null;
    let supplierChart = null;
    let trendChart = null;
    let salesAmountChart = null;
    let monthlyAmountChart = null;
    let topProductsChart = null;
    let topCategoriesChart = null;
    let chartsInitialized = false;

    // Initialize charts when page loads
    function initializeCharts() {
        // Prevent multiple initialization
        if (chartsInitialized) {
            return;
        }
        
        // Wait for Chart.js to be loaded
        if (typeof Chart !== 'undefined') {
            
            // Check if canvas elements exist
            const statusCanvas = document.getElementById('statusChart');
            const supplierCanvas = document.getElementById('supplierChart');
            const trendCanvas = document.getElementById('trendChart');
            const salesAmountCanvas = document.getElementById('salesAmountChart');
            const monthlyAmountCanvas = document.getElementById('monthlyAmountChart');
            const topProductsCanvas = document.getElementById('topProductsChart');
            const topCategoriesCanvas = document.getElementById('topCategoriesChart');
            
            
            if (statusCanvas && supplierCanvas && trendCanvas) {
                initStatusChart();
                initSupplierChart();
                initTrendChart();
                
                // Initialize new charts if canvas elements exist
                if (salesAmountCanvas) initSalesAmountChart();
                if (monthlyAmountCanvas) initMonthlyAmountChart();
                if (topProductsCanvas) initTopProductsChart();
                if (topCategoriesCanvas) initTopCategoriesChart();
                
                chartsInitialized = true;
            } else {
                setTimeout(() => {
                    if (!chartsInitialized) {
                        initializeCharts();
                    }
                }, 1000);
            }
        } else {
            // Retry after a short delay if Chart.js is not yet loaded
            setTimeout(function() {
                if (typeof Chart !== 'undefined' && !chartsInitialized) {
                    initializeCharts();
                } else if (!chartsInitialized) {
                    // Show fallback message
                    document.querySelectorAll('canvas').forEach(canvas => {
                        canvas.parentElement.innerHTML = '<div class="text-center text-gray-500 py-8"><p>Chart tidak dapat dimuat</p></div>';
                    });
                }
            }, 2000);
        }
    }

    // Charts will be initialized after loading skeleton completes

    // Cleanup charts on page unload
    window.addEventListener('beforeunload', function() {
        if (statusChart) {
            statusChart.destroy();
            statusChart = null;
        }
        if (supplierChart) {
            supplierChart.destroy();
            supplierChart = null;
        }
        if (trendChart) {
            trendChart.destroy();
            trendChart = null;
        }
    });

    // Download functions removed - using direct links instead

    function initStatusChart() {
        try {
            const canvas = document.getElementById('statusChart');
            if (!canvas) {
                return;
            }
            
            // Destroy existing chart if any
            if (statusChart) {
                statusChart.destroy();
                statusChart = null;
            }
            
            const ctx = canvas.getContext('2d');
            const statusData = chartData.statusData || {};
            
            // Check if data is empty
            if (Object.keys(statusData).length === 0) {
                canvas.parentElement.innerHTML = '<div class="text-center text-gray-500 py-8"><p>Tidak ada data untuk ditampilkan</p></div>';
                return;
            }
            
            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusData).map(key => {
                        const labels = {
                            'pending': 'Pending',
                            'approved': 'Approved', 
                            'rejected': 'Rejected'
                        };
                        return labels[key] || key;
                    }),
                    datasets: [{
                        data: Object.values(statusData),
                        backgroundColor: [
                            '#F59E0B', // Yellow for pending
                            '#10B981', // Green for approved
                            '#EF4444'  // Red for rejected
                        ],
                        borderWidth: 3,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Hide legend since we have custom legend
                        }
                    },
                    cutout: '60%'
                }
            });
        } catch (error) {
        }
    }

    function initSupplierChart() {
        try {
            const canvas = document.getElementById('supplierChart');
            if (!canvas) {
                return;
            }
            
            // Destroy existing chart if any
            if (supplierChart) {
                supplierChart.destroy();
                supplierChart = null;
            }
            
            const ctx = canvas.getContext('2d');
            const supplierData = chartData.supplierData || {};
            
            // Check if data is empty
            if (Object.keys(supplierData).length === 0) {
                canvas.parentElement.innerHTML = '<div class="text-center text-gray-500 py-8"><p>Tidak ada data untuk ditampilkan</p></div>';
                return;
            }
            
            supplierChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(supplierData).map(name => name.length > 12 ? name.substring(0, 12) + '...' : name),
                    datasets: [{
                        label: 'Jumlah PO',
                        data: Object.values(supplierData),
                        backgroundColor: '#3B82F6',
                        borderColor: '#1D4ED8',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        } catch (error) {
        }
    }

    function initTrendChart() {
        try {
            const canvas = document.getElementById('trendChart');
            if (!canvas) {
                return;
            }
            
            // Destroy existing chart if any
            if (trendChart) {
                trendChart.destroy();
                trendChart = null;
            }
            
            const ctx = canvas.getContext('2d');
            const monthlyData = chartData.monthlyData || { labels: [], values: [] };
            
            // Check if data is empty
            if (!monthlyData.labels || monthlyData.labels.length === 0) {
                canvas.parentElement.innerHTML = '<div class="text-center text-gray-500 py-8"><p>Tidak ada data untuk ditampilkan</p></div>';
                return;
            }
            
            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthlyData.labels,
                    datasets: [{
                        label: 'Total PO',
                        data: monthlyData.values,
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#8B5CF6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        } catch (error) {
        }
    }

    // Initialize Sales Amount Chart
    function initSalesAmountChart() {
        try {
            const ctx = document.getElementById('salesAmountChart');
            if (!ctx) {
                return;
            }
            
            salesAmountChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.salesAmountData.labels,
                    datasets: [{
                        label: 'Amount (Rp)',
                        data: chartData.salesAmountData.values,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
        }
    }

    // Initialize Monthly Amount Chart
    function initMonthlyAmountChart() {
        try {
            const ctx = document.getElementById('monthlyAmountChart').getContext('2d');
            monthlyAmountChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.monthlyAmountData.labels,
                    datasets: [{
                        label: 'Amount (Rp)',
                        data: chartData.monthlyAmountData.values,
                        borderColor: 'rgba(59, 130, 246, 1)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: 'rgba(59, 130, 246, 1)',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
        }
    }

    // Initialize Top Products Chart
    function initTopProductsChart() {
        try {
            const ctx = document.getElementById('topProductsChart');
            if (!ctx) {
                return;
            }
            
            topProductsChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartData.topProductsData.labels,
                    datasets: [{
                        data: chartData.topProductsData.values,
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(147, 51, 234, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(6, 182, 212, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(251, 146, 60, 0.8)',
                            'rgba(139, 92, 246, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        } catch (error) {
        }
    }

    // Initialize Top Categories Chart
    function initTopCategoriesChart() {
        try {
            const ctx = document.getElementById('topCategoriesChart');
            if (!ctx) {
                return;
            }
            
            topCategoriesChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: chartData.topCategoriesData.labels,
                    datasets: [{
                        data: chartData.topCategoriesData.values,
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(147, 51, 234, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(6, 182, 212, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(251, 146, 60, 0.8)',
                            'rgba(139, 92, 246, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        } catch (error) {
        }
    }
</script>

<!-- jQuery and Select2 CSS/JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Wait for jQuery and Select2 to be loaded
function initializeSelect2() {
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        setTimeout(initializeSelect2, 100);
        return;
    }
    
    try {
        // Initialize Select2 for Supplier with custom template
        $('#supplierSelect').select2({
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
                         +   '<small class="text-muted" style="margin-left:auto; text-align:right; display:block;">' + code + '</small>'
                         + '</div>';
                return $(html);
            },
            templateSelection: function (data) {
                if (!data.id) { return data.text; }
                var full = (data.text || '').toString();
                var parts = full.split(' - ');
                var code = parts[0] || '';
                var name = parts.slice(1).join(' - ') || code || full;
                var html = '<div style="display:flex; align-items:center; justify-content:space-between; width:100%">'
                         +   '<span>' + name + '</span>'
                         +   '<small class="text-muted" style="margin-left:auto; text-align:right; display:block;">' + code + '</small>'
                         + '</div>';
                return $(html);
            }
        });
        
        // Initialize Select2 for Status
        $('#statusSelect').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih Status',
            allowClear: true
        });
    } catch (error) {
        // Select2 initialization failed, continue without it
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSelect2);
} else {
    initializeSelect2();
    }
</script>
@endsection