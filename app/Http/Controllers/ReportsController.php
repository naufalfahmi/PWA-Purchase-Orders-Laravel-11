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
        $query = SalesTransaction::with(['product', 'supplier', 'sales', 'approver']);

        // Filter by transaction date range
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

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by approval status
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        // Filter by PO number
        if ($request->filled('po_number')) {
            $query->where('po_number', 'like', '%' . $request->po_number . '%');
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(20);

        // Get filter options
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $approvalStatuses = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected'
        ];

        // Calculate summary statistics
        $summary = $this->calculateSummary($query->get());
        $monthlyData = $this->getMonthlyData($query->get());
        
        // Additional chart data
        $salesAmountData = $this->getSalesAmountData($query->get());
        $monthlyAmountData = $this->getMonthlyAmountData($query->get());
        $topProductsData = $this->getTopProductsData($query->get());
        $topCategoriesData = $this->getTopCategoriesData($query->get());

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
            
            return Excel::download(new \App\Exports\POReportExport($transactions), $fileName);
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
        $totalTransactions = $transactions->count();
        $totalAmount = $transactions->sum('total_amount');
        $totalQuantity = $transactions->sum('total_quantity_piece');
        
        $statusCounts = $transactions->groupBy('approval_status')->map->count();
        
        $supplierCounts = $transactions->groupBy('supplier.nama_supplier')->map->count();
        
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
        // Get last 6 months data
        $months = [];
        $values = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $count = $transactions->where('transaction_date', '>=', $date->startOfMonth())
                                 ->where('transaction_date', '<=', $date->endOfMonth())
                                 ->count();
            
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
