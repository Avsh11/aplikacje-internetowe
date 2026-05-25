<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-white d-flex align-items-center justify-content-center min-vh-100">
    <div class="text-center">
        <h1 class="text-danger">Admin</h1>
        <p class="text-muted">Panel admina</p>
        
        <!-- Formularz wylogowania -->
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="btn btn-outline-light">Wyloguj sie</button>
        </form>
    </div>
</body>
</html>