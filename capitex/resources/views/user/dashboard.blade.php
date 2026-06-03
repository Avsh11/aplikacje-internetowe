<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitex - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .cursor-pointer { cursor: pointer; }
        .cursor-pointer:hover { background-color: #343a40 !important; }
        .delete-btn { opacity: 0.3; transition: 0.2s; }
        .delete-btn:hover { opacity: 1 !important; }
    </style>
</head>
<body class="bg-dark text-light">

    @php
        $totalProfit    = 0;
        $totalCostBasis = 0;
        $chartLabels    = [];
        $chartValues    = [];

        foreach($holdings as $h) {
            $totalProfit    += $h['profit_loss'];
            $totalCostBasis += $h['total_cost_converted'];
            if ($h['quantity'] > 0) {
                $chartLabels[] = $h['asset']->ticker;
                $chartValues[] = round($h['current_value_converted'], 2);
            }
        }
        $totalProfitPct = $totalCostBasis > 0 ? ($totalProfit / $totalCostBasis) * 100 : 0;
    @endphp

    <nav class="navbar navbar-dark bg-secondary bg-opacity-10 border-bottom border-secondary py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">📈 Capitex</a>
            <div class="d-flex align-items-center gap-4">
                <span class="text-muted small">
                    Zalogowany: <strong class="text-light">{{ Auth::user()->name }}</strong>
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
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'bg-secondary bg-opacity-25 text-light fw-bold' : 'bg-transparent text-light' }} border-secondary">
                        📊 Dashboard
                    </a>
                    <a href="{{ route('transactions.index') }}" class="list-group-item list-group-item-action bg-transparent text-light border-secondary">
                        📋 Transakcje
                    </a>
                    <a href="{{ route('settings.index') }}" class="list-group-item list-group-item-action bg-transparent text-light border-secondary">
                        ⚙️ Ustawienia
                    </a>
                </div>

                <div class="card bg-secondary bg-opacity-10 border-secondary mb-3">
                    <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Moje Portfele</span>
                        <button class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addPortfolioModal">+</button>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse ($portfolios as $portfolio)
                            <div class="list-group-item bg-transparent border-secondary d-flex justify-content-between align-items-center {{ request()->fullUrlIs(route('portfolios.show', $portfolio->id)) ? 'bg-secondary bg-opacity-25' : '' }}">
                                <a href="{{ route('portfolios.show', $portfolio->id) }}" class="text-decoration-none text-light flex-grow-1">
                                    <span class="{{ request()->fullUrlIs(route('portfolios.show', $portfolio->id)) ? 'fw-bold' : '' }}">💼 {{ $portfolio->name }}</span>
                                </a>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary" style="font-size: 0.7rem;">{{ ucfirst($portfolio->category) }}</span>
                                    <form action="{{ route('portfolios.destroy', $portfolio->id) }}" method="POST" class="m-0" onsubmit="return confirm('Czy na pewno chcesz usunąć ten portfel?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link p-0 text-danger text-decoration-none delete-btn">🗑️</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item bg-transparent text-muted border-secondary text-center small py-3">Brak portfeli.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- GŁÓWNA ZAWARTOŚĆ -->
            <div class="col-md-9">

                <!-- Statystyki Top -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-secondary bg-opacity-10 border-secondary p-3 h-100">
                            <div class="text-muted small mb-1 text-uppercase fw-bold">Wartość całkowita</div>
                            <div class="fs-4 fw-bold">{{ number_format($totalValue, 2) }} {{ Auth::user()->currency }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-secondary bg-opacity-10 border-secondary p-3 h-100">
                            <div class="text-muted small mb-1 text-uppercase fw-bold">Niezrealizowany zysk</div>
                            <div class="fs-4 fw-bold {{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalProfit, 2) }} {{ Auth::user()->currency }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-secondary bg-opacity-10 border-secondary p-3 h-100">
                            <div class="text-muted small mb-1 text-uppercase fw-bold">Zwrot (%)</div>
                            <div class="fs-4 fw-bold {{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $totalProfit >= 0 ? '▲' : '▼' }} {{ number_format($totalProfitPct, 2) }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WYKRESY -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card bg-secondary bg-opacity-10 border-secondary h-100">
                            <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Rozwój portfela</span>
                                <div class="btn-group shadow-sm" id="chartTimeFilters">
                                    <button type="button" class="btn btn-sm btn-outline-primary {{ Auth::user()->default_chart_range === '1D' ? 'active' : '' }}" data-time="1D">1D</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary {{ Auth::user()->default_chart_range === '7D' ? 'active' : '' }}" data-time="7D">7D</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary {{ Auth::user()->default_chart_range === '1M' ? 'active' : '' }}" data-time="1M">1M</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary {{ Auth::user()->default_chart_range === '1Y' ? 'active' : '' }}" data-time="1Y">1Y</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary {{ Auth::user()->default_chart_range === 'ALL' ? 'active' : '' }}" data-time="ALL">ALL</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="lineChart" style="height: 250px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-secondary bg-opacity-10 border-secondary h-100">
                            <div class="card-header border-secondary"><span class="fw-bold">Alokacja</span></div>
                            <div class="card-body d-flex justify-content-center align-items-center">
                                @if(empty($chartValues))
                                    <span class="text-muted small">Brak aktywów</span>
                                @else
                                    <canvas id="donutChart" style="max-height: 200px;"></canvas>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela Aktywów -->
                <div class="card bg-secondary bg-opacity-10 border-secondary">
                    <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Moje Aktywa</span>
                        <button class="btn btn-sm btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#addTransactionModal" {{ count($portfolios) == 0 ? 'disabled' : '' }}>
                            + Dodaj transakcję
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-muted border-secondary px-3">AKTYWO</th>
                                        <th class="text-muted border-secondary text-end">ILOŚĆ</th>
                                        <th class="text-muted border-secondary text-end">ŚR. CENA ZAKUPU</th>
                                        <th class="text-muted border-secondary text-end">AKTUALNA CENA</th>
                                        <th class="text-muted border-secondary text-end">WARTOŚĆ</th>
                                        <th class="text-muted border-secondary text-end px-3">ZYSK/STRATA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($holdings as $h)
                                        <tr>
                                            <td class="border-secondary px-3">
                                                <strong>{{ $h['asset']->name }}</strong>
                                                <div class="small text-muted">{{ $h['asset']->ticker }}</div>
                                            </td>
                                            <td class="border-secondary text-end align-middle">
                                                {{ number_format($h['quantity'], 4) }}
                                            </td>
                                            <td class="border-secondary text-end align-middle">
                                                {{-- Średnia cena zakupu = koszt całkowity / ilość --}}
                                                {{ $h['quantity'] > 0 ? number_format($h['total_cost_converted'] / $h['quantity'], 2) : '0.00' }} {{ Auth::user()->currency }}
                                            </td>
                                            <td class="border-secondary text-end align-middle text-info fw-bold">
                                                {{ number_format($h['current_price_converted'], 2) }} {{ Auth::user()->currency }}
                                            </td>
                                            <td class="border-secondary text-end align-middle fw-bold">
                                                {{ number_format($h['current_value_converted'], 2) }} {{ Auth::user()->currency }}
                                            </td>
                                            <td class="border-secondary text-end align-middle px-3 {{ $h['profit_loss'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $h['profit_loss'] >= 0 ? '+' : '' }}{{ number_format($h['profit_loss'], 2) }} {{ Auth::user()->currency }}
                                                <div class="small fw-normal">({{ number_format($h['profit_loss_pct'], 2) }}%)</div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5 border-secondary">Brak aktywów w portfelu.</td>
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

    <!-- MODAL: Dodaj portfel -->
    <div class="modal fade" id="addPortfolioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border-secondary text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold">Dodaj nowy portfel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('portfolios.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Nazwa (np. Mój Binance, XTB)</label>
                            <input type="text" name="name" class="form-control bg-dark text-light border-secondary" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Kategoria</label>
                            <select name="category" class="form-select bg-dark text-light border-secondary" required>
                                <option value="broker">Broker (Akcje, ETF)</option>
                                <option value="exchange">Giełda (Krypto)</option>
                                <option value="alternative">Alternatywne (Gotówka, Metale)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-primary fw-bold">Zapisz portfel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: Dodaj transakcję -->
    <div class="modal fade" id="addTransactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border-secondary text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold">Dodaj transakcję</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addTransactionForm" action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label text-muted small">Portfel docelowy</label>
                            <select name="portfolio_id" id="portfolio_select" class="form-select bg-dark text-light border-secondary" required>
                                @foreach($portfolios as $p)
                                    <option value="{{ $p->id }}" data-category="{{ $p->category }}">{{ $p->name }} ({{ ucfirst($p->category) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 position-relative">
                            <label class="form-label text-muted small">Wyszukaj aktywo (min. 2 znaki)</label>
                            <input type="text" id="asset_search" class="form-control bg-dark text-light border-secondary" placeholder="np. AAPL, Tesla, BTC" autocomplete="off" required>
                            <ul id="search_results" class="list-group position-absolute w-100 mt-1 shadow-lg" style="display:none; z-index:1000; max-height:200px; overflow-y:auto;"></ul>

                            <input type="hidden" name="asset_ticker"       id="hidden_asset_ticker">
                            <input type="hidden" name="asset_name"         id="hidden_asset_name">
                            <input type="hidden" name="asset_type"         id="hidden_asset_type">
                            <input type="hidden" name="asset_currency"     id="hidden_asset_currency" value="USD">
                            <input type="hidden" name="asset_price_source" id="hidden_asset_price_source">
                            {{-- KLUCZOWE: kurs waluty do PLN w momencie transakcji --}}
                            <input type="hidden" name="exchange_rate_pln"  id="hidden_exchange_rate" value="1.0">
                        </div>

                        {{-- DEMO v1: tylko kupno – sprzedaż wyłączona w UI (kod sell zachowany poniżej) --}}
                        <input type="hidden" name="type" value="buy">
                        {{--
                        <div class="mb-3">
                            <label class="form-label text-muted small">Rodzaj transakcji</label>
                            <select name="type" class="form-select bg-dark text-light border-secondary" required>
                                <option value="buy">Kupno</option>
                                <option value="sell">Sprzedaż</option>
                            </select>
                        </div>
                        --}}

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small">Ilość</label>
                                <input type="number" id="quantity_input" step="0.00000001" min="0.00000001" name="quantity" class="form-control bg-dark text-light border-secondary" placeholder="0.00" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small">Cena za jedn. (<span id="currency_indicator" class="text-white fw-bold">USD</span>)</label>
                                <input type="number" id="price_input" step="0.0001" min="0.0001" name="price_per_unit" class="form-control bg-dark text-light border-secondary" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="small text-warning mb-3" id="api_error" style="display:none;"></div>
                        <div class="small text-danger mb-3" id="form_error" style="display:none;"></div>

                        <div class="mb-3">
                            <label class="form-label text-muted small">Data transakcji</label>
                            <input type="datetime-local" id="date_input" name="transaction_date" class="form-control bg-dark text-light border-secondary" required>
                        </div>

                        <div class="p-3 rounded mb-3" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted fw-bold">WARTOŚĆ CAŁKOWITA:</span>
                                <span id="total_amount_display" class="fw-bold fs-5">0.00 USD</span>
                            </div>
                            {{-- Info o kursie PLN dla użytkownika --}}
                            <div class="small text-muted mt-1" id="rate_info" style="display:none;">
                                Kurs: 1 <span id="rate_currency">USD</span> = <span id="rate_value">1.00</span> PLN
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-success fw-bold" id="save_transaction_btn">Zapisz transakcję</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Chart.js z CDN + Bootstrap modal --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{--
        SKRYPTY DASHBOARDU (vanilla JS, bez frameworka)
        Uruchamiaja sie po DOMContentLoaded - cala strona juz w DOM.
        Dane z PHP: $chartLabels, $chartValues, $chartHistory (PortfolioService).
        AJAX do Laravel: /api/search, /api/price (PortfolioController -> ApiService).
    --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // ========== WYKRES KOLowy (donut) – alokacja portfela ==========
        // Etykiety i wartosci liczone w @php na gorze blade (ticker + wartosc pozycji)
        const donutCtx = document.getElementById('donutChart');
        if (donutCtx) {
            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        data: {!! json_encode($chartValues) !!},
                        backgroundColor: ['#3b82f6','#8b5cf6','#22c55e','#f59e0b','#ef4444','#14b8a6'],
                        borderWidth: 0, hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'right', labels: { color: '#adb5bd', font: { size: 11 } } } }
                }
            });
        }

        // ========== WYKRES LINIOWY – rozwoj portfela w czasie ==========
        const lineCtx = document.getElementById('lineChart');
        let lineChart; // referencja potrzebna do lineChart.update() przy zmianie filtra
        if (lineCtx) {
            // Historia z serwera: labels = daty, values = wartosc skumulowana w walucie usera
            const historyLabels = {!! json_encode($chartHistory['labels'] ?? []) !!};
            const historyValues = {!! json_encode($chartHistory['values'] ?? []) !!};
            const preferredRange = @json(Auth::user()->default_chart_range ?? 'ALL');

            // Filtruje punkty wykresu po przycisku 1D/7D/1M/1Y/ALL (tylko po stronie klienta)
            function getFilteredHistory(timeFilter) {
                if (timeFilter === 'ALL' || historyLabels.length <= 2) {
                    return { labels: historyLabels, values: historyValues };
                }

                const daysMap = { '1D': 1, '7D': 7, '1M': 30, '1Y': 365 };
                const days = daysMap[timeFilter] ?? 365;
                const threshold = new Date();
                threshold.setDate(threshold.getDate() - days);

                const filtered = historyLabels.reduce((acc, label, idx) => {
                    const date = new Date(label);
                    if (!Number.isNaN(date.getTime()) && date >= threshold) {
                        acc.labels.push(label);
                        acc.values.push(historyValues[idx]);
                    }
                    return acc;
                }, { labels: [], values: [] });

                // jak za malo punktow po filtrze - pokaz ostatnie 2 z pelnej historii
                if (filtered.labels.length >= 2) {
                    return filtered;
                }

                const start = Math.max(0, historyLabels.length - 2);
                return {
                    labels: historyLabels.slice(start),
                    values: historyValues.slice(start),
                };
            }

            const initialData = getFilteredHistory(preferredRange);

            lineChart = new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: initialData.labels,
                    datasets: [{
                        label: 'Wartość', data: initialData.values,
                        borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)',
                        borderWidth: 2, fill: true, tension: 0.4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: { y: { display: false }, x: { grid: { display: false }, ticks: { color: '#adb5bd' } } },
                    plugins: { legend: { display: false } }
                }
            });

            // Przelacznik zakresu – podmienia dane w istniejacym wykresie bez przeładowania strony
            document.querySelectorAll('#chartTimeFilters button').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('#chartTimeFilters button').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const time = this.getAttribute('data-time');
                    const filtered = getFilteredHistory(time);
                    lineChart.data.labels = filtered.labels;
                    lineChart.data.datasets[0].data = filtered.values;
                    lineChart.update();
                });
            });
        }

        // ========== MODAL DODAWANIA TRANSAKCJI ==========
        // Referencje do pol formularza – latwiej niz document.getElementById w kazdej funkcji
        const portfolioSelect  = document.getElementById('portfolio_select');
        const searchInput      = document.getElementById('asset_search');
        const searchResults    = document.getElementById('search_results');
        const dateInput        = document.getElementById('date_input');
        const priceInput       = document.getElementById('price_input');
        const quantityInput    = document.getElementById('quantity_input');
        const totalDisplay     = document.getElementById('total_amount_display');
        const currencyIndicator = document.getElementById('currency_indicator');
        const transactionForm  = document.getElementById('addTransactionForm');
        const saveBtn          = document.getElementById('save_transaction_btn');
        const apiError         = document.getElementById('api_error');
        const formError        = document.getElementById('form_error');
        const hiddenRate       = document.getElementById('hidden_exchange_rate');
        const rateInfo         = document.getElementById('rate_info');
        const rateCurrency     = document.getElementById('rate_currency');
        const rateValue        = document.getElementById('rate_value');

        let timeoutId;       // debounce autocomplete (500 ms)
        let nbpRates = {};   // cache kursow – uzupelniane przy starcie strony

        // Kursy NBP po stronie przegladarki (to samo zrodlo co ApiService po stronie serwera)
        // Trafia do hidden exchange_rate_pln – backend liczy total_cost_pln
        async function fetchNbpRates() {
            try {
                const res = await fetch('http://api.nbp.pl/api/exchangerates/tables/A/?format=json');
                const data = await res.json();
                data[0].rates.forEach(r => { nbpRates[r.code] = r.mid; });
                nbpRates['PLN'] = 1.0;
            } catch (e) {
                // offline / blad NBP – te same fallbacki co w ApiService
                nbpRates = { USD: 4.00, EUR: 4.30, GBP: 5.00, PLN: 1.0 };
            }
        }
        fetchNbpRates();

        function getExchangeRate(currency) {
            const code = (currency || 'PLN').toUpperCase();
            return nbpRates[code] ?? 1.0;
        }

        // datetime-local wymaga formatu YYYY-MM-DDTHH:mm w strefie lokalnej
        function setCurrentDate() {
            const now = new Date();
            const offset = now.getTimezoneOffset() * 60000;
            dateInput.value = (new Date(now - offset)).toISOString().slice(0, 16);
        }

        // Reset modala po zamknieciu / ponownym otwarciu – zeby nie zostaly stare dane
        function clearModal() {
            transactionForm.reset();
            document.getElementById('hidden_asset_ticker').value    = '';
            document.getElementById('hidden_asset_currency').value  = 'USD';
            hiddenRate.value = '1.0';
            currencyIndicator.innerText = 'USD';
            totalDisplay.innerText = '0.00 USD';
            searchResults.style.display = 'none';
            rateInfo.style.display = 'none';
            apiError.style.display = 'none';
            apiError.innerText = '';
            formError.style.display = 'none';
            formError.innerText = '';
            saveBtn.disabled = true;
            setCurrentDate();
        }

        document.getElementById('addTransactionModal').addEventListener('hidden.bs.modal', clearModal);
        document.querySelector('[data-bs-target="#addTransactionModal"]').addEventListener('click', clearModal);

        // Pokazuje kurs waluty aktywa -> PLN i zapisuje go w ukrytym polu formularza
        function updateRateDisplay(currency) {
            const rate = getExchangeRate(currency);
            hiddenRate.value = rate;

            if (currency !== 'PLN') {
                rateCurrency.innerText = currency;
                rateValue.innerText    = rate.toFixed(4);
                rateInfo.style.display = 'block';
            } else {
                rateInfo.style.display = 'none';
            }
        }

        // Podglad: ilosc * cena w walucie aktywa (nie PLN – to liczy serwer)
        function calculateTotal() {
            const qty      = parseFloat(quantityInput.value) || 0;
            const price    = parseFloat(priceInput.value) || 0;
            const currency = document.getElementById('hidden_asset_currency').value || 'USD';
            totalDisplay.innerText = (qty * price).toLocaleString(undefined, {
                minimumFractionDigits: 2, maximumFractionDigits: 2
            }) + ' ' + currency;
            currencyIndicator.innerText = currency;
        }

        // Przycisk Zapisz wylaczony dopoki qty i cena > 0 (dubluje walidacje gt:0 w TransactionController)
        function validateForm() {
            const qty = parseFloat(quantityInput.value);
            const price = parseFloat(priceInput.value);
            const hasQty = Number.isFinite(qty) && qty > 0;
            const hasPrice = Number.isFinite(price) && price > 0;

            if (!hasQty || !hasPrice) {
                formError.innerText = 'Ilosc i cena musza byc wieksze od zera.';
                formError.style.display = 'block';
                saveBtn.disabled = true;
                return;
            }

            formError.style.display = 'none';
            saveBtn.disabled = false;
        }

        // GET /api/price/{ticker} – cena z Yahoo/Binance na wybrana date transakcji
        async function fetchPrice(ticker, source) {
            const date = dateInput.value;
            priceInput.placeholder = 'Pobieranie API...';
            priceInput.value = '';
            calculateTotal();
            validateForm();
            try {
                const res  = await fetch(`/api/price/${ticker}?date=${date}&source=${source}`);
                const data = await res.json();
                if (data.price) {
                    priceInput.value = parseFloat(data.price).toFixed(4);
                    apiError.style.display = 'none';
                    apiError.innerText = '';
                    calculateTotal();
                    validateForm();
                } else {
                    // API null – user moze wpisac cene recznie (offline / brak notowania)
                    priceInput.placeholder = 'Wpisz ręcznie (Brak danych)';
                    apiError.innerText = 'Brak danych z API dla tego aktywa/daty. Wpisz cene recznie.';
                    apiError.style.display = 'block';
                    validateForm();
                }
            } catch (e) {
                priceInput.placeholder = 'Błąd API';
                apiError.innerText = 'API nie odpowiada. Wpisz cene recznie.';
                apiError.style.display = 'block';
                validateForm();
            }
        }

        // AUTocomplete aktyw – GET /api/search?q=...&category=broker|exchange
        // category z data-category opcji portfela (akcje vs krypto)
        searchInput.addEventListener('input', function(e) {
            clearTimeout(timeoutId);
            const query    = e.target.value;
            const category = portfolioSelect.options[portfolioSelect.selectedIndex].getAttribute('data-category');
            if (query.length < 2) { searchResults.style.display = 'none'; return; }

            // debounce 500ms – nie strzelamy do API przy kazdej literze
            timeoutId = setTimeout(async () => {
                const res  = await fetch(`/api/search?q=${query}&category=${category}`);
                const data = await res.json();
                searchResults.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item list-group-item-action bg-dark text-light border-secondary cursor-pointer';
                        li.innerHTML = `<strong>${item.ticker}</strong> - ${item.name} <span class="badge bg-secondary float-end">${item.currency}</span>`;
                        li.onclick = function() {
                            const currency = item.currency || 'USD';

                            // hidden pola – wysylane POSTem do TransactionController@store
                            document.getElementById('hidden_asset_ticker').value       = item.ticker;
                            document.getElementById('hidden_asset_name').value         = item.name;
                            document.getElementById('hidden_asset_type').value         = item.type;
                            document.getElementById('hidden_asset_currency').value     = currency;
                            document.getElementById('hidden_asset_price_source').value = item.price_source;

                            searchInput.value = `${item.ticker} - ${item.name}`;
                            searchResults.style.display = 'none';

                            updateRateDisplay(currency);
                            calculateTotal();
                            validateForm();
                            fetchPrice(item.ticker, item.price_source);
                        };
                        searchResults.appendChild(li);
                    });
                    searchResults.style.display = 'block';
                }
            }, 500);
        });

        // Klik poza lista – chowamy wyniki wyszukiwania
        document.addEventListener('click', e => {
            if (e.target !== searchInput) searchResults.style.display = 'none';
        });

        // Zmiana daty – pobierz cene historyczna jesli aktywo juz wybrane
        dateInput.addEventListener('change', () => {
            const ticker = document.getElementById('hidden_asset_ticker').value;
            const source = document.getElementById('hidden_asset_price_source').value;
            if (ticker) fetchPrice(ticker, source);
        });

        quantityInput.addEventListener('input', () => { calculateTotal(); validateForm(); });
        priceInput.addEventListener('input', () => { calculateTotal(); validateForm(); });
        setCurrentDate();
    });
    </script>
</body>
</html>