@extends('layouts.app')

@section('title', 'Edit PO - Munah - Purchase Orders')
@section('page-title', 'Edit PO')

<style>
/* Normalize header font size and padding to match other pages */
@media (max-width: 768px) {
    .text-lg {
        font-size: 1.125rem !important;
    }
    
    /* Ensure header title has consistent font size */
    header h1.text-lg {
        font-size: 1.125rem !important;
    }
    
    /* Normalize header padding to match other pages */
    header .px-4 {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    header .py-3 {
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
    }
}

/* Custom styles for the simplified product layout */
.product-row {
    transition: all 0.2s ease-in-out;
}

.product-row:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.quantity-input:focus {
    z-index: 10;
}

.quantity-type-select:focus {
    z-index: 10;
}

/* Quantity display styling */
.quantity-total {
    color: #1f2937;
    font-weight: 600;
}

.quantity-unit {
    color: #6b7280;
    font-size: 0.75rem;
}

/* Compact quantity display for col-6 layout */
.space-y-2 > div:last-child {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.25rem;
}

/* Ensure quantity input and type select are properly connected */
.flex .quantity-type-select {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    width: 96px; /* w-24 = 96px */
    text-align: center;
    font-size: 14px;
}

.flex .quantity-input {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    flex: 1;
}

/* Ensure consistent width for all input fields */
.input-field {
    width: 100%;
    box-sizing: border-box;
}

/* Select2 width consistency */
.select2-container {
    width: 100% !important;
}

.select2-container .select2-selection {
    width: 100% !important;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    min-height: 38px;
    padding: 0.5rem;
}

/* Input field to match Select2 styling */
.input-field {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    min-height: 38px;
    padding: 0.5rem;
    font-size: 14px;
}

/* Mobile: separate fields with proper spacing */
@media (max-width: 639px) {
    .flex .quantity-input,
    .flex .quantity-type-select {
        border-radius: 0.375rem; /* Reset to normal border radius on mobile */
        border: 1px solid #d1d5db;
    }
    
    .flex .quantity-input:focus,
    .flex .quantity-type-select:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
}

/* Mobile touch optimization */
@media (max-width: 1024px) {
    /* Larger touch targets */
    .input-field {
        min-height: 48px;
        font-size: 16px; /* Prevent zoom on iOS */
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        width: 100%;
        box-sizing: border-box;
    }
    
    /* Ensure consistent width for all input fields */
    .product-row .input-field {
        width: 100%;
        box-sizing: border-box;
    }
    
    /* Mobile Select2 width consistency */
    .product-row .select2-container {
        width: 100% !important;
    }
    
    .product-row .select2-container .select2-selection {
        width: 100% !important;
        min-height: 48px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.75rem;
        font-size: 16px;
    }
    
    /* Mobile input field to match Select2 styling */
    .product-row .input-field {
        width: 100%;
        box-sizing: border-box;
        min-height: 48px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.75rem;
        font-size: 16px;
    }
    
    
    
    .input-field:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Button touch optimization */
    button {
        min-height: 48px;
        min-width: 48px;
        font-size: 16px;
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
    }
    
    /* Select touch optimization */
    select {
        min-height: 48px;
        font-size: 16px;
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        background-color: #ffffff;
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Improved spacing for mobile */
    .card {
        margin-bottom: 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    /* Better button styling for mobile */
    .btn-primary, .btn-secondary {
        min-height: 48px;
        font-size: 16px;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary:hover, .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    /* Mobile-specific improvements */
    .btn-primary:active, .btn-secondary:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    
    /* Better spacing for mobile forms */
    .card {
        padding: 1rem;
    }
    
    /* Improved mobile grid for header information */
    .grid.grid-cols-1.md\\:grid-cols-2 {
        gap: 1rem;
    }
    
    /* Mobile-friendly form spacing */
    .mb-6 {
        margin-bottom: 1.5rem;
    }
    
    /* Better mobile product row spacing */
    #productsContainer {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
}

/* Close button styling */
.product-row button[onclick="removeProductRow(this)"] {
    transition: all 0.2s ease-in-out;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem; /* p-2 */
    border-radius: 50%; /* rounded-full */
    width: 2.5rem; /* w-10 */
    height: 2.5rem; /* h-10 */
    flex-shrink: 0;
}

.product-row button[onclick="removeProductRow(this)"]:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.product-row button[onclick="removeProductRow(this)"]:active {
    transform: scale(0.95);
}

/* Mobile touch optimization for close button */
@media (max-width: 1024px) {
    .product-row button[onclick="removeProductRow(this)"] {
        width: 2.25rem; /* w-9 */
        height: 2.25rem; /* h-9 */
        padding: 0.375rem;
    }
    
    .product-row button[onclick="removeProductRow(this)"] svg {
        width: 1rem; /* w-4 */
        height: 1rem; /* h-4 */
    }
}

/* Extra small mobile devices */
@media (max-width: 480px) {
    .product-row button[onclick="removeProductRow(this)"] {
        width: 2rem; /* w-8 */
        height: 2rem; /* h-8 */
        padding: 0.25rem;
    }
    
    .product-row button[onclick="removeProductRow(this)"] svg {
        width: 0.875rem; /* w-3.5 */
        height: 0.875rem; /* h-3.5 */
    }
}

/* Mobile responsiveness - iPhone & Android optimized */
@media (max-width: 1024px) {
    .product-row {
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: #ffffff;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .product-row .grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .product-row .lg\\:col-span-3,
    .product-row .lg\\:col-span-2,
    .product-row .lg\\:col-span-1 {
        grid-column: span 1;
    }
    
    /* Mobile product field */
    .product-row .lg\\:col-span-3 {
        margin-bottom: 1rem;
    }
    
    /* Additional spacing for product select on mobile */
    .product-row .lg\\:col-span-3.mb-3 {
        margin-bottom: 1.25rem;
    }
    
    /* Mobile quantity field - improved layout */
    .product-row .lg\\:col-span-2 {
        margin-bottom: 0.75rem;
    }
    
    .mobile-quantity-container {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .mobile-quantity-container .flex {
        display: flex;
        gap: 0.5rem;
        align-items: stretch;
    }
    
    .mobile-quantity-container .quantity-type-select {
        flex-shrink: 0;
        width: 96px; /* w-24 = 96px */
        font-size: 14px;
        text-align: center;
    }
    
    .mobile-quantity-container .quantity-input {
        flex: 1;
        min-width: 0;
    }
    
    .quantity-display-mobile {
        display: block;
        text-align: center;
        font-weight: 500;
        border: 1px solid #d1d5db;
    }
    
    /* Mobile unit price and action layout */
    .product-row .lg\\:col-span-1 {
        margin-bottom: 0.75rem;
    }
    
    .mobile-price-container .unit-price {
        width: 100%;
    }
    
    /* Mobile notes field */
    .product-row > div:last-child {
        margin-top: 0.75rem;
    }
    
    /* Better spacing for mobile */
    .product-row .input-field {
        font-size: 16px; /* Prevent zoom on iOS */
        padding: 0.75rem;
        min-height: 48px;
    }
    
    .product-row label {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
}

/* Extra small mobile devices */
@media (max-width: 480px) {
    .product-row {
        padding: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .mobile-quantity-container .flex {
        flex-direction: row;
        gap: 0.5rem;
    }
    
    .mobile-quantity-container .quantity-type-select {
        width: 96px;
        flex-shrink: 0;
    }
    
    .mobile-quantity-container .quantity-input {
        flex: 1;
        min-width: 0;
    }
    
    
    .quantity-display-mobile {
        font-size: 13px;
        padding: 0.5rem;
    }
}


/* Tablet responsiveness */
@media (min-width: 769px) and (max-width: 1024px) {
    .product-row .grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    
    .product-row .md\\:col-span-3 {
        grid-column: span 2;
    }
    
    .product-row .md\\:col-span-2,
    .product-row .md\\:col-span-1 {
        grid-column: span 1;
    }
}
</style>

@section('content')
<div class="p-4">
    <form method="POST" action="{{ route('sales-transaction.update-po', $poNumber) }}" id="salesTransactionEditForm">
        @csrf
        @method('PATCH')

        <div class="card p-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Umum</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Transaksi <span class="text-red-500">*</span></label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', $header['transaction_date']) }}" class="input-field @error('transaction_date') border-red-500 @enderror" required>
                    @error('transaction_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengiriman</label>
                    <input type="date" name="delivery_date" value="{{ old('delivery_date', $header['delivery_date']) }}" class="input-field @error('delivery_date') border-red-500 @enderror">
                    @error('delivery_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sales <span class="text-red-500">*</span></label>
                    @php
                        $selectedSalesId = old('sales_id', $header['sales_id']);
                    @endphp
                    <select name="sales_id" class="input-field @error('sales_id') border-red-500 @enderror" required {{ isset($currentSales) ? 'disabled' : '' }}>
                        <option value="">Pilih Sales</option>
                        @foreach($salesList as $sales)
                            <option value="{{ $sales->id }}" {{ (string)$selectedSalesId === (string)$sales->id ? 'selected' : '' }}>{{ $sales->name }}</option>
                        @endforeach
                    </select>
                    @if(isset($currentSales))
                        <input type="hidden" name="sales_id" value="{{ $currentSales->id }}">
                    @endif
                    @error('sales_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">PO Number</label>
                    <input type="text" name="po_number" value="{{ old('po_number', $header['po_number']) }}" class="input-field @error('po_number') border-red-500 @enderror">
                    @error('po_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Umum</label>
                    <textarea name="general_notes" rows="3" class="input-field @error('general_notes') border-red-500 @enderror">{{ old('general_notes', $header['general_notes']) }}</textarea>
                    @error('general_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Order Acc By</label>
                    <select name="order_acc_by" id="orderAccBy" class="input-field @error('order_acc_by') border-red-500 @enderror">
                        <option value="">Pilih Toko</option>
                        @if(isset($orderAccOptions) && count($orderAccOptions))
                            @foreach($orderAccOptions as $opt)
                                <option value="{{ $opt }}" {{ old('order_acc_by', $header['order_acc_by']) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('order_acc_by')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier <span class="text-red-500">*</span></label>
                    @php
                        $selectedSupplierId = old('supplier_id');
                        // Get supplier from first transaction if no old value
                        if (!$selectedSupplierId && isset($transactions) && count($transactions) > 0) {
                            $firstTransaction = $transactions->first();
                            if ($firstTransaction && $firstTransaction->product && $firstTransaction->product->supplier) {
                                $selectedSupplierId = $firstTransaction->product->supplier->id;
                            }
                        }
                    @endphp
                    <select name="supplier_id" id="mainSupplierSelect" class="input-field @error('supplier_id') border-red-500 @enderror" required onchange="loadProductsByMainSupplier(this)">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ (string)$selectedSupplierId === (string)$supplier->id ? 'selected' : '' }}>
                                {{ $supplier->kode_supplier }} - {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="card p-4 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Produk</h2>
                <button type="button" onclick="addProductRowPrompt()" class="btn-primary flex items-center justify-center space-x-2 cursor-pointer w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Tambah Produk</span>
                </button>
            </div>

            <div id="productsContainer">
                @foreach($transactions as $i => $t)
                    @php
                        // Determine quantity type and value - same logic as detail page
                        $isCarton = ($t->quantity_carton ?? 0) > 0;
                        $isPiece = ($t->quantity_piece ?? 0) > 0;
                        $quantityType = $isCarton ? 'carton' : 'piece';
                        $quantityValue = $isCarton ? $t->quantity_carton : $t->quantity_piece;
                        
                                @endphp
                    <div class="product-row border rounded-lg p-4 mb-4 bg-white hover:bg-gray-50 transition-colors relative" data-index="{{ $i }}">
                        
                        <!-- Hidden fields for backend compatibility -->
                        <input type="hidden" name="products[{{ $i }}][supplier_id]" class="supplier-id-input" value="{{ optional(optional($t->product)->supplier)->id }}">
                        <input type="hidden" name="products[{{ $i }}][quantity_type]" class="quantity-type-hidden" value="{{ $quantityType }}">
                        <input type="hidden" name="products[{{ $i }}][quantity_carton]" class="quantity-carton-hidden" value="{{ $isCarton ? $quantityValue : 0 }}">
                        <input type="hidden" name="products[{{ $i }}][quantity_piece]" class="quantity-piece-hidden" value="{{ $isPiece ? $quantityValue : 0 }}">
                        
                        <!-- Main Product Row -->
                        <div class="grid grid-cols-1 lg:grid-cols-6 gap-4 mb-3 min-w-0">
                            <!-- Produk -->
                            <div class="lg:col-span-3 min-w-0">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Produk <span class="text-red-500">*</span></label>
                                <select class="input-field product-select w-full" name="products[{{ $i }}][product_id]" required>
                                    @if($t->product)
                                        <option value="{{ $t->product_id }}" selected data-name="{{ $t->product->name }}" data-price="{{ $t->product->price }}" data-carton="{{ $t->product->quantity_per_carton ?? 1 }}">{{ $t->product->name }} - Rp {{ number_format($t->product->price, 0, ',', '.') }}</option>
                                    @else
                                        <option value="">Pilih Produk</option>
                                    @endif
                                </select>
                            </div>
                            
                            <!-- Quantity -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                                <div class="mobile-quantity-container">
                                    <div class="flex gap-2">
                                        <select class="quantity-type-select input-field w-24 flex-shrink-0" onchange="onQuantityTypeChange(this)">
                                            <option value="carton" {{ $quantityType === 'carton' ? 'selected' : '' }}>CTN</option>
                                            <option value="piece" {{ $quantityType === 'piece' ? 'selected' : '' }}>PCS</option>
                                </select>
                                        <input type="number" name="products[{{ $i }}][{{ $isCarton ? 'quantity_carton' : 'quantity_piece' }}]" min="0" class="input-field quantity-input flex-1" oninput="calculateRowTotal(this)" onkeyup="calculateRowTotal(this)" onfocus="clearZeroValue(this)" placeholder="Qty" value="{{ $quantityValue }}" data-original-name="products[{{ $i }}][quantity_carton]" data-carton-name="products[{{ $i }}][quantity_carton]" data-piece-name="products[{{ $i }}][quantity_piece]">
                                        <input type="number" name="products[{{ $i }}][unit_price]" step="0.01" min="0" class="input-field unit-price flex-1" oninput="calculateRowTotal(this)" placeholder="Harga" value="{{ $t->unit_price }}">
                            </div>
                            </div>
                            </div>
                            
                           
                            </div>
                        
                        <!-- Catatan Produk -->
                        <div class="flex items-end gap-3">
                            <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Produk</label>
                                <input type="text" name="products[{{ $i }}][notes]" class="input-field w-full" placeholder="Catatan khusus untuk produk ini" value="{{ $t->notes }}">
                            </div>
                            <button type="button" onclick="removeProductRow(this)" class="bg-red-500 hover:bg-red-600 text-white rounded-full transition-colors flex items-center justify-center flex-shrink-0" title="Hapus produk">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                </button>
                        </div>
                    </div>
                @endforeach
            </div>

            @error('products')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Summary Section -->
        <div class="card p-6 mb-6 bg-white border-2 border-gray-300 shadow-lg">
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">PURCHASE ORDER</h3>
            </div>

            <!-- Receipt Content -->
            <div class="space-y-4">
                <!-- Date -->
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tanggal:</span>
                    <span class="font-medium" id="summaryDate">{{ date('d/m/Y') }}</span>
                </div>

                <!-- Sales -->
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Sales:</span>
                    <span class="font-medium" id="summarySales">-</span>
                </div>

                <!-- Supplier -->
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Supplier:</span>
                    <span class="font-medium" id="summarySupplier">-</span>
                </div>

                <!-- Dotted Line -->
                <div class="border-t border-dotted border-gray-400 my-4"></div>

                <!-- Products Summary -->
                <div class="space-y-2" id="productsSummary">
                    <!-- Products will be listed here dynamically -->
                </div>

                <!-- Dotted Line -->
                <div class="border-t border-dotted border-gray-400 my-4"></div>

                <!-- Totals -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Total Items:</span>
                        <span class="font-medium" id="totalProducts">0</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Total Quantity:</span>
                        <span class="font-medium" id="totalQuantity">0 pcs</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-900">TOTAL AMOUNT:</span>
                        <span class="text-lg font-bold text-blue-600" id="totalAmount">Rp 0</span>
                    </div>
                </div>

                <!-- Footer -->
               
            </div>
        </div>

        <div class="flex space-x-3">
            <a href="{{ route('sales-transaction.index') }}" class="btn-secondary flex-1 text-center">Batal</a>
            <button type="submit" class="btn-primary flex-1">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

<!-- Product Row Template -->
<template id="productRowTemplate">
    <div class="product-row border rounded-lg p-4 mb-4 bg-white hover:bg-gray-50 transition-colors relative" data-index="">
        
        <!-- Hidden fields for backend compatibility -->
        <input type="hidden" name="products[INDEX][supplier_id]" class="supplier-id-input" value="">
        <input type="hidden" name="products[INDEX][quantity_type]" class="quantity-type-hidden" value="">
        <input type="hidden" name="products[INDEX][quantity_carton]" class="quantity-carton-hidden" value="0">
        <input type="hidden" name="products[INDEX][quantity_piece]" class="quantity-piece-hidden" value="0">
        
        <!-- Main Product Row -->
        <div class="grid grid-cols-1 lg:grid-cols-6 gap-4 mb-3 min-w-0">
            <!-- Produk -->
            <div class="lg:col-span-3 min-w-0">
                <label class="block text-sm font-medium text-gray-700 mb-2">Produk <span class="text-red-500">*</span></label>
                <select class="input-field product-select w-full" name="products[INDEX][product_id]" required>
                    <option value="">Pilih Supplier terlebih dahulu</option>
                </select>
            </div>
            
            <!-- Quantity -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                <div class="mobile-quantity-container">
                    <div class="flex gap-2">
                        <select class="quantity-type-select input-field w-24 flex-shrink-0" onchange="onQuantityTypeChange(this)">
                            <option value="carton">CTN</option>
                            <option value="piece">PCS</option>
                </select>
                        <input type="number" name="products[INDEX][quantity_carton]" min="0" class="input-field quantity-input flex-1" oninput="calculateRowTotal(this)" onkeyup="calculateRowTotal(this)" onfocus="clearZeroValue(this)" placeholder="Qty" data-original-name="products[INDEX][quantity_carton]" data-carton-name="products[INDEX][quantity_carton]" data-piece-name="products[INDEX][quantity_piece]">
                        <input type="number" name="products[INDEX][unit_price]" step="0.01" min="0" class="input-field unit-price flex-1" oninput="calculateRowTotal(this)" placeholder="Harga">
            </div>
            </div>
            </div>
            
           
            </div>
        
        <!-- Catatan Produk -->
        <div class="flex items-end gap-3">
            <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Produk</label>
                <input type="text" name="products[INDEX][notes]" class="input-field w-full" placeholder="Catatan khusus untuk produk ini">
            </div>
            <button type="button" onclick="removeProductRow(this)" class="bg-red-500 hover:bg-red-600 text-white rounded-full transition-colors flex items-center justify-center flex-shrink-0" title="Hapus produk">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                </button>
        </div>
    </div>
</template>

<!-- Select2 CSS/JS -->
<link href="{{ asset('libs/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('libs/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">
<script src="{{ asset('libs/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('libs/select2.min.js') }}"></script>
<script>
let productIndex = 0;

// Function untuk load produk berdasarkan main supplier selection
function loadProductsByMainSupplier(supplierSelect) {
    const supplierId = supplierSelect.value;
    
    if (supplierId) {
        // Update semua hidden supplier_id fields di product rows
        updateAllProductRowsSupplier(supplierId);
        
        // Load produk untuk semua product rows
        loadProductsForAllRows(supplierId);
        
        // Don't reset quantity and price for existing rows - they already have data from database
        // resetAllRowsQuantityAndPrice();
    } else {
        // Clear semua product selects
        clearAllProductSelects();
    }
}

function updateAllProductRowsSupplier(supplierId) {
    const supplierInputs = document.querySelectorAll('.supplier-id-input');
    supplierInputs.forEach(input => {
        input.value = supplierId;
    });
}

function loadProductsForAllRows(supplierId) {
    // Get CSRF token
    const metaCsrf = document.querySelector('meta[name="csrf-token"]');
    const inputTokenEl = document.querySelector('input[name="_token"]');
    const csrfToken = (metaCsrf && metaCsrf.getAttribute('content')) || (inputTokenEl && inputTokenEl.value) || '';
    
    // Load products
    const noAuthXhr = new XMLHttpRequest();
    noAuthXhr.open('GET', `{{ route('sales-transaction.get-products-no-auth') }}?supplier_id=${supplierId}`, true);
    noAuthXhr.setRequestHeader('Accept', 'application/json');
    noAuthXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    noAuthXhr.onreadystatechange = function() {
        if (noAuthXhr.readyState === 4) {
            if (noAuthXhr.status === 200) {
                try {
                    const data = JSON.parse(noAuthXhr.responseText);
                    if (data && Array.isArray(data.products)) {
                        updateAllProductSelects(data.products);
        return;
                    }
                } catch (e) {
                    console.error('Error parsing products data:', e);
                }
            }
            
            // Fallback to auth method
            loadProductsWithAuth(supplierId);
        }
    };
    noAuthXhr.send();
}

function updateAllProductSelects(products) {
    const productSelects = document.querySelectorAll('.product-select');
    productSelects.forEach(select => {
        // Store current selection before updating
        const currentValue = select.value;
        const currentOption = select.querySelector(`option[value="${currentValue}"]`);
        const currentData = currentOption ? {
            name: currentOption.dataset.name,
            price: currentOption.dataset.price,
            carton: currentOption.dataset.carton
        } : null;
        
        updateProductSelect(select, products, currentValue, currentData);
    });
}

function clearAllProductSelects() {
    const productSelects = document.querySelectorAll('.product-select');
    productSelects.forEach(select => {
        select.innerHTML = '<option value="">Pilih Supplier terlebih dahulu</option>';
        // Clear selection if using Select2
        if (typeof $ !== 'undefined' && $(select).data('select2')) {
            $(select).val(null).trigger('change');
        } else {
            select.value = '';
        }
    });
}

function loadProductsWithAuth(supplierId) {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value;
    
            const xhr = new XMLHttpRequest();
    xhr.open('GET', `{{ route('sales-transaction.get-products') }}?supplier_id=${supplierId}`, true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.withCredentials = true;
    if (csrfToken) {
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    }
        
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                    if (xhr.responseText.trim().startsWith('<!DOCTYPE html>') || xhr.responseText.trim().startsWith('<html')) {
                        console.error('Server returned HTML instead of JSON');
                        if (xhr.responseText.includes('login') || xhr.responseText.includes('Login')) {
                            clearAllProductSelects();
                            return;
                        }
                    }
                    
                    const products = JSON.parse(xhr.responseText);
                    updateAllProductSelects(products);
                    } catch (e) {
                    console.error('Error parsing JSON:', e);
                    clearAllProductSelects();
                    }
                } else {
                console.error('HTTP Error:', xhr.status);
                clearAllProductSelects();
            }
                }
            };
        
            xhr.onerror = function() {
        console.error('Network error');
        clearAllProductSelects();
            };
        
            xhr.send();
}


function updateProductSelect(productSelect, products, selectedProductId = null, selectedProductData = null) {
    if (!products || products.length === 0) {
        productSelect.innerHTML = '<option value="">Tidak ada produk</option>';
        // Clear selection if using Select2
                    if (typeof $ !== 'undefined' && $(productSelect).data('select2')) {
                        $(productSelect).val(null).trigger('change');
        } else {
            productSelect.value = '';
        }
        return;
    }
    productSelect.innerHTML = '<option value="">Pilih Produk</option>';
    products.forEach(product => {
        const option = document.createElement('option');
        option.value = product.id;
        option.dataset.price = product.price;
        option.dataset.carton = product.quantity_per_carton || 1;
        option.dataset.name = product.name;
        option.textContent = `${product.name} - Rp ${parseFloat(product.price).toLocaleString('id-ID')}`;
        
        // Mark as selected if this is the product we want to restore
        if (selectedProductId && selectedProductId == product.id) {
            option.selected = true;
        }
        
        productSelect.appendChild(option);
    });
    
    // If we have selected product data but no matching product in the list, add it manually
    if (selectedProductId && selectedProductData && !productSelect.querySelector(`option[value="${selectedProductId}"]`)) {
        const option = document.createElement('option');
        option.value = selectedProductId;
        option.dataset.price = selectedProductData.price;
        option.dataset.carton = selectedProductData.carton;
        option.dataset.name = selectedProductData.name;
        option.textContent = `${selectedProductData.name} - Rp ${parseFloat(selectedProductData.price).toLocaleString('id-ID')}`;
        option.selected = true;
        productSelect.appendChild(option);
    }
    
    // Update Select2 if it exists
    if (typeof $ !== 'undefined' && $(productSelect).data('select2')) {
        $(productSelect).trigger('change');
    }
    
    // Don't update unit price for existing rows - preserve sales_transactions.unit_price
    // Only update for new rows or if no existing price
    if (selectedProductId && selectedProductData) {
        const row = productSelect.closest('.product-row');
        const unitPriceInput = row.querySelector('.unit-price');
        
        // Check if this row has existing price from database
        const hasExistingPrice = unitPriceInput && unitPriceInput.value && parseFloat(unitPriceInput.value) > 0;
        
        if (!hasExistingPrice && unitPriceInput && selectedProductData.price) {
            // Only update price for new rows without existing data
            unitPriceInput.value = selectedProductData.price;
        }
        
        // Trigger updateProductInfo to ensure all fields are updated
    setTimeout(() => {
            updateProductInfo(productSelect);
        }, 100);
    }
}

// Function to clear zero value when user focuses on input
function clearZeroValue(input) {
    if (input.value === '0') {
        input.value = '';
    }
}

// Function to reset quantity and price when supplier or product changes
function resetQuantityAndPrice(row) {
    const quantityInput = row.querySelector('.quantity-input');
    const unitPriceInput = row.querySelector('.unit-price');
    const quantityTypeSelect = row.querySelector('.quantity-type-select');
    
    // Only reset if the row doesn't have existing data
    const hasExistingQuantity = quantityInput && quantityInput.value && parseFloat(quantityInput.value) > 0;
    const hasExistingPrice = unitPriceInput && unitPriceInput.value && parseFloat(unitPriceInput.value) > 0;
    const hasExistingData = hasExistingQuantity || hasExistingPrice;
    
    if (!hasExistingData) {
        // Reset quantity input
        if (quantityInput) {
            quantityInput.value = '0';
        }
        
        // Reset unit price input
        if (unitPriceInput) {
            unitPriceInput.value = '';
        }
        
        // Reset quantity type to default (CTN)
        if (quantityTypeSelect) {
            quantityTypeSelect.value = 'carton';
            onQuantityTypeChange(quantityTypeSelect);
        }
    }
    
    // Update quantity display
    updateQuantityDisplay(row);
    
    // Trigger calculation
    if (quantityInput) {
        calculateRowTotal(quantityInput);
    }
}

// Function to reset all existing rows quantity and price
function resetAllRowsQuantityAndPrice() {
    const productRows = document.querySelectorAll('.product-row');
    productRows.forEach(row => {
        resetQuantityAndPrice(row);
    });
}

// New function for handling quantity type change in the simplified layout
function onQuantityTypeChange(select) {
    const row = select.closest('.product-row');
    const quantityInput = row.querySelector('.quantity-input');
    const quantityTypeHidden = row.querySelector('.quantity-type-hidden');
    const quantityCartonHidden = row.querySelector('.quantity-carton-hidden');
    const quantityPieceHidden = row.querySelector('.quantity-piece-hidden');
    
    // Get the current quantity value
    const currentQuantity = parseFloat(quantityInput.value || 0);
    
    // Get the data attributes for names
    const cartonName = quantityInput.getAttribute('data-carton-name');
    const pieceName = quantityInput.getAttribute('data-piece-name');
    
    if (!cartonName || !pieceName) return;
    
    // Update placeholder and name based on quantity type
    if (select.value === 'carton') {
        quantityInput.placeholder = 'Qty in CTN';
        quantityInput.name = cartonName;
        // Sync current value to carton hidden field
        quantityCartonHidden.value = currentQuantity;
        quantityPieceHidden.value = '0';
    } else if (select.value === 'piece') {
        quantityInput.placeholder = 'Qty in PCS';
        quantityInput.name = pieceName;
        // Sync current value to piece hidden field
        quantityPieceHidden.value = currentQuantity;
        quantityCartonHidden.value = '0';
    }
    
    // Update hidden field for quantity type
    quantityTypeHidden.value = select.value;
    
    // Update quantity display
    updateQuantityDisplay(row);
    
    // Trigger calculation
    calculateRowTotal(quantityInput);
}

// Function to update the quantity display
function updateQuantityDisplay(row) {
    const quantityInput = row.querySelector('.quantity-input');
    const quantityTypeSelect = row.querySelector('.quantity-type-select');
    const quantityTotalSpan = row.querySelector('.quantity-total');
    const quantityUnitSpan = row.querySelector('.quantity-unit');
    
    if (!quantityInput || !quantityTypeSelect) return;
    
    const quantity = parseFloat(quantityInput.value) || 0;
    const quantityType = quantityTypeSelect.value;
    
    let totalQuantity = quantity;
    let unit = 'pcs';
    
    if (quantityType === 'carton') {
        // Get quantity_per_carton from product data
        const productSelect = row.querySelector('.product-select');
        let quantityPerCarton = 1;
        
        if (productSelect.value) {
            const selectedOption = productSelect.querySelector('option:checked');
            if (selectedOption && selectedOption.dataset.carton) {
                quantityPerCarton = parseInt(selectedOption.dataset.carton) || 1;
            }
        }
        
        totalQuantity = quantity * quantityPerCarton;
        unit = 'pcs';
    } else if (quantityType === 'piece') {
        totalQuantity = quantity;
        unit = 'pcs';
    }
    
    // Update both desktop and mobile quantity displays
    if (quantityTotalSpan && quantityUnitSpan) {
        quantityTotalSpan.textContent = totalQuantity.toLocaleString('id-ID');
        quantityUnitSpan.textContent = unit;
    }
    
    // Update mobile quantity display if it exists
    const mobileQuantityDisplay = row.querySelector('.quantity-display-mobile');
    if (mobileQuantityDisplay) {
        const mobileTotalSpan = mobileQuantityDisplay.querySelector('.quantity-total');
        const mobileUnitSpan = mobileQuantityDisplay.querySelector('.quantity-unit');
        if (mobileTotalSpan && mobileUnitSpan) {
            mobileTotalSpan.textContent = totalQuantity.toLocaleString('id-ID');
            mobileUnitSpan.textContent = unit;
        }
    }
}


// Legacy function - kept for compatibility but simplified
function toggleQuantityInputs(select) {
    // This function is no longer needed with the new layout
    // but kept for backward compatibility
    calculateRowTotal(select);
}

function addProductRow() {
    const template = document.getElementById('productRowTemplate');
    const container = document.getElementById('productsContainer');
    const clone = template.content.cloneNode(true);
    
    // Update index
    const html = clone.querySelector('.product-row').outerHTML.replace(/INDEX/g, productIndex);
    container.insertAdjacentHTML('beforeend', html);
    
    // Add event listeners to the new row
    const newRow = container.lastElementChild;
    const productSelect = newRow.querySelector('.product-select');
    const supplierInput = newRow.querySelector('.supplier-id-input');
    const quantityTypeHidden = newRow.querySelector('.quantity-type-hidden');
    const quantityTypeSelect = newRow.querySelector('.quantity-type-select');
    
    // Set supplier_id from main supplier select
    const mainSupplierSelect = document.getElementById('mainSupplierSelect');
    if (mainSupplierSelect && mainSupplierSelect.value) {
        supplierInput.value = mainSupplierSelect.value;
        
        // Load products for this new row
        loadProductsForNewRow(mainSupplierSelect.value, productSelect);
    }
    
    productSelect.addEventListener('change', function() {
        updateProductInfo(this);
    });
    
    // Add event listener for quantity input to clear zero value
    const quantityInput = newRow.querySelector('.quantity-input');
    if (quantityInput) {
        quantityInput.addEventListener('focus', function() {
            clearZeroValue(this);
        });
    }
    
    // Initialize Select2 on new selects
    initSelect2ForRow(newRow);
    
    // Default quantity type to CTN and initialize
    if (quantityTypeSelect) {
        quantityTypeSelect.value = 'carton';
        onQuantityTypeChange(quantityTypeSelect);
    }
    
    // Initialize quantity display
    updateQuantityDisplay(newRow);
    
    productIndex++;
    updateSummary();
}

function loadProductsForNewRow(supplierId, productSelect, selectedProductId = null, selectedProductData = null) {
    // Get CSRF token
    const metaCsrf = document.querySelector('meta[name="csrf-token"]');
    const inputTokenEl = document.querySelector('input[name="_token"]');
    const csrfToken = (metaCsrf && metaCsrf.getAttribute('content')) || (inputTokenEl && inputTokenEl.value) || '';
    
    const noAuthXhr = new XMLHttpRequest();
    noAuthXhr.open('GET', `{{ route('sales-transaction.get-products-no-auth') }}?supplier_id=${supplierId}`, true);
    noAuthXhr.setRequestHeader('Accept', 'application/json');
    noAuthXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    noAuthXhr.onreadystatechange = function() {
        if (noAuthXhr.readyState === 4) {
            if (noAuthXhr.status === 200) {
                try {
                    const data = JSON.parse(noAuthXhr.responseText);
                    if (data && Array.isArray(data.products)) {
                        updateProductSelect(productSelect, data.products, selectedProductId, selectedProductData);
                        return;
                    }
                } catch (e) {
                    console.error('Error parsing products data:', e);
                }
            }
            
            // Fallback to auth method
            loadProductsWithAuthForRow(supplierId, productSelect, selectedProductId, selectedProductData);
        }
    };
    noAuthXhr.send();
}

function loadProductsWithAuthForRow(supplierId, productSelect, selectedProductId = null, selectedProductData = null) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value;
    
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `{{ route('sales-transaction.get-products') }}?supplier_id=${supplierId}`, true);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.withCredentials = true;
    if (csrfToken) {
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    }
        
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    if (xhr.responseText.trim().startsWith('<!DOCTYPE html>') || xhr.responseText.trim().startsWith('<html')) {
                        console.error('Server returned HTML instead of JSON');
                        productSelect.innerHTML = '<option value="">Server error</option>';
                        return;
                    }
                    
                    const products = JSON.parse(xhr.responseText);
                    updateProductSelect(productSelect, products, selectedProductId, selectedProductData);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    productSelect.innerHTML = '<option value="">Error parsing response</option>';
                }
            } else {
                console.error('HTTP Error:', xhr.status);
                productSelect.innerHTML = `<option value="">Error ${xhr.status}</option>`;
            }
        }
    };
        
    xhr.onerror = function() {
        console.error('Network error');
        productSelect.innerHTML = '<option value="">Network error</option>';
    };
        
    xhr.send();
}

function initSelect2ForRow(row) {
    if (typeof $ === 'undefined' || !row) return;
    var $row = $(row);
    var commonOptions = {
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $row
    };
    
    $row.find('.product-select').each(function() {
        if ($(this).data('select2')) { $(this).select2('destroy'); }
        $(this).select2(Object.assign({}, commonOptions, {
            templateResult: function (data) {
                if (!data.id) { return data.text; }
                var $option = $(data.element);
                var name = $option.data('name') || data.text || '';
                var price = $option.data('price');
                var formatted = formatCurrencyID(price);
                var html = '<div style="display:flex; align-items:center; justify-content:space-between; width:100%">'
                         +   '<span>' + name + '</span>'
                         +   '<small class="text-muted" style="margin-left:auto; text-align:right; display:block;">Rp ' + formatted + '</small>'
                         + '</div>';
                return $(html);
            },
            templateSelection: function (data) {
                if (!data.id) { return data.text; }
                var $option = $(data.element);
                var name = $option.data('name') || data.text || '';
                var price = $option.data('price');
                var formatted = formatCurrencyID(price);
                var html = '<div style="display:flex; align-items:center; justify-content:space-between; width:100%">'
                         +   '<span>' + name + '</span>'
                         +   '<small class="text-muted" style="margin-left:auto; text-align:right; display:block;">Rp ' + formatted + '</small>'
                         + '</div>';
                return $(html);
            }
        }));
        // Ensure price is set when selecting via Select2
        $(this).off('select2:select._product').on('select2:select._product', (e) => {
            // Trigger native change to call updateProductInfo
            this.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
}

function formatCurrencyID(value) {
    var num = parseFloat(value);
    if (!isFinite(num)) { num = 0; }
    return num.toLocaleString('id-ID');
}


function addProductRows(count) {
    count = parseInt(count || 0);
    if (!count || count < 1) return;
    // Hard cap to avoid accidental huge insertions
    if (count > 100) count = 100;
    for (let i = 0; i < count; i++) {
        addProductRow();
    }
}

function addProductRowPrompt() {
    var input = prompt('Tambah berapa produk?', '1');
    if (input === null) return; // cancelled
    var num = parseInt(input);
    if (isNaN(num) || num < 1) {
        alert('Masukkan angka yang valid (>= 1).');
        return;
    }
    addProductRows(num);
}

function removeProductRow(button) {
    const row = button.closest('.product-row');
        row.remove();
        updateSummary();
}

function updateProductInfo(select) {
    const row = select.closest('.product-row');
    let selectedOption = select.selectedOptions && select.selectedOptions[0];
    if (!selectedOption && select.value) {
        selectedOption = select.querySelector('option[value="' + select.value + '"]');
    }
    const price = parseFloat((selectedOption && selectedOption.dataset && selectedOption.dataset.price) || 0);
    const carton = parseInt((selectedOption && selectedOption.dataset && selectedOption.dataset.carton) || 1);
    
    const unitPriceInput = row.querySelector('.unit-price');
    const displayUnitPrice = row.querySelector('.display-unit-price');
    
    // Only reset quantity and type if this is a new row or if no existing data
    const quantityInput = row.querySelector('.quantity-input');
    const quantityTypeSelect = row.querySelector('.quantity-type-select');
    
    // Check if this row has existing data from database
    const hasExistingQuantity = quantityInput && quantityInput.value && parseFloat(quantityInput.value) > 0;
    const hasExistingPrice = unitPriceInput && unitPriceInput.value && parseFloat(unitPriceInput.value) > 0;
    const hasExistingData = hasExistingQuantity || hasExistingPrice;
    
    if (!hasExistingData) {
        // Reset quantity when product changes (only for new rows)
        if (quantityInput) {
            quantityInput.value = '0';
        }
        
        // Reset quantity type to default (CTN) (only for new rows)
        if (quantityTypeSelect) {
            quantityTypeSelect.value = 'carton';
            onQuantityTypeChange(quantityTypeSelect);
        }
        
        // Update price for new rows
        unitPriceInput.value = price;
        if (displayUnitPrice) {
            displayUnitPrice.textContent = 'Rp ' + price.toLocaleString('id-ID');
        }
    } else {
        // For existing rows, preserve the original price from sales_transactions
        // Don't update price based on product selection to maintain data integrity
        // The price should only be updated manually by the user if needed
    }
    
    // Update quantity display and trigger calculation
    updateQuantityDisplay(row);
    if (quantityInput) {
        calculateRowTotal(quantityInput);
    }
}

function calculateRowTotal(input) {
    const row = input.closest('.product-row');
    const unitPrice = parseFloat((row.querySelector('.unit-price') && row.querySelector('.unit-price').value) || 0);
    
    // Get quantity from the unified quantity input
    const quantityInput = row.querySelector('.quantity-input');
    const quantityTypeSelect = row.querySelector('.quantity-type-select');
    const totalQuantity = parseFloat(quantityInput.value || 0);
    
    // Update hidden quantity fields for backend compatibility
    const quantityTypeHidden = row.querySelector('.quantity-type-hidden');
    if (quantityTypeSelect) {
        quantityTypeHidden.value = quantityTypeSelect.value;
        
        // Update the appropriate quantity field for backend
        const cartonField = row.querySelector('input[name*="[quantity_carton]"]');
        const pieceField = row.querySelector('input[name*="[quantity_piece]"]');
        
        if (quantityTypeSelect.value === 'carton') {
            if (cartonField) cartonField.value = totalQuantity;
            if (pieceField) pieceField.value = 0;
        } else if (quantityTypeSelect.value === 'piece') {
            if (cartonField) cartonField.value = 0;
            if (pieceField) pieceField.value = totalQuantity;
        }
    }
    
    const totalAmount = totalQuantity * unitPrice;
    
    // Update quantity display
    updateQuantityDisplay(row);
    
    updateSummary();
}

function updateSummary() {
    const rows = document.querySelectorAll('.product-row');
    let totalProducts = 0;
    let totalQuantity = 0;
    let totalAmount = 0;
    let productsSummary = '';
    
    // Update date, sales, and supplier
    const transactionDate = document.querySelector('input[name="transaction_date"]').value;
    const salesSelect = document.querySelector('select[name="sales_id"]');
    const salesSelected = salesSelect && salesSelect.selectedOptions && salesSelect.selectedOptions[0];
    const salesName = (salesSelected && salesSelected.textContent) || '-';
    
    const supplierSelect = document.getElementById('mainSupplierSelect');
    const supplierSelected = supplierSelect && supplierSelect.selectedOptions && supplierSelect.selectedOptions[0];
    const supplierName = (supplierSelected && supplierSelected.textContent) || '-';
    
    if (transactionDate) {
        const date = new Date(transactionDate);
        document.getElementById('summaryDate').textContent = date.toLocaleDateString('id-ID');
    }
    document.getElementById('summarySales').textContent = salesName;
    document.getElementById('summarySupplier').textContent = supplierName;
    
    rows.forEach(row => {
        const productSelect = row.querySelector('.product-select');
        const supplierInput = row.querySelector('.supplier-id-input');
        const quantityInput = row.querySelector('.quantity-input');
        const quantityTypeSelect = row.querySelector('.quantity-type-select');
        const unitPrice = parseFloat((row.querySelector('.unit-price') && row.querySelector('.unit-price').value) || 0);
        
        // Check both quantity fields
        const cartonField = row.querySelector('input[name*="[quantity_carton]"]');
        const pieceField = row.querySelector('input[name*="[quantity_piece]"]');
        const cartonQuantity = parseFloat(cartonField ? cartonField.value : 0);
        const pieceQuantity = parseFloat(pieceField ? pieceField.value : 0);
        const hasQuantity = cartonQuantity > 0 || pieceQuantity > 0;
        
        if (productSelect.value && supplierInput.value && hasQuantity && unitPrice > 0) {
            const rowQuantity = parseFloat(quantityInput.value || 0);
            const quantityType = quantityTypeSelect ? quantityTypeSelect.value : 'carton';
            const quantityDisplay = `${rowQuantity} ${quantityType.toUpperCase()}`;
            
            if (rowQuantity > 0) {
                totalProducts++;
                totalQuantity += rowQuantity;
                totalAmount += rowQuantity * unitPrice;
                
                const ps2 = productSelect.selectedOptions && productSelect.selectedOptions[0];
                const productName = (ps2 && ps2.dataset && ps2.dataset.name) || productSelect.value;
                
                // Get supplier name from main supplier select
                const mainSupplierSelect = document.getElementById('mainSupplierSelect');
                const supplierName = mainSupplierSelect && mainSupplierSelect.selectedOptions && mainSupplierSelect.selectedOptions[0] 
                    ? mainSupplierSelect.selectedOptions[0].textContent.split(' - ')[1] || ''
                    : '';
                
                const rowTotal = rowQuantity * unitPrice;
                
                productsSummary += `
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">${productName}</span>
                        <span class="font-medium">${quantityDisplay}</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Supplier: ${supplierName}</span>
                        <span>@ Rp ${unitPrice.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="flex justify-between text-sm font-medium mb-2">
                        <span>Subtotal:</span>
                        <span>Rp ${rowTotal.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="border-t border-dotted border-gray-300 mb-2"></div>
                `;
            }
        }
    });
    
    document.getElementById('productsSummary').innerHTML = productsSummary;
    document.getElementById('totalProducts').textContent = totalProducts;
    document.getElementById('totalQuantity').textContent = totalQuantity.toLocaleString('id-ID') + ' pcs';
    document.getElementById('totalAmount').textContent = 'Rp ' + totalAmount.toLocaleString('id-ID');
}

// Function to sync all quantity fields before form submission
function syncAllQuantityFields() {
    const rows = document.querySelectorAll('.product-row');
    rows.forEach(row => {
        const quantityInput = row.querySelector('.quantity-input');
        const quantityTypeSelect = row.querySelector('.quantity-type-select');
        const cartonField = row.querySelector('input[name*="[quantity_carton]"]');
        const pieceField = row.querySelector('input[name*="[quantity_piece]"]');
        
        if (quantityInput && quantityTypeSelect) {
            const totalQuantity = parseFloat(quantityInput.value || 0);
            
            if (quantityTypeSelect.value === 'carton') {
                if (cartonField) cartonField.value = totalQuantity;
                if (pieceField) pieceField.value = 0;
            } else if (quantityTypeSelect.value === 'piece') {
                if (cartonField) cartonField.value = 0;
                if (pieceField) pieceField.value = totalQuantity;
            }
        }
    });
}

// Add first product row on page load
function safeInit() {
    try {
        const tpl = document.getElementById('productRowTemplate');
        const container = document.getElementById('productsContainer');
        if (tpl && container) {
            // Initialize existing rows
            const existingRows = container.querySelectorAll('.product-row');
            existingRows.forEach(row => {
                // Store the selected product value before initializing Select2
    const productSelect = row.querySelector('.product-select');
                const selectedProductId = productSelect ? productSelect.value : null;
                const selectedProductData = productSelect && productSelect.selectedOptions[0] ? {
                    name: productSelect.selectedOptions[0].dataset.name,
                    price: productSelect.selectedOptions[0].dataset.price,
                    carton: productSelect.selectedOptions[0].dataset.carton
                } : null;
                
                // Store current unit price before any changes
                const unitPriceInput = row.querySelector('.unit-price');
                const currentUnitPrice = unitPriceInput ? unitPriceInput.value : null;
                
                initSelect2ForRow(row);
                updateQuantityDisplay(row);
                
                // Restore unit price if it was set
                if (unitPriceInput && currentUnitPrice) {
                    unitPriceInput.value = currentUnitPrice;
                }
                
                // Note: onQuantityTypeChange is not called here because PHP already sets the correct name
                // This prevents JavaScript from overriding the correct PHP-generated name
                
                // Load products for existing rows if supplier is selected
                const supplierSelect = document.getElementById('mainSupplierSelect');
                if (supplierSelect && supplierSelect.value) {
                    if (productSelect && !productSelect.value) {
                        // Load products for this row
                        loadProductsForNewRow(supplierSelect.value, productSelect);
                    } else if (selectedProductId) {
                        // Load products and restore selection
                        loadProductsForNewRow(supplierSelect.value, productSelect, selectedProductId, selectedProductData);
                    }
                }
            });
        }
        // Init Select2 for Order Acc By and Main Supplier
    if (typeof $ !== 'undefined') {
        var $orderAcc = $('#orderAccBy');
        if ($orderAcc.length) {
                $orderAcc.select2({ theme: 'bootstrap-5', width: '100%' });
            }
            
            var $mainSupplier = $('#mainSupplierSelect');
            if ($mainSupplier.length) {
                $mainSupplier.select2({ 
                theme: 'bootstrap-5',
                    width: '100%',
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
        }
    }
    var dateInput = document.querySelector('input[name="transaction_date"]');
        if (dateInput) { dateInput.addEventListener('change', updateSummary); }
    var salesSelect = document.querySelector('select[name="sales_id"]');
        if (salesSelect) { salesSelect.addEventListener('change', updateSummary); }
        var supplierSelect = document.getElementById('mainSupplierSelect');
        if (supplierSelect) { supplierSelect.addEventListener('change', updateSummary); }
        
        // Load products for existing supplier if already selected
        if (supplierSelect && supplierSelect.value) {
            loadProductsByMainSupplier(supplierSelect);
        }
        
        // Initialize summary
        updateSummary();
        
        // Add form submit handler to ensure quantity fields are synced
        const form = document.getElementById('salesTransactionEditForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Sync all quantity fields before submit
                syncAllQuantityFields();
            });
        }
    } catch (e) {
        console.error('Initialization error:', e);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', safeInit);
} else {
    safeInit();
}
</script>