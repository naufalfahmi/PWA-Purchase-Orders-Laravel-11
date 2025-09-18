@extends('layouts.app')

@section('title', 'Input PO (Bulk) - Admin PWA')
@section('page-title', 'Input PO')

@section('content')
<div class="p-4">
    <form method="POST" action="{{ route('sales-transaction.bulk-store') }}" id="salesTransactionForm">
        @csrf
        
        <!-- Header Information -->
        <div class="card p-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Umum</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Transaksi <span class="text-red-500">*</span></label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" class="input-field @error('transaction_date') border-red-500 @enderror" required>
                    @error('transaction_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengiriman</label>
                    <input type="date" name="delivery_date" value="{{ old('delivery_date') }}" class="input-field @error('delivery_date') border-red-500 @enderror">
                    @error('delivery_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sales <span class="text-red-500">*</span></label>
                    @php
                        $selectedSalesId = old('sales_id') ?? ($currentSales->id ?? null);
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
                    <input type="text" name="po_number" value="{{ old('po_number', $defaultPoNumber ?? '') }}" class="input-field @error('po_number') border-red-500 @enderror" placeholder="Auto-generated jika kosong">
                    @error('po_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Umum</label>
                    <textarea name="general_notes" rows="3" class="input-field @error('general_notes') border-red-500 @enderror" placeholder="Catatan umum untuk seluruh transaksi">{{ old('general_notes') }}</textarea>
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
                                <option value="{{ $opt }}" {{ old('order_acc_by') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('order_acc_by')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="card p-4 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Produk</h2>
                <button type="button" onclick="addProductRowPrompt()" class="btn-primary flex items-center space-x-2 cursor-pointer">
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
                    <span class="font-medium" id="summaryDate">{{ date('d/m/Y') }}</span>
                </div>

                <!-- Sales -->
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Sales:</span>
                    <span class="font-medium" id="summarySales">-</span>
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
        <div class="flex space-x-3">
            <a href="{{ route('sales-transaction.index') }}" class="btn-secondary flex-1 text-center">Batal</a>
            <button type="submit" class="btn-primary flex-1">Simpan PO</button>
        </div>
    </form>
</div>

<!-- Product Row Template -->
<template id="productRowTemplate">
    <div class="product-row border rounded-lg p-4 mb-4 bg-white" data-index="">
        <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier <span class="text-red-500">*</span></label>
                <select class="input-field supplier-select" name="products[INDEX][supplier_id]" onchange="loadProductsBySupplier(this)" required>
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">
                            {{ $supplier->kode_supplier }} - {{ $supplier->nama_supplier }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Produk <span class="text-red-500">*</span></label>
                <select class="input-field product-select" name="products[INDEX][product_id]" required>
                    <option value="">Pilih Supplier terlebih dahulu</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Type <span class="text-red-500">*</span></label>
                <select class="input-field quantity-type" name="products[INDEX][quantity_type]" onchange="toggleQuantityInputs(this)" required>
                    <option value="">Pilih Type</option>
                    <option value="carton">Carton (CTN)</option>
                    <option value="piece">Piece (PCS)</option>
                </select>
            </div>
            
            <div class="quantity-carton-field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Order Qty (CTN)</label>
                <input type="number" name="products[INDEX][quantity_carton]" min="0" value="0" class="input-field quantity-carton" oninput="calculateRowTotal(this)" onkeyup="calculateRowTotal(this)">
            </div>
            
            <div class="quantity-piece-field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Order Qty (PCS)</label>
                <input type="number" name="products[INDEX][quantity_piece]" min="0" value="0" class="input-field quantity-piece" oninput="calculateRowTotal(this)" onkeyup="calculateRowTotal(this)">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                <input type="number" name="products[INDEX][unit_price]" step="0.01" min="0" class="input-field unit-price" oninput="calculateRowTotal(this)">
            </div>
            
            <div class="flex items-end"></div>
        </div>
        
        <div class="mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Produk</label>
            <input type="text" name="products[INDEX][notes]" class="input-field" placeholder="Catatan khusus untuk produk ini">
            <div class="mt-3">
                <button type="button" onclick="removeProductRow(this)" class="w-full bg-red-600 hover:bg-red-700 text-white rounded-md py-2 cursor-pointer">
                    <svg class="w-4 h-4 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<!-- Select2 CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let productIndex = 0;

function loadProductsBySupplier(supplierSelect) {
    const supplierId = supplierSelect.value;
    const row = supplierSelect.closest('.product-row');
    const productSelect = row.querySelector('.product-select');
    
    
    if (supplierId) {
        // Get CSRF token (no optional chaining for older browsers)
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
                        // Always update the product select, even when empty
                        if (data && Array.isArray(data.products)) {
                            updateProductSelect(productSelect, data.products);
                            return; // Do not fallback to auth; handled by no-auth
                        }
                    } catch (e) {
                    }
                }
                
                // If no auth failed, try with auth
                loadProductsWithAuth(supplierId, productSelect);
            }
        };
        noAuthXhr.send();
    } else {
        productSelect.innerHTML = '<option value="">Pilih Supplier terlebih dahulu</option>';
    }
}

function loadProductsWithAuth(supplierId, productSelect) {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value;
    
    // Use XMLHttpRequest instead of fetch for better compatibility
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `{{ route('sales-transaction.get-products') }}?supplier_id=${supplierId}`, true);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.withCredentials = true; // Include cookies for session
    if (csrfToken) {
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        }
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                
                if (xhr.status === 200) {
                    try {
                        // Check if response is HTML (likely an error page)
                        if (xhr.responseText.trim().startsWith('<!DOCTYPE html>') || xhr.responseText.trim().startsWith('<html')) {
                            console.error('Server returned HTML instead of JSON:', xhr.responseText.substring(0, 500));
                            
                            // Check if it's a login redirect
                            if (xhr.responseText.includes('login') || xhr.responseText.includes('Login')) {
                                console.error('Detected login redirect - user not authenticated');
                                productSelect.innerHTML = '<option value="">Please login again</option>';
                            } else {
                                productSelect.innerHTML = '<option value="">Server error - HTML response</option>';
                            }
                            return;
                        }
                        
                        const products = JSON.parse(xhr.responseText);
                        updateProductSelect(productSelect, products);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        console.error('Response text:', xhr.responseText.substring(0, 500));
                        productSelect.innerHTML = '<option value="">Error parsing response</option>';
                    }
                } else {
                    console.error('HTTP Error:', xhr.status, xhr.responseText.substring(0, 500));
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

function updateProductSelect(productSelect, products) {
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
        productSelect.appendChild(option);
    });
}

function toggleQuantityInputs(select) {
    const row = select.closest('.product-row');
    const cartonField = row.querySelector('.quantity-carton-field');
    const pieceField = row.querySelector('.quantity-piece-field');
    
    if (select.value === 'carton') {
        cartonField.style.display = 'block';
        pieceField.style.display = 'none';
        pieceField.querySelector('input').value = 0;
    } else if (select.value === 'piece') {
        cartonField.style.display = 'none';
        pieceField.style.display = 'block';
        cartonField.querySelector('input').value = 0;
    } else {
        cartonField.style.display = 'none';
        pieceField.style.display = 'none';
        cartonField.querySelector('input').value = 0;
        pieceField.querySelector('input').value = 0;
    }
    
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
    productSelect.addEventListener('change', function() {
        updateProductInfo(this);
    });
    // Initialize Select2 on new selects
    initSelect2ForRow(newRow);
    // Default quantity type to CTN and show CTN input immediately
    const qtyType = newRow.querySelector('.quantity-type');
    if (qtyType) {
        qtyType.value = 'carton';
        toggleQuantityInputs(qtyType);
    }
    
    productIndex++;
    updateSummary();
}

function initSelect2ForRow(row) {
    if (typeof $ === 'undefined' || !row) return;
    var $row = $(row);
    var commonOptions = {
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $row
    };
    $row.find('.supplier-select').each(function() {
        if ($(this).data('select2')) { $(this).select2('destroy'); }
        $(this).select2(Object.assign({}, commonOptions, {
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
        }));
        // Ensure JS handler runs on Select2 selection
        $(this).off('select2:select._supplier').on('select2:select._supplier', (e) => {
            // Trigger native change to reuse existing handler on element
            this.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
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
    
    unitPriceInput.value = price;
    if (displayUnitPrice) {
        displayUnitPrice.textContent = 'Rp ' + price.toLocaleString('id-ID');
    }
    
    calculateRowTotal(select);
}

function calculateRowTotal(input) {
    const row = input.closest('.product-row');
    const unitPrice = parseFloat((row.querySelector('.unit-price') && row.querySelector('.unit-price').value) || 0);
    
    // Get quantity based on quantity type
    const quantityType = row.querySelector('.quantity-type').value;
    let totalQuantity = 0;
    
    if (quantityType === 'carton') {
        const carton = parseInt((row.querySelector('.quantity-carton') && row.querySelector('.quantity-carton').value) || 0);
        // Treat CTN same as PCS: use the entered number as total quantity
        totalQuantity = carton;
    } else if (quantityType === 'piece') {
        const piece = parseInt(row.querySelector('.quantity-piece').value || 0);
        totalQuantity = piece;
    }
    
    const totalAmount = totalQuantity * unitPrice;
    
    updateSummary();
}

function updateSummary() {
    const rows = document.querySelectorAll('.product-row');
    let totalProducts = 0;
    let totalQuantity = 0;
    let totalAmount = 0;
    let productsSummary = '';
    
    // Update date and sales
    const transactionDate = document.querySelector('input[name="transaction_date"]').value;
    const salesSelect = document.querySelector('select[name="sales_id"]');
    const salesSelected = salesSelect && salesSelect.selectedOptions && salesSelect.selectedOptions[0];
    const salesName = (salesSelected && salesSelected.textContent) || '-';
    
    if (transactionDate) {
        const date = new Date(transactionDate);
        document.getElementById('summaryDate').textContent = date.toLocaleDateString('id-ID');
    }
    document.getElementById('summarySales').textContent = salesName;
    
    rows.forEach(row => {
        const productSelect = row.querySelector('.product-select');
        const supplierSelect = row.querySelector('.supplier-select');
        const quantityType = row.querySelector('.quantity-type').value;
        const unitPrice = parseFloat((row.querySelector('.unit-price') && row.querySelector('.unit-price').value) || 0);
        
        if (productSelect.value && supplierSelect.value && quantityType && unitPrice > 0) {
            let rowQuantity = 0;
            let quantityDisplay = '';
            
            if (quantityType === 'carton') {
                const carton = parseInt(row.querySelector('.quantity-carton').value || 0);
                rowQuantity = carton;
                quantityDisplay = `${carton} CTN`;
            } else if (quantityType === 'piece') {
                const piece = parseInt(row.querySelector('.quantity-piece').value || 0);
                rowQuantity = piece;
                quantityDisplay = `${piece} PCS`;
            }
            
            if (rowQuantity > 0) {
                totalProducts++;
                totalQuantity += rowQuantity;
                totalAmount += rowQuantity * unitPrice;
                
                const ps2 = productSelect.selectedOptions && productSelect.selectedOptions[0];
                const productName = (ps2 && ps2.dataset && ps2.dataset.name) || productSelect.value;
                const ss = supplierSelect.selectedOptions && supplierSelect.selectedOptions[0];
                const supplierName = (ss && ss.textContent && ss.textContent.split(' - ')[1]) || '';
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

// Add first product row on page load
function safeInit() {
    try {
        const tpl = document.getElementById('productRowTemplate');
        const container = document.getElementById('productsContainer');
        if (tpl && container) {
            addProductRow();
            // Initialize Select2 for the first row after it's added
            initSelect2ForRow(container.lastElementChild);
        }
        // Init Select2 for Order Acc By
        if (typeof $ !== 'undefined') {
            var $orderAcc = $('#orderAccBy');
            if ($orderAcc.length) {
                $orderAcc.select2({ theme: 'bootstrap-5', width: '100%' });
            }
        }
        var dateInput = document.querySelector('input[name="transaction_date"]');
        if (dateInput) { dateInput.addEventListener('change', updateSummary); }
        var salesSelect = document.querySelector('select[name="sales_id"]');
        if (salesSelect) { salesSelect.addEventListener('change', updateSummary); }
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
