<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SalesTransaction;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sales = Sales::all();
        $products = Product::all();
        $suppliers = Supplier::all();
        
        if ($sales->count() == 0) {
            $this->command->info('No sales found. Please run SalesSeeder first.');
            return;
        }
        
        if ($products->count() == 0) {
            $this->command->info('No products found. Please run ProductSeeder first.');
            return;
        }
        
        if ($suppliers->count() == 0) {
            $this->command->info('No suppliers found. Please run SupplierSeeder first.');
            return;
        }

        $approvalStatuses = ['pending', 'approved', 'rejected'];
        $notes = [
            'Order untuk restock barang',
            'Order khusus untuk event',
            'Order untuk pelanggan VIP',
            'Order rutin bulanan',
            'Order untuk promo',
            'Order untuk gudang baru',
            'Order untuk ekspansi',
            'Order untuk musim liburan',
            'Order untuk acara khusus',
            'Order untuk pelanggan setia',
        ];

        $generalNotes = [
            'Mohon segera diproses',
            'Prioritas tinggi',
            'Dengan catatan khusus',
            'Untuk kebutuhan mendesak',
            'Dengan kualitas terbaik',
            'Untuk pelanggan premium',
            'Dengan pengiriman cepat',
            'Untuk stok gudang',
            'Dengan diskon khusus',
            'Untuk event besar',
        ];

        $orderAccBy = [
            'Manager Gudang',
            'Supervisor Sales',
            'Manager Operasional',
            'Direktur',
            'Kepala Divisi',
            'Manager Marketing',
            'Supervisor Logistik',
            'Manager Keuangan',
            'Kepala Cabang',
            'Manager Regional',
        ];

        for ($i = 1; $i <= 50; $i++) {
            // Random sales
            $randomSales = $sales->random();
            
            // Random product
            $randomProduct = $products->random();
            
            // Random supplier (use product's supplier or random)
            $randomSupplier = $randomProduct->supplier_id ? 
                $suppliers->where('id', $randomProduct->supplier_id)->first() : 
                $suppliers->random();
            
            // Random dates
            $transactionDate = Carbon::now()->subDays(rand(0, 30));
            $deliveryDate = $transactionDate->copy()->addDays(rand(1, 14));
            
            // Random quantities
            $quantityType = rand(0, 1) ? 'carton' : 'piece';
            $quantityCarton = $quantityType === 'carton' ? rand(1, 10) : 0;
            $quantityPiece = $quantityType === 'piece' ? rand(1, 50) : 0;
            
            // Calculate total quantity piece
            $totalQuantityPiece = ($quantityCarton * $randomProduct->quantity_per_carton) + $quantityPiece;
            
            // Random unit price (variation of product price)
            $basePrice = $randomProduct->price;
            $priceVariation = rand(90, 110) / 100; // 90% to 110% of base price
            $unitPrice = $basePrice * $priceVariation;
            
            // Calculate total amount
            $totalAmount = $totalQuantityPiece * $unitPrice;
            
            // Random approval status
            $approvalStatus = $approvalStatuses[array_rand($approvalStatuses)];
            
            // Generate PO number
            $poNumber = 'PO-' . $transactionDate->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            
            // Generate transaction number
            $transactionNumber = 'ST-' . $transactionDate->format('Ymd') . '-' . strtoupper(substr(md5($i . time()), 0, 6));

            $salesTransaction = SalesTransaction::create([
                'transaction_number' => $transactionNumber,
                'transaction_date' => $transactionDate,
                'delivery_date' => $deliveryDate,
                'product_id' => $randomProduct->id,
                'sales_id' => $randomSales->id,
                'supplier_id' => $randomSupplier->id,
                'quantity_carton' => $quantityCarton,
                'quantity_piece' => $quantityPiece,
                'total_quantity_piece' => $totalQuantityPiece,
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'approval_status' => $approvalStatus,
                'notes' => $notes[array_rand($notes)],
                'general_notes' => $generalNotes[array_rand($generalNotes)],
                'order_acc_by' => $orderAccBy[array_rand($orderAccBy)],
                'po_number' => $poNumber,
                'approved_by' => $approvalStatus !== 'pending' ? 1 : null, // Admin user ID
                'approved_at' => $approvalStatus !== 'pending' ? $transactionDate->copy()->addHours(rand(1, 24)) : null,
                'approval_notes' => $approvalStatus !== 'pending' ? 'Auto approved by system' : null,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate->copy()->addHours(rand(1, 48)),
            ]);

            // Add some variation to created_at and updated_at
            $salesTransaction->created_at = $transactionDate;
            $salesTransaction->updated_at = $transactionDate->copy()->addHours(rand(1, 48));
            $salesTransaction->save();
        }

        $this->command->info('50 Purchase Orders created successfully!');
        $this->command->info('Each with different sales representatives and products.');
    }
}
