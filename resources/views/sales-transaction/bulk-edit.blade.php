@extends('layouts.app')

@section('title', 'Edit PO - Admin PWA')
@section('page-title', 'Edit PO')

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
            </div>
        </div>

        <div class="card p-4 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Produk</h2>
                <button type="button" onclick="addProductRow()" class="btn-primary flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>Tambah Produk</span>
                </button>
            </div>

            <div id="productsContainer">
                @foreach($transactions as $i => $t)
                    <div class="product-row border rounded-lg p-4 mb-4 bg-white" data-index="{{ $i }}">
                        <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier <span class="text-red-500">*</span></label>
                                <select class="input-field supplier-select" name="products[{{ $i }}][supplier_id]" required>
                                    <option value="">Pilih Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ (string)optional(optional($t->product)->supplier)->id === (string)$supplier->id ? 'selected' : '' }}>{{ $supplier->kode_supplier }} - {{ $supplier->nama_supplier }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Produk <span class="text-red-500">*</span></label>
                                @php
                                    $prefilledProductId = $t->product_id;
                                @endphp
                                <select class="input-field product-select" name="products[{{ $i }}][product_id]" data-selected-product="{{ $prefilledProductId }}" required>
                                    @if($t->product)
                                        <option value="{{ $t->product_id }}" selected data-name="{{ $t->product->name }}" data-price="{{ $t->product->price }}">{{ $t->product->name }}</option>
                                    @else
                                        <option value="">Pilih Produk</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Type <span class="text-red-500">*</span></label>
                                <select class="input-field quantity-type" name="products[{{ $i }}][quantity_type]" required>
                                    <option value="">Pilih Type</option>
                                    <option value="carton" {{ $t->quantity_carton > 0 ? 'selected' : '' }}>Carton (CTN)</option>
                                    <option value="piece" {{ $t->quantity_piece > 0 ? 'selected' : '' }}>Piece (PCS)</option>
                                </select>
                            </div>
                            <div class="quantity-carton-field" style="display: {{ $t->quantity_carton > 0 ? 'block' : 'none' }};">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Order Qty (CTN)</label>
                                <input type="number" name="products[{{ $i }}][quantity_carton]" min="0" value="{{ $t->quantity_carton }}" class="input-field quantity-carton">
                            </div>
                            <div class="quantity-piece-field" style="display: {{ $t->quantity_piece > 0 ? 'block' : 'none' }};">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Order Qty (PCS)</label>
                                <input type="number" name="products[{{ $i }}][quantity_piece]" min="0" value="{{ $t->quantity_piece }}" class="input-field quantity-piece">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                                <input type="number" name="products[{{ $i }}][unit_price]" step="0.01" min="0" value="{{ $t->unit_price }}" class="input-field unit-price">
                            </div>
                            <div class="flex items-end"></div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Produk</label>
                            <input type="text" name="products[{{ $i }}][notes]" class="input-field" value="{{ $t->notes }}" placeholder="Catatan khusus untuk produk ini">
                            <div class="mt-3">
                                <button type="button" onclick="removeProductRow(this)" class="w-full bg-red-600 hover:bg-red-700 text-white rounded-md py-2 cursor-pointer">
                                    <svg class="w-4 h-4 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('products')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="card p-6 mb-6 bg-white border-2 border-gray-300 shadow-lg">
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">PURCHASE ORDER</h3>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tanggal:</span>
                    <span class="font-medium" id="summaryDate">{{ date('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Sales:</span>
                    <span class="font-medium" id="summarySales">-</span>
                </div>
                <div class="border-t border-dotted border-gray-400 my-4"></div>
                <div class="space-y-2" id="productsSummary"></div>
                <div class="border-t border-dotted border-gray-400 my-4"></div>
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
            </div>
        </div>

        <div class="flex space-x-3">
            <a href="{{ route('sales-transaction.index') }}" class="btn-secondary flex-1 text-center">Batal</a>
            <button type="submit" class="btn-primary flex-1">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

<template id="productRowTemplate">
    <div class="product-row border rounded-lg p-4 mb-4 bg-white" data-index="">
        <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier <span class="text-red-500">*</span></label>
                <select class="input-field supplier-select" name="products[INDEX][supplier_id]" required>
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" data-id="{{ $supplier->id }}" data-code="{{ $supplier->kode_supplier }}" data-name="{{ $supplier->nama_supplier }}">{{ $supplier->kode_supplier }} - {{ $supplier->nama_supplier }}</option>
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
                <select class="input-field quantity-type" name="products[INDEX][quantity_type]" required>
                    <option value="">Pilih Type</option>
                    <option value="carton">Carton (CTN)</option>
                    <option value="piece">Piece (PCS)</option>
                </select>
            </div>
            <div class="quantity-carton-field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Order Qty (CTN)</label>
                <input type="number" name="products[INDEX][quantity_carton]" min="0" value="0" class="input-field quantity-carton">
            </div>
            <div class="quantity-piece-field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Order Qty (PCS)</label>
                <input type="number" name="products[INDEX][quantity_piece]" min="0" value="0" class="input-field quantity-piece">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                <input type="number" name="products[INDEX][unit_price]" step="0.01" min="0" class="input-field unit-price">
            </div>
            <div class="flex items-end"></div>
        </div>
        <div class="mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Produk</label>
            <input type="text" name="products[INDEX][notes]" class="input-field" placeholder="Catatan khusus untuk produk ini">
            <div class="mt-3">
                <button type="button" onclick="removeProductRow(this)" class="w-full bg-red-600 hover:bg-red-700 text-white rounded-md py-2 cursor-pointer">
                    <svg class="w-4 h-4 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Shared helpers
function formatCurrencyID(value) {
    var num = parseFloat(value);
    if (!isFinite(num)) {
        num = 0;
    }
    return num.toLocaleString('id-ID');
}

let productIndex = (function() {
    var existing = document.querySelectorAll('#productsContainer .product-row').length;
    return isNaN(existing) ? 0 : existing;
})();

function loadProductsBySupplier(supplierSelect, productToSelect = null) {
    const supplierId = supplierSelect.value;
    const row = supplierSelect.closest('.product-row');
    const productSelect = row.querySelector('.product-select');
    
    // Check if the product list is empty
    if (!supplierId) {
        productSelect.innerHTML = '<option value="">Pilih Supplier terlebih dahulu</option>';
        if (typeof $ !== 'undefined' && $(productSelect).data('select2')) {
            $(productSelect).val(null).trigger('change');
        }
        return;
    }
    
    const fetchProducts = (url) => {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.withCredentials = true;
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        resolve(data.products || data);
                    } catch (e) {
                        reject('Error parsing response');
                    }
                } else {
                    reject(`Error ${xhr.status}`);
                }
            };
            xhr.onerror = function() {
                reject('Network error');
            };
            xhr.send();
        });
    };

    fetchProducts(`{{ route('sales-transaction.get-products-no-auth') }}?supplier_id=${supplierId}`)
        .then(products => {
            updateProductSelect(productSelect, products, productToSelect);
        })
        .catch(error => {
            fetchProducts(`{{ route('sales-transaction.get-products') }}?supplier_id=${supplierId}`)
                .then(products => {
                    updateProductSelect(productSelect, products, productToSelect);
                })
                .catch(authError => {
                    console.error(`Error loading products: ${authError}`);
                    productSelect.innerHTML = `<option value="">${authError}</option>`;
                    if (typeof $ !== 'undefined' && $(productSelect).data('select2')) {
                        $(productSelect).val(null).trigger('change');
                    }
                });
        });
}

