<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator with full access',
            ],
            [
                'name' => 'Owner',
                'slug' => 'owner',
                'description' => 'Business owner with management access',
            ],
            [
                'name' => 'Sales',
                'slug' => 'sales',
                'description' => 'Sales representative with limited access',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        $this->command->info('Roles created successfully!');
    }
}