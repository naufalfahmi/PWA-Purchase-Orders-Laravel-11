@forelse($poList as $po)
    <div class="po-item cursor-pointer hover:shadow-lg transition-all duration-200" data-po="{{ $po->po_number }}" onclick="window.location.href='{{ route('sales-transaction.show', $po->transaction_number) }}'">
        <!-- Document-style card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow {{ $po->received_at ? 'border border-purple-200' : ($po->approval_status === 'approved' ? 'border border-green-200' : ($po->approval_status === 'rejected' ? 'border border-red-200' : 'border border-gray-200')) }}">
            <!-- Document header with fold effect -->
            <div class="{{ $po->received_at ? 'bg-gradient-to-r from-purple-50 to-violet-50' : ($po->approval_status === 'approved' ? 'bg-gradient-to-r from-green-50 to-emerald-50' : ($po->approval_status === 'rejected' ? 'bg-gradient-to-r from-red-50 to-rose-50' : 'bg-gradient-to-r from-blue-50 to-indigo-50')) }} border-b {{ $po->received_at ? 'border-purple-200' : ($po->approval_status === 'approved' ? 'border-green-200' : ($po->approval_status === 'rejected' ? 'border-red-200' : 'border-gray-200')) }} rounded-t-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <!-- Document icon -->
                        <div class="w-10 h-10 {{ $po->received_at ? 'bg-purple-100' : ($po->approval_status === 'approved' ? 'bg-green-100' : ($po->approval_status === 'rejected' ? 'bg-red-100' : 'bg-blue-100')) }} rounded-lg flex items-center justify-center">
                            @if($po->received_at)
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            @elseif($po->approval_status === 'approved')
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($po->approval_status === 'rejected')
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $po->po_number }}</h3>
                            <p class="text-sm text-gray-600">Purchase Order</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $po->status_badge['class'] }}">
                            {{ $po->status_badge['label'] }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Document content -->
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Left column -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Sales:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $po->sales->name ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Tanggal:</span>
                            <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($po->created_at)->format('d M Y') }}</span>
                        </div>
                        
                        @if($po->delivery_date)
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Delivery:</span>
                            <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($po->delivery_date)->format('d M Y') }}</span>
                        </div>
                        @endif
                        
                        @if($po->order_acc_by)
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Order Acc By:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $po->order_acc_by }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Right column -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Items:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $po->total_items }} produk</span>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-9 0a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2M9 4h6"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Quantity:</span>
                            <span class="text-sm font-medium text-gray-900">{{ number_format($po->total_quantity, 0, ',', '.') }} {{ $po->total_quantity > 0 ? 'units' : 'pcs' }}</span>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Total Amount:</span>
                            <span class="text-sm font-bold text-blue-600">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                @if($po->general_notes)
                <div class="mt-4 pt-3 border-t border-gray-100">
                    <div class="flex items-start space-x-2">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        <div>
                            <span class="text-sm text-gray-600">Notes:</span>
                            <p class="text-sm text-gray-700 mt-1">{{ Str::limit($po->general_notes, 100) }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Document footer -->
            @if($po->received_at)
                <div class="bg-purple-50 border-t border-purple-100 rounded-b-lg px-4 py-2">
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center text-purple-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span class="font-medium">Purchase Order sudah diterima</span>
                        </div>
                        <span class="text-purple-500">{{ \Carbon\Carbon::parse($po->received_at)->locale('id')->diffForHumans() }}</span>
                    </div>
                </div>
            @elseif($po->approval_status === 'approved')
                <div class="bg-green-50 border-t border-green-100 rounded-b-lg px-3 py-3 sm:px-4 sm:py-2">
                    <!-- Mobile Layout: Stack vertically -->
                    <div class="block sm:hidden">
                        <div class="flex items-center text-green-600 mb-2">
                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-medium text-xs">Purchase Order sudah disetujui</span>
                        </div>
                        @if(Auth::user()->isSales())
                            <button onclick="event.stopPropagation(); receivePO('{{ $po->po_number }}')" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-medium rounded-lg transition-all duration-200 touch-manipulation">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <span>Mark as Received</span>
                                </div>
                            </button>
                        @endif
                        <div class="text-center mt-2">
                            <span class="text-green-500 text-xs">{{ \Carbon\Carbon::parse($po->created_at)->locale('id')->diffForHumans() }}</span>
                        </div>
                    </div>
                    
                    <!-- Desktop Layout: Horizontal -->
                    <div class="hidden sm:flex items-center justify-between text-xs">
                        <div class="flex items-center text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-medium">Purchase Order sudah disetujui</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if(Auth::user()->isSales())
                                <button onclick="event.stopPropagation(); receivePO('{{ $po->po_number }}')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-full transition-colors touch-manipulation">
                                    Mark as Received
                                </button>
                            @endif
                            <span class="text-green-500">{{ \Carbon\Carbon::parse($po->created_at)->locale('id')->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @elseif($po->approval_status === 'rejected')
                <div class="bg-red-50 border-t border-red-100 rounded-b-lg px-4 py-2">
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center text-red-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="font-medium">Purchase Order ditolak</span>
                        </div>
                        <span class="text-red-500">{{ \Carbon\Carbon::parse($po->created_at)->locale('id')->diffForHumans() }}</span>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 border-t border-gray-100 rounded-b-lg px-4 py-2">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>
                            @if(Auth::user()->isOwner())
                                Klik untuk melihat detail dan melakukan persetujuan
                            @else
                                Klik untuk melihat detail
                            @endif
                        </span>
                        <span>{{ \Carbon\Carbon::parse($po->created_at)->locale('id')->diffForHumans() }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
@empty
    <div class="text-center py-8">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-gray-500 text-lg">Belum ada Purchase Order</p>
        <p class="text-gray-400 text-sm">Mulai dengan membuat sales transaction baru</p>
    </div>
@endforelse
