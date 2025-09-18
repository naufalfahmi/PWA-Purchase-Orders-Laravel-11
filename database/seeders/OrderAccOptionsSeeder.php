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
                'name' => 'MAIMUNAH',
                'code' => 'MAI',
                'description' => 'Order account untuk Maimunah',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'DIFA',
                'code' => 'DIF',
                'description' => 'Order account untuk Difa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SULTHON',
                'code' => 'SUL',
                'description' => 'Order account untuk Sulthon',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'DINA',
                'code' => 'DIN',
                'description' => 'Order account untuk Dina',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('order_acc_options')->insert($orderAccOptions);
    }
}