<?php

namespace App\Models;

use App\Enums\UserPermission;
use App\Enums\UserRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug;
    }

    public function hasAnyRole(array|string $slugs): bool
    {
        $roleSlugs = is_array($slugs) ? $slugs : [$slugs];

        return in_array($this->role?->slug, $roleSlugs, true);
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function permissions(): EloquentCollection
    {
        if (! $this->relationLoaded('role')) {
            $this->load('role');
        }

        if (! $this->role) {
            return new EloquentCollection();
        }

        $this->role->loadMissing('permissions');

        return $this->role->permissions;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        return $this->permissions()
            ->contains(fn (Permission $item) => $item->slug === $permission);
    }

    public function hasAnyPermission(array|string $permissions): bool
    {
        $permissionSlugs = is_array($permissions) ? $permissions : [$permissions];

        if ($this->isOwner()) {
            return true;
        }

        return $this->permissions()
            ->contains(fn (Permission $item) => in_array($item->slug, $permissionSlugs, true));
    }

    public function homeRoute(): string
    {
        return match ($this->role?->slug) {
            UserRole::OWNER->value => 'dashboard',
            UserRole::PROCUREMENT->value => 'suppliers.index',
            UserRole::SALES->value => 'clients.index',
            UserRole::ADMIN_EXPORT->value,
            UserRole::FINANCE->value => 'orders.index',
            default => $this->fallbackHomeRoute(),
        };
    }

    private function fallbackHomeRoute(): string
    {
        $routeMap = [
            UserPermission::DASHBOARD_VIEW->value => 'dashboard',
            UserPermission::USERS_VIEW->value => 'settings.users.index',
            UserPermission::USERS_MANAGE->value => 'settings.users.index',
            UserPermission::SETTINGS_MANAGE->value => 'settings.roles.index',
            UserPermission::SUPPLIERS_VIEW->value => 'suppliers.index',
            UserPermission::CLIENTS_VIEW->value => 'clients.index',
            UserPermission::ORDERS_VIEW->value => 'orders.index',
        ];

        foreach ($routeMap as $permission => $route) {
            if ($this->hasPermission($permission)) {
                return $route;
            }
        }

        return 'dashboard';
    }

    public function createdSuppliers(): HasMany
    {
        return $this->hasMany(Supplier::class, 'created_by');
    }

    public function createdClients(): HasMany
    {
        return $this->hasMany(Client::class, 'created_by');
    }

    public function createdOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
