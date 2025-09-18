@forelse($salesTransactions as $transaction)
    <div class="card p-4 transaction-item" data-id="{{ $transaction->id }}">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2">
                    <h3 class="font-semibold text-gray-900">{{ $transaction->product->name ?? 'Produk tidak ditemukan' }}</h3>
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->status_badge['class'] }}">
                        {{ $transaction->status_badge['label'] }}
                    </span>
                </div>
                
                <div class="space-y-1 text-sm text-gray-600">
                    <p><span class="font-medium">Sales:</span> {{ $transaction->sales->name ?? 'N/A' }}</p>
                    <p><span class="font-medium">Quantity:</span> {{ $transaction->total_quantity_piece }} pcs</p>
                    <p><span class="font-medium">Amount:</span> Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                    <p><span class="font-medium">Tanggal:</span> {{ $transaction->created_at->format('d M Y') }}</p>
                    @if($transaction->po_number)
                        <p><span class="font-medium">PO:</span> {{ $transaction->po_number }}</p>
                    @endif
                    @if($transaction->notes)
                        <p><span class="font-medium">Notes:</span> {{ $transaction->notes }}</p>
                    @endif
                </div>
            </div>
            
            <div class="flex flex-col space-y-2 ml-4">
                <a href="{{ route('sales-transaction.show', $transaction->transaction_number) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Detail
                </a>
                
            </div>
        </div>
    </div>
@empty
    <div class="text-center py-8">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-gray-500 text-lg">Belum ada sales transaction</p>
        <p class="text-gray-400 text-sm">Mulai dengan membuat sales transaction baru</p>
    </div>
@endforelse
