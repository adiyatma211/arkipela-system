<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Owner', 'slug' => 'owner', 'description' => 'Full access to all operational data'],
            ['name' => 'Admin Export', 'slug' => 'admin-export', 'description' => 'Manage export operations and orders'],
            ['name' => 'Procurement', 'slug' => 'procurement', 'description' => 'Manage suppliers and sourcing'],
            ['name' => 'Sales', 'slug' => 'sales', 'description' => 'Manage clients and sales pipeline'],
            ['name' => 'QC Admin', 'slug' => 'qc-admin', 'description' => 'Manage QC reporting and review flow'],
            ['name' => 'Finance', 'slug' => 'finance', 'description' => 'Manage costing and payment data'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['slug' => $role['slug']],
                $role,
            );
        }
    }
}
