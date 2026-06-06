<?php

namespace Database\Seeders;

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
        $ownerRole = Role::query()->where('slug', 'owner')->first();

        User::query()->updateOrCreate(
            ['email' => 'owner@archipela.test'],
            [
                'role_id' => $ownerRole?->id,
                'name' => 'Archipela Owner',
                'password' => 'password',
                'status' => 'active',
            ],
        );
    }
}
