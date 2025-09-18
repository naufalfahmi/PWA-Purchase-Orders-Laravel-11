<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderAccOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderAccOptions = [
            [
                'name' => 'Toko Utama',
                'code' => 'TU',
                'description' => 'Toko utama di pusat kota',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Toko Cabang 1',
                'code' => 'TC1',
                'description' => 'Toko cabang di area timur',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Toko Cabang 2',
                'code' => 'TC2',
                'description' => 'Toko cabang di area barat',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Toko Online',
                'code' => 'TO',
                'description' => 'Penjualan melalui platform online',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Distributor',
                'code' => 'DIST',
                'description' => 'Penjualan ke distributor',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Reseller',
                'code' => 'RES',
                'description' => 'Penjualan ke reseller',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('order_acc_options')->insert($orderAccOptions);
    }
}