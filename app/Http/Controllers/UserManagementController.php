<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserManagementRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
            'role_id' => $request->string('role_id')->toString(),
        ];

        $users = User::query()
            ->with('role')
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['role_id'], fn ($query, $roleId) => $query->where('role_id', $roleId))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('settings.users.index', [
            'pageTitle' => 'User Management',
            'pageSubtitle' => 'Kelola akun internal, role, dan status user dari interface admin.',
            'users' => $users,
            'filters' => $filters,
            'roles' => $this->roles(),
        ]);
    }

    public function create(): View
    {
        return view('settings.users.create', [
            'pageTitle' => 'Add User',
            'pageSubtitle' => 'Buat akun user baru dan assign role operasionalnya.',
            'userModel' => new User([
                'status' => 'active',
            ]),
            'roles' => $this->roles(),
            'statusOptions' => $this->statusOptions(),
            'formAction' => route('settings.users.store'),
            'formMethod' => 'POST',
            'submitLabel' => 'Save User',
        ]);
    }

    public function store(UserManagementRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        $user = User::query()->create($payload);

        $this->activityLogService->log(
            moduleName: 'users',
            record: $user,
            action: 'created',
            newValue: $user->fresh('role')?->toArray(),
            description: "User {$user->email} created",
        );

        return redirect()
            ->route('settings.users.index')
            ->with('status', "User {$user->email} created successfully.");
    }

    public function edit(User $user): View
    {
        return view('settings.users.edit', [
            'pageTitle' => 'Edit User',
            'pageSubtitle' => "Update akun {$user->email}.",
            'userModel' => $user->load('role'),
            'roles' => $this->roles(),
            'statusOptions' => $this->statusOptions(),
            'formAction' => route('settings.users.update', $user),
            'formMethod' => 'PUT',
            'submitLabel' => 'Update User',
        ]);
    }

    public function update(UserManagementRequest $request, User $user): RedirectResponse
    {
        $oldValue = $user->load('role')->toArray();
        $payload = $request->validated();

        $this->guardSensitiveUserUpdate($request->user(), $user, $payload);

        if (blank($payload['password'] ?? null)) {
            unset($payload['password']);
        }

        $user->update($payload);

        $this->activityLogService->log(
            moduleName: 'users',
            record: $user,
            action: 'updated',
            oldValue: $oldValue,
            newValue: $user->fresh('role')?->toArray(),
            description: "User {$user->email} updated",
        );

        return redirect()
            ->route('settings.users.index')
            ->with('status', "User {$user->email} updated successfully.");
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $payload = $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $oldValue = $user->load('role')->toArray();
        $this->guardSensitiveStatusChange($request->user(), $user, $payload['status']);

        $user->update([
            'status' => $payload['status'],
        ]);

        $this->activityLogService->log(
            moduleName: 'users',
            record: $user,
            action: $payload['status'] === 'active' ? 'reactivated' : 'deactivated',
            oldValue: $oldValue,
            newValue: $user->fresh('role')?->toArray(),
            description: "User {$user->email} status changed to {$payload['status']}",
        );

        return redirect()
            ->route('settings.users.index')
            ->with('status', "User {$user->email} is now {$payload['status']}.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $user->load('role');
        $oldValue = $user->toArray();

        $this->guardSensitiveDeletion($request->user(), $user);

        $email = $user->email;
        $user->delete();

        $this->activityLogService->log(
            moduleName: 'users',
            record: $user,
            action: 'deleted',
            oldValue: $oldValue,
            newValue: null,
            description: "User {$email} deleted",
        );

        return redirect()
            ->route('settings.users.index')
            ->with('status', "User {$email} deleted successfully.");
    }

    private function roles()
    {
        return Role::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }

    private function statusOptions(): array
    {
        return [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
        ];
    }

    private function guardSensitiveUserUpdate(User $actor, User $target, array $payload): void
    {
        $target->loadMissing('role');
        $newRole = isset($payload['role_id'])
            ? Role::query()->find($payload['role_id'])
            : null;
        $ownerCount = $this->ownerCount();

        if ($target->id === $actor->id && ($payload['status'] ?? $target->status) !== 'active') {
            throw ValidationException::withMessages([
                'status' => 'You cannot deactivate your own account.',
            ]);
        }

        if ($target->role?->slug === 'owner' && $ownerCount <= 1 && $newRole?->slug !== 'owner') {
            throw ValidationException::withMessages([
                'role_id' => 'At least one owner account must remain assigned.',
            ]);
        }

        if ($target->role?->slug === 'owner' && $ownerCount <= 1 && ($payload['status'] ?? $target->status) !== 'active') {
            throw ValidationException::withMessages([
                'status' => 'The last owner account cannot be deactivated.',
            ]);
        }
    }

    private function guardSensitiveStatusChange(User $actor, User $target, string $status): void
    {
        $target->loadMissing('role');
        $ownerCount = $this->ownerCount();

        if ($target->id === $actor->id && $status !== 'active') {
            throw ValidationException::withMessages([
                'status' => 'You cannot deactivate your own account.',
            ]);
        }

        if ($target->role?->slug === 'owner' && $ownerCount <= 1 && $status !== 'active') {
            throw ValidationException::withMessages([
                'status' => 'The last owner account cannot be deactivated.',
            ]);
        }
    }

    private function guardSensitiveDeletion(User $actor, User $target): void
    {
        $target->loadMissing('role');
        $ownerCount = $this->ownerCount();

        if ($target->id === $actor->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot delete your own account.',
            ]);
        }

        if ($target->role?->slug === 'owner' && $ownerCount <= 1) {
            throw ValidationException::withMessages([
                'user' => 'The last owner account cannot be deleted.',
            ]);
        }
    }

    private function ownerCount(): int
    {
        return User::query()
            ->whereHas('role', fn ($query) => $query->where('slug', 'owner'))
            ->count();
    }
}
