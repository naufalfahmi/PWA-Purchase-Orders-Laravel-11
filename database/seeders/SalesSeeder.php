<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sales;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salesData = [
            [
                'name' => 'Ahmad Wijaya',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 123, Jakarta Pusat',
                'is_active' => true,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'phone' => '081234567891',
                'address' => 'Jl. Sudirman No. 456, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567892',
                'address' => 'Jl. Thamrin No. 789, Jakarta Pusat',
                'is_active' => true,
            ],
            [
                'name' => 'Dewi Sartika',
                'phone' => '081234567893',
                'address' => 'Jl. Gatot Subroto No. 321, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Eko Prasetyo',
                'phone' => '081234567894',
                'address' => 'Jl. HR Rasuna Said No. 654, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Fina Rahayu',
                'phone' => '081234567895',
                'address' => 'Jl. Kebon Jeruk No. 987, Jakarta Barat',
                'is_active' => true,
            ],
            [
                'name' => 'Gunawan Sari',
                'phone' => '081234567896',
                'address' => 'Jl. Mangga Dua No. 147, Jakarta Utara',
                'is_active' => true,
            ],
            [
                'name' => 'Hesti Lestari',
                'phone' => '081234567897',
                'address' => 'Jl. Kelapa Gading No. 258, Jakarta Utara',
                'is_active' => true,
            ],
            [
                'name' => 'Indra Kurniawan',
                'phone' => '081234567898',
                'address' => 'Jl. Cempaka Putih No. 369, Jakarta Pusat',
                'is_active' => true,
            ],
            [
                'name' => 'Jihan Maharani',
                'phone' => '081234567899',
                'address' => 'Jl. Senayan No. 741, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Kurniawan Adi',
                'phone' => '081234567800',
                'address' => 'Jl. Kemang Raya No. 852, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Lina Marlina',
                'phone' => '081234567801',
                'address' => 'Jl. Pondok Indah No. 963, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Muhammad Rizki',
                'phone' => '081234567802',
                'address' => 'Jl. Fatmawati No. 159, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Nina Sari',
                'phone' => '081234567803',
                'address' => 'Jl. Blok M No. 357, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Oscar Wijaya',
                'phone' => '081234567804',
                'address' => 'Jl. Tebet No. 468, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Putri Maharani',
                'phone' => '081234567805',
                'address' => 'Jl. Pancoran No. 579, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Rizki Pratama',
                'phone' => '081234567806',
                'address' => 'Jl. Kuningan No. 680, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Sari Dewi',
                'phone' => '081234567807',
                'address' => 'Jl. Setiabudi No. 791, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Tono Kurniawan',
                'phone' => '081234567808',
                'address' => 'Jl. Mampang No. 802, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Umi Kalsum',
                'phone' => '081234567809',
                'address' => 'Jl. Pasar Minggu No. 913, Jakarta Selatan',
                'is_active' => true,
            ],
        ];

        foreach ($salesData as $sales) {
            Sales::create($sales);
        }

        $this->command->info('Sales data created successfully!');
    }
}
