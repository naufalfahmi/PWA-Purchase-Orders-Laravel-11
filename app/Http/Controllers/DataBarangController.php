<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;

class DataBarangController extends Controller
{
    public function index(Request $request)
    {
        $supplierId = $request->get('supplier_id');
        
        $query = Product::with('supplier')->active();
        
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        
        $products = $query->latest()->paginate(20);
        $suppliers = Supplier::active()->get();
        
        if ($request->ajax()) {
            return response()->json([
                'products' => $products->items(),
                'hasMorePages' => $products->hasMorePages(),
                'nextPageUrl' => $products->nextPageUrl()
            ]);
        }
        
        return view('data-barang.index', compact('products', 'suppliers'));
    }

    public function loadMore(Request $request)
    {
        $supplierId = $request->get('supplier_id');
        $page = $request->get('page', 2);
        
        $query = Product::with('supplier')->active();
        
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        
        $products = $query->latest()->paginate(20, ['*'], 'page', $page);
        
        return response()->json([
            'products' => $products->items(),
            'hasMorePages' => $products->hasMorePages(),
            'nextPageUrl' => $products->nextPageUrl(),
            'currentPage' => $products->currentPage(),
            'total' => $products->total()
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::active()->get();
        return view('data-barang.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'sub_category' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'stock_unit' => 'nullable|in:PCS,CTN',
            'pieces_per_carton' => 'nullable|integer|min:1',
            'quantity_per_carton' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:50|unique:products,sku',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        // Prepare data for Product creation
        $productData = $request->except(['stock_quantity', 'stock_unit']);
        
        // Add stock unit information
        $productData['stock_unit'] = $request->stock_unit;
        $productData['stock_quantity'] = $request->stock_quantity;
        $productData['pieces_per_carton'] = $request->pieces_per_carton;

        Product::create($productData);

        return redirect()->route('data-barang.index')
            ->with('success', 'Data barang berhasil ditambahkan.');
    }

    public function show(Product $dataBarang)
    {
        $dataBarang->load(['supplier', 'salesTransactions']);
        return view('data-barang.show', compact('dataBarang'));
    }

    public function edit(Product $dataBarang)
    {
        $suppliers = Supplier::active()->get();
        return view('data-barang.edit', compact('dataBarang', 'suppliers'));
    }

    public function update(Request $request, Product $dataBarang)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'sub_category' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'stock_unit' => 'nullable|in:PCS,CTN',
            'pieces_per_carton' => 'nullable|integer|min:1',
            'quantity_per_carton' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:50|unique:products,sku,' . $dataBarang->id,
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        // Prepare data for Product update
        $productData = $request->except(['stock_quantity', 'stock_unit']);
        
        // Add stock unit information
        $productData['stock_unit'] = $request->stock_unit;
        $productData['stock_quantity'] = $request->stock_quantity;
        $productData['pieces_per_carton'] = $request->pieces_per_carton;

        $dataBarang->update($productData);

        return redirect()->route('data-barang.index')
            ->with('success', 'Data barang berhasil diupdate.');
    }

    public function destroy(Product $dataBarang)
    {
        $dataBarang->delete();
        return redirect()->route('data-barang.index')
            ->with('success', 'Data barang berhasil dihapus.');
    }
}