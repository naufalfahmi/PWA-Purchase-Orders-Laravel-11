@extends('layouts.app')

@section('title', 'Detail PO - Munah - Purchase Orders')
@section('page-title', 'Detail PO')

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

    <div class="space-y-6">

        <div class="card p-6 mb-6 bg-white border-2 border-gray-300 shadow-lg">
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">PURCHASE ORDER</h3>
                <div class="border-t border-b border-gray-300 py-2">
                    <p class="text-sm text-gray-600">{{ $transactions->first()->po_number }}</p>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tanggal:</span>
                    <span class="font-medium">{{ $transactions->first()->transaction_date->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Sales:</span>
                    <span class="font-medium">{{ $transactions->first()->sales->name ?? 'N/A' }}</span>
                </div>
                @if($transactions->first()->delivery_date)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Delivery:</span>
                    <span class="font-medium">{{ $transactions->first()->delivery_date->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($transactions->first()->order_acc_by)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Order Acc By:</span>
                    <span class="font-medium">{{ $transactions->first()->order_acc_by }}</span>
                </div>
                @endif
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Status:</span>
                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full {{ $transactions->first()->status_badge['class'] }}">
                        {{ $transactions->first()->status_badge['label'] }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Supplier:</span>
                    <span class="font-medium">{{ $transactions->first()->product->supplier->nama_supplier ?? 'N/A' }}</span>
                </div>
                <div class="border-t border-dotted border-gray-400 my-4"></div>
                <div class="space-y-2">
                    @foreach($transactions as $transaction)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700">{{ $transaction->product->name ?? 'Produk tidak ditemukan' }}</span>
                            <span class="font-medium">
                                @php
                                    $displayQty = '';
                                    if (($transaction->quantity_carton ?? 0) > 0) {
                                        $displayQty .= $transaction->quantity_carton . ' CTN';
                                    }
                                    if (($transaction->quantity_piece ?? 0) > 0) {
                                        if ($displayQty) $displayQty .= ' + ';
                                        $displayQty .= $transaction->quantity_piece . ' PCS';
                                    }
                                @endphp
                                {{ $displayQty }}
                            </span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>{{ number_format($transaction->total_quantity_piece, 0, ',', '.') }} pcs total</span>
                            <span>@ Rp {{ number_format($transaction->unit_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-medium mb-2">
                            <span>Subtotal:</span>
                            <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-dotted border-gray-300 mb-2"></div>
                    @endforeach
                </div>
                <div class="border-t border-dotted border-gray-400 my-4"></div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Total Items:</span>
                        <span class="font-medium">{{ $transactions->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Total Quantity:</span>
                        <span class="font-medium">{{ number_format($totalQuantity, 0, ',', '.') }} pcs</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-900">TOTAL AMOUNT:</span>
                        <span class="text-lg font-bold text-blue-600">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>
                </div>
                @if($transactions->first()->general_notes)
                <div class="border-t border-gray-300 pt-4">
                    <p class="text-sm text-gray-600 mb-1">Notes:</p>
                    <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded">{{ $transactions->first()->general_notes }}</p>
                </div>
                @endif
                
            </div>
        </div>

        @if(Auth::user()->isOwner())
            <div class="card p-6">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Persetujuan Purchase Order</h3>
                    <p class="text-sm text-gray-600">Tinjau dan berikan persetujuan untuk PO ini</p>
                </div>
                
                <form id="approvalForm" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            Catatan (Opsional)
                        </label>
                        <textarea name="approval_notes" rows="3" class="input-field" placeholder="Tambahkan catatan persetujuan atau penolakan..."></textarea>
                    </div>
                    
                    <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input type="checkbox" id="send_whatsapp" name="send_whatsapp" value="1" checked class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label for="send_whatsapp" class="ml-3 flex items-center">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                    </svg>
                                    <span class="text-sm font-medium text-green-800">
                                        Kirim notifikasi ke WhatsApp Sales
                                    </span>
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-green-600 mt-1 ml-7">
                            Membuka WhatsApp dengan pesan persetujuan yang sudah diformat
                        </p>
                        <div id="whatsapp-status" class="mt-2 ml-7 text-xs text-gray-600 hidden">
                            <span id="status-text">Checking WhatsApp availability...</span>
                        </div>
                    </div>
                    
                    @if($transactions->first()->isPending())
                        <div class="flex space-x-4">
                            <button type="button" onclick="submitApproval('reject')" style="background-color: #dc2626 !important; color: white !important; font-weight: 500 !important; padding: 12px 16px !important; border-radius: 8px !important; border: none !important; flex: 1 !important; display: flex !important; align-items: center !important; justify-content: center !important; transition: all 0.2s !important; cursor: pointer !important;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">
                                <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tolak PO
                            </button>
                            <button type="button" onclick="submitApproval('approve')" style="background-color: #16a34a !important; color: white !important; font-weight: 500 !important; padding: 12px 16px !important; border-radius: 8px !important; border: none !important; flex: 1 !important; display: flex !important; align-items: center !important; justify-content: center !important; transition: all 0.2s !important; cursor: pointer !important;" onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'">
                                <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Setujui PO
                            </button>
                        </div>
                    @else
                        <div class="text-center p-4 bg-gray-100 rounded-lg">
                            <p class="text-gray-600">PO ini sudah {{ $transactions->first()->approval_status === 'approved' ? 'disetujui' : 'ditolak' }}</p>
                        </div>
                    @endif
                </form>
            </div>
        @endif

        @if(Auth::user()->isOwner() && !$transactions->first()->isPending())
            <!-- Kirim Ulang ke WhatsApp untuk PO yang sudah disetujui/ditolak -->
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                        </svg>
                        <span class="text-sm font-medium text-blue-800">
                            Kirim Ulang Notifikasi WhatsApp
                        </span>
                    </div>
                </div>
                <p class="text-xs text-blue-600 mt-1 ml-7">
                    Kirim ulang notifikasi persetujuan ke WhatsApp Sales
                </p>
                <div class="mt-3 ml-7">
                    <button type="button" onclick="resendWhatsAppNotification()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                        </svg>
                        Kirim Ulang ke WhatsApp
                    </button>
                </div>
                <div id="whatsapp-status" class="mt-2 ml-7 text-xs text-gray-600 hidden">
                    <span id="status-text">Checking WhatsApp availability...</span>
                </div>
            </div>
        @endif

        @if($transactions->first()->approved_at)
            <div class="card p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Persetujuan</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">
                                {{ $transactions->first()->approval_status === 'approved' ? 'Disetujui' : 'Ditolak' }} oleh 
                                {{ $transactions->first()->approver->name ?? 'Unknown' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $transactions->first()->approved_at->format('d M Y H:i') }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transactions->first()->status_badge['class'] }}">
                            {{ $transactions->first()->status_badge['label'] }}
                        </span>
                    </div>
                    @if($transactions->first()->approval_notes)
                        <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-900">{{ $transactions->first()->approval_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @php
            $poNumber = $transactions->first()->po_number ?? null;
            $canDelete = false;
            $user = Auth::user();
            $transaction = $transactions->first();
            
            if ($user->isOwner()) {
                $canDelete = true;
            } elseif ($user->isSales()) {
                $currentSales = \App\Models\Sales::where('name', $user->name)->first();
                $canDelete = $currentSales && ($transaction->sales_id === $currentSales->id);
            }
        @endphp
        
        <div class="flex flex-wrap gap-2 justify-center sm:justify-start">
            <!-- Kembali Button -->
            <a href="{{ route('sales-transaction.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            
            @if($poNumber)
                <!-- Edit Button -->
                <a href="{{ route('sales-transaction.edit-po', $poNumber) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                
                <!-- Export PDF Button -->
                <a href="{{ route('sales-transaction.export-pdf', $poNumber) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    PDF
                </a>
                
                <!-- Delete PO Button -->
                <form action="{{ route('sales-transaction.delete-po', $poNumber) }}" method="POST" onsubmit="return confirmDeletePO(event)" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                            style="background-color: #dc2626;"
                            onmouseover="this.style.backgroundColor='#b91c1c'"
                            onmouseout="this.style.backgroundColor='#dc2626'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                </form>
            @endif
        </div>
        
        
    </div>
</div>

<script>
    function submitApproval(action) {
        const form = document.getElementById('approvalForm');
        const notes = document.querySelector('textarea[name="approval_notes"]').value.trim();
        const sendWhatsApp = document.getElementById('send_whatsapp').checked;
        
        let confirmationMessage = '';
        if (action === 'approve') {
            confirmationMessage = 'Apakah Anda yakin ingin menyetujui Purchase Order ini?';
            form.action = '{{ route("sales-transaction.approve-po", $transactions->first()->po_number) }}';
        } else if (action === 'reject') {
            confirmationMessage = 'Apakah Anda yakin ingin menolak Purchase Order ini?';
            form.action = '{{ route("sales-transaction.reject-po", $transactions->first()->po_number) }}';
        }
        
        if (confirm(confirmationMessage)) {
            if (sendWhatsApp) {
                const isWhatsAppOpened = openWhatsApp(action, notes);
                
                if (isWhatsAppOpened) {
                    setTimeout(() => {
                        form.submit();
                    }, 500);
                } else {
                    form.submit();
                }
            } else {
                form.submit();
            }
        }
    }

    function openWhatsApp(status, notes = '') {
        const salesPhone = '{{ $transactions->first()->sales->phone ?? "" }}';
        const poNumber = '{{ $transactions->first()->po_number ?? "" }}';
        const amount = '{{ number_format($totalAmount, 0, ",", ".") }}';
        const salesName = '{{ $transactions->first()->sales->name ?? "Sales" }}';
        const approverName = '{{ auth()->user()->name ?? "Owner" }}';
        const supplierName = '{{ $transactions->first()->product->supplier->nama_supplier ?? "N/A" }}';
        const transactionDate = '{{ $transactions->first()->transaction_date->format("d/m/Y") }}';
        const deliveryDate = '{{ $transactions->first()->delivery_date ? $transactions->first()->delivery_date->format("d/m/Y") : "TBD" }}';
        
        const statusText = status === 'approve' ? 'DISETUJUI' : 'DITOLAK';
        const statusSymbol = status === 'approve' ? '[✓]' : '[✗]';
        
        // Use the same template as resendWhatsAppNotification
        let message = '*NOTIFIKASI PERSETUJUAN PO*\n\n';
        message += statusSymbol + ' Status: *' + statusText + '*\n';
        message += 'PO Number: *' + poNumber + '*\n';
        message += 'Tanggal: ' + transactionDate + '\n';
        message += 'Delivery: ' + deliveryDate + '\n';
        message += 'Supplier: *' + supplierName + '*\n';
        message += 'Sales: ' + salesName + '\n';
        message += 'Disetujui oleh: *' + approverName + '*\n\n';
        
        message += '*DETAIL PRODUK:*\n';
        @foreach($transactions as $transaction)
            @php
                $displayQty = '';
                if (($transaction->quantity_carton ?? 0) > 0) {
                    $displayQty .= $transaction->quantity_carton . ' CTN';
                }
                if (($transaction->quantity_piece ?? 0) > 0) {
                    if ($displayQty) $displayQty .= ' + ';
                    $displayQty .= $transaction->quantity_piece . ' PCS';
                }
                $displayQty .= ' (' . number_format($transaction->total_quantity_piece, 0, ',', '.') . ' pcs total)';
            @endphp
            message += '• {{ $transaction->product->name ?? "N/A" }}\n';
            message += '  Qty: {{ $displayQty }}\n';
            message += '  Harga: Rp {{ number_format($transaction->unit_price, 0, ",", ".") }}\n';
            message += '  Subtotal: Rp {{ number_format($transaction->total_amount, 0, ",", ".") }}\n\n';
        @endforeach
        
        message += 'TOTAL: *Rp ' + amount + '*\n';
        
        if (notes) {
            message += '\nCatatan: ' + notes + '\n';
        }
        
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: 'Asia/Jakarta'
        };
        const indonesianTime = now.toLocaleString('id-ID', options);
        message += '\nWaktu: ' + indonesianTime + ' WIB\n';
        message += '\nTerima kasih!';
        
        let cleanPhone = salesPhone.replace(/\D/g, '');
        if (cleanPhone.startsWith('0')) {
            cleanPhone = '62' + cleanPhone.substring(1);
        } else if (!cleanPhone.startsWith('62')) {
            cleanPhone = '62' + cleanPhone;
        }
        
        return tryOpenWhatsApp(message, cleanPhone);
    }
    
    function tryOpenWhatsApp(message, cleanPhone) {
        // Clean the message to avoid encoding issues
        const cleanMessage = message.replace(/[\u200B-\u200D\uFEFF]/g, ''); // Remove zero-width characters
        
        // console.log('Opening WhatsApp with clean message:', cleanMessage);
        updateWhatsAppStatus('🚀 Opening WhatsApp...', 'info');
        
        // Try to open WhatsApp app first
        tryOpenWhatsAppApp(cleanMessage, cleanPhone);
        
        return true;
    }
    
    function tryOpenWhatsAppApp(message, cleanPhone) {
        // console.log('Attempting to open WhatsApp app...');
        
        // Detect if we're on mobile or desktop
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        // console.log('Device type:', isMobile ? 'Mobile' : 'Desktop');
        
        if (isMobile) {
            tryOpenWhatsAppMobile(message, cleanPhone);
        } else {
            tryOpenWhatsAppDesktop(message, cleanPhone);
        }
    }
    
    function tryOpenWhatsAppMobile(message, cleanPhone) {
        // console.log('Trying mobile WhatsApp app...');
        updateWhatsAppStatus('📱 Mobile detected - trying WhatsApp app...', 'info');
        
        try {
            const appUrl = `whatsapp://send?phone=${cleanPhone}&text=${encodeURIComponent(message)}`;
        // console.log('Trying WhatsApp mobile app:', appUrl);
            
            // Create a temporary link and try to open it
            const link = document.createElement('a');
            link.href = appUrl;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Check if the app opened by monitoring focus/blur events
            let appOpened = false;
            let focusLost = false;
            
            const handleBlur = () => {
                focusLost = true;
        // console.log('Page lost focus - WhatsApp app likely opened');
                updateWhatsAppStatus('📱 WhatsApp app opened successfully!', 'success');
            };
            
            const handleFocus = () => {
                if (focusLost) {
                    appOpened = true;
        // console.log('WhatsApp app opened successfully (mobile)');
                    document.removeEventListener('blur', handleBlur);
                    document.removeEventListener('focus', handleFocus);
                }
            };
            
            // Listen for focus events
            window.addEventListener('blur', handleBlur);
            window.addEventListener('focus', handleFocus);
            
            // If no app opened within 3 seconds, try web fallback
            setTimeout(() => {
                if (!appOpened && !focusLost) {
        // console.log('WhatsApp mobile app not available, trying web version');
                    updateWhatsAppStatus('📱 WhatsApp app not detected, opening web version...', 'warning');
                    window.removeEventListener('blur', handleBlur);
                    window.removeEventListener('focus', handleFocus);
                    tryOpenWhatsAppWeb(message, cleanPhone);
                }
            }, 3000);
            
        } catch (e) {
            // console.log('WhatsApp mobile app failed:', e);
            tryOpenWhatsAppWeb(message, cleanPhone);
        }
    }
    
    function tryOpenWhatsAppDesktop(message, cleanPhone) {
        // console.log('Trying desktop WhatsApp...');
        updateWhatsAppStatus('💻 Desktop detected - opening WhatsApp Web...', 'info');
        
        // On desktop, try WhatsApp Web directly
        tryOpenWhatsAppWeb(message, cleanPhone);
    }
    
    function tryOpenWhatsAppWeb(message, cleanPhone) {
        // console.log('Opening WhatsApp web version...');
        
        // Method 2: Try wa.me with timeout detection
        try {
            const waUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`;
        // console.log('Trying wa.me:', waUrl);
            
            // Create a hidden iframe with allow-downloads sandbox attribute
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.sandbox = 'allow-scripts allow-same-origin allow-downloads allow-popups allow-forms';
            iframe.src = waUrl;
            document.body.appendChild(iframe);
            
            // Also try direct window.open
            setTimeout(() => {
                const win = window.open(waUrl, '_blank', 'noopener,noreferrer');
                
                if (win) {
        // console.log('WhatsApp web opened successfully');
                    updateWhatsAppStatus('💻 WhatsApp Web opened successfully!', 'success');
                } else {
        // console.log('WhatsApp web blocked, trying fallback');
                    updateWhatsAppStatus('⚠️ WhatsApp Web blocked, trying fallback...', 'warning');
                    tryFallbackMethods(message, cleanPhone);
                }
                
                // Clean up iframe
                setTimeout(() => {
                    if (document.body.contains(iframe)) {
                        document.body.removeChild(iframe);
                    }
                }, 1000);
                
            }, 100);
            
        } catch (e) {
            // console.log('wa.me failed:', e);
            tryFallbackMethods(message, cleanPhone);
        }
    }
    
    function tryFallbackMethods(message, cleanPhone) {
        // Method 2: Try web.whatsapp.com
        try {
            const webUrl = `https://web.whatsapp.com/send?phone=${cleanPhone}&text=${encodeURIComponent(message)}`;
        // console.log('Trying web.whatsapp.com:', webUrl);
            
            const win = window.open(webUrl, '_blank');
            if (win) {
        // console.log('web.whatsapp.com opened');
                return true;
            }
        } catch (e) {
            // console.log('web.whatsapp.com failed:', e);
        }
        
        // Method 3: Try WhatsApp protocol
        try {
            const protocolUrl = `whatsapp://send?phone=${cleanPhone}&text=${encodeURIComponent(message)}`;
        // console.log('Trying whatsapp:// protocol:', protocolUrl);
            
            window.location.href = protocolUrl;
            setTimeout(() => {
        // console.log('WhatsApp protocol attempted');
            }, 500);
            return true;
            
        } catch (e) {
            // console.log('WhatsApp protocol failed:', e);
        }
        
        // Method 4: Show copy dialog as final fallback
        showCopyDialog(message, cleanPhone);
        return false;
    }
    
    function showCopyDialog(message, cleanPhone) {
        // Copy message to clipboard first
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(message).then(() => {
                showAlert('✅ Pesan telah disalin ke clipboard!\n\n📱 Silakan buka WhatsApp dan kirim ke nomor: ' + cleanPhone + '\n\n📋 Pesan sudah siap untuk di-paste!');
            }).catch(() => {
                showAlert('📱 WhatsApp tidak bisa dibuka otomatis.\n\n📞 Nomor WhatsApp: ' + cleanPhone + '\n\n📋 Silakan copy pesan berikut secara manual:\n\n' + message);
            });
        } else {
            showAlert('📱 WhatsApp tidak bisa dibuka otomatis.\n\n📞 Nomor WhatsApp: ' + cleanPhone + '\n\n📋 Silakan copy pesan berikut secara manual:\n\n' + message);
        }
    }
    
    function showAlert(message) {
        // Create a modal-style alert
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            font-family: Arial, sans-serif;
        `;
        
        const content = document.createElement('div');
        content.style.cssText = `
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 400px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            text-align: left;
            white-space: pre-line;
            font-size: 14px;
            line-height: 1.5;
        `;
        
        const button = document.createElement('button');
        button.textContent = 'Tutup';
        button.style.cssText = `
            background: #25D366;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            width: 100%;
            font-size: 16px;
        `;
        
        button.onclick = () => {
            document.body.removeChild(modal);
        };
        
        content.textContent = message;
        content.appendChild(button);
        modal.appendChild(content);
        document.body.appendChild(modal);
        
        // Auto-close after 10 seconds
        setTimeout(() => {
            if (document.body.contains(modal)) {
                document.body.removeChild(modal);
            }
        }, 10000);
    }

    function confirmDeletePO(e) {
        if (!confirm('Yakin ingin menghapus PO ini? Tindakan ini tidak dapat dibatalkan.')) {
            e.preventDefault();
            return false;
        }
        return true;
    }
    
    
    // Function to resend WhatsApp notification for approved/rejected POs
    function resendWhatsAppNotification() {
        const salesPhone = '{{ $transactions->first()->sales->phone ?? "" }}';
        const poNumber = '{{ $transactions->first()->po_number ?? "" }}';
        const amount = '{{ number_format($totalAmount, 0, ",", ".") }}';
        const salesName = '{{ $transactions->first()->sales->name ?? "Sales" }}';
        const approvalStatus = '{{ $transactions->first()->approval_status }}';
        const approvalNotes = '{{ $transactions->first()->approval_notes ?? "" }}';
        const approverName = '{{ $transactions->first()->approver->name ?? "Owner" }}';
        const supplierName = '{{ $transactions->first()->product->supplier->nama_supplier ?? "N/A" }}';
        const transactionDate = '{{ $transactions->first()->transaction_date->format("d/m/Y") }}';
        const deliveryDate = '{{ $transactions->first()->delivery_date ? $transactions->first()->delivery_date->format("d/m/Y") : "TBD" }}';
        
        // Determine status text and symbol based on approval status
        let statusText, statusSymbol;
        if (approvalStatus === 'approved') {
            statusText = 'DISETUJUI';
            statusSymbol = '[✓]';
        } else if (approvalStatus === 'rejected') {
            statusText = 'DITOLAK';
            statusSymbol = '[✗]';
        } else {
            statusText = 'PENDING';
            statusSymbol = '[⏳]';
        }
        
        // Create message
        let message = '*NOTIFIKASI PERSETUJUAN PO*\n\n';
        message += statusSymbol + ' Status: *' + statusText + '*\n';
        message += 'PO Number: *' + poNumber + '*\n';
        message += 'Tanggal: ' + transactionDate + '\n';
        message += 'Delivery: ' + deliveryDate + '\n';
        message += 'Supplier: *' + supplierName + '*\n';
        message += 'Sales: ' + salesName + '\n';
        message += 'Disetujui oleh: *' + approverName + '*\n\n';
        
        // Add product details
        message += '*DETAIL PRODUK:*\n';
        @foreach($transactions as $transaction)
            @php
                $displayQty = '';
                if (($transaction->quantity_carton ?? 0) > 0) {
                    $displayQty .= $transaction->quantity_carton . ' CTN';
                }
                if (($transaction->quantity_piece ?? 0) > 0) {
                    if ($displayQty) $displayQty .= ' + ';
                    $displayQty .= $transaction->quantity_piece . ' PCS';
                }
                $displayQty .= ' (' . number_format($transaction->total_quantity_piece, 0, ',', '.') . ' pcs total)';
            @endphp
            message += '• {{ $transaction->product->name ?? "N/A" }}\n';
            message += '  Qty: {{ $displayQty }}\n';
            message += '  Harga: Rp {{ number_format($transaction->unit_price, 0, ",", ".") }}\n';
            message += '  Subtotal: Rp {{ number_format($transaction->total_amount, 0, ",", ".") }}\n\n';
        @endforeach
        
        message += 'TOTAL: *Rp ' + amount + '*\n';
        
        if (approvalNotes) {
            message += '\nCatatan: ' + approvalNotes + '\n';
        }
        
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: 'Asia/Jakarta'
        };
        const indonesianTime = now.toLocaleString('id-ID', options);
        message += '\nWaktu: ' + indonesianTime + ' WIB\n';
        message += '\nTerima kasih!';
        
        // Open WhatsApp with the message
        openWhatsAppWithMessage(message, salesPhone);
    }
    
    // Function to open WhatsApp with specific message
    function openWhatsAppWithMessage(message, cleanPhone) {
        // Clean phone number
        cleanPhone = cleanPhone.replace(/\D/g, '');
        if (cleanPhone.startsWith('0')) {
            cleanPhone = '62' + cleanPhone.substring(1);
        } else if (!cleanPhone.startsWith('62')) {
            cleanPhone = '62' + cleanPhone;
        }
        
        // console.log('Resending WhatsApp notification to:', cleanPhone);
        updateWhatsAppStatus('📤 Mengirim ulang notifikasi WhatsApp...', 'info');
        
        // Try to open WhatsApp app first
        tryOpenWhatsAppApp(message, cleanPhone);
    }
    
    // Update status display functions
    function updateWhatsAppStatus(message, type = 'info') {
        const statusDiv = document.getElementById('whatsapp-status');
        const statusText = document.getElementById('status-text');
        
        if (statusDiv && statusText) {
            statusDiv.classList.remove('hidden');
            statusText.textContent = message;
            
            switch (type) {
                case 'success':
                    statusText.className = 'text-green-600';
                    break;
                case 'warning':
                    statusText.className = 'text-orange-600';
                    break;
                case 'error':
                    statusText.className = 'text-red-600';
                    break;
                default:
                    statusText.className = 'text-blue-600';
            }
            
            // Auto-hide after 3 seconds
            setTimeout(() => {
                statusDiv.classList.add('hidden');
            }, 3000);
        }
    }
</script>
@endsection