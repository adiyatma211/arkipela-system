<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h3>{{ $pageTitle ?? 'ArkipelaSpice Web' }}</h3>
            @isset($pageSubtitle)
                <p class="text-subtitle text-muted mb-0">{{ $pageSubtitle }}</p>
            @endisset
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <h6 class="mb-0">{{ auth()->user()?->name }}</h6>
                <small class="text-muted">{{ auth()->user()?->role?->name ?? 'No role assigned' }}</small>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
            </form>
        </div>
    </div>
</div>
