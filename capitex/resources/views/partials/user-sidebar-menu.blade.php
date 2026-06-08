<div class="nav-section text-muted small text-uppercase fw-bold mb-2">Menu</div>
<div class="list-group list-group-capitex mb-4">
    <a href="{{ route('dashboard') }}"
       class="list-group-item list-group-item-action border-secondary {{ request()->routeIs('dashboard') || request()->routeIs('portfolios.show') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
    </a>
    <a href="{{ route('transactions.index') }}"
       class="list-group-item list-group-item-action border-secondary {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
        <i class="bi bi-clock-history me-2"></i>Transakcje
    </a>
    <a href="{{ route('settings.index') }}"
       class="list-group-item list-group-item-action border-secondary {{ request()->routeIs('settings.*') ? 'active' : '' }}">
        <i class="bi bi-gear me-2"></i>Ustawienia
    </a>
    @if ((int) Auth::user()->role_id === 1)
        <a href="{{ route('admin.dashboard') }}"
           class="list-group-item list-group-item-action border-secondary text-danger {{ request()->routeIs('admin.*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock me-2"></i>Panel admina
        </a>
    @endif
</div>
