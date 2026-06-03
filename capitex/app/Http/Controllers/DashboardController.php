<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\PortfolioService;
use App\Models\Asset;

// Kontroler glownego dashboardu po zalogowaniu
// Trasa: GET /dashboard (name: dashboard) w web.php, middleware auth + verified
// To jest "router" widoku: admin leci gdzie indziej, user dostaje panel inwestycyjny
// Ciezka logike liczenia portfela trzymamy w PortfolioService, tu tylko zbieramy dane i przekazujemy do blade

class DashboardController extends Controller
{
    protected PortfolioService $portfolioService;

    // Dependency Injection: Laravel sam wstrzykuje PortfolioService przy tworzeniu kontrolera
    // Dzieki temu nie robimy "new PortfolioService()" recznie w metodach
    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    // GET /dashboard
    // Jedno wejscie po logowaniu - rozroznia role i wybiera wlasciwy widok / przekierowanie

    public function index()
    {
        // role_id z bazy: 1 = admin, 2 = zwykly user (patrz seeder / tabela roles)
        $roleId = (int) Auth::user()->role_id;

        // Admin nie widzi wykresow i portfeli - ma osobny panel pod /admin/dashboard
        if ($roleId === 1) {
            return redirect()->route('admin.dashboard');
        }

        if ($roleId === 2) {
            // Lista portfeli zalogowanego usera (lewy panel, filtry)
            $portfolios = $this->portfolioService->getUserPortfolios();

            // Wszystkie aktywa z tabeli assets - slownik do selecta w modalu "dodaj transakcje"
            $assets = Asset::all();

            // getHoldings() liczy: pozycje (holdings), wartosc calego portfela, dane pod wykres liniowy
            // opcjonalnie mozna podac portfolioId - u nas na glownym dashboardzie null = wszystkie portfele
            $data = $this->portfolioService->getHoldings();

            // Przekazujemy tablice do user/dashboard.blade.php - tam @foreach, Chart.js itd.
            return view('user.dashboard', [
                'portfolios'   => $portfolios,
                'assets'       => $assets,
                'holdings'     => $data['assets'],        // tablica pozycji z P/L, cena, ilosc
                'totalValue'   => $data['total_value'],   // suma wartosci w walucie usera
                'chartHistory' => $data['chart_history'], // punkty pod wykres w czasie
            ]);
        }

        // Jak kiedys dodamy inna role albo cos sie rozjedzie w bazie - bezpieczny stop
        abort(403, 'Brak uprawnien');
    }
}
