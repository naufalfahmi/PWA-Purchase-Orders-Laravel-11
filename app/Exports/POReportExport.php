<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Helpers\SettingsHelper;
use Carbon\Carbon;

class POReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Transaksi',
            'Nomor PO',
            'Supplier',
            'Nama Barang',
            'Kategori',
            'Sub Kategori',
            'Jumlah Karton',
            'Jumlah Piece',
            'Total Piece',
            'Harga Satuan',
            'Total Harga',
            'Status Approval',
            'Diapprove Oleh',
            'Tanggal Approval',
            'Catatan Approval',
            'Sales',
            'Catatan Umum'
        ];
    }

    public function map($transaction): array
    {
        static $counter = 0;
        $counter++;

        return [
            $counter,
            $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-',
            $transaction->po_number ?? '-',
            $transaction->supplier->nama_supplier ?? '-',
            $transaction->product->name ?? '-',
            $transaction->product->category ?? '-',
            $transaction->product->sub_category ?? '-',
            $transaction->quantity_carton ?? 0,
            $transaction->quantity_piece ?? 0,
            $transaction->total_quantity_piece ?? 0,
            number_format($transaction->unit_price ?? 0, 0, ',', '.'),
            number_format($transaction->total_amount ?? 0, 0, ',', '.'),
            $this->getStatusLabel($transaction->approval_status),
            $transaction->approver->name ?? '-',
            $transaction->approved_at ? $transaction->approved_at->format('d/m/Y H:i') : '-',
            $transaction->approval_notes ?? '-',
            $transaction->sales->name ?? '-',
            $transaction->general_notes ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styles
        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563eb']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Data rows styles
        $lastRow = $this->transactions->count() + 1;
        $sheet->getStyle('A2:R' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Alternating row colors
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':R' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'f8fafc']
                    ]
                ]);
            }
        }

        // Number formatting for currency columns
        $sheet->getStyle('K2:L' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // Tanggal Transaksi
            'C' => 20,  // Nomor PO
            'D' => 25,  // Supplier
            'E' => 30,  // Nama Barang
            'F' => 15,  // Kategori
            'G' => 15,  // Sub Kategori
            'H' => 12,  // Jumlah Karton
            'I' => 12,  // Jumlah Piece
            'J' => 12,  // Total Piece
            'K' => 15,  // Harga Satuan
            'L' => 15,  // Total Harga
            'M' => 15,  // Status Approval
            'N' => 20,  // Diapprove Oleh
            'O' => 18,  // Tanggal Approval
            'P' => 25,  // Catatan Approval
            'Q' => 20,  // Sales
            'R' => 30,  // Catatan Umum
        ];
    }

    public function title(): string
    {
        return 'Laporan PO ' . Carbon::now()->format('Y-m-d');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastDataColumn = 'R';

                // Insert header template at the top (4-5 rows depending on delivery date)
                $headerRows = 4;
                if (request('delivery_start_date') || request('delivery_end_date')) {
                    $headerRows = 5;
                }
                $sheet->insertNewRowBefore(1, $headerRows);

                // Determine period from data if available
                $dates = $this->transactions->pluck('transaction_date')->filter();
                $minDate = $dates->min();
                $maxDate = $dates->max();
                $periodText = 'Periode Transaksi: ';
                if ($minDate && $maxDate) {
                    $periodText .= Carbon::parse($minDate)->format('d M Y') . ' - ' . Carbon::parse($maxDate)->format('d M Y');
                } else {
                    $periodText .= 'Semua Data';
                }

                // Check for delivery date filters
                $deliveryPeriodText = '';
                if (request('delivery_start_date') || request('delivery_end_date')) {
                    $deliveryPeriodText = 'Periode Pengiriman: ';
                    if (request('delivery_start_date') && request('delivery_end_date')) {
                        $deliveryPeriodText .= Carbon::parse(request('delivery_start_date'))->format('d M Y') . ' - ' . Carbon::parse(request('delivery_end_date'))->format('d M Y');
                    } elseif (request('delivery_start_date')) {
                        $deliveryPeriodText .= 'Dari ' . Carbon::parse(request('delivery_start_date'))->format('d M Y');
                    } elseif (request('delivery_end_date')) {
                        $deliveryPeriodText .= 'Sampai ' . Carbon::parse(request('delivery_end_date'))->format('d M Y');
                    }
                }

                // Company header
                $sheet->setCellValue('A1', SettingsHelper::companyName());
                $sheet->setCellValue('A2', SettingsHelper::companyAddress());
                $sheet->setCellValue('A3', SettingsHelper::exportHeaderTitle());
                $sheet->setCellValue('A4', $periodText);
                if ($deliveryPeriodText) {
                    $sheet->setCellValue('A5', $deliveryPeriodText);
                }

                // Style company header
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A4')->getFont()->setSize(12);
                if ($deliveryPeriodText) {
                    $sheet->getStyle('A5')->getFont()->setSize(12);
                }

                // Merge cells for company info
                $sheet->mergeCells('A1:' . $lastDataColumn . '1');
                $sheet->mergeCells('A2:' . $lastDataColumn . '2');
                $sheet->mergeCells('A3:' . $lastDataColumn . '3');
                $sheet->mergeCells('A4:' . $lastDataColumn . '4');
                if ($deliveryPeriodText) {
                    $sheet->mergeCells('A5:' . $lastDataColumn . '5');
                }

                // Center align company info
                $styleRange = 'A1:A4';
                if ($deliveryPeriodText) {
                    $styleRange = 'A1:A5';
                }
                $sheet->getStyle($styleRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Get the last row (after header insertion)
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                
                // Add borders to data cells only (starting after header)
                $dataStartRow = $headerRows + 1;
                $sheet->getStyle('A' . $dataStartRow . ':' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                // Add total row
                $totalRow = $lastRow + 2;
                $sheet->setCellValue('A' . $totalRow, 'TOTAL KESELURUHAN');
                $sheet->setCellValue('L' . $totalRow, $this->transactions->sum('total_amount'));
                
                // Style the total row
                $sheet->getStyle('A' . $totalRow . ':' . $lastDataColumn . $totalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9E2F3']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                // Add summary info
                $summaryRow = $totalRow + 2;
                $sheet->setCellValue('A' . $summaryRow, 'Total Transaksi: ' . $this->transactions->count());
                $sheet->setCellValue('A' . ($summaryRow + 1), 'Total Quantity (PC): ' . number_format($this->transactions->sum('total_quantity_piece'), 0, ',', '.'));
                $sheet->setCellValue('A' . ($summaryRow + 2), 'Dicetak pada: ' . now()->format('d F Y H:i:s'));

                // Auto-fit columns
                foreach (range('A', $lastColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            }
        ];
    }

    private function getStatusLabel($status)
    {
        $statusLabels = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected'
        ];

        return $statusLabels[$status] ?? ucfirst($status);
    }
}
