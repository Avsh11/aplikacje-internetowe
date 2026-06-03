<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Capitex - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light">

    <nav class="navbar navbar-dark bg-secondary bg-opacity-10 border-bottom border-secondary py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold text-danger">Capitex Admin</span>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">Wyloguj</button>
            </form>
        </div>
    </nav>

    <div class="container-fluid px-4 mt-4">
        @if (session('status'))
            <div class="alert alert-success border-success text-success small">{{ session('status') }}</div>
        @endif
        @error('delete')
            <div class="alert alert-danger border-danger text-danger small">{{ $message }}</div>
        @enderror

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-secondary bg-opacity-10 border-secondary p-3">
                    <div class="text-muted small">Uzytkownicy</div>
                    <div class="fs-4 fw-bold">{{ $usersCount }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-secondary bg-opacity-10 border-secondary p-3">
                    <div class="text-muted small">Portfele</div>
                    <div class="fs-4 fw-bold">{{ $portfoliosCount }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-secondary bg-opacity-10 border-secondary p-3">
                    <div class="text-muted small">Transakcje (lacznie)</div>
                    <div class="fs-4 fw-bold">{{ $transactionsCount }}</div>
                </div>
            </div>
        </div>

        <div class="card bg-secondary bg-opacity-10 border-secondary">
            <div class="card-header border-secondary fw-bold">Uzytkownicy</div>
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-muted border-secondary">Nazwa</th>
                            <th class="text-muted border-secondary">Email</th>
                            <th class="text-muted border-secondary">Rola</th>
                            <th class="text-muted border-secondary text-end">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td class="border-secondary">{{ $u->name }}</td>
                                <td class="border-secondary">{{ $u->email }}</td>
                                <td class="border-secondary">{{ $u->role->name ?? 'user' }}</td>
                                <td class="border-secondary text-end">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary">Edytuj</a>
                                    @if ($u->id !== Auth::id())
                                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Usunac uzytkownika wraz z portfelami?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Usun</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
