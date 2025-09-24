<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Purchase Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 40px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        .header-table td {
            padding: 10px;
        }
        .page-header {
            page-break-inside: avoid;
            margin-bottom: 20px;
        }
        .page-content {
            margin-top: 0;
        }
        @page {
            margin-top: 0;
        }
        .table-header-repeat {
            page-break-inside: avoid;
        }
        .company-info {
            font-size: 12px;
            line-height: 1.4;
        }
        .company-info h1 {
            margin-bottom: 8px;
            font-size: 16px;
            font-weight: bold;
        }
        .company-info p {
            margin: 3px 0;
        }
        .po-details {
            text-align: right;
            font-size: 12px;
            line-height: 1.4;
        }
        .po-details p {
            margin: 4px 0;
        }
        hr {
            border: 0;
            border-top: 1px solid #000;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-section-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        .info-section-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        .info-left, .info-right {
            width: 50%;
        }
        .info-left h2, .info-right h2 {
            font-size: 13px;
            margin-bottom: 5px;
            text-decoration: underline;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 5px;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 30%;
            font-weight: bold;
        }
        .info-table td:nth-child(2) {
            width: 5%;
            text-align: center;
        }
        .info-table td:last-child {
            width: 65%;
        }
        .purchase-order-info {
            text-align: right;
            font-size: 12px;
            line-height: 1.4;
        }
        .purchase-order-info p {
            margin: 4px 0;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
            table-layout: fixed;
        }
        .item-table th, .item-table td {
            border: 1px solid #000;
            padding: 10px 8px;
            text-align: center;
            vertical-align: middle;
        }
        .item-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
        }
        .item-table .text-left {
            text-align: left;
        }
        .item-table td:first-child {
            width: 35%;
        }
        .item-table td:nth-child(2),
        .item-table td:nth-child(3),
        .item-table td:nth-child(4) {
            width: 12%;
        }
        .item-table td:nth-child(5),
        .item-table td:nth-child(6) {
            width: 15%;
        }
        .footer-section {
            display: flex;
            justify-content: space-between;
        }
        .special-instruction {
            width: 45%;
        }
        .special-instruction h3 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .instruction-box {
            border: 1px solid #000;
            padding: 10px;
            height: 50px;
        }
        .contact-info {
            width: 45%;
        }
        .contact-info p {
            margin: 4px 0;
            font-size: 12px;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    @if($transactions->count() > 0)
        @php $isFirstPage = true; @endphp
        
        @foreach($transactions->groupBy('po_number') as $poNumber => $poGroup)
            @if($isFirstPage)
                <!-- Header untuk halaman pertama -->
                <div class="page-header">
                    <table class="header-table">
                        <tr>
                            <td>
                                <div class="company-info">
                                    <h1>PT Sultan Zahra Monajaya Sejahtera</h1>
                                    <p>Jl. Raya Susukan No. 10, RT.1/RW.3, Susukan</p>
                                    <p>Kecamatan Bojonggede, Kabupaten Bogor, Jawa Barat 16920</p>
                                </div>
                            </td>
                            <td>
                                <div class="po-details">
                                    <p><strong>Date</strong>    : {{ now()->format('d F Y') }}</p>
                                    <p><strong>Report Type</strong> : Laporan Purchase Order</p>
                                    <p><strong>Periode</strong>: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : 'Semua' }} - {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : 'Semua' }}</p>
                                    <p><strong>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</strong></p>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <hr>
                </div>
                
                <!-- Summary hanya di halaman pertama -->
                <table class="info-section-table">
                    <tr>
                        <td class="info-left">
                            <h2>SUMMARY INFORMATION</h2>
                            <table class="info-table">
                                <tr>
                                    <td>TOTAL TRANSACTION</td>
                                    <td>:</td>
                                    <td>{{ $transactions->groupBy('po_number')->count() }}</td>
                                </tr>
                                <tr>
                                    <td>TOTAL AMOUNT</td>
                                    <td>:</td>
                                    <td>Rp {{ number_format($transactions->sum('total_amount'), 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </td>
                        <td class="info-right">
                            <h2>REPORT INFORMATION</h2>
                            <table class="info-table">
                                <tr>
                                    <td>TOTAL QUANTITY</td>
                                    <td>:</td>
                                    <td>{{ number_format($transactions->sum('total_quantity_piece')) }}</td>
                                </tr>
                                <tr>
                                    <td>SUPPLIER COUNT</td>
                                    <td>:</td>
                                    <td>{{ $transactions->groupBy(function($t){ return optional($t->supplier)->nama_supplier ?: 'N/A'; })->count() }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                @php $isFirstPage = false; @endphp
            @else
                <!-- Header untuk halaman berikutnya -->
                <div class="page-header" style="page-break-before: always;">
                    <table class="header-table">
                        <tr>
                            <td>
                                <div class="company-info">
                                    <h1>PT Sultan Zahra Monajaya Sejahtera</h1>
                                    <p>Jl. Raya Susukan No. 10, RT.1/RW.3, Susukan</p>
                                    <p>Kecamatan Bojonggede, Kabupaten Bogor, Jawa Barat 16920</p>
                                </div>
                            </td>
                            <td>
                                <div class="po-details">
                                    <p><strong>Date</strong>    : {{ now()->format('d F Y') }}</p>
                                    <p><strong>Report Type</strong> : Laporan Purchase Order</p>
                                    <p><strong>Periode</strong>: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : 'Semua' }} - {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : 'Semua' }}</p>
                                    <p><strong>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</strong></p>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <hr>
                </div>
            @endif
            
            <!-- Table per PO -->
            <table class="item-table" style="margin-bottom: 30px;">
                <thead>
                    <tr>
                        <th class="text-left">Item Description</th>
                        <th>Order Qty (CTN)</th>
                        <th>Order Qty (PC)</th>
                        <th>Total SKU</th>
                        <th>Unit Price</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($poGroup as $transaction)
                        <tr>
                            <td class="text-left">
                                <strong>{{ $transaction->product->name ?? 'N/A' }}</strong><br>
                                <small style="color: #666;">
                                    <strong>Supplier:</strong> {{ optional($transaction->supplier)->nama_supplier ?? 'N/A' }}<br>
                                    <strong>Sales:</strong> {{ optional($transaction->sales)->name ?? 'N/A' }}<br>
                                    <strong>Toko:</strong> {{ $transaction->order_acc_by ?? 'N/A' }}<br>
                                    <strong>Status:</strong> {{ ucfirst($transaction->approval_status ?? 'pending') }}
                                </small>
                            </td>
                            <td>{{ $transaction->quantity_carton ?? '-' }}</td>
                            <td>{{ $transaction->quantity_piece ?? '-' }}</td>
                            <td>{{ $transaction->total_quantity_piece ?? '-' }}</td>
                            <td>{{ number_format($transaction->unit_price ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($transaction->total_amount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <!-- Total per PO -->
                    <tr style="border-top: 2px solid #000; background-color: #f0f0f0;">
                        <td colspan="5" class="text-left" style="font-weight: bold; padding: 8px;">
                            TOTAL PO {{ $poNumber }}
                        </td>
                        <td style="font-weight: bold; font-size: 14px;">{{ number_format($poGroup->sum('total_amount'), 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @endforeach
        
        <!-- Grand Total -->
        <table class="item-table" style="margin-top: 20px;">
            <tbody>
                <tr style="border-top: 3px solid #000; background-color: #e0e0e0;">
                    <td colspan="5" class="text-left" style="font-weight: bold; font-size: 16px;">GRAND TOTAL SEMUA PO</td>
                    <td style="font-weight: bold; font-size: 16px;">{{ number_format($transactions->sum('total_amount'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #64748b; font-style: italic;">
            <p>Tidak ada data PO yang ditemukan untuk periode yang dipilih.</p>
        </div>
    @endif

</body>
</html>
