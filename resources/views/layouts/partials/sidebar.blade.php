@php
    $menuItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'bi-grid-fill'],
        ['label' => 'Suppliers', 'route' => 'suppliers.index', 'icon' => 'bi-people-fill'],
        ['label' => 'Clients', 'route' => 'clients.index', 'icon' => 'bi-person-badge-fill'],
        ['label' => 'Orders', 'route' => 'orders.index', 'icon' => 'bi-journal-check'],
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
                    <li class="sidebar-item {{ request()->routeIs($item['route']) ? 'active' : '' }}">
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
