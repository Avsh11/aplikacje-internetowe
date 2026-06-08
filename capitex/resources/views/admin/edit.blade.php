<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Capitex - Edycja użytkownika</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light capitex-app">

    <nav class="navbar navbar-dark border-bottom border-secondary py-3 mb-4">
        <div class="container-fluid px-4">
            @include('partials.capitex-brand', ['href' => route('admin.dashboard'), 'admin' => true])
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Powrót</a>
        </div>
    </nav>

    <div class="container py-2" style="max-width: 600px;">
        <div class="card border-secondary">
            <div class="card-header border-secondary">
                <span class="fw-bold"><i class="bi bi-person-gear me-1"></i>Edycja: {{ $user->name }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person me-1"></i>Nazwa</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-envelope me-1"></i>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-shield me-1"></i>Rola</label>
                        @if ($user->id === Auth::id())
                            <input type="hidden" name="role_id" value="1">
                            <input type="text" class="form-control" value="admin" disabled>
                            <div class="text-muted small mt-1">Nie możesz zmienić własnej roli.</div>
                        @else
                            <select name="role_id" class="form-select">
                                <option value="2" @selected(old('role_id', $user->role_id) == 2)>user</option>
                                <option value="1" @selected(old('role_id', $user->role_id) == 1)>admin</option>
                            </select>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-currency-exchange me-1"></i>Waluta</label>
                        <select name="currency" class="form-select">
                            @foreach (['PLN', 'USD', 'EUR'] as $c)
                                <option value="{{ $c }}" @selected(old('currency', $user->currency) === $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Zapisz</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Anuluj</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
