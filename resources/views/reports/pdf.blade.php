<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan PO - {{ now()->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .company-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .company-logo h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        
        .company-details {
            margin-top: 10px;
        }
        
        .company-details p {
            margin: 5px 0;
            font-size: 11px;
            color: #666;
        }
        
        .report-title {
            text-align: center;
            margin: 20px 0;
        }
        
        .report-title h2 {
            margin: 0;
            font-size: 18px;
            color: #2c3e50;
        }
        
        .report-title p {
            margin: 5px 0;
            font-size: 11px;
            color: #666;
        }
        
        .summary {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .summary h3 {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 10px 0;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            display: block;
        }
        
        .summary-label {
            font-size: 10px;
            color: #64748b;
            margin-top: 5px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .table th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #1d4ed8;
            font-size: 9px;
        }
        
        .table td {
            padding: 6px;
            border: 1px solid #d1d5db;
            font-size: 9px;
            vertical-align: top;
        }
        
        .table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        .table tr:hover {
            background-color: #f1f5f9;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .status-received {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 4px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            line-height: 1.2;
            display: inline-block;
            text-align: center;
        }
        
        .status-received small {
            font-size: 6px;
            font-weight: normal;
            opacity: 0.9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 8px;
            color: #64748b;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #64748b;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Company Header -->
    <div class="company-header">
        <div class="company-logo">
            <h1>{{ \App\Helpers\SettingsHelper::companyName() }}</h1>
        </div>
        <div class="company-details">
            <p>{{ \App\Helpers\SettingsHelper::companyAddress() }}</p>
            <p>Telp: {{ \App\Helpers\SettingsHelper::companyPhone() }} | Email: {{ \App\Helpers\SettingsHelper::companyEmail() }}</p>
            <p>Website: {{ \App\Helpers\SettingsHelper::companyWebsite() }}</p>
        </div>
    </div>
    
    <!-- Report Title -->
    <div class="report-title">
        <h2>{{ \App\Helpers\SettingsHelper::exportHeaderTitle() }}</h2>
        <p>Periode Transaksi: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : 'Semua' }} - {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : 'Semua' }}</p>
        @if(request('delivery_start_date') || request('delivery_end_date'))
        <p>Periode Pengiriman: {{ request('delivery_start_date') ? \Carbon\Carbon::parse(request('delivery_start_date'))->format('d M Y') : 'Semua' }} - {{ request('delivery_end_date') ? \Carbon\Carbon::parse(request('delivery_end_date'))->format('d M Y') : 'Semua' }}</p>
        @endif
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <!-- Summary -->
    <div class="summary">
        <h3>Ringkasan Data</h3>
        <?php
            $totalTransactions = $transactions->groupBy('po_number')->count();
            // Use total_amount and total_quantity_piece from database
            $totalAmount = $transactions->sum('total_amount');
            $totalQuantity = $transactions->sum('total_quantity_piece');
            $supplierCounts = $transactions->groupBy(function($t){ return optional($t->supplier)->nama_supplier ?: 'N/A'; })->count();
        ?>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-value">{{ number_format($totalTransactions) }}</span>
                <div class="summary-label">Total Transaksi</div>
            </div>
            <div class="summary-item">
                <span class="summary-value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                <div class="summary-label">Total Nilai</div>
            </div>
            <div class="summary-item">
                <span class="summary-value">{{ number_format($totalQuantity) }}</span>
                <div class="summary-label">Total Quantity</div>
            </div>
            <div class="summary-item">
                <span class="summary-value">{{ $supplierCounts }}</span>
                <div class="summary-label">Jumlah Supplier</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    @if($transactions->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 8%;">Tanggal</th>
                    <th style="width: 8%;">Pengiriman</th>
                    <th style="width: 12%;">PO Number</th>
                    <th style="width: 15%;">Supplier</th>
                    <th style="width: 20%;">Produk</th>
                    <th style="width: 8%;">Kategori</th>
                    <th style="width: 6%;">Qty Karton</th>
                    <th style="width: 6%;">Qty Piece</th>
                    
                    <th style="width: 10%;">Harga Satuan</th>
                    <th style="width: 10%;">Total Harga</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 12%;">Approver</th>
                    <th style="width: 12%;">Diterima Oleh</th>
                    <th style="width: 10%;">Tanggal Diterima</th>
                </tr>
            </thead>
            <tbody>
                @php $poIndex = 0; @endphp
                @foreach($transactions->groupBy('po_number') as $poNumber => $poGroup)
                    @php 
                        $poIndex++;
                        $rowspan = $poGroup->count();
                        $first = $poGroup->first();
                    @endphp
                    @foreach($poGroup as $rowIndex => $transaction)
                        <tr>
                            @if($rowIndex === 0)
                                <td class="text-center" rowspan="{{ $rowspan }}">{{ $poIndex }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $first->transaction_date ? $first->transaction_date->format('d/m/Y') : '-' }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $first->delivery_date ? $first->delivery_date->format('d/m/Y') : '-' }}</td>
                                <td rowspan="{{ $rowspan }}" style="font-weight: bold; color: #2563eb;">{{ $poNumber ?? '-' }}</td>
                                <td rowspan="{{ $rowspan }}">{{ optional($first->supplier)->nama_supplier ?? '-' }}</td>
                            @endif
                            <td>{{ $transaction->product->name ?? '-' }}</td>
                            <td>{{ $transaction->product->category ?? '-' }}</td>
                            <td class="text-center">{{ $transaction->quantity_carton ?? 0 }}</td>
                            <td class="text-center">{{ $transaction->quantity_piece ?? 0 }}</td>
                            <td class="text-right">Rp {{ number_format($transaction->unit_price ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($transaction->total_amount ?? 0, 0, ',', '.') }}</td>
                            @if($rowIndex === 0)
                                <td class="text-center" rowspan="{{ $rowspan }}">
                                    @if($first->received_at)
                                        <span class="status-received">
                                            Received<br>
                                            <small>{{ \Carbon\Carbon::parse($first->received_at)->format('d/m/Y H:i:s') }}</small><br>
                                            <small>{{ optional($first->receiver)->name ?? '-' }}</small>
                                        </span>
                                    @elseif($first->approval_status == 'pending')
                                        <span class="status-pending">Pending</span>
                                    @elseif($first->approval_status == 'approved')
                                        <span class="status-approved">Approved</span>
                                    @elseif($first->approval_status == 'rejected')
                                        <span class="status-rejected">Rejected</span>
                                    @else
                                        <span class="status-pending">{{ ucfirst($first->approval_status) }}</span>
                                    @endif
                                </td>
                                <td rowspan="{{ $rowspan }}">{{ optional($first->approver)->name ?? '-' }}</td>
                                <td rowspan="{{ $rowspan }}">{{ optional($first->receiver)->name ?? '-' }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $first->received_at ? \Carbon\Carbon::parse($first->received_at)->format('d/m/Y H:i:s') : '-' }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data PO yang ditemukan untuk periode yang dipilih.</p>
        </div>
    @endif

    <!-- Status Breakdown -->
    @if($summary['status_counts']->count() > 0)
        <div class="page-break"></div>
        <div class="summary">
            <h3>Breakdown Status Approval</h3>
            <div class="summary-grid">
                @foreach($summary['status_counts'] as $status => $count)
                    <div class="summary-item">
                        <span class="summary-value">{{ $count }}</span>
                        <div class="summary-label">{{ ucfirst($status) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Supplier Breakdown -->
    @if($summary['supplier_counts']->count() > 0)
        <div class="summary" style="margin-top: 20px;">
            <h3>Breakdown per Supplier</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th class="text-center">Jumlah Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary['supplier_counts'] as $supplier => $count)
                        <tr>
                            <td>{{ $supplier }}</td>
                            <td class="text-center">{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem {{ \App\Helpers\SettingsHelper::appName() }}</p>
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }} | Halaman: <span class="pagenum"></span></p>
    </div>
</body>
</html>