function updateProductSelect(productSelect, products, productToSelect = null) {
    const $productSelect = $(productSelect);
    
    if (!products || products.length === 0) {
        $productSelect.html('<option value="">Tidak ada produk</option>');
        $productSelect.val(null).trigger('change');
        return;
    }

    let optionsHtml = '<option value="">Pilih Produk</option>';
    products.forEach(product => {
        optionsHtml += `<option value="${product.id}" data-price="${product.price}" data-carton="${product.quantity_per_carton || 1}" data-name="${product.name}">${product.name} - Rp ${parseFloat(product.price).toLocaleString('id-ID')}</option>`;
    });

    $productSelect.html(optionsHtml);

    if (productToSelect) {
        $productSelect.val(String(productToSelect));
    }
    
    setTimeout(() => {
        $productSelect.trigger('change.select2');
        // Removed the automatic call to updateProductInfo here.
    }, 50);
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
    if (!template || !container) return;
    const cloneHtml = template.innerHTML.replace(/INDEX/g, productIndex);
    const wrapper = document.createElement('div');
    wrapper.innerHTML = cloneHtml.trim();
    const newRow = wrapper.firstElementChild;
    container.appendChild(newRow);
    bindRowEvents(newRow);
    initSelect2ForRow(newRow);
    const qtyType = newRow.querySelector('.quantity-type');
    if (qtyType) {
        qtyType.value = 'carton';
        toggleQuantityInputs(qtyType);
    }
    productIndex++;
    updateSummary();
}

function removeProductRow(button) {
    const row = button.closest('.product-row');
    if (row) {
        row.remove();
        updateSummary();
    }
}

