<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolePermissionRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function index(): View
    {
        $roles = Role::query()
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('settings.roles.index', [
            'pageTitle' => 'Role & Permission Settings',
            'pageSubtitle' => 'Atur hak akses tiap role dari database tanpa perlu ubah code route satu per satu.',
            'roles' => $roles,
        ]);
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');

        $permissions = Permission::query()
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        return view('settings.roles.edit', [
            'pageTitle' => 'Edit Role Permissions',
            'pageSubtitle' => "Atur akses untuk role {$role->name}. Owner tetap memiliki full access.",
            'role' => $role,
            'permissionGroups' => $permissions,
        ]);
    }

    public function update(RolePermissionRequest $request, Role $role): RedirectResponse
    {
        $oldPermissions = $role->permissions()
            ->orderBy('slug')
            ->pluck('slug')
            ->all();

        $permissionIds = collect($request->validated('permissions', []))
            ->map(fn ($permissionId) => (int) $permissionId)
            ->unique()
            ->values()
            ->all();

        $role->permissions()->sync($permissionIds);

        $newPermissions = $role->permissions()
            ->orderBy('slug')
            ->pluck('slug')
            ->all();

        $this->activityLogService->log(
            moduleName: 'role-permissions',
            record: $role,
            action: 'updated',
            oldValue: ['permissions' => $oldPermissions],
            newValue: ['permissions' => $newPermissions],
            description: "Permissions for role {$role->slug} updated",
        );

        return redirect()
            ->route('settings.roles.index')
            ->with('status', "Permissions for role {$role->name} updated successfully.");
    }
}
