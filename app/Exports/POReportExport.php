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
    protected $groupedByPO;

    public function __construct($transactions, $groupedByPO = false)
    {
        $this->transactions = $transactions;
        $this->groupedByPO = (bool) $groupedByPO;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        if ($this->groupedByPO) {
            return [
                'No',
                'Tanggal Transaksi',
                'Nomor PO',
                'Supplier',
                'Sales',
                'Total Items',
                'Total Quantity',
                'Total Amount',
                'Status Approval',
                'Diapprove Oleh',
                'Tanggal Approval',
                'Catatan Approval',
                'Catatan Umum',
                'Tanggal Pengiriman'
            ];
        }
        return [
            'No',
            'Tanggal Transaksi',
            'Nomor PO',
            'Supplier',
            'Nama Barang',
            'Kategori',
            'Jumlah Karton',
            'Jumlah Piece',
            'Harga Satuan',
            'Total Harga',
            'Status Approval',
            'Diapprove Oleh',
            'Tanggal Approval',
            'Catatan Approval',
            'Sales',
            'Catatan Umum',
            'Tanggal Pengiriman'
        ];
    }

    public function map($transaction): array
    {
        // Numbering once per PO (merged rows in sheet)
        static $poCounter = 0;
        static $lastPo = null;
        $isNewPo = $lastPo !== $transaction->po_number;
        if ($isNewPo) {
            $poCounter++;
            $lastPo = $transaction->po_number;
        }

        if ($this->groupedByPO) {
            return [
                $poCounter,
                $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-',
                $transaction->po_number ?? '-',
                optional($transaction->supplier)->nama_supplier ?? '-',
                optional($transaction->sales)->name ?? '-',
                (int) ($transaction->total_items ?? 0),
                (int) ($transaction->total_quantity ?? 0),
                (float) ($transaction->total_amount ?? 0),
                $this->getStatusLabel($transaction->approval_status),
                optional($transaction->approver)->name ?? '-',
                $transaction->approved_at ? \Carbon\Carbon::parse($transaction->approved_at)->format('d/m/Y H:i') : '-',
                $transaction->approval_notes ?? '-',
                $transaction->general_notes ?? '-',
                $transaction->delivery_date ? \Carbon\Carbon::parse($transaction->delivery_date)->format('d/m/Y') : '-',
            ];
        }

        return [
            $isNewPo ? $poCounter : '',
            $isNewPo ? ($transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-') : '',
            $isNewPo ? ($transaction->po_number ?? '-') : '',
            $isNewPo ? ($transaction->supplier->nama_supplier ?? '-') : '',
            $transaction->product->name ?? '-',
            $transaction->product->category ?? '-',
            $transaction->quantity_carton ?? 0,
            $transaction->quantity_piece ?? 0,
            (float) ($transaction->unit_price ?? 0),
            (float) ((((($transaction->quantity_carton ?? 0) > 0) ? ($transaction->quantity_carton ?? 0) : ($transaction->quantity_piece ?? 0)) * ($transaction->unit_price ?? 0))),
            $this->getStatusLabel($transaction->approval_status),
            $transaction->approver->name ?? '-',
            $transaction->approved_at ? $transaction->approved_at->format('d/m/Y H:i') : '-',
            $transaction->approval_notes ?? '-',
            $transaction->sales->name ?? '-',
            $transaction->general_notes ?? '-',
            $isNewPo ? ($transaction->delivery_date ? $transaction->delivery_date->format('d/m/Y') : '-') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styles
        $lastHeaderColumn = $this->groupedByPO ? 'N' : 'Q';
        $sheet->getStyle('A1:' . $lastHeaderColumn . '1')->applyFromArray([
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
        $sheet->getStyle('A2:' . $lastHeaderColumn . $lastRow)->applyFromArray([
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
                $sheet->getStyle('A' . $row . ':' . $lastHeaderColumn . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'f8fafc']
                    ]
                ]);
            }
        }

        // Number formatting for currency columns
        if ($this->groupedByPO) {
            // Total Amount at column H
            $sheet->getStyle('H2:H' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        } else {
            // Unit Price (I) and Total Harga (J)
            $sheet->getStyle('I2:J' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        }
    }

    public function columnWidths(): array
    {
        if ($this->groupedByPO) {
            return [
                'A' => 5,   // No
                'B' => 15,  // Tanggal Transaksi
                'C' => 20,  // Nomor PO
                'D' => 25,  // Supplier
                'E' => 20,  // Sales
                'F' => 12,  // Total Items
                'G' => 15,  // Total Quantity
                'H' => 15,  // Total Amount
                'I' => 18,  // Status Approval
                'J' => 20,  // Diapprove Oleh
                'K' => 18,  // Tanggal Approval
                'L' => 25,  // Catatan Approval
                'M' => 30,  // Catatan Umum
                'N' => 18,  // Tanggal Pengiriman
            ];
        }
        return [
            'A' => 5,   // No
            'B' => 15,  // Tanggal Transaksi
            'C' => 20,  // Nomor PO
            'D' => 25,  // Supplier
            'E' => 30,  // Nama Barang
            'F' => 15,  // Kategori
            'G' => 12,  // Jumlah Karton
            'H' => 12,  // Jumlah Piece
            'I' => 15,  // Harga Satuan
            'J' => 15,  // Total Harga
            'K' => 15,  // Status Approval
            'L' => 20,  // Diapprove Oleh
            'M' => 18,  // Tanggal Approval
            'N' => 25,  // Catatan Approval
            'O' => 20,  // Sales
            'P' => 30,  // Catatan Umum
            'Q' => 18,  // Tanggal Pengiriman
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
                $lastDataColumn = $this->groupedByPO ? 'M' : 'Q';

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
                // Total amount column differs between grouped and detailed
                if ($this->groupedByPO) {
                    $sheet->setCellValue('H' . $totalRow, $this->transactions->sum('total_amount'));
                } else {
                    $sheet->setCellValue('J' . $totalRow, $this->transactions->sum('total_amount'));
                }
                
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
                $totalTransactions = $this->groupedByPO ? $this->transactions->count() : $this->transactions->count();
                $sheet->setCellValue('A' . $summaryRow, 'Total Transaksi (PO): ' . $totalTransactions);
                $sheet->setCellValue('A' . ($summaryRow + 1), 'Dicetak pada: ' . now()->format('d F Y H:i:s'));
                $summaryRow += 2;

                // Merge grouped columns per PO: A (No), B (Tanggal), C (PO), D (Supplier), Q (Pengiriman)
                $currentPo = null;
                $groupStart = $dataStartRow;
                for ($r = $dataStartRow; $r <= $lastRow; $r++) {
                    $poValue = $sheet->getCell('C' . $r)->getValue();
                    if ($currentPo === null) {
                        $currentPo = $poValue;
                        $groupStart = $r;
                    }
                    if ($poValue !== $currentPo || $r === $lastRow) {
                        $groupEnd = ($poValue !== $currentPo) ? ($r - 1) : $r;
                        if ($groupEnd > $groupStart) {
                            foreach (['A','B','C','D','Q'] as $col) {
                                $sheet->mergeCells($col . $groupStart . ':' . $col . $groupEnd);
                                $sheet->getStyle($col . $groupStart . ':' . $col . $groupEnd)->getAlignment()
                                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                                    ->setVertical(Alignment::VERTICAL_CENTER);
                            }
                        }
                        $currentPo = $poValue;
                        $groupStart = $r;
                    }
                }
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
