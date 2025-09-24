@extends('layouts.app')

@section('title', 'Edit PO - Munah - Purchase Orders')
@section('page-title', 'Edit PO')

<style>
/* Mobile responsive adjustments for quantity layout */
@media (max-width: 1024px) {
    .product-row .grid {
        gap: 0.75rem;
    }
    
    .product-row .lg\\:col-span-2 {
        grid-column: span 1;
    }
}

@media (max-width: 640px) {
    .product-row {
        padding: 0.75rem;
    }
    
    .product-row .grid {
        gap: 0.5rem;
    }
    
    /* Keep quantity inputs side by side on mobile */
    .product-row .grid-cols-2 {
        grid-template-columns: 1fr 1fr !important;
        gap: 0.5rem;
    }
    
    /* Force side by side layout for quantity container */
    .product-row .lg\\:col-span-2 .grid-cols-2 {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 0.5rem;
    }
    
    /* Make labels smaller on mobile */
    .product-row label {
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
    }
    
    .product-row .text-xs {
        font-size: 0.625rem;
    }
    
    /* Ensure inputs are touch-friendly on mobile */
    .product-row input[type="number"] {
        min-height: 44px;
        font-size: 16px; /* Prevent zoom on iOS */
    }
}

/* Extra small mobile devices - still keep side by side */
@media (max-width: 480px) {
    .product-row .grid-cols-2 {
        grid-template-columns: 1fr 1fr !important;
        gap: 0.25rem;
    }
    
    .product-row .lg\\:col-span-2 .grid-cols-2 {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 0.25rem;
    }
    
    /* Make inputs even more compact on very small screens */
    .product-row input[type="number"] {
        min-height: 40px;
        font-size: 14px;
        padding: 0.5rem;
    }
    
    /* Smaller labels on very small screens */
    .product-row .text-xs {
        font-size: 0.5rem;
    }
}
</style>

@section('content')
<div class="p-4">
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('sales-transaction.update-po', $poNumber) }}" id="salesTransactionForm">
        @csrf
        @method('PATCH')
        
        <!-- Header Information -->
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
                        $selectedSalesId = old('sales_id') ?? $header['sales_id'];
                    @endphp
                    <select name="sales_id" class="input-field @error('sales_id') border-red-500 @enderror" required {{ isset($currentSales) ? 'disabled' : '' }}>
                        <option value="">Pilih Sales</option>
                        @foreach($salesList as $sales)
                            <option value="{{ $sales->id }}" {{ (string)$selectedSalesId === (string)$sales->id ? 'selected' : '' }}>
                                {{ $sales->name }}
                            </option>
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
                    <input type="text" name="po_number" value="{{ old('po_number', $header['po_number']) }}" class="input-field @error('po_number') border-red-500 @enderror" placeholder="Auto-generated jika kosong">
                    @error('po_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Umum</label>
                    <textarea name="general_notes" rows="3" class="input-field @error('general_notes') border-red-500 @enderror" placeholder="Catatan umum untuk seluruh transaksi">{{ old('general_notes', $header['general_notes']) }}</textarea>
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
                    <select name="supplier_id" id="mainSupplierSelect" class="input-field @error('supplier_id') border-red-500 @enderror" required onchange="loadProductsByMainSupplier(this)">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $transactions->first()->supplier_id) == $supplier->id ? 'selected' : '' }}>
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
                <!-- Product rows will be added here -->
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
                    <span class="font-medium" id="summaryDate">{{ \Carbon\Carbon::parse($header['transaction_date'])->format('d/m/Y') }}</span>
                </div>

                <!-- Sales -->
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Sales:</span>
                    <span class="font-medium" id="summarySales">{{ $transactions->first()->sales->name ?? 'N/A' }}</span>
                </div>

                <!-- Supplier -->
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Supplier:</span>
                    <span class="font-medium" id="summarySupplier">{{ $transactions->first()->product->supplier->nama_supplier ?? 'N/A' }}</span>
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

        <!-- Action Buttons -->
        <div class="flex flex-row gap-3">
            <a href="{{ route('sales-transaction.show', $transactions->first()->transaction_number) }}" class="btn-secondary flex-1 text-center">Batal</a>
            <button type="submit" class="btn-primary flex-1">Update PO</button>
        </div>
    </form>
</div>

