<nav class="navbar navbar-dark border-bottom border-secondary py-3">
    <div class="container-fluid px-4">
        @include('partials.capitex-brand', ['href' => route('dashboard')])
        <div class="d-flex align-items-center gap-3">
            @if (Auth::user()->avatarUrl())
                <img src="{{ Auth::user()->avatarUrl() }}" alt="Avatar" class="rounded-circle border border-secondary" width="32" height="32" style="object-fit: cover;">
            @endif
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-person-circle me-1"></i>
                <strong class="text-light">{{ Auth::user()->name }}</strong>
                <span class="text-muted">· {{ Auth::user()->currency }}</span>
            </span>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-1"></i>Wyloguj
                </button>
            </form>
        </div>
    </div>
</nav>
