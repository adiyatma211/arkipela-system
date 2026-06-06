@php
    $user = auth()->user();
    $user?->loadMissing('role.permissions');
    $menuItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'bi-grid-fill', 'permissions' => [\App\Enums\UserPermission::DASHBOARD_VIEW->value]],
        ['label' => 'Suppliers', 'route' => 'suppliers.index', 'match' => 'suppliers.*', 'icon' => 'bi-people-fill', 'permissions' => [\App\Enums\UserPermission::SUPPLIERS_VIEW->value, \App\Enums\UserPermission::SUPPLIERS_MANAGE->value]],
        ['label' => 'Clients', 'route' => 'clients.index', 'match' => 'clients.*', 'icon' => 'bi-person-badge-fill', 'permissions' => [\App\Enums\UserPermission::CLIENTS_VIEW->value, \App\Enums\UserPermission::CLIENTS_MANAGE->value]],
        ['label' => 'Orders', 'route' => 'orders.index', 'match' => 'orders.*', 'icon' => 'bi-journal-check', 'permissions' => [\App\Enums\UserPermission::ORDERS_VIEW->value, \App\Enums\UserPermission::ORDERS_MANAGE->value]],
        ['label' => 'Users', 'route' => 'settings.users.index', 'match' => 'settings.users.*', 'icon' => 'bi-person-lines-fill', 'permissions' => [\App\Enums\UserPermission::USERS_VIEW->value, \App\Enums\UserPermission::USERS_MANAGE->value]],
        ['label' => 'Settings', 'route' => 'settings.roles.index', 'match' => 'settings.roles.*', 'icon' => 'bi-shield-lock-fill', 'permissions' => [\App\Enums\UserPermission::SETTINGS_MANAGE->value]],
    ];
@endphp

<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Archipela Web</h5>
                    <small class="text-muted">Export operating system</small>
                </div>
                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Main Menu</li>

                @foreach ($menuItems as $item)
                    @continue(! $user?->hasAnyPermission($item['permissions']))
                    <li class="sidebar-item {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                        <a href="{{ route($item['route']) }}" class="sidebar-link">
                            <i class="bi {{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
