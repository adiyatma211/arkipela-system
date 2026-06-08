@php
    $user = auth()->user();
    $user?->loadMissing('role.permissions');
    $canAccess = static fn (array $permissions): bool => $user?->hasAnyPermission($permissions) ?? false;
    $menuItems = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'match' => 'dashboard',
            'icon' => 'bi-grid-fill',
            'permissions' => [\App\Enums\UserPermission::DASHBOARD_VIEW->value],
        ],
        [
            'label' => 'Suppliers',
            'route' => 'suppliers.index',
            'match' => 'suppliers.*',
            'icon' => 'bi-people-fill',
            'permissions' => [
                \App\Enums\UserPermission::SUPPLIERS_VIEW->value,
                \App\Enums\UserPermission::SUPPLIERS_MANAGE->value,
            ],
        ],
        [
            'label' => 'Clients',
            'route' => 'clients.index',
            'match' => 'clients.*',
            'icon' => 'bi-person-badge-fill',
            'permissions' => [
                \App\Enums\UserPermission::CLIENTS_VIEW->value,
                \App\Enums\UserPermission::CLIENTS_MANAGE->value,
            ],
        ],
        [
            'label' => 'Orders',
            'route' => 'orders.index',
            'match' => 'orders.*',
            'icon' => 'bi-journal-check',
            'permissions' => [
                \App\Enums\UserPermission::ORDERS_VIEW->value,
                \App\Enums\UserPermission::ORDERS_MANAGE->value,
            ],
        ],
        [
            'label' => 'Reports',
            'icon' => 'bi-bar-chart-line-fill',
            'children' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'reports.dashboard',
                    'match' => 'reports.dashboard',
                    'permissions' => [\App\Enums\UserPermission::REPORTS_VIEW->value],
                ],
                [
                    'label' => 'Orders',
                    'route' => 'reports.orders',
                    'match' => 'reports.orders',
                    'permissions' => [\App\Enums\UserPermission::REPORTS_VIEW->value],
                ],
                [
                    'label' => 'Clients',
                    'route' => 'reports.clients',
                    'match' => 'reports.clients',
                    'permissions' => [\App\Enums\UserPermission::REPORTS_VIEW->value],
                ],
                [
                    'label' => 'Products',
                    'route' => 'reports.products',
                    'match' => 'reports.products',
                    'permissions' => [\App\Enums\UserPermission::REPORTS_VIEW->value],
                ],
            ],
        ],
        [
            'label' => 'Settings',
            'icon' => 'bi-shield-lock-fill',
            'children' => [
                [
                    'label' => 'Users',
                    'route' => 'settings.users.index',
                    'match' => 'settings.users.*',
                    'permissions' => [
                        \App\Enums\UserPermission::USERS_VIEW->value,
                        \App\Enums\UserPermission::USERS_MANAGE->value,
                    ],
                ],
                [
                    'label' => 'Roles',
                    'route' => 'settings.roles.index',
                    'match' => 'settings.roles.*',
                    'permissions' => [\App\Enums\UserPermission::SETTINGS_MANAGE->value],
                ],
                [
                    'label' => 'Parameters',
                    'route' => 'settings.parameters.index',
                    'match' => 'settings.parameters.*',
                    'permissions' => [\App\Enums\UserPermission::SETTINGS_MANAGE->value],
                ],
            ],
        ],
    ];

    $menuItems = collect($menuItems)
        ->map(function (array $item) use ($canAccess) {
            $children = collect($item['children'] ?? [])
                ->filter(fn (array $child) => $canAccess($child['permissions'] ?? []))
                ->values()
                ->all();

            $item['children'] = $children;
            $item['is_visible'] = ! empty($children)
                || $canAccess($item['permissions'] ?? []);

            return $item;
        })
        ->filter(fn (array $item) => $item['is_visible'])
        ->values();
@endphp

