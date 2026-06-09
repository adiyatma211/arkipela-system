<?php

namespace Database\Seeders;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionIds = Permission::query()
            ->pluck('id', 'slug');

        $rolePermissions = [
            UserRole::OWNER->value => UserPermission::cases(),
            UserRole::ADMIN_EXPORT->value => [
                UserPermission::PRODUCTS_VIEW,
                UserPermission::SUPPLIERS_VIEW,
                UserPermission::CLIENTS_VIEW,
                UserPermission::ORDERS_VIEW,
                UserPermission::ORDERS_MANAGE,
                UserPermission::REPORTS_VIEW,
            ],
            UserRole::PROCUREMENT->value => [
                UserPermission::PRODUCTS_VIEW,
                UserPermission::PRODUCTS_MANAGE,
                UserPermission::SUPPLIERS_VIEW,
                UserPermission::SUPPLIERS_MANAGE,
                UserPermission::ORDERS_VIEW,
            ],
            UserRole::SALES->value => [
                UserPermission::PRODUCTS_VIEW,
                UserPermission::CLIENTS_VIEW,
                UserPermission::CLIENTS_MANAGE,
            ],
            UserRole::QC_ADMIN->value => [
                UserPermission::PRODUCTS_VIEW,
                UserPermission::ORDERS_VIEW,
            ],
            UserRole::FINANCE->value => [
                UserPermission::PRODUCTS_VIEW,
                UserPermission::ORDERS_VIEW,
                UserPermission::REPORTS_VIEW,
            ],
        ];

        foreach ($rolePermissions as $roleSlug => $permissions) {
            $role = Role::query()->where('slug', $roleSlug)->first();

            if (! $role) {
                continue;
            }

            $role->permissions()->sync(
                collect($permissions)
                    ->map(fn (UserPermission $permission) => $permissionIds[$permission->value] ?? null)
                    ->filter()
                    ->values()
                    ->all()
            );
        }
    }
}
