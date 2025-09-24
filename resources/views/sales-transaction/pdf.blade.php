<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 40px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .header-table td {
            padding: 10px;
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
                    <p><strong>Date</strong>    : {{ $transactions->first()->delivery_date ? $transactions->first()->delivery_date->format('d F Y') : 'N/A' }}</p>
                    <p><strong>Purchase order No</strong> : {{ $transactions->first()->po_number }}</p>
                    <p><strong>Order ACC By</strong>: {{ $transactions->first()->order_acc_by ?? 'N/A' }}</p>
                    <p><strong>{{ \DB::table('order_acc_options')->where('is_active', true)->pluck('name')->implode(' / ') ?: 'N/A' }}</strong></p>
                </div>
            </td>
        </tr>
    </table>

    <hr>

    <table class="info-section-table">
        <tr>
            <td class="info-left">
                <h2>VENDOR INFORMATION</h2>
                <table class="info-table">
                    <tr>
                        <td>NAMA VENDOR</td>
                        <td>:</td>
                        <td>{{ $transactions->first()->product->supplier->nama_supplier ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>ALAMAT</td>
                        <td>:</td>
                        <td>{{ $transactions->first()->product->supplier->alamat_supplier ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
            <td class="info-right">
                <h2>SALES INFORMATION</h2>
                <table class="info-table">
                    <tr>
                        <td>SALES PERSON</td>
                        <td>:</td>
                        <td>{{ $transactions->first()->sales->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>CONTACT NO</td>
                        <td>:</td>
                        <td>{{ $transactions->first()->sales->phone ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="item-table">
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
            @foreach($transactions as $transaction)
            <tr>
                <td class="text-left">{{ $transaction->product->name ?? 'N/A' }}</td>
                <td>{{ $transaction->quantity_carton ?? '-' }}</td>
                <td>{{ $transaction->quantity_piece ?? '-' }}</td>
                <td>{{ $transaction->total_quantity_piece ?? '-' }}</td>
                <td>{{ number_format($transaction->unit_price ?? 0, 0, ',', '.') }}</td>
                <td>{{ number_format($transaction->total_amount ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="border-top: 2px solid #000;">
                <td colspan="5" class="text-left" style="font-weight: bold;">TOTAL</td>
                <td style="font-weight: bold;">{{ number_format($transactions->sum('total_amount'), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>