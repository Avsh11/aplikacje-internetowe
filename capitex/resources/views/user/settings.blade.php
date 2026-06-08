<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Ustawienia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light capitex-app">

    @include('partials.user-navbar')

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
                @include('partials.user-sidebar-menu')
            </div>

            <div class="col-md-9">
                <div class="card border-secondary mb-4">
                    <div class="card-header border-secondary p-3">
                        <span class="fw-bold"><i class="bi bi-person-gear me-1"></i>Profil i preferencje</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label class="form-label text-muted small">Avatar</label>
                                    <div class="d-flex align-items-center gap-3">
                                        @if ($user->avatarUrl())
                                            <img src="{{ $user->avatarUrl() }}" alt="Avatar" class="rounded-circle border border-secondary" width="64" height="64" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle border border-secondary d-flex align-items-center justify-content-center bg-dark text-muted" style="width: 64px; height: 64px;"><i class="bi bi-person fs-4"></i></div>
                                        @endif
                                        <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="form-control bg-dark text-light border-secondary">
                                    </div>
                                    @error('avatar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    <div class="text-muted small mt-1">JPG, PNG lub WEBP, max 2 MB.</div>
                                </div>
                            </div>

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

                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Zapisz ustawienia</button>
                        </form>
                    </div>
                </div>

                <div class="card border-secondary">
                    <div class="card-header border-secondary p-3">
                        <span class="fw-bold"><i class="bi bi-key me-1"></i>Zmiana hasła</span>
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

                            <button type="submit" class="btn btn-outline-light"><i class="bi bi-shield-lock me-1"></i>Zmień hasło</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
