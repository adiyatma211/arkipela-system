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

@once
    @push('styles')
        <style>
            .archipela-sidebar-brand {
                display: flex;
                align-items: center;
                gap: 0.85rem;
                flex: 1;
                min-width: 0;
                color: inherit;
                text-decoration: none;
            }

            .archipela-sidebar-brand:hover {
                color: inherit;
            }

            .archipela-sidebar-brand__logo {
                width: 2.1rem;
                height: 2.1rem;
                border-radius: 0.65rem;
                object-fit: cover;
                box-shadow: 0 8px 20px rgba(37, 57, 111, 0.12);
                flex-shrink: 0;
            }

            .archipela-sidebar-brand__title {
                font-weight: 800;
                font-size: 1rem;
                color: #25396f;
                line-height: 1.1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .archipela-sidebar-brand__subtitle {
                color: #7c8db5;
                font-size: 0.72rem;
                line-height: 1.1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .archipela-sidebar-header-wrap {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 0.75rem;
                width: 100%;
                min-width: 0;
            }
        </style>
    @endpush
@endonce

<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="archipela-sidebar-header-wrap">
                <a href="{{ route($user?->homeRoute() ?? 'dashboard') }}" class="archipela-sidebar-brand">
                    <img src="{{ asset('assetes/logo/logo.png') }}" alt="Archipela Logo" class="archipela-sidebar-brand__logo">
                    <div>
                        <div class="archipela-sidebar-brand__title">Archipela Web</div>
                        <div class="archipela-sidebar-brand__subtitle">Export operating system</div>
                    </div>
                </a>
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
