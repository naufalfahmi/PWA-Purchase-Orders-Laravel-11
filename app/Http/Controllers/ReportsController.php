<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesTransaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Sales;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\POReportExport;
use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function __construct()
    {
        // Middleware is now handled in routes
    }

    /**
     * Display the reports index page
     */
    public function index(Request $request)
    {
        // Build grouped-by-PO dataset (same base as PO list)
        $query = SalesTransaction::select([
            'po_number',
            \DB::raw('MAX(transaction_number) as transaction_number'),
            \DB::raw('MAX(transaction_date) as transaction_date'),
            \DB::raw('MAX(delivery_date) as delivery_date'),
            \DB::raw('MAX(sales_id) as sales_id'),
            \DB::raw('MAX(supplier_id) as supplier_id'),
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
        ->with(['sales', 'approver', 'supplier'])
        ->groupBy('po_number');

        // Filter by transaction date range (pre-group filter)
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        // Filter by delivery date range
        if ($request->filled('delivery_start_date')) {
            $query->whereDate('delivery_date', '>=', $request->delivery_start_date);
        }
        if ($request->filled('delivery_end_date')) {
            $query->whereDate('delivery_date', '<=', $request->delivery_end_date);
        }

        // Filter by supplier (pre-group filter)
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by approval status (pre-group filter)
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        // Filter by PO number
        if ($request->filled('po_number')) {
            $query->where('po_number', 'like', '%' . $request->po_number . '%');
        }

        $transactions = $query->orderBy(\DB::raw('MIN(created_at)'), 'desc')->paginate(20);

        // Get filter options
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $approvalStatuses = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected'
        ];

        // Calculate summary statistics using grouped-by-PO
        $collection = $query->get();
        $summary = $this->calculateSummary($collection);
        $monthlyData = $this->getMonthlyData($collection);

        // Sales and monthly amount charts can use grouped data
        $salesAmountData = $this->getSalesAmountData($collection);
        $monthlyAmountData = $this->getMonthlyAmountData($collection);

        // Build detailed dataset (no grouping) for product/category charts
        $detailedQuery = SalesTransaction::with(['product', 'supplier', 'sales', 'approver']);
        if ($request->filled('start_date')) {
            $detailedQuery->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $detailedQuery->whereDate('transaction_date', '<=', $request->end_date);
        }
        if ($request->filled('delivery_start_date')) {
            $detailedQuery->whereDate('delivery_date', '>=', $request->delivery_start_date);
        }
        if ($request->filled('delivery_end_date')) {
            $detailedQuery->whereDate('delivery_date', '<=', $request->delivery_end_date);
        }
        if ($request->filled('supplier_id')) {
            $detailedQuery->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('approval_status')) {
            $detailedQuery->where('approval_status', $request->approval_status);
        }
        if ($request->filled('po_number')) {
            $detailedQuery->where('po_number', 'like', '%' . $request->po_number . '%');
        }
        $detailed = $detailedQuery->get();

        // Use detailed transactions to populate product/category charts
        $topProductsData = $this->getTopProductsData($detailed);
        $topCategoriesData = $this->getTopCategoriesData($detailed);

        return view('reports.index', compact(
            'transactions',
            'suppliers',
            'approvalStatuses',
            'summary',
            'monthlyData',
            'salesAmountData',
            'monthlyAmountData',
            'topProductsData',
            'topCategoriesData'
        ));
    }

    /**
     * Export PO data to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            \Log::info('Excel export started', ['user_id' => auth()->id(), 'request' => $request->all()]);
            
            // Check authentication
            if (!auth()->check()) {
                \Log::error('Excel export failed - not authenticated');
                return redirect()->route('login')->with('error', 'Please login first');
            }
            
            // Check role
            if (!auth()->user()->hasRole('owner')) {
                \Log::error('Excel export failed - insufficient role', ['user_role' => auth()->user()->roles->first()->name ?? 'none']);
                return redirect()->back()->with('error', 'Insufficient permissions');
            }
            
            // Use detailed item rows so product-level columns are populated
            $query = SalesTransaction::with(['product', 'supplier', 'sales', 'approver']);

            // Apply same filters as index
            if ($request->filled('start_date')) {
                $query->whereDate('transaction_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('transaction_date', '<=', $request->end_date);
            }
            if ($request->filled('delivery_start_date')) {
                $query->whereDate('delivery_date', '>=', $request->delivery_start_date);
            }
            if ($request->filled('delivery_end_date')) {
                $query->whereDate('delivery_date', '<=', $request->delivery_end_date);
            }
            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }
            if ($request->filled('approval_status')) {
                $query->where('approval_status', $request->approval_status);
            }
            if ($request->filled('po_number')) {
                $query->where('po_number', 'like', '%' . $request->po_number . '%');
            }

            $transactions = $query->orderBy('transaction_date', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->get();

            \Log::info('Excel export data loaded', ['count' => $transactions->count()]);

            $fileName = 'PO_Report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            \Log::info('Excel export created', [
                'record_count' => $transactions->count()
            ]);
            
            return Excel::download(new \App\Exports\POReportExport($transactions, false), $fileName);
        } catch (\Exception $e) {
            \Log::error('Excel export failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export PO data to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            \Log::info('PDF export started', ['user_id' => auth()->id(), 'request' => $request->all()]);
            
            // Check authentication
            if (!auth()->check()) {
                \Log::error('PDF export failed - not authenticated');
                return redirect()->route('login')->with('error', 'Please login first');
            }
            
            // Check role
            if (!auth()->user()->hasRole('owner')) {
                \Log::error('PDF export failed - insufficient role', ['user_role' => auth()->user()->roles->first()->name ?? 'none']);
                return redirect()->back()->with('error', 'Insufficient permissions');
            }
            
            // Use detailed item rows so product-level columns are populated in PDF table
            $query = SalesTransaction::with(['product', 'supplier', 'sales', 'approver']);

            // Apply same filters as index
            if ($request->filled('start_date')) {
                $query->whereDate('transaction_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('transaction_date', '<=', $request->end_date);
            }
            if ($request->filled('delivery_start_date')) {
                $query->whereDate('delivery_date', '>=', $request->delivery_start_date);
            }
            if ($request->filled('delivery_end_date')) {
                $query->whereDate('delivery_date', '<=', $request->delivery_end_date);
            }
            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }
            if ($request->filled('approval_status')) {
                $query->where('approval_status', $request->approval_status);
            }
            if ($request->filled('po_number')) {
                $query->where('po_number', 'like', '%' . $request->po_number . '%');
            }

            $transactions = $query->orderBy('transaction_date', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->get();

            \Log::info('PDF export data loaded', ['count' => $transactions->count()]);

            $summary = $this->calculateSummary($transactions);

            $html = view('reports.pdf', compact('transactions', 'summary'))->render();
            
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            $fileName = 'PO_Report_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            $output = $dompdf->output();

            \Log::info('PDF export completed', ['file_size' => strlen($output)]);

            $response = response($output, 200);
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            $response->headers->set('Content-Length', strlen($output));
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Accept-Ranges', 'bytes');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
            $response->headers->set('Cross-Origin-Embedder-Policy', 'unsafe-none');
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
            
            return $response;
        } catch (\Exception $e) {
            \Log::error('PDF export failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummary($transactions)
    {
        // Group by unique PO number to avoid double-counting per PO
        $groupedByPO = $transactions->groupBy('po_number');

        // Total transaksi should be by number PO (unique PO count)
        $totalTransactions = $groupedByPO->count();

        // Aggregate totals across unique POs
        $totalAmount = $groupedByPO->map(function ($poGroup) {
            return $poGroup->sum('total_amount');
        })->sum();

        $totalQuantity = $groupedByPO->map(function ($poGroup) {
            return $poGroup->sum('total_quantity_piece');
        })->sum();

        // Status counts by PO: use the most frequent status within the PO
        $statusCounts = $groupedByPO->map(function ($poGroup) {
            return $poGroup->groupBy('approval_status')->map->count()->sortDesc()->keys()->first();
        })->groupBy(function ($status) {
            return $status;
        })->map->count();

        // Supplier counts by PO (count unique POs per supplier)
        $supplierCounts = $groupedByPO->map(function ($poGroup) {
            return optional($poGroup->first()->supplier)->nama_supplier ?: 'N/A';
        })->groupBy(function ($supplierName) {
            return $supplierName;
        })->map->count();

        return [
            'total_transactions' => $totalTransactions,
            'total_amount' => $totalAmount,
            'total_quantity' => $totalQuantity,
            'status_counts' => $statusCounts,
            'supplier_counts' => $supplierCounts,
        ];
    }

    public function getMonthlyData($transactions)
    {
        // Get last 6 months data, counting unique PO numbers per month
        $months = [];
        $values = [];

        // Determine a representative date per PO (use earliest transaction_date in the PO)
        $poWithDate = $transactions->groupBy('po_number')->map(function ($poGroup) {
            return $poGroup->filter(function ($t) {
                return !empty($t->transaction_date);
            })->sortBy('transaction_date')->first();
        })->filter();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');

            $count = $poWithDate->filter(function ($t) use ($date) {
                return $t->transaction_date >= $date->copy()->startOfMonth() && $t->transaction_date <= $date->copy()->endOfMonth();
            })->count();

            $months[] = $monthName;
            $values[] = $count;
        }

        return [
            'labels' => $months,
            'values' => $values
        ];
    }

    /**
     * Get sales amount data for chart
     */
    private function getSalesAmountData($transactions)
    {
        $salesAmounts = $transactions->groupBy('sales.name')
            ->map(function ($group) {
                return $group->sum('total_amount');
            })
            ->sortDesc()
            ->take(10);

        return [
            'labels' => $salesAmounts->keys()->toArray(),
            'values' => $salesAmounts->values()->toArray()
        ];
    }

    /**
     * Get monthly amount data for chart
     */
    private function getMonthlyAmountData($transactions)
    {
        $months = [];
        $values = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $amount = $transactions->where('transaction_date', '>=', $date->startOfMonth())
                                 ->where('transaction_date', '<=', $date->endOfMonth())
                                 ->sum('total_amount');
            
            $months[] = $monthName;
            $values[] = $amount;
        }
        
        return [
            'labels' => $months,
            'values' => $values
        ];
    }

    /**
     * Get top products data for chart
     */
    private function getTopProductsData($transactions)
    {
        $productCounts = $transactions->groupBy('product.name')
            ->map->count()
            ->sortDesc()
            ->take(10);

        return [
            'labels' => $productCounts->keys()->toArray(),
            'values' => $productCounts->values()->toArray()
        ];
    }

    /**
     * Get top categories data for chart
     */
    private function getTopCategoriesData($transactions)
    {
        $categoryCounts = $transactions->groupBy('product.category')
            ->map->count()
            ->sortDesc()
            ->take(10);

        return [
            'labels' => $categoryCounts->keys()->toArray(),
            'values' => $categoryCounts->values()->toArray()
        ];
    }
}
