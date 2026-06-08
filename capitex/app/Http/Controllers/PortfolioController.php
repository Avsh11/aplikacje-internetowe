<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Portfolio;
use App\Models\Asset;
use App\Services\PortfolioService;

// Kontroler portfeli + endpointy API pod formularz transakcji (szukaj aktywa, pobierz cene)
// Trasy w web.php (middleware auth):
//   POST   /portfolios              -> store
//   GET    /portfolios/{id}         -> show
//   DELETE /portfolios/{portfolio}  -> destroy
//   GET    /api/search?q=...        -> searchAssets (JSON dla autocomplete)
//   GET    /api/price/{ticker}      -> getAssetPrice (JSON cena na date)
// CRUD portfela w bazie jest w PortfolioService, tu walidacja requestu i odpowiedz HTML/JSON

class PortfolioController extends Controller
{
    protected PortfolioService $portfolioService;

    // DI - ten sam serwis co w DashboardController (portfele, holdings, usuwanie)
    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    // POST /portfolios (name: portfolios.store)
    // Formularz z dashboardu: nazwa + kategoria (broker / exchange / alternative)

    public function store(Request $request)
    {
        // validate() - jak fail to automatyczny redirect back z bledami (@error w blade)
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:broker,exchange,alternative'],
        ]);

        // createPortfolio() dopina user_id z Auth::user() w serwisie
        $this->portfolioService->createPortfolio($validated);

        // back() = wracamy na poprzednia strone (dashboard), status w sesji pod komunikat
        return back()->with('status', 'Portfel dodany pomyślnie!');
    }

    // GET /portfolios/{id} (name: portfolios.show)
    // Ten sam widok co glowny dashboard, ale getHoldings($id) liczy tylko jeden portfel

    public function show($id)
    {
        // $id z URL to portfolio_id (nie Route Model Binding - zwykly parametr)
        $portfolios = $this->portfolioService->getUserPortfolios();
        $assets = Asset::all();

        // przekazujemy $id do serwisu - filtr where portfolio_id w zapytaniu o transakcje
        $data = $this->portfolioService->getHoldings($id);

        // ten sam blade user/dashboard - rozroznienie po aktywnym portfelu w UI (lewy panel)
        return view('user.dashboard', [
            'portfolios'   => $portfolios,
            'assets'       => $assets,
            'holdings'     => $data['assets'],
            'totalValue'   => $data['total_value'],
            'chartHistory' => $data['chart_history'],
            'isAdmin'      => (int) Auth::user()->role_id === 1,
        ]);
    }

    // DELETE /portfolios/{portfolio} (name: portfolios.destroy)
    // Portfolio $portfolio = Route Model Binding (Laravel szuka po ID z URL)

    public function destroy(Portfolio $portfolio)
    {
        // w serwisie sprawdzamy portfolio->user_id === Auth::id() zeby nie usunac cudzego
        $this->portfolioService->deletePortfolio($portfolio);

        return redirect()->route('dashboard')->with('status', 'Portfel usunięty!');
    }

    // GET /api/search?q=XTB&category=broker
    // Wywolywane z JS (fetch) przy wpisywaniu tickera w modalu transakcji - zwraca JSON, nie HTML

    public function searchAssets(Request $request)
    {
        // query() bierze parametry z URL (?q=...&category=...)
        $query = $request->query('q');
        $category = $request->query('category');

        // ApiService tu tworzymy recznie (prosto dla 2 endpointow API, bez DI w konstruktorze)
        $api = new \App\Services\ApiService();

        // Portfele alternatywne – reczny opis zamiast wyszukiwarki API
        if ($category === 'alternative') {
            return response()->json([]);
        }

        // exchange = krypto (Binance), reszta = akcje/ETF (Yahoo)
        return response()->json(
            ($category === 'exchange') ? $api->searchCrypto($query) : $api->searchStocks($query)
        );
    }

    // GET /api/price/{ticker}?date=2024-01-15&source=binance
    // Frontend pyta o cene na wybrana date (buy) - odpowiedz { "price": 123.45 }

    public function getAssetPrice(Request $request, $ticker)
    {
        $api = new \App\Services\ApiService();

        // jesli aktywo juz bylo w bazie (wczesniejsza transakcja) - znamy type crypto/stock
        $asset = Asset::where('ticker', $ticker)->first();
        $date = $request->query('date');
        $source = $request->query('source'); // binance vs yahoo gdy aktywa jeszcze nie ma w assets

        if ($asset) {
            $price = ($asset->type === 'crypto')
                ? $api->getCryptoPrice($ticker, $date)
                : $api->getStockPrice($ticker, $date);
        } else {
            // nowe aktywo w formularzu - rozrozniamy po source z frontu
            $price = ($source === 'binance')
                ? $api->getCryptoPrice($ticker, $date)
                : $api->getStockPrice($ticker, $date);
        }

        return response()->json(['price' => $price]);
    }
}
