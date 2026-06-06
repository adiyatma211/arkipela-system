<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleIds = Role::query()
            ->pluck('id', 'slug');

        $users = [
            [
                'name' => 'Archipela Owner',
                'email' => 'owner@archipela.test',
                'role_slug' => UserRole::OWNER->value,
                'status' => 'active',
            ],
            [
                'name' => 'Export Admin',
                'email' => 'admin.export@archipela.test',
                'role_slug' => UserRole::ADMIN_EXPORT->value,
                'status' => 'active',
            ],
            [
                'name' => 'Procurement Team',
                'email' => 'procurement@archipela.test',
                'role_slug' => UserRole::PROCUREMENT->value,
                'status' => 'active',
            ],
            [
                'name' => 'Sales Team',
                'email' => 'sales@archipela.test',
                'role_slug' => UserRole::SALES->value,
                'status' => 'active',
            ],
            [
                'name' => 'QC Admin',
                'email' => 'qc.admin@archipela.test',
                'role_slug' => UserRole::QC_ADMIN->value,
                'status' => 'active',
            ],
            [
                'name' => 'Finance Team',
                'email' => 'finance@archipela.test',
                'role_slug' => UserRole::FINANCE->value,
                'status' => 'active',
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'role_id' => $roleIds[$user['role_slug']] ?? null,
                    'name' => $user['name'],
                    'password' => 'password',
                    'status' => $user['status'],
                ],
            );
        }
    }
}
