<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Ustawienia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light">

    <nav class="navbar navbar-dark bg-secondary bg-opacity-10 border-bottom border-secondary py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">📈 Capitex</a>
            <div class="d-flex align-items-center gap-4">
                <span class="text-muted small">
                    Zalogowany jako: <strong class="text-light">{{ Auth::user()->name }}</strong>
                    (Waluta: {{ Auth::user()->currency }})
                </span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Wyloguj się</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4 px-4">
        @if (session('status') === 'settings-updated')
            <div class="alert alert-success bg-transparent border-success text-success p-2 small mb-4">
                Ustawienia zostały zapisane.
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="alert alert-success bg-transparent border-success text-success p-2 small mb-4">
                Hasło zostało zmienione.
            </div>
        @endif

        <div class="row">
            <div class="col-md-3">
                <div class="nav-section text-muted small text-uppercase fw-bold mb-2">Menu</div>
                <div class="list-group mb-4">
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action bg-transparent text-light border-secondary">
                        📊 Dashboard
                    </a>
                    <a href="{{ route('transactions.index') }}" class="list-group-item list-group-item-action bg-transparent text-light border-secondary">
                        📋 Transakcje
                    </a>
                    <a href="{{ route('settings.index') }}" class="list-group-item list-group-item-action bg-secondary bg-opacity-25 text-light border-secondary fw-bold">
                        ⚙️ Ustawienia
                    </a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card bg-secondary bg-opacity-10 border-secondary mb-4">
                    <div class="card-header border-secondary p-3">
                        <span class="fw-bold">Profil i preferencje</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.update') }}">
                            @csrf
                            @method('PATCH')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Nazwa użytkownika</label>
                                    <input type="text" name="name" class="form-control bg-dark text-light border-secondary" value="{{ old('name', $user->name) }}" required>
                                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Email</label>
                                    <input type="email" name="email" class="form-control bg-dark text-light border-secondary" value="{{ old('email', $user->email) }}" required>
                                    @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Waluta wyświetlania</label>
                                    <select name="currency" class="form-select bg-dark text-light border-secondary" required>
                                        @foreach (['PLN', 'USD', 'EUR'] as $currency)
                                            <option value="{{ $currency }}" {{ old('currency', $user->currency) === $currency ? 'selected' : '' }}>
                                                {{ $currency }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('currency') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary fw-bold">Zapisz ustawienia</button>
                        </form>
                    </div>
                </div>

                <div class="card bg-secondary bg-opacity-10 border-secondary">
                    <div class="card-header border-secondary p-3">
                        <span class="fw-bold">Zmiana hasła</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small">Obecne hasło</label>
                                    <input type="password" name="current_password" class="form-control bg-dark text-light border-secondary" required>
                                    @error('current_password', 'updatePassword') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small">Nowe hasło</label>
                                    <input type="password" name="password" class="form-control bg-dark text-light border-secondary" required>
                                    @error('password', 'updatePassword') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small">Powtórz nowe hasło</label>
                                    <input type="password" name="password_confirmation" class="form-control bg-dark text-light border-secondary" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning fw-bold">Zmień hasło</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
