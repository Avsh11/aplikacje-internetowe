<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Rejestracja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light capitex-app">

<div class="capitex-auth-wrap">
    <div class="w-100 d-flex flex-column align-items-center">
        <div class="mb-4">
            @include('partials.capitex-brand', ['href' => '/'])
        </div>

        <div class="card border-secondary capitex-auth-card shadow-lg" style="max-width: 460px;">
            <h3 class="auth-title text-white mb-1">Utwórz konto</h3>
            <p class="text-muted small mb-4">Zacznij monitorować swój portfel inwestycyjny.</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label"><i class="bi bi-person me-1"></i>Imię</label>
                    <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label"><i class="bi bi-envelope me-1"></i>Adres e-mail</label>
                    <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="currency" class="form-label"><i class="bi bi-currency-exchange me-1"></i>Domyślna waluta</label>
                    <select id="currency" name="currency" class="form-select" required>
                        <option value="PLN" {{ old('currency') == 'PLN' ? 'selected' : '' }}>PLN</option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                    </select>
                    @error('currency') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label"><i class="bi bi-lock me-1"></i>Hasło (min. 8 znaków)</label>
                    <input id="password" type="password" name="password" class="form-control" required>
                    @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Powtórz hasło</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3"><i class="bi bi-person-plus me-1"></i>Utwórz konto</button>

                <div class="text-center">
                    <span class="small text-muted">Masz już konto? <a href="{{ route('login') }}" class="text-decoration-none text-primary">Zaloguj się</a></span>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
