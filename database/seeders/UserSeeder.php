<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('slug', 'admin')->first();
        $ownerRole = Role::where('slug', 'owner')->first();
        $salesRole = Role::where('slug', 'sales')->first();

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->roles()->attach($adminRole);

        // Create Owner Users
        $owners = [
            [
                'name' => 'Budi Santoso',
                'username' => 'budi_owner',
                'email' => 'budi@company.com',
                'password' => 'owner123',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'username' => 'siti_owner',
                'email' => 'siti@company.com',
                'password' => 'owner123',
            ],
            [
                'name' => 'Ahmad Wijaya',
                'username' => 'ahmad_owner',
                'email' => 'ahmad@company.com',
                'password' => 'owner123',
            ],
        ];

        foreach ($owners as $ownerData) {
            $owner = User::create([
                'name' => $ownerData['name'],
                'username' => $ownerData['username'],
                'email' => $ownerData['email'],
                'password' => Hash::make($ownerData['password']),
                'email_verified_at' => now(),
            ]);
            $owner->roles()->attach($ownerRole);
        }

        // Create Sales Users
        $salesUsers = [
            [
                'name' => 'Dewi Sartika',
                'username' => 'dewi_sales',
                'email' => 'dewi@company.com',
                'password' => 'sales123',
            ],
            [
                'name' => 'Eko Prasetyo',
                'username' => 'eko_sales',
                'email' => 'eko@company.com',
                'password' => 'sales123',
            ],
            [
                'name' => 'Fina Rahayu',
                'username' => 'fina_sales',
                'email' => 'fina@company.com',
                'password' => 'sales123',
            ],
            [
                'name' => 'Gunawan Sari',
                'username' => 'gunawan_sales',
                'email' => 'gunawan@company.com',
                'password' => 'sales123',
            ],
            [
                'name' => 'Hesti Lestari',
                'username' => 'hesti_sales',
                'email' => 'hesti@company.com',
                'password' => 'sales123',
            ],
            [
                'name' => 'Indra Kurniawan',
                'username' => 'indra_sales',
                'email' => 'indra@company.com',
                'password' => 'sales123',
            ],
            [
                'name' => 'Jihan Maharani',
                'username' => 'jihan_sales',
                'email' => 'jihan@company.com',
                'password' => 'sales123',
            ],
            [
                'name' => 'Kurniawan Adi',
                'username' => 'kurniawan_sales',
                'email' => 'kurniawan@company.com',
                'password' => 'sales123',
            ],
            [
                'name' => 'Lina Marlina',
                'username' => 'lina_sales',
                'email' => 'lina@company.com',
                'password' => 'sales123',
            ],
            [
                'name' => 'Muhammad Rizki',
                'username' => 'rizki_sales',
                'email' => 'rizki@company.com',
                'password' => 'sales123',
            ],
        ];

        foreach ($salesUsers as $salesData) {
            $sales = User::create([
                'name' => $salesData['name'],
                'username' => $salesData['username'],
                'email' => $salesData['email'],
                'password' => Hash::make($salesData['password']),
                'email_verified_at' => now(),
            ]);
            $sales->roles()->attach($salesRole);
        }

        $this->command->info('Users created successfully!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Owners: budi@company.com, siti@company.com, ahmad@company.com / owner123');
        $this->command->info('Sales: dewi@company.com, eko@company.com, fina@company.com, etc. / sales123');
    }
}