<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\PortfolioService;
use App\Models\Asset;

// Kontroler glownego dashboardu po zalogowaniu
// Trasa: GET /dashboard (name: dashboard) w web.php, middleware auth + verified
// To jest "router" widoku: user i admin dostaja panel portfela; admin ma dodatkowy link do /admin
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

        if (! in_array($roleId, [1, 2], true)) {
            abort(403, 'Brak uprawnien');
        }

        $portfolios = $this->portfolioService->getUserPortfolios();
        $assets = Asset::all();
        $data = $this->portfolioService->getHoldings();

        return view('user.dashboard', [
            'portfolios'     => $portfolios,
            'assets'         => $assets,
            'holdings'       => $data['assets'],
            'totalValue'     => $data['total_value'],
            'chartHistory'   => $data['chart_history'],
            'isAdmin'        => $roleId === 1,
        ]);
    }
}
