<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Supplier;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();
        
        if ($suppliers->count() == 0) {
            $this->command->info('No suppliers found. Please run SupplierSeeder first.');
            return;
        }

        $products = [
            [
                'name' => 'Gula Merah 500g',
                'sku' => 'GM001',
                'category' => 'Food',
                'price' => 15000,
                'quantity_per_carton' => 24,
                'description' => 'Gula merah premium 500g',
                'supplier_id' => $suppliers->where('kode_supplier', '3')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Gula Merah 1kg',
                'sku' => 'GM002',
                'category' => 'Food',
                'price' => 25000,
                'quantity_per_carton' => 12,
                'description' => 'Gula merah premium 1kg',
                'supplier_id' => $suppliers->where('kode_supplier', '3')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Aice Chocolate',
                'sku' => 'AICE001',
                'category' => 'Ice Cream',
                'price' => 3000,
                'quantity_per_carton' => 48,
                'description' => 'Aice Ice Cream Chocolate',
                'supplier_id' => $suppliers->where('kode_supplier', 'AICE52')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Aice Vanilla',
                'sku' => 'AICE002',
                'category' => 'Ice Cream',
                'price' => 3000,
                'quantity_per_carton' => 48,
                'description' => 'Aice Ice Cream Vanilla',
                'supplier_id' => $suppliers->where('kode_supplier', 'AICE52')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Keripik AINIE',
                'sku' => 'AIN001',
                'category' => 'Snack',
                'price' => 5000,
                'quantity_per_carton' => 24,
                'description' => 'Keripik AINIE original',
                'supplier_id' => $suppliers->where('kode_supplier', '220007')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Keripik AINIE Pedas',
                'sku' => 'AIN002',
                'category' => 'Snack',
                'price' => 5500,
                'quantity_per_carton' => 24,
                'description' => 'Keripik AINIE pedas',
                'supplier_id' => $suppliers->where('kode_supplier', '220007')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Agromas Organic Rice',
                'sku' => 'AG001',
                'category' => 'Food',
                'price' => 45000,
                'quantity_per_carton' => 10,
                'description' => 'Beras organik premium 5kg',
                'supplier_id' => $suppliers->where('kode_supplier', 'AG6630')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Agromas Honey',
                'sku' => 'AG002',
                'category' => 'Food',
                'price' => 35000,
                'quantity_per_carton' => 12,
                'description' => 'Madu organik 500ml',
                'supplier_id' => $suppliers->where('kode_supplier', 'AG6630')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Bangkok Snack Mix',
                'sku' => 'BK001',
                'category' => 'Snack',
                'price' => 8000,
                'quantity_per_carton' => 20,
                'description' => 'Snack mix Bangkok original',
                'supplier_id' => $suppliers->where('kode_supplier', '50')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Bangkok Seaweed',
                'sku' => 'BK002',
                'category' => 'Snack',
                'price' => 12000,
                'quantity_per_carton' => 15,
                'description' => 'Seaweed snack Bangkok',
                'supplier_id' => $suppliers->where('kode_supplier', '50')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'HT1 Premium Coffee',
                'sku' => 'HT001',
                'category' => 'Beverage',
                'price' => 28000,
                'quantity_per_carton' => 12,
                'description' => 'Kopi premium HT1 200g',
                'supplier_id' => $suppliers->where('kode_supplier', 'HT1')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'HT1 Tea Mix',
                'sku' => 'HT002',
                'category' => 'Beverage',
                'price' => 15000,
                'quantity_per_carton' => 20,
                'description' => 'Teh mix HT1 25sachet',
                'supplier_id' => $suppliers->where('kode_supplier', 'HT1')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Bogor Eratel Cable',
                'sku' => 'BE001',
                'category' => 'Electronics',
                'price' => 25000,
                'quantity_per_carton' => 10,
                'description' => 'Kabel data USB-C 1m',
                'supplier_id' => $suppliers->where('kode_supplier', '240503')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Bogor Eratel Charger',
                'sku' => 'BE002',
                'category' => 'Electronics',
                'price' => 45000,
                'quantity_per_carton' => 8,
                'description' => 'Charger fast charging 18W',
                'supplier_id' => $suppliers->where('kode_supplier', '240503')->first()->id ?? $suppliers->first()->id,
            ],
            // Products for SP001 - SUPPLIER MINUMAN
            [
                'name' => 'Teh Botol Sosro 500ml',
                'sku' => 'SP001-001',
                'category' => 'Beverage',
                'price' => 6000,
                'quantity_per_carton' => 24,
                'description' => 'Teh botol sosro original 500ml',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP001')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Coca Cola 330ml',
                'sku' => 'SP001-002',
                'category' => 'Beverage',
                'price' => 8000,
                'quantity_per_carton' => 24,
                'description' => 'Coca cola original 330ml',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP001')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Aqua 600ml',
                'sku' => 'SP001-003',
                'category' => 'Water',
                'price' => 3000,
                'quantity_per_carton' => 24,
                'description' => 'Air mineral aqua 600ml',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP001')->first()->id ?? $suppliers->first()->id,
            ],
            // Products for SP002 - TOKO ROTI BAKERY
            [
                'name' => 'Roti Tawar Sari Roti',
                'sku' => 'SP002-001',
                'category' => 'Bakery',
                'price' => 15000,
                'quantity_per_carton' => 12,
                'description' => 'Roti tawar sari roti 500g',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP002')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Donat J.CO',
                'sku' => 'SP002-002',
                'category' => 'Bakery',
                'price' => 8000,
                'quantity_per_carton' => 24,
                'description' => 'Donat j.co original',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP002')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Kue Lapis Legit',
                'sku' => 'SP002-003',
                'category' => 'Bakery',
                'price' => 45000,
                'quantity_per_carton' => 6,
                'description' => 'Kue lapis legit premium 1kg',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP002')->first()->id ?? $suppliers->first()->id,
            ],
            // Products for SP003 - DISTRIBUTOR KOSMETIK
            [
                'name' => 'Wardah Lightening Series',
                'sku' => 'SP003-001',
                'category' => 'Cosmetics',
                'price' => 85000,
                'quantity_per_carton' => 12,
                'description' => 'Wardah lightening facial wash',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP003')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Emina Lip Tint',
                'sku' => 'SP003-002',
                'category' => 'Cosmetics',
                'price' => 35000,
                'quantity_per_carton' => 24,
                'description' => 'Emina lip tint berry',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP003')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Sariayu Temulawak',
                'sku' => 'SP003-003',
                'category' => 'Cosmetics',
                'price' => 55000,
                'quantity_per_carton' => 12,
                'description' => 'Sariayu temulawak facial foam',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP003')->first()->id ?? $suppliers->first()->id,
            ],
            // Products for SP004 - SUPPLIER PERALATAN RUMAH
            [
                'name' => 'Panci Teflon 24cm',
                'sku' => 'SP004-001',
                'category' => 'Kitchen',
                'price' => 125000,
                'quantity_per_carton' => 6,
                'description' => 'Panci teflon anti lengket 24cm',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP004')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Wajan Wok Stainless',
                'sku' => 'SP004-002',
                'category' => 'Kitchen',
                'price' => 95000,
                'quantity_per_carton' => 8,
                'description' => 'Wajan wok stainless steel 32cm',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP004')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Set Gelas Kaca 6pcs',
                'sku' => 'SP004-003',
                'category' => 'Kitchen',
                'price' => 75000,
                'quantity_per_carton' => 12,
                'description' => 'Set gelas kaca 6 pcs premium',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP004')->first()->id ?? $suppliers->first()->id,
            ],
            // Products for SP005 - DISTRIBUTOR OLAHRAGA
            [
                'name' => 'Sepatu Nike Air Max',
                'sku' => 'SP005-001',
                'category' => 'Sports',
                'price' => 850000,
                'quantity_per_carton' => 6,
                'description' => 'Sepatu nike air max original',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP005')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Jersey Adidas',
                'sku' => 'SP005-002',
                'category' => 'Sports',
                'price' => 250000,
                'quantity_per_carton' => 12,
                'description' => 'Jersey adidas football',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP005')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Bola Sepak Mikasa',
                'sku' => 'SP005-003',
                'category' => 'Sports',
                'price' => 180000,
                'quantity_per_carton' => 8,
                'description' => 'Bola sepak mikasa official',
                'supplier_id' => $suppliers->where('kode_supplier', 'SP005')->first()->id ?? $suppliers->first()->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
