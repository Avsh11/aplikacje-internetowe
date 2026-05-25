<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Rejestracja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light d-flex align-items-center justify-content-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <!-- col-md-6 sprawia, ze na komputerze karta zajmuje polowe ekranu, a na telefonie caly -->
            <div class="col-12 col-md-6 col-lg-5">
                
                <!-- Logo nad karta rejestracji -->
                <div class="text-center mb-4">
                    <a href="/" class="text-decoration-none text-white fs-2 fw-bold">📈 Capitex</a>
                </div>

                <!-- Karta rejestracji -->
                <div class="card bg-secondary bg-opacity-10 border-secondary p-4 rounded-3">
                    <h3 class="fw-bold mb-3 text-white">Utworz konto</h3>
                    <p class="text-muted small mb-4">Wpisz swoje dane, aby rozpoczac monitorowanie portfela.</p>

                    <!-- Formularz wysyla dane POST do trasy 'register' obsługiwanej przez Breeze -->
                    <form method="POST" action="{{ route('register') }}">
                        @csrf <!-- Zabezpieczenie tokenem przed atakami CSRF -->

                        <!-- Pole: Imie -->
                        <div class="mb-3">
                            <label for="name" class="form-label small text-muted">Twoje imie</label>
                            <input id="name" type="text" name="name" class="form-control bg-dark text-white border-secondary" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pole: Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label small text-muted">Adres e-mail</label>
                            <input id="email" type="email" name="email" class="form-control bg-dark text-white border-secondary" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pole: Waluta domyslna (PLN, USD, EUR) -->
                        <div class="mb-3">
                            <label for="currency" class="form-label small text-muted">Domyslna waluta portfela</label>
                            <select id="currency" name="currency" class="form-select bg-dark text-white border-secondary" required>
                                <option value="PLN" {{ old('currency') == 'PLN' ? 'selected' : '' }}>PLN (Polski Zloty)</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (Dolar Amerykanski)</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                            </select>
                            @error('currency')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pole: Haslo -->
                        <div class="mb-3">
                            <label for="password" class="form-label small text-muted">Haslo (min. 8 znakow)</label>
                            <input id="password" type="password" name="password" class="form-control bg-dark text-white border-secondary" required>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pole: Potwierdzenie hasla -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label small text-muted">Potwierdz haslo</label>
                            <!-- Name musi byc dokladnie takie: password_confirmation (wymog walidacji Laravela) -->
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control bg-dark text-white border-secondary" required>
                        </div>

                        <!-- Przycisk rejestracji -->
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mb-3">Zarejestruj sie</button>

                        <div class="text-center">
                            <span class="small text-muted">Masz juz konto? <a href="{{ route('login') }}" class="text-decoration-none text-primary">Zaloguj sie</a></span>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</body>
</html>