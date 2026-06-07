<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Monitor Portfela</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light d-flex flex-column min-vh-100">

    <nav class="navbar navbar-dark bg-dark border-bottom border-secondary py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="/">📈 Capitex</a>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm fw-bold">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm fw-bold">Zaloguj sie</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm fw-bold">Rejestracja</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container my-auto py-5">
        <div class="row align-items-center justify-content-center g-5">
            <div class="col-lg-8 text-center">
                <h1 class="display-4 fw-bold lh-1 mb-4 text-white">Monitoruj swoje inwestycje w prosty sposob.</h1>
                <p class="fs-5 text-muted mb-4">Twoje inwestycje w jednym miejscu.</p>

                <div class="row g-3 justify-content-center mb-5">
                    <div class="col-sm-4">
                        <div class="card bg-secondary bg-opacity-10 border-secondary p-3 h-100">
                            <div class="text-muted small text-uppercase">Użytkownicy</div>
                            <div class="fs-3 fw-bold text-white">{{ $usersCount }}</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card bg-secondary bg-opacity-10 border-secondary p-3 h-100">
                            <div class="text-muted small text-uppercase">Portfele</div>
                            <div class="fs-3 fw-bold text-white">{{ $portfoliosCount }}</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card bg-secondary bg-opacity-10 border-secondary p-3 h-100">
                            <div class="text-muted small text-uppercase">Transakcje</div>
                            <div class="fs-3 fw-bold text-white">{{ $transactionsCount }}</div>
                        </div>
                    </div>
                </div>

                <p class="text-muted mb-4">Dołącz do społeczności Capitex i śledź akcje, ETF-y oraz krypto w jednym panelu.</p>

                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 fw-bold">Zaloz darmowe konto</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4 text-light fw-bold">Zaloguj sie</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer mt-auto py-3 bg-dark border-top border-secondary text-center text-muted">
        <div class="container">
            <span class="small">Capitex &copy; {{ date('Y') }}</span>
        </div>
    </footer>

</body>
</html>
