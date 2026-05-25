<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Logowanie</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark min-vh-100 d-flex align-items-center">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5">
            <div class="card bg-dark border-secondary p-4 shadow-lg">
                <h2 class="text-white fw-bold mb-1">Witaj ponownie</h2>
                <p class="text-muted small mb-4">Zaloguj sie, aby przejsc do dashboardu.</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Adres e-mail</label>
                        <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Haslo</label>
                        <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold mb-3">Zaloguj sie</button>
                    <div class="text-center">
                        <a href="{{ route('password.request') }}" class="text-decoration-none small text-primary">Zapomniales hasla?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>