<!-- Product Row Template -->
<template id="productRowTemplate">
    <div class="product-row border rounded-lg p-4 mb-4 bg-white hover:bg-gray-50 transition-colors relative" data-index="">
        
        <!-- Hidden fields for backend compatibility -->
        <input type="hidden" name="products[INDEX][supplier_id]" class="supplier-id-input" value="">
        
        <!-- Main Product Row -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-3 min-w-0">
            <!-- Produk -->
            <div class="lg:col-span-2 min-w-0">
                <label class="block text-sm font-medium text-gray-700 mb-2">Produk <span class="text-red-500">*</span></label>
                <select class="input-field product-select w-full" name="products[INDEX][product_id]" required onchange="updateProductInfo(this)">
                    <option value="">Pilih Supplier terlebih dahulu</option>
                </select>
            </div>
            
            <!-- Quantity Row - CTN and PCS side by side -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">CTN</label>
                        <input type="number" name="products[INDEX][quantity_carton]" min="0" class="input-field quantity-carton-input w-full" oninput="calculateRowTotal(this)" onkeyup="calculateRowTotal(this)" onfocus="clearZeroValue(this)" placeholder="0" value="0">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">PCS</label>
                        <input type="number" name="products[INDEX][quantity_piece]" min="0" class="input-field quantity-piece-input w-full" oninput="calculateRowTotal(this)" onkeyup="calculateRowTotal(this)" onfocus="clearZeroValue(this)" placeholder="0" value="0">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Unit Price Row -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-3 min-w-0">
            <div class="lg:col-span-2"></div> <!-- Empty space for alignment -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Satuan</label>
                <input type="number" name="products[INDEX][unit_price]" step="0.01" min="0" class="input-field unit-price w-full" oninput="calculateRowTotal(this)" placeholder="0" readonly>
            </div>
        </div>
        
        <!-- Total Quantity Display -->
        <div class="mb-3">
            <div class="bg-gray-50 p-3 rounded-lg">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Total Quantity:</span>
                    <span class="font-semibold text-gray-900">
                        <span class="total-quantity-display">0</span> pcs
                    </span>
                </div>
                <div class="flex justify-between items-center text-sm mt-1">
                    <span class="text-gray-600">Total Amount:</span>
                    <span class="font-semibold text-blue-600">
                        Rp <span class="total-amount-display">0</span>
                    </span>
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
let existingTransactions = @json($transactions);
console.log('Existing transactions loaded:', existingTransactions);
console.log('Number of transactions:', existingTransactions ? existingTransactions.length : 0);

// Function untuk load produk berdasarkan main supplier selection
function loadProductsByMainSupplier(supplierSelect) {
    const supplierId = supplierSelect.value;
    console.log('loadProductsByMainSupplier called with supplier ID:', supplierId);
    
    if (supplierId) {
        // Update semua hidden supplier_id fields di product rows
        updateAllProductRowsSupplier(supplierId);
        
        // Load produk untuk semua product rows
        loadProductsForAllRows(supplierId);
        
        // Don't reset quantities for edit mode - we want to preserve existing data
        // resetAllRowsQuantityAndPrice();
    } else {
        // Clear semua product selects
        clearAllProductSelects();
    }
}

function updateAllProductRowsSupplier(supplierId) {
    console.log('Updating all product rows supplier ID to:', supplierId);
    const supplierInputs = document.querySelectorAll('.supplier-id-input');
    console.log('Found supplier inputs:', supplierInputs.length);
    supplierInputs.forEach((input, index) => {
        console.log(`Setting supplier input ${index} to:`, supplierId);
        input.value = supplierId;
    });
}

function loadProductsForAllRows(supplierId) {
    console.log('Loading products for supplier ID:', supplierId);
    
    // Get CSRF token
    const metaCsrf = document.querySelector('meta[name="csrf-token"]');
    const inputTokenEl = document.querySelector('input[name="_token"]');
    const csrfToken = (metaCsrf && metaCsrf.getAttribute('content')) || (inputTokenEl && inputTokenEl.value) || '';
    
    console.log('CSRF token found:', !!csrfToken);
    
    // Load products
    const noAuthXhr = new XMLHttpRequest();
    const url = `{{ route('sales-transaction.get-products-no-auth') }}?supplier_id=${supplierId}`;
    console.log('Making request to:', url);
    
    noAuthXhr.open('GET', url, true);
    noAuthXhr.setRequestHeader('Accept', 'application/json');
    noAuthXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    noAuthXhr.onreadystatechange = function() {
        if (noAuthXhr.readyState === 4) {
            console.log('No-auth response status:', noAuthXhr.status);
            console.log('No-auth response text:', noAuthXhr.responseText);
            if (noAuthXhr.status === 200) {
                try {
                    const data = JSON.parse(noAuthXhr.responseText);
                    console.log('No-auth response data:', data);
                    if (data && Array.isArray(data.products)) {
                        updateAllProductSelects(data.products);
                        return;
                    }
                } catch (e) {
                    console.error('Error parsing products data:', e);
                }
            }
            
            // Fallback to auth method
            console.log('Falling back to auth method');
            loadProductsWithAuth(supplierId);
        }
    };
    noAuthXhr.send();
}