@once
    <style>
            body.archipela-sidebar-collapsed #sidebar .sidebar-wrapper {
                width: 5.75rem;
            }

            body.archipela-sidebar-collapsed #main {
                margin-left: 5.75rem;
            }

            body.archipela-sidebar-collapsed #sidebar .sidebar-header {
                padding-left: 1rem;
                padding-right: 1rem;
            }

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
                font-weight: 400;
                font-size: 0.68rem;
                color: #25396f;
                line-height: 1.1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .archipela-sidebar-brand__subtitle {
                color: #7c8db5;
                font-size: 0.45rem;
                line-height: 1.1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .archipela-sidebar-header-wrap {
                display: flex;
                align-items: center;
                width: 100%;
                min-width: 0;
                position: relative;
                min-height: 2.5rem;
                padding-right: 3.25rem;
            }

            .archipela-sidebar-actions {
                position: absolute;
                top: 50%;
                right: 0;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                transform: translateY(-50%);
            }

            .archipela-sidebar-close {
                color: #25396f;
                font-size: 1.35rem;
            }

            .archipela-sidebar-toggle {
                width: 2.1rem;
                height: 2.1rem;
                border-radius: 0.7rem;
                border: 1px solid #dce3f1;
                background: #fff;
                color: #25396f;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 10px 24px rgba(37, 57, 111, 0.08);
                transition: all 0.2s ease;
                padding: 0;
                line-height: 1;
            }

            .archipela-sidebar-toggle i {
                font-size: 1.05rem;
                line-height: 1;
            }

            .archipela-sidebar-toggle:hover {
                background: #f5f7fb;
                color: #1b2f63;
            }

            body.archipela-sidebar-collapsed .archipela-sidebar-brand {
                justify-content: flex-start;
            }

            body.archipela-sidebar-collapsed .archipela-sidebar-brand__title,
            body.archipela-sidebar-collapsed .archipela-sidebar-brand__subtitle,
            body.archipela-sidebar-collapsed #sidebar .sidebar-title,
            body.archipela-sidebar-collapsed #sidebar .sidebar-link span {
                display: none;
            }

            body.archipela-sidebar-collapsed #sidebar .sidebar-item {
                width: 100%;
            }

            body.archipela-sidebar-collapsed #sidebar .sidebar-link {
                justify-content: center;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            body.archipela-sidebar-collapsed #sidebar .sidebar-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }

            body.archipela-sidebar-collapsed #sidebar .sidebar-item.has-sub > .sidebar-link::after,
            body.archipela-sidebar-collapsed #sidebar .sidebar-item.has-sub .submenu {
                display: none !important;
            }

            body.archipela-sidebar-collapsed .archipela-sidebar-header-wrap {
                min-height: 3rem;
                padding-right: 3.35rem;
            }

            body.archipela-sidebar-collapsed .archipela-sidebar-brand__logo {
                margin: 0;
            }

            body.archipela-sidebar-collapsed .archipela-sidebar-actions {
                right: -0.1rem;
            }

            @media (max-width: 1199.98px) {
                body.archipela-sidebar-collapsed #main {
                    margin-left: 0;
                }
            }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const storageKey = 'archipela-sidebar-collapsed';
                const toggleButton = document.querySelector('[data-sidebar-desktop-toggle]');
                const desktopBreakpoint = 1200;

                const isDesktop = () => window.innerWidth >= desktopBreakpoint;

                const applyState = (collapsed) => {
                    document.body.classList.toggle('archipela-sidebar-collapsed', isDesktop() && collapsed);
                };

                const syncState = () => {
                    const collapsed = window.localStorage.getItem(storageKey) === 'true';
                    applyState(collapsed);
                };

                toggleButton?.addEventListener('click', function () {
                    const nextState = !(window.localStorage.getItem(storageKey) === 'true');
                    window.localStorage.setItem(storageKey, String(nextState));
                    applyState(nextState);
                });

                window.addEventListener('resize', syncState);
                syncState();
            });
        </script>
    @endpush
@endonce

<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="archipela-sidebar-header-wrap">
                <a href="{{ route($user?->homeRoute() ?? 'dashboard') }}" class="archipela-sidebar-brand">
                    <img src="{{ asset('assetes/logo/logo.png') }}" alt="Archipela Logo"
                        class="archipela-sidebar-brand__logo">
                    <div>
                        <div class="archipela-sidebar-brand__title">Archipela Web</div>
                        <div class="archipela-sidebar-brand__subtitle">Export operating system</div>
                    </div>
                </a>
                <div class="archipela-sidebar-actions">
                    <button type="button" class="archipela-sidebar-toggle d-none d-xl-inline-flex"
                        data-sidebar-desktop-toggle aria-label="Toggle sidebar">
                        <i class="bi bi-layout-sidebar-inset"></i>
                    </button>
                    <a href="#" class="sidebar-hide d-xl-none d-block archipela-sidebar-close">
                        <i class="bi bi-x bi-middle"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Main Menu</li>

                @foreach ($menuItems as $item)
                    @php
                        $hasChildren = ! empty($item['children']);
                        $isParentActive = $hasChildren
                            ? collect($item['children'])->contains(fn (array $child) => request()->routeIs($child['match']))
                            : request()->routeIs($item['match'] ?? '');
                    @endphp

                    @if ($hasChildren)
                        <li class="sidebar-item has-sub {{ $isParentActive ? 'active' : '' }}">
                            <a href="#" class="sidebar-link">
                                <i class="bi {{ $item['icon'] }}"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>

                            <ul class="submenu {{ $isParentActive ? 'active' : '' }}">
                                @foreach ($item['children'] as $child)
                                    <li class="submenu-item {{ request()->routeIs($child['match']) ? 'active' : '' }}">
                                        <a href="{{ route($child['route']) }}" class="submenu-link">
                                            {{ $child['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class="sidebar-item {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                            <a href="{{ route($item['route']) }}" class="sidebar-link">
                                <i class="bi {{ $item['icon'] }}"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>
