<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Reset Hasła</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light d-flex align-items-center justify-content-center min-vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5">
            <div class="card bg-dark border-secondary p-4 shadow-lg">
                <h3 class="fw-bold mb-3 text-white">Zapomniales hasla?</h3>
                <p class="text-muted small mb-4">
                    Podaj adres e-mail, a wyslemy Ci link do zresetowania hasla.
                </p>

                <!-- Status wysylki (pokazuje komunikat zwrotny z Laravela) -->
                @if (session('status'))
                    <div class="alert alert-success bg-transparent border-success text-success p-2 small mb-3">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label small text-muted">Adres e-mail</label>
                        <input type="email" name="email" class="form-control bg-dark text-white border-secondary" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Wyslij link do resetu</button>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-decoration-none small text-primary">Wroc do logowania</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>