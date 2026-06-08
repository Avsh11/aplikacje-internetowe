<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Monitor Portfela</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light capitex-app d-flex flex-column min-vh-100">

    <nav class="navbar navbar-dark border-bottom border-secondary py-3">
        <div class="container">
            @include('partials.capitex-brand', ['href' => '/'])
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Zaloguj się</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Rejestracja</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container my-auto py-5">
        <div class="row align-items-center justify-content-center g-5">
            <div class="col-lg-8 text-center">
                <h1 class="display-4 capitex-hero-title lh-1 mb-4 text-white">Monitoruj swoje inwestycje w prosty sposób.</h1>
                <p class="fs-5 text-muted mb-4">Twoje inwestycje w jednym miejscu.</p>

                <div class="row g-3 justify-content-center mb-5">
                    <div class="col-sm-4">
                        <div class="card border-secondary p-3 h-100 capitex-stat-pill">
                            <i class="bi bi-people"></i>
                            <div class="text-muted small text-uppercase">Użytkownicy</div>
                            <div class="fs-3 fw-bold text-white capitex-page-title">{{ $usersCount }}</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-secondary p-3 h-100 capitex-stat-pill">
                            <i class="bi bi-briefcase"></i>
                            <div class="text-muted small text-uppercase">Portfele</div>
                            <div class="fs-3 fw-bold text-white capitex-page-title">{{ $portfoliosCount }}</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-secondary p-3 h-100 capitex-stat-pill">
                            <i class="bi bi-arrow-left-right"></i>
                            <div class="text-muted small text-uppercase">Transakcje</div>
                            <div class="fs-3 fw-bold text-white capitex-page-title">{{ $transactionsCount }}</div>
                        </div>
                    </div>
                </div>

                <p class="text-muted mb-4">Dołącz do społeczności Capitex i śledź akcje, ETF-y oraz krypto w jednym panelu.</p>

                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4"><i class="bi bi-person-plus me-1"></i>Załóż darmowe konto</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4 text-light">Zaloguj się</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer mt-auto py-3 border-top border-secondary text-center text-muted">
        <div class="container">
            <span class="small">Capitex &copy; {{ date('Y') }}</span>
        </div>
    </footer>

</body>
</html>
