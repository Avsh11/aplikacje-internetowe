<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Capitex - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light capitex-app">

    <nav class="navbar navbar-dark border-bottom border-secondary py-3">
        <div class="container-fluid px-4">
            @include('partials.capitex-brand', ['href' => route('admin.dashboard'), 'admin' => true])
            <div class="d-flex gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-briefcase me-1"></i>Mój portfel</a>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right me-1"></i>Wyloguj</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 mt-4">
        @if (session('status'))
            <div class="alert alert-success border-success text-success small"><i class="bi bi-check-circle me-1"></i>{{ session('status') }}</div>
        @endif
        @error('delete')
            <div class="alert alert-danger border-danger text-danger small">{{ $message }}</div>
        @enderror

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-secondary p-3 capitex-admin-stat">
                    <i class="bi bi-people"></i>
                    <div class="text-muted small text-uppercase">Użytkownicy</div>
                    <div class="fs-4 fw-bold capitex-page-title">{{ $usersCount }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-secondary p-3 capitex-admin-stat">
                    <i class="bi bi-briefcase"></i>
                    <div class="text-muted small text-uppercase">Portfele</div>
                    <div class="fs-4 fw-bold capitex-page-title">{{ $portfoliosCount }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-secondary p-3 capitex-admin-stat">
                    <i class="bi bi-arrow-left-right"></i>
                    <div class="text-muted small text-uppercase">Transakcje (łącznie)</div>
                    <div class="fs-4 fw-bold capitex-page-title">{{ $transactionsCount }}</div>
                </div>
            </div>
        </div>

        <div class="card border-secondary">
            <div class="card-header border-secondary fw-bold"><i class="bi bi-people me-1"></i>Użytkownicy</div>
            <div class="card-body border-bottom border-secondary">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1"><i class="bi bi-search me-1"></i>Nazwa</label>
                        <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="form-control form-control-sm" placeholder="Szukaj po nazwie">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1">Email</label>
                        <input type="text" name="email" value="{{ $filters['email'] ?? '' }}" class="form-control form-control-sm" placeholder="Szukaj po emailu">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted small mb-1">Rola</label>
                        <select name="role_id" class="form-select form-select-sm">
                            <option value="">Wszystkie</option>
                            <option value="1" {{ ($filters['role_id'] ?? '') == '1' ? 'selected' : '' }}>Admin</option>
                            <option value="2" {{ ($filters['role_id'] ?? '') == '2' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted small mb-1">Sortowanie</label>
                        <select name="sort" class="form-select form-select-sm">
                            <option value="role" {{ ($filters['sort'] ?? 'role') === 'role' ? 'selected' : '' }}>Administratorzy na górze</option>
                            <option value="name" {{ ($filters['sort'] ?? '') === 'name' ? 'selected' : '' }}>Nazwa (A-Z)</option>
                            <option value="email" {{ ($filters['sort'] ?? '') === 'email' ? 'selected' : '' }}>Email (A-Z)</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Filtruj</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">Wyczyść</a>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover capitex-table mb-0">
                    <thead>
                        <tr>
                            <th class="border-secondary">Nazwa</th>
                            <th class="border-secondary">Email</th>
                            <th class="border-secondary">Rola</th>
                            <th class="border-secondary text-end">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $u)
                            <tr>
                                <td class="border-secondary">{{ $u->name }}</td>
                                <td class="border-secondary">{{ $u->email }}</td>
                                <td class="border-secondary">
                                    @if ((int) $u->role_id === 1)
                                        <span class="badge badge-cx-exchange">admin</span>
                                    @else
                                        <span class="badge badge-cx-broker">user</span>
                                    @endif
                                </td>
                                <td class="border-secondary text-end">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    @if ($u->id !== Auth::id())
                                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Usunąć użytkownika wraz z portfelami?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4 border-secondary">
                                    <i class="bi bi-inbox d-block fs-4 mb-2 opacity-50"></i>
                                    Brak użytkowników dla wybranych filtrów.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
