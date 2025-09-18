<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesTransaction;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        // Base query for filtering by sales if user is sales
        $baseQuery = SalesTransaction::query();
        if (auth()->user()->isSales()) {
            $currentSales = \App\Models\Sales::where('name', auth()->user()->name)->first();
            if ($currentSales) {
                $baseQuery->where('sales_id', $currentSales->id);
            }
        }

        // Count PO numbers using same logic as index page (group by po_number and other fields)
        $totalPOs = (clone $baseQuery)->select('po_number')
            ->groupBy('po_number')
            ->get()
            ->count();
            
        $pendingPOs = (clone $baseQuery)->where('approval_status', 'pending')
            ->select('po_number')
            ->groupBy('po_number')
            ->get()
            ->count();
            
        $approvedPOs = (clone $baseQuery)->where('approval_status', 'approved')
            ->select('po_number')
            ->groupBy('po_number')
            ->get()
            ->count();
            
        $rejectedPOs = (clone $baseQuery)->where('approval_status', 'rejected')
            ->select('po_number')
            ->groupBy('po_number')
            ->get()
            ->count();
        
        // Only show product stats for owner accounts
        $totalProducts = auth()->user()->isOwner() ? Product::count() : 0;
        $activeProducts = auth()->user()->isOwner() ? Product::where('is_active', true)->count() : 0;

        $recentPOs = (clone $baseQuery)->select([
            'po_number',
            'transaction_number',
            'sales_id',
            'approval_status',
            \DB::raw('COUNT(*) as total_items'),
            \DB::raw('SUM(total_amount) as total_amount'),
            \DB::raw('MIN(created_at) as created_at')
        ])
        ->with(['sales'])
        ->groupBy(['po_number', 'transaction_number', 'sales_id', 'approval_status'])
        ->latest('created_at')
        ->take(5)
        ->get();

        $stats = [
            'total_pos' => $totalPOs,
            'pending_pos' => $pendingPOs,
            'approved_pos' => $approvedPOs,
            'rejected_pos' => $rejectedPOs,
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
        ];

        return view('dashboard.index', compact('stats', 'recentPOs'));
    }
}