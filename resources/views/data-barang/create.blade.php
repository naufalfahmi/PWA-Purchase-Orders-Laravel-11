@extends('layouts.app')

@section('title', 'Tambah Barang - Munah - Purchase Orders')
@section('page-title', 'Tambah Barang')

@section('content')
<div class="p-4">
    <div class="card p-6">
        <form method="POST" action="{{ route('data-barang.store') }}">
            @csrf
            
            <div class="space-y-4">
                <!-- Nama Barang -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Barang <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        class="input-field @error('name') border-red-500 @enderror"
                        placeholder="Masukkan nama barang"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SKU -->
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">
                        SKU <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="sku" 
                        name="sku" 
                        value="{{ old('sku') }}"
                        class="input-field @error('sku') border-red-500 @enderror"
                        placeholder="Masukkan SKU barang"
                        required
                    >
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Supplier -->
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Supplier <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="supplier_id" 
                        name="supplier_id" 
                        class="input-field @error('supplier_id') border-red-500 @enderror"
                        required
                    >
                        <option value="">Pilih Supplier...</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->kode_supplier }} - {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="category" 
                        name="category" 
                        class="input-field @error('category') border-red-500 @enderror"
                        required
                    >
                        <option value="">Pilih atau ketik kategori...</option>
                        <option value="Elektronik" {{ old('category') == 'Elektronik' ? 'selected' : '' }}>Elektronik</option>
                        <option value="Fashion" {{ old('category') == 'Fashion' ? 'selected' : '' }}>Fashion</option>
                        <option value="Makanan" {{ old('category') == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                        <option value="Kesehatan" {{ old('category') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                        <option value="Olahraga" {{ old('category') == 'Olahraga' ? 'selected' : '' }}>Olahraga</option>
                        <option value="Buku" {{ old('category') == 'Buku' ? 'selected' : '' }}>Buku</option>
                        <option value="Ice Cream" {{ old('category') == 'Ice Cream' ? 'selected' : '' }}>Ice Cream</option>
                        <option value="Snack" {{ old('category') == 'Snack' ? 'selected' : '' }}>Snack</option>
                        <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sub Kategori -->
                <div>
                    <label for="sub_category" class="block text-sm font-medium text-gray-700 mb-2">
                        Sub Kategori
                    </label>
                    <select 
                        id="sub_category" 
                        name="sub_category" 
                        class="input-field @error('sub_category') border-red-500 @enderror"
                    >
                        <option value="">Pilih atau ketik sub kategori...</option>
                        <option value="Smartphone" {{ old('sub_category') == 'Smartphone' ? 'selected' : '' }}>Smartphone</option>
                        <option value="Laptop" {{ old('sub_category') == 'Laptop' ? 'selected' : '' }}>Laptop</option>
                        <option value="Aksesoris" {{ old('sub_category') == 'Aksesoris' ? 'selected' : '' }}>Aksesoris</option>
                        <option value="Pakaian" {{ old('sub_category') == 'Pakaian' ? 'selected' : '' }}>Pakaian</option>
                        <option value="Sepatu" {{ old('sub_category') == 'Sepatu' ? 'selected' : '' }}>Sepatu</option>
                        <option value="Tas" {{ old('sub_category') == 'Tas' ? 'selected' : '' }}>Tas</option>
                        <option value="Makanan Ringan" {{ old('sub_category') == 'Makanan Ringan' ? 'selected' : '' }}>Makanan Ringan</option>
                        <option value="Minuman" {{ old('sub_category') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                        <option value="Obat" {{ old('sub_category') == 'Obat' ? 'selected' : '' }}>Obat</option>
                        <option value="Vitamin" {{ old('sub_category') == 'Vitamin' ? 'selected' : '' }}>Vitamin</option>
                        <option value="Fitness" {{ old('sub_category') == 'Fitness' ? 'selected' : '' }}>Fitness</option>
                        <option value="Sepak Bola" {{ old('sub_category') == 'Sepak Bola' ? 'selected' : '' }}>Sepak Bola</option>
                        <option value="Basket" {{ old('sub_category') == 'Basket' ? 'selected' : '' }}>Basket</option>
                        <option value="Teknologi" {{ old('sub_category') == 'Teknologi' ? 'selected' : '' }}>Teknologi</option>
                        <option value="Bisnis" {{ old('sub_category') == 'Bisnis' ? 'selected' : '' }}>Bisnis</option>
                        <option value="Novel" {{ old('sub_category') == 'Novel' ? 'selected' : '' }}>Novel</option>
                        <option value="Pendidikan" {{ old('sub_category') == 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                        <option value="Chocolate" {{ old('sub_category') == 'Chocolate' ? 'selected' : '' }}>Chocolate</option>
                        <option value="Vanilla" {{ old('sub_category') == 'Vanilla' ? 'selected' : '' }}>Vanilla</option>
                        <option value="Strawberry" {{ old('sub_category') == 'Strawberry' ? 'selected' : '' }}>Strawberry</option>
                        <option value="Keripik" {{ old('sub_category') == 'Keripik' ? 'selected' : '' }}>Keripik</option>
                        <option value="Permen" {{ old('sub_category') == 'Permen' ? 'selected' : '' }}>Permen</option>
                    </select>
                    @error('sub_category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Harga (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="price" 
                        name="price" 
                        value="{{ old('price') }}"
                        min="0"
                        step="100"
                        class="input-field @error('price') border-red-500 @enderror"
                        placeholder="Masukkan harga barang"
                        required
                    >
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stok dengan Unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Stok
                    </label>
                    <div class="space-y-3">
                        <div class="flex space-x-4">
                            <select 
                                id="stock_unit" 
                                name="stock_unit" 
                                class="w-1/2 input-field @error('stock_unit') border-red-500 @enderror"
                            >
                                <option value="PCS" {{ old('stock_unit', 'PCS') == 'PCS' ? 'selected' : '' }}>PCS</option>
                                <option value="CTN" {{ old('stock_unit') == 'CTN' ? 'selected' : '' }}>CTN</option>
                            </select>
                            &nbsp;&nbsp;
                            <input 
                                type="number" 
                                id="stock_quantity" 
                                name="stock_quantity" 
                                value="{{ old('stock_quantity', 0) }}"
                                min="0"
                                step="0.01"
                                class="w-1/2 input-field @error('stock_quantity') border-red-500 @enderror"
                                placeholder="Masukkan jumlah stok"
                            >
                        </div>
                        <!-- Pieces per carton (only show when CTN is selected) -->
                        <div id="pieces_per_carton_container" class="hidden">
                            <label for="pieces_per_carton" class="block text-sm font-medium text-gray-600 mb-1">
                                Jumlah pcs per carton
                            </label>
                            <input 
                                type="number" 
                                id="pieces_per_carton" 
                                name="pieces_per_carton" 
                                value="{{ old('pieces_per_carton', 12) }}"
                                min="1"
                                class="input-field @error('pieces_per_carton') border-red-500 @enderror"
                                placeholder="Contoh: 12"
                            >
                        </div>
                    </div>
                    @error('stock_quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('stock_unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('pieces_per_carton')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hidden field for quantity_per_carton (will be calculated) -->
                <input type="hidden" id="quantity_per_carton" name="quantity_per_carton" value="{{ old('quantity_per_carton', 0) }}">

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="3"
                        class="input-field @error('description') border-red-500 @enderror"
                        placeholder="Masukkan deskripsi barang (opsional)"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 pt-4">
                    <a href="{{ route('data-barang.index') }}" class="flex-1 btn-secondary text-center">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 btn-primary">
                        Simpan Barang
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

<!-- Select2 CSS/JS -->
<link href="{{ asset('libs/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('libs/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">
<script src="{{ asset('libs/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('libs/select2.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for all select elements
    if (typeof $ !== 'undefined') {
        // Supplier Select2
        $('#supplier_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih Supplier...',
            allowClear: true,
            templateResult: function (data) {
                if (!data.id) { return data.text; }
                var full = (data.text || '').toString();
                var parts = full.split(' - ');
                var code = parts[0] || '';
                var name = parts.slice(1).join(' - ') || code || full;
                var html = '<div style="display:flex; align-items:center; justify-content:space-between; width:100%">'
                         +   '<span>' + name + '</span>'
                         +   '<span style="color:#6b7280; font-size:0.875rem;">' + code + '</span>'
                         + '</div>';
                return $(html);
            },
            templateSelection: function (data) {
                if (!data.id) { return data.text; }
                var full = (data.text || '').toString();
                var parts = full.split(' - ');
                var name = parts.slice(1).join(' - ') || parts[0] || full;
                return name;
            }
        });

        // Category Select2 with tags (allow custom entries)
        $('#category').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih atau ketik kategori...',
            allowClear: true,
            tags: true,
            tokenSeparators: [','],
            createTag: function (params) {
                var term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            }
        });

        // Sub Category Select2 with tags (allow custom entries)
        $('#sub_category').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih atau ketik sub kategori...',
            allowClear: true,
            tags: true,
            tokenSeparators: [','],
            createTag: function (params) {
                var term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            }
        });

        // Stock Unit Select2
        $('#stock_unit').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih unit...',
            minimumResultsForSearch: Infinity // Disable search for this dropdown
        });
    }

    // Handle stock unit conversion and show/hide pieces per carton field
    function updateQuantityPerCarton() {
        const stockQuantity = parseFloat(document.getElementById('stock_quantity').value) || 0;
        const stockUnit = document.getElementById('stock_unit').value || 'PCS'; // Default to PCS
        const quantityPerCartonField = document.getElementById('quantity_per_carton');
        const piecesPerCartonContainer = document.getElementById('pieces_per_carton_container');
        
        // Show/hide pieces per carton field based on unit selection
        if (stockUnit === 'CTN') {
            piecesPerCartonContainer.classList.remove('hidden');
        } else {
            piecesPerCartonContainer.classList.add('hidden');
        }
        
        // Only calculate if stock quantity is provided
        if (stockQuantity > 0) {
            if (stockUnit === 'PCS') {
                // If unit is PCS, quantity_per_carton = stock_quantity
                quantityPerCartonField.value = stockQuantity;
            } else if (stockUnit === 'CTN') {
                // If unit is CTN, we need to convert to PCS
                const piecesPerCarton = parseFloat(document.getElementById('pieces_per_carton').value) || 12;
                quantityPerCartonField.value = stockQuantity * piecesPerCarton;
            }
        } else {
            quantityPerCartonField.value = 0;
        }
    }

    // Add event listeners for stock calculation
    document.getElementById('stock_quantity').addEventListener('input', updateQuantityPerCarton);
    document.getElementById('stock_unit').addEventListener('change', updateQuantityPerCarton);
    document.getElementById('pieces_per_carton').addEventListener('input', updateQuantityPerCarton);

    // Initialize on page load
    updateQuantityPerCarton();
});
</script>
