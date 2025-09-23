<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesTransactionController;
use App\Http\Controllers\DataBarangController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;

// Public debug endpoint (no auth, must be BEFORE resource catch-all routes)
Route::get('/sales-transaction/get-products-no-auth', [SalesTransactionController::class, 'getProductsBySupplierNoAuth'])->name('sales-transaction.get-products-no-auth');

// Authentication Routes with Mobile Check
Route::middleware(['mobile.only'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Redirect root to login
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // Protected Routes - Only Sales and Owner can access
    Route::middleware(['auth', 'role:sales,owner'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Sales Transaction Routes
        Route::get('/sales-transaction/load-more', [SalesTransactionController::class, 'loadMore'])->name('sales-transaction.load-more');
        Route::resource('sales-transaction', SalesTransactionController::class)->only(['index', 'show']);
        Route::get('/sales-transaction/bulk/create', [SalesTransactionController::class, 'bulkCreate'])->name('sales-transaction.bulk-create');
        Route::post('/sales-transaction/bulk/store', [SalesTransactionController::class, 'bulkStore'])->name('sales-transaction.bulk-store');
        Route::get('/sales-transaction/test-ajax', [SalesTransactionController::class, 'testAjax'])->name('sales-transaction.test-ajax');
        Route::get('/sales-transaction/get-products', [SalesTransactionController::class, 'getProductsBySupplier'])->name('sales-transaction.get-products');
        
        Route::patch('/sales-transaction/{salesTransaction}/approve', [SalesTransactionController::class, 'approve'])->name('sales-transaction.approve');
        Route::patch('/sales-transaction/{salesTransaction}/reject', [SalesTransactionController::class, 'reject'])->name('sales-transaction.reject');
        Route::patch('/sales-transaction/po/{poNumber}/approve', [SalesTransactionController::class, 'approvePO'])->name('sales-transaction.approve-po');
        Route::patch('/sales-transaction/po/{poNumber}/reject', [SalesTransactionController::class, 'rejectPO'])->name('sales-transaction.reject-po');
        Route::get('/sales-transaction/po/{poNumber}/edit', [SalesTransactionController::class, 'editPO'])->name('sales-transaction.edit-po');
        Route::patch('/sales-transaction/po/{poNumber}', [SalesTransactionController::class, 'updatePO'])->name('sales-transaction.update-po');
        Route::delete('/sales-transaction/po/{poNumber}', [SalesTransactionController::class, 'deletePO'])->name('sales-transaction.delete-po');
        
        // Data Barang Routes
        Route::get('/data-barang/load-more', [DataBarangController::class, 'loadMore'])->name('data-barang.load-more');
        Route::resource('data-barang', DataBarangController::class);
        
        // Profile Routes
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        
        // Reports Routes - Owner Only
        Route::middleware('role:owner')->group(function () {
            Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
            Route::get('/reports/export/excel', [ReportsController::class, 'exportExcel'])->name('reports.export.excel');
            Route::get('/reports/export/pdf', [ReportsController::class, 'exportPdf'])->name('reports.export.pdf');
        });
    });
});

// Test endpoint without authentication for debugging
Route::get('/test-ajax-simple', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Simple AJAX test working',
        'timestamp' => now()
    ]);
});

// Desktop login test routes (bypass mobile.only middleware)
Route::get('/desktop-login', [AuthController::class, 'showLogin'])->name('desktop-login');
Route::post('/desktop-login', [AuthController::class, 'login']);
Route::get('/desktop', function () {
    return redirect()->route('desktop-login');
});


// API endpoints for offline sync
Route::middleware(['auth', 'role:sales,owner'])->group(function () {
    // Sales Transaction API - Use SalesTransactionController
    Route::post('/api/sales-transaction', [SalesTransactionController::class, 'bulkStore']);
    
    // Purchase Order API - Use SalesTransactionController
    Route::post('/api/purchase-order', [SalesTransactionController::class, 'bulkStore']);
    
    // Product API
    Route::post('/api/products', function (Illuminate\Http\Request $request) {
        try {
            $data = $request->all();
            $data['created_by'] = auth()->id();
            
            $product = \App\Models\Product::create($data);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => $product
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    });
});

// Test endpoint with authentication but no mobile middleware
Route::middleware(['auth', 'role:sales,owner'])->group(function () {
    Route::get('/test-ajax-auth', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Authenticated AJAX test working',
            'user' => auth()->user()->name,
            'timestamp' => now()
        ]);
    });
    
    // Test loadMore without mobile middleware
    Route::get('/test-load-more', [SalesTransactionController::class, 'loadMore'])->name('test-load-more');
    
    // Test chart data
    Route::get('/test-chart-data', function () {
        $transactions = \App\Models\SalesTransaction::with(['product', 'supplier', 'sales', 'approver'])->get();
        
        $controller = new \App\Http\Controllers\ReportsController();
        $reflection = new ReflectionClass($controller);
        
        $salesAmountMethod = $reflection->getMethod('getSalesAmountData');
        $salesAmountMethod->setAccessible(true);
        $salesAmountData = $salesAmountMethod->invoke($controller, $transactions);
        
        $topProductsMethod = $reflection->getMethod('getTopProductsData');
        $topProductsMethod->setAccessible(true);
        $topProductsData = $topProductsMethod->invoke($controller, $transactions);
        
        $topCategoriesMethod = $reflection->getMethod('getTopCategoriesData');
        $topCategoriesMethod->setAccessible(true);
        $topCategoriesData = $topCategoriesMethod->invoke($controller, $transactions);
        
        return response()->json([
            'salesAmountData' => $salesAmountData,
            'topProductsData' => $topProductsData,
            'topCategoriesData' => $topCategoriesData,
            'total_transactions' => $transactions->count()
        ]);
    });
});

// (moved above)
