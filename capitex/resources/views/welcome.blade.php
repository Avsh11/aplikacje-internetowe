<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Monitor Portfela</title>
    <!-- Vite laczy nasz widok z plikami CSS i JS Bootstrapa -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light d-flex flex-column min-vh-100">

    <!-- Nawigacja -->
    <nav class="navbar navbar-dark bg-dark border-bottom border-secondary py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="/">📈 Capitex</a>
            <div class="d-flex gap-2">
                @auth
                    <!-- Jesli user jest zalogowany, od razu kierujemy go do dashboardu -->
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm fw-bold">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm fw-bold">Zaloguj sie</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm fw-bold">Rejestracja</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section (Glowna tresc) -->
    <main class="container my-auto py-5">
        <div class="row align-items-center justify-content-center g-5">
            <div class="col-lg-8 text-center">
                <!-- display-4 robi bardzo duzy, wyrazny tekst tytulowy -->
                <h1 class="display-4 fw-bold lh-1 mb-4 text-white">Monitoruj swoje inwestycje w prosty sposob.</h1>
                <!-- leading-relaxed dodaje przestrzeni miedzy liniami tekstu, zeby lepiej sie czytalo -->
                <p class="fs-5 text-muted mb-5">Twoje inwestycje w jednym miejscu.</p>
                
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 fw-bold">Zaloz darmowe konto</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4 text-light fw-bold">Zaloguj sie</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Stopka -->
    <footer class="footer mt-auto py-3 bg-dark border-top border-secondary text-center text-muted">
        <div class="container">
            <span class="small">Capitex &copy; {{ date('Y') }}</span>
        </div>
    </footer>

</body>
</html>