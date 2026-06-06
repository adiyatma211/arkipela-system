<?php

namespace Database\Seeders;

use App\Enums\UserPermission;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (UserPermission::cases() as $permission) {
            Permission::query()->updateOrCreate(
                ['slug' => $permission->value],
                [
                    'name' => $permission->label(),
                    'module' => $permission->module(),
                    'description' => $permission->description(),
                ],
            );
        }
    }
}