function updateAllProductSelects(products) {
    console.log('Updating all product selects with products:', products);
    const productSelects = document.querySelectorAll('.product-select');
    console.log('Found product selects:', productSelects.length);
    productSelects.forEach((select, index) => {
        console.log(`Updating product select ${index}:`, select);
        updateProductSelect(select, products);
    });
}

function clearAllProductSelects() {
    console.log('Clearing all product selects');
    const productSelects = document.querySelectorAll('.product-select');
    console.log('Found product selects to clear:', productSelects.length);
    productSelects.forEach((select, index) => {
        console.log(`Clearing product select ${index}:`, select);
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
    console.log('Loading products with auth, supplier ID:', supplierId);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value;
    
    console.log('CSRF token found:', !!csrfToken);
    
    const xhr = new XMLHttpRequest();
    const url = `{{ route('sales-transaction.get-products') }}?supplier_id=${supplierId}`;
    console.log('Making auth request to:', url);
    
    xhr.open('GET', url, true);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.withCredentials = true;
    if (csrfToken) {
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    }
        
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('Auth response status:', xhr.status);
            console.log('Auth response text:', xhr.responseText);
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
                    console.log('Auth response data:', products);
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

function updateProductSelect(productSelect, products) {
    console.log('Updating product select with products:', products);
    console.log('Product select element:', productSelect);
    console.log('Number of products:', products ? products.length : 0);
    
    if (!products || products.length === 0) {
        console.log('No products to display');
        productSelect.innerHTML = '<option value="">Tidak ada produk</option>';
        // Clear selection if using Select2
        if (typeof $ !== 'undefined' && $(productSelect).data('select2')) {
            $(productSelect).val(null).trigger('change');
        } else {
            productSelect.value = '';
        }
        return;
    }
    
    console.log('Adding', products.length, 'products to select');
    productSelect.innerHTML = '<option value="">Pilih Produk</option>';
    products.forEach((product, index) => {
        console.log(`Adding product ${index}:`, product);
        const option = document.createElement('option');
        option.value = product.id;
        option.dataset.price = product.price;
        option.dataset.carton = product.quantity_per_carton || 1;
        option.dataset.name = product.name;
        option.textContent = `${product.name} - Rp ${parseFloat(product.price).toLocaleString('id-ID')}`;
        productSelect.appendChild(option);
    });
    
    console.log('Product select updated with', products.length, 'products');
}

// Function to clear zero value when user focuses on input
function clearZeroValue(input) {
    console.log('Clearing zero value for input:', input);
    if (input.value === '0') {
        input.value = '';
        console.log('Cleared zero value');
    }
}

// Function to reset quantity and price when supplier or product changes
function resetQuantityAndPrice(row) {
    console.log('Resetting quantity and price for row:', row);
    const cartonInput = row.querySelector('.quantity-carton-input');
    const pieceInput = row.querySelector('.quantity-piece-input');
    const unitPriceInput = row.querySelector('.unit-price');
    
    // Reset quantity inputs
    if (cartonInput) {
        cartonInput.value = '0';
    }
    if (pieceInput) {
        pieceInput.value = '0';
    }
    
    // Reset unit price input
    if (unitPriceInput) {
        unitPriceInput.value = '';
    }
    
    // Trigger calculation
    if (unitPriceInput) {
        calculateRowTotal(unitPriceInput);
    }
}

// Function to reset all existing rows quantity and price
function resetAllRowsQuantityAndPrice() {
    console.log('Resetting all rows quantity and price');
    const productRows = document.querySelectorAll('.product-row');
    console.log('Found product rows to reset:', productRows.length);
    productRows.forEach((row, index) => {
        console.log(`Resetting row ${index}:`, row);
        resetQuantityAndPrice(row);
    });
}

// Function to calculate total quantity and amount for dual quantity input
function calculateRowTotal(input) {
    console.log('Calculating row total for input:', input);
    const row = input.closest('.product-row');
    const cartonInput = row.querySelector('.quantity-carton-input');
    const pieceInput = row.querySelector('.quantity-piece-input');
    const unitPriceInput = row.querySelector('.unit-price');
    const productSelect = row.querySelector('.product-select');
    
    // Get values
    const cartonQty = parseFloat(cartonInput.value) || 0;
    const pieceQty = parseFloat(pieceInput.value) || 0;
    const unitPrice = parseFloat(unitPriceInput.value) || 0;
    
    console.log('Row values:', {
        cartonQty,
        pieceQty,
        unitPrice,
        productId: productSelect.value
    });
    
    // Get quantity_per_carton from selected product
    let quantityPerCarton = 1;
    if (productSelect.value) {
        const selectedOption = productSelect.querySelector('option:checked');
        if (selectedOption && selectedOption.dataset.carton) {
            quantityPerCarton = parseInt(selectedOption.dataset.carton) || 1;
        }
    }
    
    // Calculate total quantity in pieces
    const totalQuantityPieces = (cartonQty * quantityPerCarton) + pieceQty;
    
    // Calculate total amount
    const totalAmount = totalQuantityPieces * unitPrice;
    
    console.log('Calculated totals:', {
        quantityPerCarton,
        totalQuantityPieces,
        totalAmount
    });
    
    // Update display
    const totalQuantityDisplay = row.querySelector('.total-quantity-display');
    const totalAmountDisplay = row.querySelector('.total-amount-display');
    
    if (totalQuantityDisplay) {
        totalQuantityDisplay.textContent = totalQuantityPieces.toLocaleString('id-ID');
    }
    
    if (totalAmountDisplay) {
        totalAmountDisplay.textContent = totalAmount.toLocaleString('id-ID');
    }
    
    // Update summary
    updateSummary();
}

// Function to update product info when product is selected
function updateProductInfo(select) {
    console.log('Updating product info for select:', select);
    console.log('Select value:', select.value);
    console.log('Select options:', Array.from(select.options).map(opt => ({value: opt.value, text: opt.text})));
    
    const row = select.closest('.product-row');
    const unitPriceInput = row.querySelector('.unit-price');
    const cartonInput = row.querySelector('.quantity-carton-input');
    const pieceInput = row.querySelector('.quantity-piece-input');
    
    console.log('Row elements found:', {
        row: !!row,
        unitPriceInput: !!unitPriceInput,
        cartonInput: !!cartonInput,
        pieceInput: !!pieceInput
    });
    
    // Get product data
    let selectedOption = select.selectedOptions && select.selectedOptions[0];
    if (!selectedOption && select.value) {
        selectedOption = select.querySelector('option[value="' + select.value + '"]');
    }
    
    console.log('Selected option:', selectedOption);
    console.log('Selected option dataset:', selectedOption ? selectedOption.dataset : 'No option');
    
    const price = parseFloat((selectedOption && selectedOption.dataset && selectedOption.dataset.price) || 0);
    const carton = parseInt((selectedOption && selectedOption.dataset && selectedOption.dataset.carton) || 1);
    
    console.log('Product data:', {
        selectedOption: !!selectedOption,
        price,
        carton,
        productId: select.value
    });
    
    // Set unit price
    unitPriceInput.value = price;
    
    // Reset quantities
    cartonInput.value = '0';
    pieceInput.value = '0';
    
    // Trigger calculation
    calculateRowTotal(unitPriceInput);
}

function addProductRow() {
    console.log('Adding product row, current index:', productIndex);
    
    const template = document.getElementById('productRowTemplate');
    const container = document.getElementById('productsContainer');
    const clone = template.content.cloneNode(true);
    
    console.log('Template and container found:', !!template, !!container);
    
    // Update index
    const html = clone.querySelector('.product-row').outerHTML.replace(/INDEX/g, productIndex);
    container.insertAdjacentHTML('beforeend', html);
    
    // Add event listeners to the new row
    const newRow = container.lastElementChild;
    const productSelect = newRow.querySelector('.product-select');
    const supplierInput = newRow.querySelector('.supplier-id-input');
    const cartonInput = newRow.querySelector('.quantity-carton-input');
    const pieceInput = newRow.querySelector('.quantity-piece-input');
    
    console.log('New row elements found:', {
        productSelect: !!productSelect,
        supplierInput: !!supplierInput,
        cartonInput: !!cartonInput,
        pieceInput: !!pieceInput
    });
    
    // Set supplier_id from main supplier select
    const mainSupplierSelect = document.getElementById('mainSupplierSelect');
    if (mainSupplierSelect && mainSupplierSelect.value) {
        console.log('Setting supplier ID from main select:', mainSupplierSelect.value);
        supplierInput.value = mainSupplierSelect.value;
        
        // Load products for this new row
        loadProductsForNewRow(mainSupplierSelect.value, productSelect);
    } else {
        console.log('Main supplier select not found or no value');
    }
    
    productSelect.addEventListener('change', function() {
        updateProductInfo(this);
    });
    
    // Add event listeners for quantity inputs to clear zero value
    if (cartonInput) {
        cartonInput.addEventListener('focus', function() {
            clearZeroValue(this);
        });
    }
    if (pieceInput) {
        pieceInput.addEventListener('focus', function() {
            clearZeroValue(this);
        });
    }
    
    // Initialize Select2 on new selects
    initSelect2ForRow(newRow);
    
    // Initialize calculation
    calculateRowTotal(cartonInput);
    
    productIndex++;
    updateSummary();
    
    console.log('Product row added successfully, new index:', productIndex);
}

function loadProductsForNewRow(supplierId, productSelect) {
    console.log('Loading products for new row, supplier ID:', supplierId);
    console.log('Product select element:', productSelect);
    
    // Get CSRF token
    const metaCsrf = document.querySelector('meta[name="csrf-token"]');
    const inputTokenEl = document.querySelector('input[name="_token"]');
    const csrfToken = (metaCsrf && metaCsrf.getAttribute('content')) || (inputTokenEl && inputTokenEl.value) || '';
    
    console.log('CSRF token found:', !!csrfToken);
    
    const noAuthXhr = new XMLHttpRequest();
    const url = `{{ route('sales-transaction.get-products-no-auth') }}?supplier_id=${supplierId}`;
    console.log('Making new row request to:', url);
    
    noAuthXhr.open('GET', url, true);
    noAuthXhr.setRequestHeader('Accept', 'application/json');
    noAuthXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    noAuthXhr.onreadystatechange = function() {
        if (noAuthXhr.readyState === 4) {
            console.log('New row no-auth response status:', noAuthXhr.status);
            console.log('New row no-auth response text:', noAuthXhr.responseText);
            if (noAuthXhr.status === 200) {
                try {
                    const data = JSON.parse(noAuthXhr.responseText);
                    console.log('New row no-auth response data:', data);
                    if (data && Array.isArray(data.products)) {
                        updateProductSelect(productSelect, data.products);
                        return;
                    }
                } catch (e) {
                    console.error('Error parsing products data for new row:', e);
                }
            }
            
            // Fallback to auth method
            console.log('Falling back to auth method for new row');
            loadProductsWithAuthForRow(supplierId, productSelect);
        }
    };
    noAuthXhr.send();
}

function loadProductsWithAuthForRow(supplierId, productSelect) {
    console.log('Loading products with auth for new row, supplier ID:', supplierId);
    console.log('Product select element:', productSelect);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value;
    
    console.log('CSRF token found:', !!csrfToken);
    
    const xhr = new XMLHttpRequest();
    const url = `{{ route('sales-transaction.get-products') }}?supplier_id=${supplierId}`;
    console.log('Making new row auth request to:', url);
    
    xhr.open('GET', url, true);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.withCredentials = true;
    if (csrfToken) {
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    }
        
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('New row auth response status:', xhr.status);
            console.log('New row auth response text:', xhr.responseText);
            if (xhr.status === 200) {
                try {
                    if (xhr.responseText.trim().startsWith('<!DOCTYPE html>') || xhr.responseText.trim().startsWith('<html')) {
                        console.error('Server returned HTML instead of JSON for new row');
                        productSelect.innerHTML = '<option value="">Server error</option>';
                        return;
                    }
                    
                    const products = JSON.parse(xhr.responseText);
                    console.log('New row auth response data:', products);
                    updateProductSelect(productSelect, products);
                } catch (e) {
                    console.error('Error parsing JSON for new row:', e);
                    productSelect.innerHTML = '<option value="">Error parsing response</option>';
                }
            } else {
                console.error('HTTP Error for new row:', xhr.status);
                productSelect.innerHTML = `<option value="">Error ${xhr.status}</option>`;
            }
        }
    };
        
    xhr.onerror = function() {
        console.error('Network error for new row');
        productSelect.innerHTML = '<option value="">Network error</option>';
    };
        
    xhr.send();
}

function initSelect2ForRow(row) {
    console.log('Initializing Select2 for row:', row);
    if (typeof $ === 'undefined' || !row) {
        console.log('jQuery not available or row not found');
        return;
    }
    var $row = $(row);
    var commonOptions = {
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $row
    };
    
    $row.find('.product-select').each(function() {
        console.log('Initializing Select2 for product select:', this);
        if ($(this).data('select2')) { 
            console.log('Destroying existing Select2 instance');
            $(this).select2('destroy'); 
        }
        console.log('Creating new Select2 instance');
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
            console.log('Select2 selection made, triggering change event');
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
    console.log('Adding product rows, count:', count);
    count = parseInt(count || 0);
    if (!count || count < 1) {
        console.log('Invalid count, returning');
        return;
    }
    // Hard cap to avoid accidental huge insertions
    if (count > 100) count = 100;
    for (let i = 0; i < count; i++) {
        console.log(`Adding product row ${i + 1} of ${count}`);
        addProductRow();
    }
}

function addProductRowPrompt() {
    console.log('addProductRowPrompt called');
    var input = prompt('Tambah berapa produk?', '1');
    if (input === null) {
        console.log('User cancelled prompt');
        return; // cancelled
    }
    var num = parseInt(input);
    if (isNaN(num) || num < 1) {
        console.log('Invalid input:', input);
        alert('Masukkan angka yang valid (>= 1).');
        return;
    }
    console.log('Adding', num, 'product rows');
    addProductRows(num);
}

function removeProductRow(button) {
    console.log('Removing product row');
    const row = button.closest('.product-row');
    if (row) {
        console.log('Row found, removing...');
        row.remove();
        updateSummary();
    } else {
        console.log('Row not found');
    }
}

function updateSummary() {
    console.log('Updating summary...');
    const rows = document.querySelectorAll('.product-row');
    console.log('Found product rows:', rows.length);
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
    
    console.log('Summary data:', {
        transactionDate,
        salesName,
        supplierName
    });
    
    if (transactionDate) {
        const date = new Date(transactionDate);
        document.getElementById('summaryDate').textContent = date.toLocaleDateString('id-ID');
    }
    document.getElementById('summarySales').textContent = salesName;
    document.getElementById('summarySupplier').textContent = supplierName;
    
    rows.forEach(row => {
        const productSelect = row.querySelector('.product-select');
        const supplierInput = row.querySelector('.supplier-id-input');
        const cartonInput = row.querySelector('.quantity-carton-input');
        const pieceInput = row.querySelector('.quantity-piece-input');
        const unitPrice = parseFloat((row.querySelector('.unit-price') && row.querySelector('.unit-price').value) || 0);
        
        if (productSelect.value && supplierInput.value && unitPrice > 0) {
            const cartonQty = parseFloat(cartonInput.value || 0);
            const pieceQty = parseFloat(pieceInput.value || 0);
            
            // Get quantity_per_carton from selected product
            let quantityPerCarton = 1;
            if (productSelect.value) {
                const selectedOption = productSelect.querySelector('option:checked');
                if (selectedOption && selectedOption.dataset.carton) {
                    quantityPerCarton = parseInt(selectedOption.dataset.carton) || 1;
                }
            }
            
            // Calculate total quantity in pieces
            const totalQuantityPieces = (cartonQty * quantityPerCarton) + pieceQty;
            const rowTotal = totalQuantityPieces * unitPrice;
            
            if (totalQuantityPieces > 0) {
                totalProducts++;
                totalQuantity += totalQuantityPieces;
                totalAmount += rowTotal;
                
                const ps2 = productSelect.selectedOptions && productSelect.selectedOptions[0];
                const productName = (ps2 && ps2.dataset && ps2.dataset.name) || productSelect.value;
                
                // Get supplier name from main supplier select
                const mainSupplierSelect = document.getElementById('mainSupplierSelect');
                const supplierName = mainSupplierSelect && mainSupplierSelect.selectedOptions && mainSupplierSelect.selectedOptions[0] 
                    ? mainSupplierSelect.selectedOptions[0].textContent.split(' - ')[1] || ''
                    : '';
                
                // Create quantity display
                let quantityDisplay = '';
                if (cartonQty > 0 && pieceQty > 0) {
                    quantityDisplay = `${cartonQty} CTN + ${pieceQty} PCS`;
                } else if (cartonQty > 0) {
                    quantityDisplay = `${cartonQty} CTN`;
                } else if (pieceQty > 0) {
                    quantityDisplay = `${pieceQty} PCS`;
                }
                
                productsSummary += `
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">${productName}</span>
                        <span class="font-medium">${quantityDisplay}</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Supplier: ${supplierName}</span>
                        <span>@ Rp ${unitPrice.toLocaleString('id-ID')}/pcs</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Total: ${totalQuantityPieces.toLocaleString('id-ID')} pcs</span>
                        <span>Subtotal: Rp ${rowTotal.toLocaleString('id-ID')}</span>
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

// Function to populate existing transactions
function populateExistingTransactions() {
    console.log('Populating existing transactions:', existingTransactions);
    
    if (!existingTransactions || existingTransactions.length === 0) {
        console.log('No existing transactions to populate');
        return;
    }
    
    // Get supplier ID from first transaction
    const supplierId = existingTransactions[0].supplier_id;
    console.log('Supplier ID from first transaction:', supplierId);
    
    const mainSupplierSelect = document.getElementById('mainSupplierSelect');
    if (mainSupplierSelect) {
        mainSupplierSelect.value = supplierId;
        console.log('Set main supplier select value to:', supplierId);
        
        // Trigger change event to load products
        mainSupplierSelect.dispatchEvent(new Event('change'));
    }
    
    // Wait longer for products to load, then populate
    setTimeout(() => {
        console.log('Starting to populate product rows...');
        existingTransactions.forEach((transaction, index) => {
            console.log(`Populating transaction ${index}:`, transaction);
            
            if (index === 0) {
                // Use the first row that's already added
                const firstRow = document.querySelector('.product-row');
                if (firstRow) {
                    console.log('Found first row, populating...');
                    populateProductRow(firstRow, transaction);
                } else {
                    console.log('First row not found');
                }
            } else {
                // Add new rows for additional transactions
                addProductRow();
                const newRow = document.querySelectorAll('.product-row')[index];
                if (newRow) {
                    console.log(`Found new row ${index}, populating...`);
                    populateProductRow(newRow, transaction);
                } else {
                    console.log(`New row ${index} not found`);
                }
            }
        });
        updateSummary();
    }, 1000); // Increased timeout to 1 second
}

function populateProductRow(row, transaction) {
    console.log('Populating product row with transaction:', transaction);
    
    const productSelect = row.querySelector('.product-select');
    const cartonInput = row.querySelector('.quantity-carton-input');
    const pieceInput = row.querySelector('.quantity-piece-input');
    const unitPriceInput = row.querySelector('.unit-price');
    const notesInput = row.querySelector('input[name*="[notes]"]');
    
    console.log('Found elements:', {
        productSelect: !!productSelect,
        cartonInput: !!cartonInput,
        pieceInput: !!pieceInput,
        unitPriceInput: !!unitPriceInput,
        notesInput: !!notesInput
    });
    
    // Set product - wait for options to be loaded
    if (productSelect) {
        console.log('Product select found, waiting for options...');
        // Wait for options to be populated
        const waitForOptions = () => {
            console.log('Checking options length:', productSelect.options.length);
            console.log('Available options:', Array.from(productSelect.options).map(opt => ({value: opt.value, text: opt.text})));
            if (productSelect.options.length > 1) {
                console.log('Options loaded, setting product value to:', transaction.product_id);
                productSelect.value = transaction.product_id;
                console.log('Product select value set to:', productSelect.value);
                // Trigger change event to load product info
                productSelect.dispatchEvent(new Event('change'));
                
                // Set quantities after product is selected
                setTimeout(() => {
                    console.log('Setting quantities and other values...');
                    if (cartonInput) {
                        cartonInput.value = transaction.quantity_carton || 0;
                        console.log('Set carton quantity to:', cartonInput.value);
                    }
                    if (pieceInput) {
                        pieceInput.value = transaction.quantity_piece || 0;
                        console.log('Set piece quantity to:', pieceInput.value);
                    }
                    
                    // Set unit price
                    if (unitPriceInput) {
                        unitPriceInput.value = transaction.unit_price || 0;
                        console.log('Set unit price to:', unitPriceInput.value);
                    }
                    
                    // Set notes
                    if (notesInput) {
                        notesInput.value = transaction.notes || '';
                        console.log('Set notes to:', notesInput.value);
                    }
                    
                    // Trigger calculation
                    if (unitPriceInput) {
                        calculateRowTotal(unitPriceInput);
                        console.log('Triggered calculation');
                    }
                }, 100);
            } else {
                console.log('Options not loaded yet, waiting...');
                // If options not loaded yet, wait a bit more
                setTimeout(waitForOptions, 100);
            }
        };
        waitForOptions();
    } else {
        console.log('Product select not found!');
    }
}

// Add first product row on page load
function safeInit() {
    console.log('Starting safeInit...');
    try {
        const tpl = document.getElementById('productRowTemplate');
        const container = document.getElementById('productsContainer');
        console.log('Template and container found:', !!tpl, !!container);
        
        if (tpl && container) {
            // Check if there are existing rows (from PHP)
            const existingRows = container.querySelectorAll('.product-row');
            console.log('Found existing rows:', existingRows.length);
            
            if (existingRows.length === 0) {
                console.log('No existing rows, adding first product row...');
                addProductRow();
                // Initialize Select2 for the first row after it's added
                initSelect2ForRow(container.lastElementChild);
            } else {
                console.log('Existing rows found, initializing them...');
                // Initialize existing rows
                existingRows.forEach((row, index) => {
                    console.log(`Initializing existing row ${index}`);
                    initSelect2ForRow(row);
                });
            }
            
            // Populate existing transactions
            console.log('Populating existing transactions...');
            populateExistingTransactions();
        }
        // Init Select2 for Order Acc By and Main Supplier
        if (typeof $ !== 'undefined') {
            console.log('jQuery available, initializing Select2...');
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
        } else {
            console.log('jQuery not available');
        }
        var dateInput = document.querySelector('input[name="transaction_date"]');
        if (dateInput) { dateInput.addEventListener('change', updateSummary); }
        var salesSelect = document.querySelector('select[name="sales_id"]');
        if (salesSelect) { salesSelect.addEventListener('change', updateSummary); }
        var supplierSelect = document.getElementById('mainSupplierSelect');
        if (supplierSelect) { supplierSelect.addEventListener('change', updateSummary); }
        
        console.log('safeInit completed successfully');
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
@endsection

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
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-3 min-w-0">
                            <!-- Produk -->
                            <div class="lg:col-span-2 min-w-0">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Produk <span class="text-red-500">*</span></label>
                                <select class="input-field product-select w-full" name="products[{{ $i }}][product_id]" required>
                                    @if($t->product)
                                        <option value="{{ $t->product_id }}" selected data-name="{{ $t->product->name }}" data-price="{{ $t->product->price }}" data-carton="{{ $t->product->quantity_per_carton ?? 1 }}">{{ $t->product->name }} - Rp {{ number_format($t->product->price, 0, ',', '.') }}</option>
                                    @else
                                        <option value="">Pilih Produk</option>
                                    @endif
                                </select>
                            </div>
                            
                            <!-- Quantity Row - CTN and PCS side by side -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">CTN</label>
                                        <input type="number" name="products[{{ $i }}][quantity_carton]" min="0" class="input-field quantity-carton-input w-full" oninput="calculateRowTotal(this)" onkeyup="calculateRowTotal(this)" onfocus="clearZeroValue(this)" placeholder="0" value="{{ $t->quantity_carton ?? 0 }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">PCS</label>
                                        <input type="number" name="products[{{ $i }}][quantity_piece]" min="0" class="input-field quantity-piece-input w-full" oninput="calculateRowTotal(this)" onkeyup="calculateRowTotal(this)" onfocus="clearZeroValue(this)" placeholder="0" value="{{ $t->quantity_piece ?? 0 }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Unit Price Row -->
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-3 min-w-0">
                            <div class="lg:col-span-2"></div> <!-- Empty space for alignment -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Satuan</label>
                                <input type="number" name="products[{{ $i }}][unit_price]" step="0.01" min="0" class="input-field unit-price w-full" oninput="calculateRowTotal(this)" placeholder="0" value="{{ $t->unit_price }}" readonly>
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
