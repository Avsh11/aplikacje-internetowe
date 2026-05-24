<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Monitor Portfela</title>
    
    <!-- Lacznik z Bootstrapem poprzez kompilator Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light d-flex flex-column min-vh-100">

    <!-- Nawigacja gorna (Navbar) -->
    <nav class="navbar navbar-dark bg-secondary bg-opacity-10 border-bottom border-secondary py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="/">📈 Capitex</a>
            <div class="d-flex gap-2">
                <!-- Sprawdzamy czy użytkownik jest zalogowany -->
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Zaloguj sie</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Zarejestruj sie</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Sekcja glowna (Hero) -->
    <main class="container my-auto py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 text-center text-lg-start">
                <h1 class="display-4 fw-bold lh-1 mb-3">Monitoruj swoje inwestycje w jednym miejscu.</h1>
                <p class="col-lg-10 fs-5 text-muted">Capitex pozwala w prosty sposob kontrolowac Twoje akcje, kryptowaluty oraz fundusze ETF. Bez zbednych arkuszy kalkulacyjnych.</p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-4">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 me-md-2">Zacznij za darmo</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4">Zaloguj sie do konta</a>
                </div>
            </div>
            <div class="col-lg-6">
                <!-- Prosta, minimalistyczna karta pokazujaca glowne zalety -->
                <div class="card bg-secondary bg-opacity-10 border-secondary p-4 rounded-3 text-light">
                    <h3 class="fw-bold mb-3">Co oferuje Capitex?</h3>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">✓ Dynamiczne pobieranie cen kryptowalut (Binance)</li>
                        <li class="mb-2">✓ Przeliczanie wielu walut w locie (NBP API)</li>
                        <li class="mb-2">✓ Agregacja wielu kont i brokerow</li>
                        <li>✓ Czysty i przejrzysty interfejs</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <!-- Stopka (Footer) -->
    <footer class="footer mt-auto py-3 bg-secondary bg-opacity-10 border-top border-secondary text-center text-muted">
        <div class="container">
            <span class="small">Capitex &copy; {{ date('Y') }} - Projekt zaliczeniowy</span>
        </div>
    </footer>

</body>
</html>