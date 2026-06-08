<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Logowanie</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light capitex-app">

<div class="capitex-auth-wrap">
    <div class="w-100 d-flex flex-column align-items-center">
        <div class="mb-4">
            @include('partials.capitex-brand', ['href' => '/'])
        </div>

        <div class="card border-secondary capitex-auth-card shadow-lg">
            <h2 class="auth-title text-white mb-1">Witaj z powrotem</h2>
            <p class="text-muted small mb-4">Zaloguj się, aby sprawdzić swój portfel.</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-envelope me-1"></i>Adres e-mail</label>
                    <input type="email" name="email" class="form-control" required autofocus placeholder="jan@kowalski.pl">
                </div>
                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-lock me-1"></i>Hasło</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3"><i class="bi bi-box-arrow-in-right me-1"></i>Zaloguj się</button>
                <div class="text-center">
                    <a href="{{ route('password.request') }}" class="text-decoration-none small text-primary">Zapomniałeś hasła?</a>
                </div>
            </form>
        </div>

        <p class="text-muted small mt-4 mb-0">
            Nie masz konta? <a href="{{ route('register') }}" class="text-primary text-decoration-none">Zarejestruj się</a>
        </p>
    </div>
</div>

</body>
</html>
