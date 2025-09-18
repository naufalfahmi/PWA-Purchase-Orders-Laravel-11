@extends('layouts.app')

@section('title', 'Detail Barang - Admin PWA')
@section('page-title', 'Detail Barang')

@section('content')
<div class="p-4 space-y-4">
    <!-- Barang Details -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">{{ $dataBarang->name }}</h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $dataBarang->stock_status['class'] }}">
                {{ $dataBarang->stock_status['label'] }}
            </span>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">SKU</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $dataBarang->sku }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600">Supplier</label>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($dataBarang->supplier)
                            {{ $dataBarang->supplier->kode_supplier }} - {{ $dataBarang->supplier->nama_supplier }}
                        @else
                            Tidak ada supplier
                        @endif
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600">Kategori</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $dataBarang->category }}</p>
                </div>
                
                @if($dataBarang->sub_category)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Sub Kategori</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $dataBarang->sub_category }}</p>
                </div>
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-600">Harga</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $dataBarang->formatted_price }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600">Stok</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">
                        @if($dataBarang->stock_quantity && $dataBarang->stock_unit)
                            {{ $dataBarang->stock_quantity }} {{ $dataBarang->stock_unit }}
                            @if($dataBarang->stock_unit === 'CTN' && $dataBarang->pieces_per_carton)
                                ({{ $dataBarang->quantity_per_carton }} pcs)
                            @endif
                        @else
                            {{ $dataBarang->quantity_per_carton }} pcs
                        @endif
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600">Tanggal Dibuat</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $dataBarang->created_at->format('d M Y, H:i') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600">Terakhir Diupdate</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $dataBarang->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            @if($dataBarang->description)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Deskripsi</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $dataBarang->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sales Transactions History -->
    @if($dataBarang->salesTransactions && $dataBarang->salesTransactions->count() > 0)
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Transaksi Penjualan</h3>
            <div class="space-y-3">
                @foreach($dataBarang->salesTransactions as $transaction)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">Transaksi #{{ $transaction->id }}</p>
                            <p class="text-sm text-gray-600">{{ $transaction->quantity }} pcs • {{ $transaction->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                            Selesai
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex space-x-3">
        <a href="{{ route('data-barang.index') }}" class="flex-1 btn-secondary text-center">
            Kembali ke Daftar
        </a>
        @if(auth()->user()->isOwner())
            <a href="{{ route('data-barang.edit', $dataBarang) }}" class="flex-1 btn-primary text-center">
                Edit Barang
            </a>
        @endif
    </div>
</div>
@endsection