function updateProductInfo(select) {
    const row = select.closest('.product-row');
    let selectedOption = select.selectedOptions && select.selectedOptions[0];
    if (!selectedOption && select.value) {
        selectedOption = select.querySelector('option[value="' + select.value + '"]');
    }
    const price = parseFloat((selectedOption && selectedOption.dataset && selectedOption.dataset.price) || 0);
    const unitPriceInput = row.querySelector('.unit-price');
    unitPriceInput.value = price;
    calculateRowTotal(select);
}

function calculateRowTotal(input) {
    const row = input.closest('.product-row');
    const unitPrice = parseFloat((row.querySelector('.unit-price') && row.querySelector('.unit-price').value) || 0);
    const quantityType = row.querySelector('.quantity-type').value;
    let rowQuantity = 0;
    if (quantityType === 'carton') {
        rowQuantity = parseInt((row.querySelector('.quantity-carton') && row.querySelector('.quantity-carton').value) || 0);
    } else if (quantityType === 'piece') {
        rowQuantity = parseInt((row.querySelector('.quantity-piece') && row.querySelector('.quantity-piece').value) || 0);
    }
    const totalAmount = rowQuantity * unitPrice; // reserved for per-row use if needed
    updateSummary();
}

function updateSummary() {
    const rows = document.querySelectorAll('.product-row');
    let totalProducts = 0,
        totalQuantity = 0,
        totalAmount = 0;
    let productsSummary = '';
    const dateInput = document.querySelector('input[name="transaction_date"]');
    const salesSelect = document.querySelector('select[name="sales_id"]');
    if (dateInput && dateInput.value) {
        const d = new Date(dateInput.value);
        const el = document.getElementById('summaryDate');
        if (el) el.textContent = d.toLocaleDateString('id-ID');
    }
    if (salesSelect) {
        const s = salesSelect.selectedOptions && salesSelect.selectedOptions[0];
        const n = (s && s.textContent) || '-';
        const el = document.getElementById('summarySales');
        if (el) el.textContent = n;
    }
    rows.forEach(row => {
        const productSelect = row.querySelector('.product-select');
        const supplierSelect = row.querySelector('.supplier-select');
        const quantityType = row.querySelector('.quantity-type').value;
        const unitPrice = parseFloat((row.querySelector('.unit-price') && row.querySelector('.unit-price').value) || 0);
        if (productSelect && productSelect.value && supplierSelect && supplierSelect.value && quantityType) {
            let rowQuantity = 0,
                quantityDisplay = '';
            if (quantityType === 'carton') {
                const c = parseInt((row.querySelector('.quantity-carton') && row.querySelector('.quantity-carton').value) || 0);
                rowQuantity = c;
                quantityDisplay = `${c} CTN`;
            } else if (quantityType === 'piece') {
                const p = parseInt((row.querySelector('.quantity-piece') && row.querySelector('.quantity-piece').value) || 0);
                rowQuantity = p;
                quantityDisplay = `${p} PCS`;
            }
            if (rowQuantity > 0) {
                totalProducts++;
                totalQuantity += rowQuantity;
                totalAmount += rowQuantity * unitPrice;
                const ps2 = productSelect.selectedOptions && productSelect.selectedOptions[0];
                const productName = (ps2 && ps2.dataset && ps2.dataset.name) || productSelect.value;
                const ss = supplierSelect.selectedOptions && supplierSelect.selectedOptions[0];
                const supplierName = (ss && ss.dataset && ss.dataset.name) || (ss && ss.textContent && ss.textContent.split(' - ')[1]) || '';
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
    const ps = document.getElementById('productsSummary');
    if (ps) ps.innerHTML = productsSummary;
    const tp = document.getElementById('totalProducts');
    if (tp) tp.textContent = totalProducts;
    const tq = document.getElementById('totalQuantity');
    if (tq) tq.textContent = totalQuantity.toLocaleString('id-ID') + ' pcs';
    const ta = document.getElementById('totalAmount');
    if (ta) ta.textContent = 'Rp ' + totalAmount.toLocaleString('id-ID');
}

function initSelect2ForRow(row) {
    if (typeof $ === 'undefined' || !row) return;
    var $row = $(row);
    var commonOptions = {
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $row
    };

    // 1) Supplier
    $row.find('.supplier-select').each(function() {
        var $el = $(this);
        if ($el.data('select2')) {
            $el.select2('destroy');
        }
        $el.select2(Object.assign({}, commonOptions, {
            templateResult: function(data) {
                if (!data.id) return data.text;
                var full = (data.text || '').toString();
                var parts = full.split(' - ');
                var code = parts[0] || '';
                var name = parts.slice(1).join(' - ') || code || full;
                var html = `<div style="display:flex; align-items:center; justify-content:space-between; width:100%"><span>${name}</span><small class="text-muted" style="margin-left:auto; text-align:right; display:block;">${code}</small></div>`;
                return $(html);
            },
            templateSelection: function(data) {
                if (!data.id) return data.text;
                var full = (data.text || '').toString();
                var parts = full.split(' - ');
                var code = parts[0] || '';
                var name = parts.slice(1).join(' - ') || code || full;
                var html = `<div style="display:flex; align-items:center; justify-content:space-between; width:100%"><span>${name}</span><small class="text-muted" style="margin-left:auto; text-align:right; display:block;">${code}</small></div>`;
                return $(html);
            }
        }));
        // Trigger product load on supplier change
        $el.on('select2:select', function (e) {
            loadProductsBySupplier(this);
        });
    });

    // 2) Product
    $row.find('.product-select').each(function() {
        var $el = $(this);
        if ($el.data('select2')) {
            $el.select2('destroy');
        }
        $el.select2(Object.assign({}, commonOptions, {
            templateResult: function(data) {
                if (!data.id) return data.text;
                var $option = $(data.element);
                var name = $option.data('name') || data.text || '';
                var price = $option.data('price');
                var formatted = formatCurrencyID(price);
                var html = `<div style="display:flex; align-items:center; justify-content:space-between; width:100%"><span>${name}</span><small class="text-muted" style="margin-left:auto; text-align:right; display:block;">Rp ${formatted}</small></div>`;
                return $(html);
            },
            templateSelection: function(data) {
                if (!data.id) return data.text;
                var $option = $(data.element);
                var name = $option.data('name') || data.text || '';
                var price = $option.data('price');
                var formatted = formatCurrencyID(price);
                var html = `<div style="display:flex; align-items:center; justify-content:space-between; width:100%"><span>${name}</span><small class="text-muted" style="margin-left:auto; text-align:right; display:block;">${formatted}</small></div>`;
                return $(html);
            }
        }));
        $el.off('select2:select._product').on('select2:select._product', (e) => {
            updateProductInfo(this);
        });
    });

    // 3) Quantity type select
    $row.find('.quantity-type').each(function() {
        var $el = $(this);
        if ($el.data('select2')) {
            $el.select2('destroy');
        }
        $el.select2(commonOptions).on('select2:select._qty', (e) => {
            toggleQuantityInputs(this);
        });
    });
}

function bindRowEvents(row) {
    const productSelect = row.querySelector('.product-select');
    if (productSelect) {
        productSelect.addEventListener('change', function() {
            updateProductInfo(this);
        });
    }
    const qtyType = row.querySelector('.quantity-type');
    if (qtyType) {
        qtyType.addEventListener('change', function() {
            toggleQuantityInputs(this);
        });
    }
    const qtyCtn = row.querySelector('.quantity-carton');
    if (qtyCtn) {
        qtyCtn.addEventListener('input', function() {
            calculateRowTotal(this);
        });
    }
    const qtyPcs = row.querySelector('.quantity-piece');
    if (qtyPcs) {
        qtyPcs.addEventListener('input', function() {
            calculateRowTotal(this);
        });
    }
    const unitPrice = row.querySelector('.unit-price');
    if (unitPrice) {
        unitPrice.addEventListener('input', function() {
            calculateRowTotal(this);
        });
    }
}

function syncRowInitialState(row) {
    const supplierSelect = row.querySelector('.supplier-select');
    const selectedSupplierId = supplierSelect.value;
    const productSelect = row.querySelector('.product-select');
    const prefilledProductId = productSelect.getAttribute('data-selected-product');
    
    if (selectedSupplierId) {
        loadProductsBySupplier(supplierSelect, prefilledProductId);
    }
    const qtyType = row.querySelector('.quantity-type');
    if (qtyType && qtyType.value) {
        toggleQuantityInputs(qtyType);
    }
}

function initPage() {
    if (typeof $ !== 'undefined') {
        var $orderAcc = $('#orderAccBy');
        if ($orderAcc.length) {
            $orderAcc.select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    }
    
    const allRows = document.querySelectorAll('#productsContainer .product-row');
    allRows.forEach(function(row) {
        initSelect2ForRow(row);
        bindRowEvents(row);
        syncRowInitialState(row);
    });

    var dateInput = document.querySelector('input[name="transaction_date"]');
    if (dateInput) {
        dateInput.addEventListener('change', updateSummary);
    }
    var salesSelect = document.querySelector('select[name="sales_id"]');
    if (salesSelect) {
        salesSelect.addEventListener('change', updateSummary);
    }

    // Call updateSummary() at the end to ensure all elements are ready
    setTimeout(updateSummary, 200);
}


if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPage);
} else {
    initPage();
}
</script>
@endpush