<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Historia Transakcji</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light">

    <!-- Nawigacja górna -->
    <nav class="navbar navbar-dark bg-secondary bg-opacity-10 border-bottom border-secondary py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">📈 Capitex</a>
            <div class="d-flex align-items-center gap-4">
                <span class="text-muted small">
                    Zalogowany jako: <strong class="text-light">{{ Auth::user()->name }}</strong> 
                    (Waluta: {{ Auth::user()->currency }})
                </span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Wyloguj się</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4 px-4">
        @if (session('status'))
            <div class="alert alert-success bg-transparent border-success text-success p-2 small mb-4">
                {{ session('status') }}
            </div>
        @endif

        <div class="row">
            
            <!-- PANEL BOCZNY -->
            <div class="col-md-3">
                <div class="nav-section text-muted small text-uppercase fw-bold mb-2">Menu</div>
                <div class="list-group mb-4">
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action bg-transparent text-light border-secondary">
                        📊 Dashboard
                    </a>
                    <a href="{{ route('transactions.index') }}" class="list-group-item list-group-item-action bg-secondary bg-opacity-25 text-light border-secondary fw-bold">
                        📋 Transakcje
                    </a>
                    <a href="{{ route('settings.index') }}" class="list-group-item list-group-item-action bg-transparent text-light border-secondary">
                        ⚙️ Ustawienia
                    </a>
                </div>
            </div>

            <!-- GŁÓWNA ZAWARTOŚĆ -->
            <div class="col-md-9">
                <div class="card bg-secondary bg-opacity-10 border-secondary h-100">
                    <div class="card-header border-secondary d-flex justify-content-between align-items-center p-3">
                        <span class="fw-bold">Historia Transakcji</span>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-muted border-secondary px-3">DATA</th>
                                        <th class="text-muted border-secondary">PORTFEL</th>
                                        <th class="text-muted border-secondary">AKTYWO</th>
                                        <th class="text-muted border-secondary">TYP</th>
                                        <th class="text-muted border-secondary text-end">ILOŚĆ</th>
                                        <th class="text-muted border-secondary text-end">CENA JEDN.</th>
                                        <th class="text-muted border-secondary text-end">WARTOŚĆ</th>
                                        <th class="text-muted border-secondary text-end px-3">AKCJA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $t)
                                        <tr>
                                            <td class="border-secondary px-3 align-middle text-muted small">
                                                {{ $t->transaction_date->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="border-secondary align-middle">
                                                <span class="badge bg-secondary">{{ $t->portfolio->name }}</span>
                                            </td>
                                            <td class="border-secondary align-middle fw-bold">
                                                {{ $t->asset->ticker }}
                                            </td>
                                            <td class="border-secondary align-middle">
                                                @if($t->type === 'buy')
                                                    <span class="text-success small fw-bold">KUPNO</span>
                                                @else
                                                    <span class="text-danger small fw-bold">SPRZEDAŻ</span>
                                                @endif
                                            </td>
                                            <td class="border-secondary text-end align-middle">{{ number_format($t->quantity, 4) }}</td>
                                            <td class="border-secondary text-end align-middle">{{ number_format($t->price_per_unit, 2) }}</td>
                                            <td class="border-secondary text-end align-middle fw-bold text-info">
                                                {{ number_format($t->quantity * $t->price_per_unit, 2) }} {{ strtoupper($t->currency ?? $t->asset->currency) }}
                                            </td>
                                            <td class="border-secondary text-end align-middle px-3">
                                                <!-- Formularz do usuwania (Metoda DELETE) -->
                                                <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tę transakcję?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 11px;">Usuń</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5 border-secondary">
                                                Historia transakcji jest pusta.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

</body>
</html>