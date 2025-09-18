<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Supplier;

class EnhancedProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First ensure we have suppliers
        $suppliers = Supplier::all();
        
        if ($suppliers->count() == 0) {
            $this->command->info('No suppliers found. Please run SupplierSeeder first.');
            return;
        }

        $enhancedProducts = [
            // Food & Beverage Products - Mixed PCS and CTN
            [
                'name' => 'Indomie Goreng Rendang',
                'sku' => 'IND001',
                'category' => 'Food',
                'sub_category' => 'Instant Noodles',
                'price' => 3500,
                'quantity_per_carton' => 40,
                'stock_unit' => 'PCS',
                'stock_quantity' => 200,
                'pieces_per_carton' => 40,
                'description' => 'Indomie goreng rasa rendang premium',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP001')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Indomie Ayam Bawang',
                'sku' => 'IND002',
                'category' => 'Food',
                'sub_category' => 'Instant Noodles',
                'price' => 3000,
                'quantity_per_carton' => 40,
                'stock_unit' => 'CTN',
                'stock_quantity' => 5,
                'pieces_per_carton' => 40,
                'description' => 'Indomie ayam bawang original',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP001')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Sari Roti Tawar Premium',
                'sku' => 'SR001',
                'category' => 'Food',
                'sub_category' => 'Bread',
                'price' => 18000,
                'quantity_per_carton' => 10,
                'stock_unit' => 'PCS',
                'stock_quantity' => 50,
                'pieces_per_carton' => 10,
                'description' => 'Sari roti tawar premium 500g',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP002')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Sari Roti Sandwich',
                'sku' => 'SR002',
                'category' => 'Food',
                'sub_category' => 'Bread',
                'price' => 12000,
                'quantity_per_carton' => 20,
                'stock_unit' => 'CTN',
                'stock_quantity' => 4,
                'pieces_per_carton' => 20,
                'description' => 'Sari roti sandwich coklat',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP002')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Teh Pucuk Harum 600ml',
                'sku' => 'TPH001',
                'category' => 'Beverage',
                'sub_category' => 'Tea',
                'price' => 5000,
                'quantity_per_carton' => 24,
                'stock_unit' => 'PCS',
                'stock_quantity' => 120,
                'pieces_per_carton' => 24,
                'description' => 'Teh pucuk harum original 600ml',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP001')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Pocari Sweat 500ml',
                'sku' => 'PS001',
                'category' => 'Beverage',
                'sub_category' => 'Sports Drink',
                'price' => 8000,
                'quantity_per_carton' => 24,
                'stock_unit' => 'CTN',
                'stock_quantity' => 4,
                'pieces_per_carton' => 24,
                'description' => 'Pocari sweat isotonic drink 500ml',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP001')->first()->id ?? $suppliers->first()->id,
            ],

            // Snacks & Chips - Mixed PCS and CTN
            [
                'name' => 'Lays Classic 75g',
                'sku' => 'LAYS001',
                'category' => 'Snack',
                'sub_category' => 'Chips',
                'price' => 12000,
                'quantity_per_carton' => 24,
                'stock_unit' => 'PCS',
                'stock_quantity' => 144,
                'pieces_per_carton' => 24,
                'description' => 'Lays classic potato chips 75g',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP007')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Cheetos Crunchy 80g',
                'sku' => 'CHEETOS001',
                'category' => 'Snack',
                'sub_category' => 'Cheese',
                'price' => 10000,
                'quantity_per_carton' => 24,
                'stock_unit' => 'CTN',
                'stock_quantity' => 4,
                'pieces_per_carton' => 24,
                'description' => 'Cheetos crunchy cheese 80g',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP007')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Chitato Sapi Panggang 90g',
                'sku' => 'CHITATO001',
                'category' => 'Snack',
                'sub_category' => 'Chips',
                'price' => 11000,
                'quantity_per_carton' => 20,
                'stock_unit' => 'PCS',
                'stock_quantity' => 80,
                'pieces_per_carton' => 20,
                'description' => 'Chitato sapi panggang 90g',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP007')->first()->id ?? $suppliers->first()->id,
            ],

            // Cosmetics & Personal Care - Mixed PCS and CTN
            [
                'name' => 'Wardah Lightening Facial Wash',
                'sku' => 'WD001',
                'category' => 'Cosmetics',
                'sub_category' => 'Facial Care',
                'price' => 45000,
                'quantity_per_carton' => 24,
                'stock_unit' => 'CTN',
                'stock_quantity' => 3,
                'pieces_per_carton' => 24,
                'description' => 'Wardah lightening facial wash 100ml',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP003')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Emina Lip Tint Berry',
                'sku' => 'EM001',
                'category' => 'Cosmetics',
                'sub_category' => 'Makeup',
                'price' => 35000,
                'quantity_per_carton' => 30,
                'stock_unit' => 'PCS',
                'stock_quantity' => 90,
                'pieces_per_carton' => 30,
                'description' => 'Emina lip tint berry 6ml',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP003')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Sariayu Temulawak Facial Foam',
                'sku' => 'SY001',
                'category' => 'Cosmetics',
                'sub_category' => 'Facial Care',
                'price' => 28000,
                'quantity_per_carton' => 24,
                'stock_unit' => 'CTN',
                'stock_quantity' => 4,
                'pieces_per_carton' => 24,
                'description' => 'Sariayu temulawak facial foam 100ml',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP003')->first()->id ?? $suppliers->first()->id,
            ],

            // Health & Medicine - Mixed PCS and CTN
            [
                'name' => 'Paracetamol 500mg',
                'sku' => 'PAR001',
                'category' => 'Health',
                'sub_category' => 'Medicine',
                'price' => 15000,
                'quantity_per_carton' => 100,
                'stock_unit' => 'CTN',
                'stock_quantity' => 5,
                'pieces_per_carton' => 100,
                'description' => 'Paracetamol tablet 500mg 10 strip',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP008')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Vitamin C 1000mg',
                'sku' => 'VIT001',
                'category' => 'Health',
                'sub_category' => 'Vitamin',
                'price' => 85000,
                'quantity_per_carton' => 30,
                'stock_unit' => 'PCS',
                'stock_quantity' => 150,
                'pieces_per_carton' => 30,
                'description' => 'Vitamin C tablet 1000mg 30 tablet',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP008')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Antiseptik Betadine',
                'sku' => 'BET001',
                'category' => 'Health',
                'sub_category' => 'Antiseptic',
                'price' => 25000,
                'quantity_per_carton' => 24,
                'stock_unit' => 'CTN',
                'stock_quantity' => 3,
                'pieces_per_carton' => 24,
                'description' => 'Betadine antiseptic solution 60ml',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP008')->first()->id ?? $suppliers->first()->id,
            ],

            // Stationery & Office Supplies - Mixed PCS and CTN
            [
                'name' => 'Buku Tulis Sinar Dunia',
                'sku' => 'BT001',
                'category' => 'Stationery',
                'sub_category' => 'Notebook',
                'price' => 8000,
                'quantity_per_carton' => 50,
                'stock_unit' => 'PCS',
                'stock_quantity' => 200,
                'pieces_per_carton' => 50,
                'description' => 'Buku tulis sinar dunia 38 lembar',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP006')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Pulpen Pilot G-2',
                'sku' => 'PILOT001',
                'category' => 'Stationery',
                'sub_category' => 'Pen',
                'price' => 12000,
                'quantity_per_carton' => 72,
                'stock_unit' => 'CTN',
                'stock_quantity' => 4,
                'pieces_per_carton' => 72,
                'description' => 'Pulpen pilot G-2 0.7mm hitam',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP006')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Pensil 2B Faber Castell',
                'sku' => 'FC001',
                'category' => 'Stationery',
                'sub_category' => 'Pencil',
                'price' => 5000,
                'quantity_per_carton' => 144,
                'stock_unit' => 'PCS',
                'stock_quantity' => 432,
                'pieces_per_carton' => 144,
                'description' => 'Pensil 2B faber castell',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', 'SP006')->first()->id ?? $suppliers->first()->id,
            ],

            // Electronics & Accessories - Mixed PCS and CTN
            [
                'name' => 'Kabel Data USB-C 2m',
                'sku' => 'KB001',
                'category' => 'Electronics',
                'sub_category' => 'Cable',
                'price' => 45000,
                'quantity_per_carton' => 20,
                'stock_unit' => 'CTN',
                'stock_quantity' => 3,
                'pieces_per_carton' => 20,
                'description' => 'Kabel data USB-C fast charging 2 meter',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', '240503')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Power Bank 20000mAh',
                'sku' => 'PB001',
                'category' => 'Electronics',
                'sub_category' => 'Power Bank',
                'price' => 250000,
                'quantity_per_carton' => 10,
                'stock_unit' => 'PCS',
                'stock_quantity' => 30,
                'pieces_per_carton' => 10,
                'description' => 'Power bank 20000mAh fast charging',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', '240503')->first()->id ?? $suppliers->first()->id,
            ],
            [
                'name' => 'Earphone Bluetooth',
                'sku' => 'EB001',
                'category' => 'Electronics',
                'sub_category' => 'Audio',
                'price' => 150000,
                'quantity_per_carton' => 12,
                'stock_unit' => 'CTN',
                'stock_quantity' => 3,
                'pieces_per_carton' => 12,
                'description' => 'Earphone bluetooth wireless dengan case',
                'is_active' => true,
                'supplier_id' => $suppliers->where('kode_supplier', '240503')->first()->id ?? $suppliers->first()->id,
            ],
        ];

        $this->command->info('Creating enhanced products...');
        
        foreach ($enhancedProducts as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }

        $this->command->info('Successfully created ' . count($enhancedProducts) . ' enhanced products with complete data!');
    }
}
