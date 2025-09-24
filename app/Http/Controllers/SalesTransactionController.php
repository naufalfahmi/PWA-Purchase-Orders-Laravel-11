<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesTransaction;
use App\Models\Product;
use App\Models\Sales;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SalesTransactionController extends Controller
{
    /**
     * Generate unique transaction number
     */
    private function generateTransactionNumber()
    {
        do {
            $transactionNumber = 'ST-' . date('Ymd') . '-' . Str::upper(Str::random(6));
        } while (SalesTransaction::where('transaction_number', $transactionNumber)->exists());
        
        return $transactionNumber;
    }

    public function index(Request $request)
    {
        // Get unique PO numbers with their latest transaction data
        $query = SalesTransaction::select([
            'po_number',
            \DB::raw('MAX(transaction_number) as transaction_number'),
            \DB::raw('MAX(transaction_date) as transaction_date'),
            \DB::raw('MAX(delivery_date) as delivery_date'),
            \DB::raw('MAX(sales_id) as sales_id'),
            \DB::raw('MAX(approval_status) as approval_status'),
            \DB::raw('MAX(approved_by) as approved_by'),
            \DB::raw('MAX(approved_at) as approved_at'),
            \DB::raw('MAX(approval_notes) as approval_notes'),
            \DB::raw('MAX(general_notes) as general_notes'),
            \DB::raw('MAX(order_acc_by) as order_acc_by'),
            \DB::raw('MAX(received_by) as received_by'),
            \DB::raw('MAX(received_at) as received_at'),
            \DB::raw('COUNT(*) as total_items'),
            \DB::raw('SUM(total_quantity_piece) as total_quantity'),
            \DB::raw('SUM(total_amount) as total_amount'),
            \DB::raw('MIN(created_at) as created_at'),
            \DB::raw('MAX(updated_at) as updated_at')
        ])
        ->with(['sales', 'approver', 'receiver'])
        ->groupBy('po_number')
        ->orderBy(\DB::raw('MIN(created_at)'), 'desc');
        
        // If logged-in user is Sales, only show their own POs
        if (auth()->check() && auth()->user()->isSales()) {
            $currentSales = Sales::where('name', auth()->user()->name)->first();
            if ($currentSales) {
                $query->where('sales_id', $currentSales->id);
            }
        }
        
        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        // Filter berdasarkan sales
        if ($request->filled('sales_id')) {
            $query->where('sales_id', $request->sales_id);
        }
        
        // Filter berdasarkan toko (order_acc_by)
        if ($request->filled('toko')) {
            $query->where('order_acc_by', $request->toko);
        }
        
        // Filter berdasarkan supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter berdasarkan approval status
        if ($request->filled('approval_status')) {
            if ($request->approval_status === 'received') {
                $query->whereNotNull('received_at');
            } else {
                $query->where('approval_status', $request->approval_status);
            }
        }

        // Filter berdasarkan search (PO Number, Transaction Number, Sales)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhere('transaction_number', 'like', "%{$search}%")
                  ->orWhereHas('sales', function($salesQuery) use ($search) {
                      $salesQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Ensure newest first
        $poList = $query->orderByDesc('created_at')->paginate(20);
        $products = Product::active()->get();
        $salesList = Sales::active()->get();

        // Debug log for pagination
        \Log::info('PO List pagination info', [
            'current_page' => $poList->currentPage(),
            'total' => $poList->total(),
            'per_page' => $poList->perPage(),
            'has_more_pages' => $poList->hasMorePages(),
            'last_page' => $poList->lastPage(),
            'items_count' => $poList->count(),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? 'unknown',
            'is_sales' => auth()->user()->isSales() ?? false
        ]);

        // Get order acc options for toko filter
        $orderAccOptions = DB::table('order_acc_options')->where('is_active', true)->get();
        
        // Get suppliers for supplier filter
        $supplierList = Supplier::all(); // Get all suppliers first for debugging
        
        // Debug: Log supplier count
        \Log::info('Total suppliers: ' . $supplierList->count());
        \Log::info('Active suppliers: ' . Supplier::active()->count());
        
        if ($supplierList->count() > 0) {
            \Log::info('First supplier: ' . $supplierList->first()->nama_supplier);
            \Log::info('First supplier active: ' . ($supplierList->first()->is_active ? 'Yes' : 'No'));
        }
        
        // Use all suppliers for now (we'll filter by active later)
        $supplierList = Supplier::all();

        return view('sales-transaction.index', compact('poList', 'products', 'salesList', 'orderAccOptions', 'supplierList'));
    }

    /**
     * Show the form for creating bulk transactions.
     */
    public function bulkCreate()
    {
        $suppliers = Supplier::active()->get();
        $salesList = Sales::active()->get();
        $currentSales = null;
        if (auth()->check()) {
            $currentSales = Sales::where('name', auth()->user()->name)->first();
        }
        $defaultPoNumber = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);
        $orderAccOptions = DB::table('order_acc_options')->where('is_active', true)->pluck('name');
        return view('sales-transaction.bulk-create', compact('suppliers', 'salesList', 'currentSales', 'defaultPoNumber', 'orderAccOptions'));
    }

    /**
     * Test endpoint for AJAX debugging
     */
    public function testAjax(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'AJAX working',
            'user' => auth()->user() ? auth()->user()->name : 'Not logged in',
            'supplier_id' => $request->input('supplier_id', 'No supplier ID')
        ]);
    }

    /**
     * Get products by supplier (temporary without auth for debugging)
     */
    public function getProductsBySupplierNoAuth(Request $request)
    {
        $supplierId = $request->input('supplier_id');
        
        if (!$supplierId) {
            return response()->json(['products' => []]);
        }
        
        $products = Product::where('supplier_id', $supplierId)->get();
        
        return response()->json([
            'status' => 'success',
            'auth_status' => auth()->check() ? 'authenticated' : 'not authenticated',
            'user' => auth()->user() ? auth()->user()->name : null,
            'products' => $products->toArray()
        ]);
    }

    /**
     * Get products by supplier
     */
    public function getProductsBySupplier(Request $request)
    {
        $supplierId = $request->input('supplier_id');
        
        if (!$supplierId) {
            return response()->json([]);
        }
        
        $products = Product::where('supplier_id', $supplierId)->get();
        
        return response()->json($products->toArray());
    }

    /**
     * Store bulk transactions.
     */
    public function bulkStore(Request $request)
    {
        // Check if this is an API request (for offline sync)
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->handleOfflineSync($request, auth()->id());
        }
        
        // Original bulk store logic for web forms
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:transaction_date',
            'sales_id' => 'required|exists:sales,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'po_number' => 'nullable|string|max:255',
            'general_notes' => 'nullable|string',
            'order_acc_by' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.supplier_id' => 'required|exists:suppliers,id',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_carton' => 'nullable|integer|min:0',
            'products.*.quantity_piece' => 'nullable|integer|min:0',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.notes' => 'nullable|string',
        ]);

        // Validate that all products belong to the same supplier
        $mainSupplierId = $validated['supplier_id'];
        foreach ($validated['products'] as $product) {
            if ($product['supplier_id'] != $mainSupplierId) {
                return back()->withErrors(['products' => 'Semua produk harus dari supplier yang sama.'])->withInput();
            }
        }

        // Validate that at least one product has quantity > 0
        $hasValidProduct = false;
        foreach ($validated['products'] as $product) {
            $cartonQty = $product['quantity_carton'] ?? 0;
            $pieceQty = $product['quantity_piece'] ?? 0;
            if ($cartonQty > 0 || $pieceQty > 0) {
                $hasValidProduct = true;
                break;
            }
        }

        if (!$hasValidProduct) {
            return back()->withErrors(['products' => 'Minimal harus ada 1 produk dengan quantity > 0.'])->withInput();
        }

        $transactions = [];
        $poNumber = $validated['po_number'] ?? 'PO-' . date('Ymd') . '-' . rand(1000, 9999);
        $transactionNumber = $this->generateTransactionNumber();

        foreach ($validated['products'] as $productData) {
            // Skip if no quantity
            $cartonQty = $productData['quantity_carton'] ?? 0;
            $pieceQty = $productData['quantity_piece'] ?? 0;
            if ($cartonQty == 0 && $pieceQty == 0) {
                continue;
            }

            $product = Product::find($productData['product_id']);
            $quantityCarton = $cartonQty;
            $quantityPiece = $pieceQty;
            $totalQuantityPiece = ($quantityCarton * $product->quantity_per_carton) + $quantityPiece;
            $totalAmount = $totalQuantityPiece * $productData['unit_price'];

            $transactions[] = [
                'transaction_number' => $transactionNumber,
                'transaction_date' => $validated['transaction_date'],
                'delivery_date' => $validated['delivery_date'] ?? null,
                'product_id' => $productData['product_id'],
                'sales_id' => $validated['sales_id'],
                'supplier_id' => $productData['supplier_id'],
                'quantity_carton' => $quantityCarton,
                'quantity_piece' => $quantityPiece,
                'total_quantity_piece' => $totalQuantityPiece,
                'unit_price' => $productData['unit_price'],
                'total_amount' => $totalAmount,
                'notes' => $productData['notes'] ?? null,
                'general_notes' => $validated['general_notes'] ?? null,
                'order_acc_by' => $validated['order_acc_by'] ?? null,
                'po_number' => $poNumber,
                'approval_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($transactions)) {
            return back()->withErrors(['products' => 'Tidak ada transaksi yang valid untuk disimpan.'])->withInput();
        }

        // Insert all transactions
        SalesTransaction::insert($transactions);

        return redirect()->route('sales-transaction.index')
            ->with('success', 'Sales Transaction berhasil disimpan (' . count($transactions) . ' item) dan menunggu persetujuan Owner. PO: ' . $poNumber);
    }

    public function show($transactionNumber)
    {
        $transactions = SalesTransaction::where('transaction_number', $transactionNumber)
            ->with(['product', 'sales', 'approver'])
            ->get();
        
        if ($transactions->isEmpty()) {
            return redirect()->route('sales-transaction.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        // Hitung total menggunakan total_quantity_piece yang sudah dikonversi dengan benar
        $totalQuantity = $transactions->sum('total_quantity_piece');
        $totalAmount = $transactions->sum('total_amount');

        return view('sales-transaction.show', compact('transactions', 'transactionNumber', 'totalAmount', 'totalQuantity'));
    }

    /**
     * Approve sales transaction (Owner only)
     */
    public function approve(Request $request, SalesTransaction $salesTransaction)
    {
        $request->validate([
            'approval_notes' => 'nullable|string',
            'send_whatsapp' => 'nullable|boolean',
        ]);

        $salesTransaction->approve(auth()->id(), $request->approval_notes);

        return back()->with('success', 'Sales Transaction berhasil disetujui.');
    }

    /**
     * Reject sales transaction (Owner only)
     */
    public function reject(Request $request, SalesTransaction $salesTransaction)
    {
        $request->validate([
            'approval_notes' => 'nullable|string',
            'send_whatsapp' => 'nullable|boolean',
        ]);

        $salesTransaction->reject(auth()->id(), $request->approval_notes);

        return back()->with('success', 'Sales Transaction berhasil ditolak.');
    }

    /**
     * Approve all transactions in a PO (Owner only)
     */
    public function approvePO(Request $request, $poNumber)
    {
        $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        $transactions = SalesTransaction::where('po_number', $poNumber)->get();
        
        foreach ($transactions as $transaction) {
            $transaction->approve(auth()->id(), $request->approval_notes);
        }

        return back()->with('success', 'Purchase Order ' . $poNumber . ' berhasil disetujui.');
    }

    /**
     * Reject all transactions in a PO (Owner only)
     */
    public function rejectPO(Request $request, $poNumber)
    {
        $request->validate([
            'approval_notes' => 'required|string',
        ]);

        $transactions = SalesTransaction::where('po_number', $poNumber)->get();
        
        foreach ($transactions as $transaction) {
            $transaction->reject(auth()->id(), $request->approval_notes);
        }

        return back()->with('success', 'Purchase Order ' . $poNumber . ' berhasil ditolak.');
    }

    /**
     * Mark all transactions in a PO as received (Sales only)
     */
    public function receivePO(Request $request, $poNumber)
    {
        // Check if user is sales
        if (!auth()->user()->isSales()) {
            return back()->with('error', 'Hanya sales yang dapat menandai PO sebagai received.');
        }

        // Check if PO belongs to the sales
        $currentSales = Sales::where('name', auth()->user()->name)->first();
        if (!$currentSales) {
            return back()->with('error', 'Sales tidak ditemukan.');
        }

        $transactions = SalesTransaction::where('po_number', $poNumber)
            ->where('sales_id', $currentSales->id)
            ->where('approval_status', 'approved')
            ->get();

        if ($transactions->isEmpty()) {
            return back()->with('error', 'PO tidak ditemukan atau belum disetujui.');
        }
        
        foreach ($transactions as $transaction) {
            $transaction->markAsReceived(auth()->id());
        }

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order ' . $poNumber . ' berhasil ditandai sebagai received.'
        ]);
    }

    /**
     * Delete all transactions in a PO (Owner only)
     */
    public function deletePO(Request $request, $poNumber)
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Prevent deleting approved POs
        $hasApproved = SalesTransaction::where('po_number', $poNumber)
            ->where('approval_status', 'approved')
            ->exists();

        if ($hasApproved) {
            return back()->with('error', 'Tidak dapat menghapus PO yang sudah disetujui.');
        }

        // Authorization: Owner can delete any pending PO; Sales can delete their own pending PO
        $canDelete = false;
        if ($user->isOwner()) {
            $canDelete = true;
        } elseif ($user->isSales()) {
            // Determine current sales record
            $currentSales = \App\Models\Sales::where('name', $user->name)->first();
            if ($currentSales) {
                $ownedBySales = SalesTransaction::where('po_number', $poNumber)
                    ->where('sales_id', $currentSales->id)
                    ->exists();
                $canDelete = $ownedBySales;
            }
        }

        if (!$canDelete) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus PO ini.');
        }

        $deleted = SalesTransaction::where('po_number', $poNumber)->delete();

        if ($deleted > 0) {
            return redirect()->route('sales-transaction.index')
                ->with('success', 'PO ' . $poNumber . ' berhasil dihapus.');
        }

        return back()->with('error', 'PO tidak ditemukan atau sudah dihapus.');
    }

    /**
     * Edit bulk PO by PO number (prefill items)
     */
    public function editPO($poNumber)
    {
        $transactions = SalesTransaction::where('po_number', $poNumber)
        ->with(['product.supplier', 'sales'])
        ->orderBy('id')
            ->get();

        if ($transactions->isEmpty()) {
            return redirect()->route('sales-transaction.index')->with('error', 'PO tidak ditemukan.');
        }

        $suppliers = Supplier::active()->get();
        $salesList = Sales::active()->get();
        $currentSales = null;
        if (auth()->check()) {
            $currentSales = Sales::where('name', auth()->user()->name)->first();
        }
        $orderAccOptions = \DB::table('order_acc_options')->where('is_active', true)->pluck('name');

        // Prepare form data
        $header = [
            'transaction_date' => optional($transactions->first())->transaction_date?->format('Y-m-d') ?? date('Y-m-d'),
            'delivery_date' => optional($transactions->first())->delivery_date?->format('Y-m-d'),
            'sales_id' => $transactions->first()->sales_id,
            'po_number' => $poNumber,
            'general_notes' => $transactions->first()->general_notes,
            'order_acc_by' => $transactions->first()->order_acc_by,
        ];
        

        return view('sales-transaction.bulk-edit', compact('transactions', 'poNumber', 'suppliers', 'salesList', 'currentSales', 'header', 'orderAccOptions'));
    }

    /**
     * Update bulk PO items and header
     */
    public function updatePO(Request $request, $poNumber)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:transaction_date',
            'sales_id' => 'required|exists:sales,id',
            'po_number' => 'required|string|max:255',
            'general_notes' => 'nullable|string',
            'order_acc_by' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.supplier_id' => 'required|exists:suppliers,id',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_carton' => 'nullable|integer|min:0',
            'products.*.quantity_piece' => 'nullable|integer|min:0',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.notes' => 'nullable|string',
        ]);

        // Generate new unique transaction number for the updated PO
        $transactionNumber = $this->generateTransactionNumber();

        // Delete old items for this PO and re-insert based on submitted items
        SalesTransaction::where('po_number', $poNumber)->delete();

        $transactions = [];

        foreach ($validated['products'] as $productData) {
            // Skip if no quantity
            $cartonQty = $productData['quantity_carton'] ?? 0;
            $pieceQty = $productData['quantity_piece'] ?? 0;
            if ($cartonQty == 0 && $pieceQty == 0) {
                continue;
            }

            $product = Product::find($productData['product_id']);
            $quantityCarton = $cartonQty;
            $quantityPiece = $pieceQty;
            $totalQuantityPiece = ($quantityCarton * $product->quantity_per_carton) + $quantityPiece;
            $totalAmount = $totalQuantityPiece * $productData['unit_price'];

            $transactions[] = [
                'transaction_number' => $transactionNumber,
                'transaction_date' => $validated['transaction_date'],
                'delivery_date' => $validated['delivery_date'] ?? null,
                'product_id' => $productData['product_id'],
                'sales_id' => $validated['sales_id'],
                'supplier_id' => $productData['supplier_id'],
                'quantity_carton' => $quantityCarton,
                'quantity_piece' => $quantityPiece,
                'total_quantity_piece' => $totalQuantityPiece,
                'unit_price' => $productData['unit_price'],
                'total_amount' => $totalAmount,
                'notes' => $productData['notes'] ?? null,
                'general_notes' => $validated['general_notes'] ?? null,
                'order_acc_by' => $validated['order_acc_by'] ?? null,
                'po_number' => $validated['po_number'],
                'approval_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($transactions)) {
            return back()->withErrors(['products' => 'Tidak ada transaksi yang valid untuk disimpan.'])->withInput();
        }

        SalesTransaction::insert($transactions);

        return redirect()->route('sales-transaction.show', $transactionNumber)
            ->with('success', 'PO berhasil diperbarui.');
    }

    /**
     * Load more sales transactions for infinite scroll
     */
    public function loadMore(Request $request)
    {
        
        
        // Same logic as index method but for pagination
        $query = SalesTransaction::select([
            'po_number',
            \DB::raw('MAX(transaction_number) as transaction_number'),
            \DB::raw('MAX(transaction_date) as transaction_date'),
            \DB::raw('MAX(delivery_date) as delivery_date'),
            \DB::raw('MAX(sales_id) as sales_id'),
            \DB::raw('MAX(approval_status) as approval_status'),
            \DB::raw('MAX(approved_by) as approved_by'),
            \DB::raw('MAX(approved_at) as approved_at'),
            \DB::raw('MAX(approval_notes) as approval_notes'),
            \DB::raw('MAX(general_notes) as general_notes'),
            \DB::raw('MAX(order_acc_by) as order_acc_by'),
            \DB::raw('COUNT(*) as total_items'),
            \DB::raw('SUM(total_quantity_piece) as total_quantity'),
            \DB::raw('SUM(total_amount) as total_amount'),
            \DB::raw('MIN(created_at) as created_at'),
            \DB::raw('MAX(updated_at) as updated_at')
        ])
        ->with(['sales', 'approver'])
        ->groupBy('po_number')
        ->orderBy(\DB::raw('MIN(created_at)'), 'desc');
        
        // If logged-in user is Sales, only show their own POs
        if (auth()->check() && auth()->user()->isSales()) {
            $currentSales = \App\Models\Sales::where('name', auth()->user()->name)->first();
            if ($currentSales) {
                $query->where('sales_id', $currentSales->id);
            }
        }
        
        // Apply same filters as index method
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('sales_id')) {
            $query->where('sales_id', $request->sales_id);
        }
        
        // Filter berdasarkan toko (order_acc_by)
        if ($request->filled('toko')) {
            $query->where('order_acc_by', $request->toko);
        }
        
        // Filter berdasarkan supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhere('transaction_number', 'like', "%{$search}%")
                  ->orWhereHas('sales', function($salesQuery) use ($search) {
                      $salesQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $page = $request->get('page', 1);
        $poList = $query->paginate(20, ['*'], 'page', $page);

        if ($request->ajax()) {
            try {
                $html = view('sales-transaction.partials.po-list', compact('poList'))->render();
                
                // Debug log for loadMore
                \Log::info('LoadMore pagination info', [
                    'current_page' => $poList->currentPage(),
                    'total' => $poList->total(),
                    'per_page' => $poList->perPage(),
                    'has_more_pages' => $poList->hasMorePages(),
                    'last_page' => $poList->lastPage(),
                    'items_count' => $poList->count()
                ]);
                
                return response()->json([
                    'html' => $html,
                    'hasMore' => $poList->hasMorePages(),
                    'nextPage' => $poList->currentPage() + 1,
                    'currentPage' => $poList->currentPage(),
                    'total' => $poList->total(),
                    'perPage' => $poList->perPage()
                ]);
            } catch (\Exception $e) {
                \Log::error('Error in loadMore AJAX: ' . $e->getMessage());
                return response()->json([
                    'error' => 'Error rendering view: ' . $e->getMessage()
                ], 500);
            }
        }

        return view('sales-transaction.index', compact('poList'));
    }

    /**
     * Handle offline sync data - Same logic as bulkStore
     */
    private function handleOfflineSync(Request $request, $userId = null)
    {
        try {
            $data = $request->all();
            $userId = $userId ?? auth()->id();
            
            // Log received data for debugging
            \Log::info('Offline Sync - Received Data', [
                'raw_data' => $data,
                'user_id' => $userId
            ]);
            
            // Use database transaction to prevent race conditions
            return \DB::transaction(function () use ($data, $userId) {
            
            // Determine PO number (do not early return; we allow multiple rows per PO)
            $poNumber = $data['po_number'] ?? 'PO-' . now()->format('Ymd') . '-' . rand(1000, 9999);
            
            // Ensure sales record exists
            $salesId = Sales::where('id', $userId)->value('id');
            if (!$salesId) {
                $user = auth()->user();
                if (!$user && $userId) {
                    $user = \App\Models\User::find($userId);
                }
                $sales = Sales::create([
                    'id' => $userId,
                    'name' => $user ? $user->name : 'Offline User',
                    'email' => $user ? $user->email : 'offline@example.com',
                    'phone' => $user ? ($user->phone ?? '') : '',
                    'status' => 'active'
                ]);
                $salesId = $sales->id;
            }
            
            // Convert offline sync data to products array format (same as bulkStore)
            $products = [];
            // Case 1: Newer offline format: products is an array of objects
            if (isset($data['products']) && is_array($data['products'])) {
                foreach ($data['products'] as $p) {
                    if (!is_array($p)) { continue; }
                    $products[] = [
                        'supplier_id' => intval($p['supplier_id'] ?? $data['supplier_id'] ?? 1),
                        'product_id' => intval($p['product_id'] ?? 0),
                        'quantity_carton' => intval($p['quantity_carton'] ?? 0),
                        'quantity_piece' => intval($p['quantity_piece'] ?? 0),
                        'quantity_type' => $p['quantity_type'] ?? 'piece',
                        'unit_price' => floatval($p['unit_price'] ?? 0),
                        'notes' => $p['notes'] ?? null,
                    ];
                }
            }
            // Case 2: Legacy bracketed multi-item format: products[0][field], products[1][field], ...
            elseif ($this->hasBracketedProducts($data)) {
                $indexes = $this->extractBracketedProductIndexes($data);
                foreach ($indexes as $idx) {
                    $products[] = [
                        'supplier_id' => intval($data["products[$idx][supplier_id]"] ?? $data['supplier_id'] ?? 1),
                        'product_id' => intval($data["products[$idx][product_id]"] ?? 0),
                        'quantity_carton' => intval($data["products[$idx][quantity_carton]"] ?? 0),
                        'quantity_piece' => intval($data["products[$idx][quantity_piece]"] ?? 0),
                        'quantity_type' => $data["products[$idx][quantity_type]"] ?? 'piece',
                        'unit_price' => floatval($data["products[$idx][unit_price]"] ?? 0),
                        'notes' => $data["products[$idx][notes]"] ?? null,
                    ];
                }
            } else {
                // Direct data format
                $products[] = [
                    'supplier_id' => intval($data['supplier_id'] ?? 1),
                    'product_id' => intval($data['product_id'] ?? 1),
                    'quantity_carton' => intval($data['quantity_carton'] ?? 0),
                    'quantity_piece' => intval($data['quantity_piece'] ?? 0),
                    'quantity_type' => $data['quantity_type'] ?? 'piece',
                    'unit_price' => floatval($data['unit_price'] ?? 0),
                    'notes' => $data['notes'] ?? null,
                ];
            }
            
            // Use same validation and logic as bulkStore
            $mainSupplierId = $products[0]['supplier_id'];

            \Log::info('Offline Sync - Parsed Products', [
                'po_number' => $poNumber,
                'products_count' => count($products),
                'products' => $products,
            ]);
            
            // Validate that at least one product has quantity > 0 (regardless of type)
            $hasValidProduct = false;
            foreach ($products as $product) {
                if ((intval($product['quantity_carton']) > 0) || (intval($product['quantity_piece']) > 0)) {
                    $hasValidProduct = true;
                    break;
                }
            }

            if (!$hasValidProduct) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Minimal harus ada 1 produk dengan quantity > 0.'
                ], 400);
            }

            $transactions = [];
            // Fetch existing product_ids for this PO to avoid duplicates on re-sync
            $existingProductIds = SalesTransaction::where('po_number', $poNumber)->pluck('product_id')->toArray();
            $transactionNumber = $this->generateTransactionNumber();

            foreach ($products as $productData) {
                // Skip if already inserted for this PO
                if (in_array($productData['product_id'], $existingProductIds, true)) {
                    \Log::info('Offline Sync - Skip existing product for PO', [
                        'po_number' => $poNumber,
                        'product_id' => $productData['product_id']
                    ]);
                    continue;
                }

                $product = Product::find($productData['product_id']);
                $quantityPerCarton = $product ? intval($product->quantity_per_carton) : intval($productData['quantity_per_carton'] ?? 1);
                if ($quantityPerCarton <= 0) { $quantityPerCarton = 1; }

                $quantityCarton = intval($productData['quantity_carton']);
                $quantityPiece = intval($productData['quantity_piece']);
                $totalQuantityPiece = ($quantityCarton * $quantityPerCarton) + $quantityPiece;

                // Skip if total is zero
                if ($totalQuantityPiece <= 0) {
                    \Log::info('Offline Sync - Skip zero quantity item', [
                        'po_number' => $poNumber,
                        'product_id' => $productData['product_id'],
                        'quantity_carton' => $quantityCarton,
                        'quantity_piece' => $quantityPiece,
                        'quantity_per_carton' => $quantityPerCarton
                    ]);
                    continue;
                }

                $unitPrice = floatval($productData['unit_price']);
                $totalAmount = $totalQuantityPiece * $unitPrice;

                $transactions[] = [
                    'transaction_number' => $transactionNumber,
                    'transaction_date' => $data['transaction_date'] ?? now()->format('Y-m-d'),
                    'delivery_date' => $data['delivery_date'] ?? now()->addDays(7)->format('Y-m-d'),
                    'product_id' => $productData['product_id'],
                    'sales_id' => $salesId,
                    'supplier_id' => $productData['supplier_id'],
                    'quantity_carton' => $quantityCarton,
                    'quantity_piece' => $quantityPiece,
                    'total_quantity_piece' => $totalQuantityPiece,
                    'unit_price' => $productData['unit_price'],
                    'total_amount' => $totalAmount,
                    'notes' => $productData['notes'] ?? null,
                    'general_notes' => $data['general_notes'] ?? null,
                    'order_acc_by' => $data['order_acc_by'] ?? 'DIFA',
                    'po_number' => $poNumber,
                    'approval_status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (empty($transactions)) {
                // If no new rows to insert but PO already exists, treat as success idempotently
                $existingForPo = SalesTransaction::where('po_number', $poNumber)->get();
                if ($existingForPo->count() > 0) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Purchase order already synced',
                        'data' => [
                            'transaction_number' => optional($existingForPo->first())->transaction_number,
                            'po_number' => $poNumber,
                            'item_count' => $existingForPo->count()
                        ]
                    ]);
                }
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada transaksi yang valid untuk disimpan.'
                ], 400);
            }

            // Insert all transactions (same as bulkStore)
            SalesTransaction::insert($transactions);
            
            \Log::info('Offline Sync - Inserted Transactions', [
                'po_number' => $poNumber,
                'inserted_count' => count($transactions)
            ]);
            
            // Log the transaction creation
            \Log::info('Offline Sync - Transaction Created', [
                'transaction_count' => count($transactions),
                'transaction_number' => $transactionNumber,
                'po_number' => $poNumber,
                'sales_id' => $salesId
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Purchase order created successfully (' . count($transactions) . ' item)',
                'data' => [
                    'transaction_number' => $transactionNumber,
                    'po_number' => $poNumber,
                    'item_count' => count($transactions)
                ]
            ]);
            
            }); // End of DB::transaction
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle unique constraint violation
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'unique_po_number') !== false) {
                \Log::info('Duplicate PO detected via unique constraint', [
                    'po_number' => $data['po_number'] ?? 'unknown',
                    'user_id' => $userId
                ]);
                
                $existingTransaction = SalesTransaction::where('po_number', $data['po_number'])->first();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Purchase order already exists',
                    'data' => $existingTransaction
                ]);
            }
            
            \Log::error('Offline Sync Failed - Database Error', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create purchase order: ' . $e->getMessage()
            ], 500);
            
        } catch (Exception $e) {
            \Log::error('Offline Sync Failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    private function hasBracketedProducts(array $data): bool
    {
        foreach ($data as $key => $value) {
            if (preg_match('/^products\\[(\\d+)\\]\\[product_id\\]$/', $key)) {
                return true;
            }
        }
        return false;
    }

    private function extractBracketedProductIndexes(array $data): array
    {
        $indexes = [];
        foreach ($data as $key => $value) {
            if (preg_match('/^products\\[(\\d+)\\]\\[product_id\\]$/', $key, $m)) {
                $indexes[] = intval($m[1]);
            }
        }
        $indexes = array_values(array_unique($indexes));
        sort($indexes);
        return $indexes;
    }

}
