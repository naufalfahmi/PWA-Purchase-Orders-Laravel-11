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
        return 'ST-' . date('Ymd') . '-' . Str::upper(Str::random(6));
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
            \DB::raw('COUNT(*) as total_items'),
            \DB::raw('SUM(CASE WHEN quantity_carton > 0 THEN quantity_carton ELSE quantity_piece END) as total_quantity'),
            \DB::raw('SUM(CASE WHEN quantity_carton > 0 THEN quantity_carton * unit_price ELSE quantity_piece * unit_price END) as total_amount'),
            \DB::raw('MIN(created_at) as created_at'),
            \DB::raw('MAX(updated_at) as updated_at')
        ])
        ->with(['sales', 'approver'])
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

        // Filter berdasarkan approval status
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
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

        return view('sales-transaction.index', compact('poList', 'products', 'salesList'));
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
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:transaction_date',
            'sales_id' => 'required|exists:sales,id',
            'po_number' => 'nullable|string|max:255',
            'general_notes' => 'nullable|string',
            'order_acc_by' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.supplier_id' => 'required|exists:suppliers,id',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_carton' => 'nullable|integer|min:0',
            'products.*.quantity_piece' => 'nullable|integer|min:0',
            'products.*.quantity_type' => 'required|in:carton,piece',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.notes' => 'nullable|string',
        ]);

        // Validate that at least one product has quantity > 0
        $hasValidProduct = false;
        foreach ($validated['products'] as $product) {
            if (($product['quantity_type'] === 'carton' && $product['quantity_carton'] > 0) || 
                ($product['quantity_type'] === 'piece' && $product['quantity_piece'] > 0)) {
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
            // Skip if no quantity based on quantity type
            if (($productData['quantity_type'] === 'carton' && $productData['quantity_carton'] == 0) || 
                ($productData['quantity_type'] === 'piece' && $productData['quantity_piece'] == 0)) {
                continue;
            }

            $product = Product::find($productData['product_id']);
            $quantityCarton = $productData['quantity_type'] === 'carton' ? $productData['quantity_carton'] : 0;
            $quantityPiece = $productData['quantity_type'] === 'piece' ? $productData['quantity_piece'] : 0;
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

        // Hitung total berbasis input mentah (tanpa konversi CTN -> PCS)
        $totalQuantity = $transactions->sum(function ($t) {
            return ($t->quantity_carton && $t->quantity_carton > 0)
                ? (int) $t->quantity_carton
                : (int) $t->quantity_piece;
        });

        $totalAmount = $transactions->sum(function ($t) {
            $rawQty = ($t->quantity_carton && $t->quantity_carton > 0)
                ? (int) $t->quantity_carton
                : (int) $t->quantity_piece;
            return $rawQty * (float) $t->unit_price;
        });

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
            'products.*.quantity_type' => 'required|in:carton,piece',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.notes' => 'nullable|string',
        ]);

        // Delete old items for this PO and re-insert based on submitted items
        SalesTransaction::where('po_number', $poNumber)->delete();

        $transactions = [];
        $transactionNumber = $this->generateTransactionNumber();

        foreach ($validated['products'] as $productData) {
            if (($productData['quantity_type'] === 'carton' && ($productData['quantity_carton'] ?? 0) == 0) || 
                ($productData['quantity_type'] === 'piece' && ($productData['quantity_piece'] ?? 0) == 0)) {
                continue;
            }

            $product = Product::find($productData['product_id']);
            $quantityCarton = $productData['quantity_type'] === 'carton' ? ($productData['quantity_carton'] ?? 0) : 0;
            $quantityPiece = $productData['quantity_type'] === 'piece' ? ($productData['quantity_piece'] ?? 0) : 0;
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
            \DB::raw('SUM(CASE WHEN quantity_carton > 0 THEN quantity_carton ELSE quantity_piece END) as total_quantity'),
            \DB::raw('SUM(CASE WHEN quantity_carton > 0 THEN quantity_carton * unit_price ELSE quantity_piece * unit_price END) as total_amount'),
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

}
