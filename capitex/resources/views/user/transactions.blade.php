<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Historia Transakcji</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light capitex-app">

    @include('partials.user-navbar')

    <div class="container-fluid mt-4 px-4">
        @if (session('status'))
            <div class="alert alert-success bg-transparent border-success text-success p-2 small mb-4">
                <i class="bi bi-check-circle me-1"></i>{{ session('status') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-3">
                @include('partials.user-sidebar-menu')
            </div>

            <div class="col-md-9">
                <div class="card border-secondary h-100">
                    <div class="card-header border-secondary p-3">
                        <span class="fw-bold"><i class="bi bi-clock-history me-1"></i>Historia Transakcji</span>
                    </div>
                    <div class="card-body border-bottom border-secondary p-3">
                        <form method="GET" action="{{ route('transactions.index') }}" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label text-muted small mb-1"><i class="bi bi-search me-1"></i>Nazwa aktywa</label>
                                <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="form-control form-control-sm" placeholder="np. NVIDIA">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small mb-1">Ticker</label>
                                <input type="text" name="ticker" value="{{ $filters['ticker'] ?? '' }}" class="form-control form-control-sm" placeholder="np. NVDA">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-muted small mb-1">Portfel</label>
                                <input type="text" name="portfolio" value="{{ $filters['portfolio'] ?? '' }}" class="form-control form-control-sm" placeholder="np. XTB">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-muted small mb-1">Sortowanie</label>
                                <select name="sort" class="form-select form-select-sm">
                                    <option value="date_desc" {{ ($filters['sort'] ?? 'date_desc') === 'date_desc' ? 'selected' : '' }}>Data (najnowsze)</option>
                                    <option value="date_asc" {{ ($filters['sort'] ?? '') === 'date_asc' ? 'selected' : '' }}>Data (najstarsze)</option>
                                    <option value="ticker" {{ ($filters['sort'] ?? '') === 'ticker' ? 'selected' : '' }}>Ticker (A-Z)</option>
                                    <option value="asset_name" {{ ($filters['sort'] ?? '') === 'asset_name' ? 'selected' : '' }}>Nazwa aktywa (A-Z)</option>
                                    <option value="portfolio_name" {{ ($filters['sort'] ?? '') === 'portfolio_name' ? 'selected' : '' }}>Portfel (A-Z)</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Filtruj</button>
                                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary">Wyczyść</a>
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover capitex-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="px-3">Data</th>
                                        <th>Portfel</th>
                                        <th>Aktywo</th>
                                        <th class="text-end">Ilość</th>
                                        <th class="text-end">Cena jedn.</th>
                                        <th class="text-end">Wartość</th>
                                        <th class="text-end px-3">Akcja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $t)
                                        <tr>
                                            <td class="px-3 align-middle text-muted small">
                                                {{ $t->transaction_date->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge badge-cx-broker">{{ $t->portfolio->name }}</span>
                                            </td>
                                            <td class="align-middle fw-semibold">
                                                {{ $t->asset->name }}
                                                <div class="small text-muted fw-normal">{{ $t->asset->ticker }}</div>
                                            </td>
                                            <td class="text-end align-middle small">{{ number_format($t->quantity, 4) }}</td>
                                            <td class="text-end align-middle small">{{ number_format($t->price_per_unit, 2) }}</td>
                                            <td class="text-end align-middle fw-semibold text-info">
                                                {{ number_format($t->quantity * $t->price_per_unit, 2) }} {{ strtoupper($t->currency ?? $t->asset->currency) }}
                                            </td>
                                            <td class="text-end align-middle px-3">
                                                <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tę transakcję?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">
                                                <i class="bi bi-inbox d-block fs-3 mb-2 opacity-50"></i>
                                                @if (!empty($filters['name']) || !empty($filters['ticker']) || !empty($filters['portfolio']))
                                                    Brak transakcji dla wybranych filtrów.
                                                @else
                                                    Historia transakcji jest pusta.
                                                @endif
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
