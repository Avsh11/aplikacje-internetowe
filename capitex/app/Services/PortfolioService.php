<?php

namespace App\Services;

use App\Models\Portfolio;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

// Serwis portfela - CRUD portfeli + glowne liczenia dashboardu
// Wywolywany z: DashboardController, PortfolioController
// DI: ApiService (ceny rynkowe + kursy NBP)
// Najwazniejsza metoda: getHoldings() -> holdings, total_value, chart_history

class PortfolioService
{
    protected ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    // Wszystkie portfele zalogowanego usera (relacja User hasMany Portfolio)

    public function getUserPortfolios()
    {
        return Auth::user()->portfolios()->get();
    }

    // POST /portfolios - user_id ustawia Laravel przez portfolios()->create()

    public function createPortfolio(array $data)
    {
        return Auth::user()->portfolios()->create([
            'name'     => $data['name'],
            'category' => $data['category'],
        ]);
    }

    // DELETE /portfolios/{portfolio}

    public function deletePortfolio(Portfolio $portfolio)
    {
        if ($portfolio->user_id !== Auth::id()) {
            abort(403, 'Brak uprawnień.');
        }

        return $portfolio->delete();
    }

    // GET dashboard lub GET /portfolios/{id}
    // $portfolioId null = wszystkie portfele usera; liczba = tylko jeden portfel
    // Zwraca tablice: assets (pozycje), total_value, chart_history (labels + values pod Chart.js)

    public function getHoldings($portfolioId = null)
    {
        $user = Auth::user();

        // transakcje tylko z portfeli tego usera (+ eager load asset)
        $query = Transaction::with('asset')
            ->whereHas('portfolio', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });

        if ($portfolioId) {
            $query->where('portfolio_id', $portfolioId);
        }

        $transactions = $query->get();
        $holdings = [];

        // Krok 1: z wielu transakcji robimy jedna pozycje per asset_id (suma buy)
        foreach ($transactions as $t) {
            $assetId = $t->asset_id;

            if (!isset($holdings[$assetId])) {
                $holdings[$assetId] = [
                    'asset'          => $t->asset,
                    'quantity'       => 0,
                    'total_cost_pln' => 0,
                ];
            }

            if ($t->type === 'buy') {
                $holdings[$assetId]['quantity']       += (float) $t->quantity;
                $holdings[$assetId]['total_cost_pln'] += (float) $t->total_cost_pln;
            } else {
                // Sell: odejmujemy ilosc i proporcjonalny koszt
                // DEMO v1: logika sell wylaczona - odkomentowac przy wlaczeniu sprzedazy w UI
                /*
                $avgCostPln = $holdings[$assetId]['quantity'] > 0
                    ? $holdings[$assetId]['total_cost_pln'] / $holdings[$assetId]['quantity']
                    : 0;
                $holdings[$assetId]['quantity']       -= (float)$t->quantity;
                $holdings[$assetId]['total_cost_pln'] -= $avgCostPln * (float)$t->quantity;
                */
            }
        }

        $holdings = array_filter($holdings, fn ($h) => $h['quantity'] > 0.000001);

        // Krok 2: kursy walut - wszystko liczymy przez PLN, potem dzielimy przez kurs waluty usera
        $rates = $this->apiService->getExchangeRates();
        $rateUser = $rates[$user->currency] ?? 1.0;

        $totalPortfolioValue = 0;

        foreach ($holdings as &$h) {
            $asset = $h['asset'];
            $assetCurrency = strtoupper($asset->currency ?? 'USD');
            if (str_ends_with($asset->ticker, '.WA')) {
                $assetCurrency = 'PLN';
            } elseif (str_ends_with($asset->ticker, '.DE')) {
                $assetCurrency = 'EUR';
            }
            $rateAsset = $rates[$assetCurrency] ?? 1.0;

            // Cena z API w walucie notowania aktywa
            $currentPrice = ($asset->type === 'crypto')
                ? $this->apiService->getCryptoPrice($asset->ticker)
                : $this->apiService->getStockPrice($asset->ticker);

            // Jak API padnie - srednia cena zakupu z total_cost_pln
            $fallbackPrice = $h['quantity'] > 0 && $rateAsset > 0
                ? ($h['total_cost_pln'] / $h['quantity']) / $rateAsset
                : 0;

            $finalPrice = $currentPrice ?? $fallbackPrice;

            // Wartosc pozycji w PLN: ilosc * cena * kurs waluty aktywa do PLN
            $currentValuePln = $h['quantity'] * $finalPrice * $rateAsset;

            // Pola pod blade: ceny i P/L w walucie usera (np. PLN, USD)
            $h['current_price_converted'] = $rateUser > 0
                ? ($finalPrice * $rateAsset) / $rateUser
                : 0;
            $h['current_value_converted'] = $rateUser > 0
                ? $currentValuePln / $rateUser
                : 0;
            $h['total_cost_converted'] = $rateUser > 0
                ? $h['total_cost_pln'] / $rateUser
                : 0;

            $h['profit_loss'] = $h['current_value_converted'] - $h['total_cost_converted'];
            $h['profit_loss_pct'] = $h['total_cost_converted'] > 0
                ? ($h['profit_loss'] / $h['total_cost_converted']) * 100
                : 0;

            $totalPortfolioValue += $h['current_value_converted'];
        }

        // Krok 3: prosta historia pod wykres (nie pelny mark-to-market kazdego dnia)
        $chartHistory = $this->buildPortfolioHistory($transactions, $totalPortfolioValue, $rateUser);

        return [
            'assets'        => $holdings,
            'total_value'   => $totalPortfolioValue,
            'chart_history' => $chartHistory,
        ];
    }

    // Prywatna metoda - sklada labels[] i values[] dla Chart.js na dashboardzie
    // Logika: skumulowane koszty buy po dniach + ostatni punkt = biezaca wartosc portfela

    private function buildPortfolioHistory($transactions, float $currentPortfolioValue, float $rateUser): array
    {
        if ($rateUser <= 0) {
            $rateUser = 1.0;
        }

        $sortedTransactions = $transactions->sortBy('transaction_date');
        $dailyFlowPln = [];

        foreach ($sortedTransactions as $transaction) {
            $dateKey = $transaction->transaction_date->format('Y-m-d');
            $amountPln = (float) $transaction->total_cost_pln;
            // DEMO v1: tylko kupna; sell: -$amountPln wylaczone
            $dailyFlowPln[$dateKey] = ($dailyFlowPln[$dateKey] ?? 0)
                + ($transaction->type === 'buy' ? $amountPln : 0);
        }

        $labels = [];
        $values = [];
        $cumulativePln = 0.0;

        foreach ($dailyFlowPln as $date => $flowPln) {
            $cumulativePln += $flowPln;
            $labels[] = $date;
            $values[] = round($cumulativePln / $rateUser, 2);
        }

        $todayLabel = now()->format('Y-m-d');
        $currentValueRounded = round($currentPortfolioValue, 2);

        if (empty($labels)) {
            $labels[] = $todayLabel;
            $values[] = $currentValueRounded;
        } elseif (end($labels) === $todayLabel) {
            $values[count($values) - 1] = $currentValueRounded;
        } else {
            $labels[] = $todayLabel;
            $values[] = $currentValueRounded;
        }

        // jeden punkt = Chart.js slabo rysuje - dodajemy sztuczny punkt dzien wczesniej z 0
        if (count($labels) === 1) {
            $firstDate = \Carbon\Carbon::parse($labels[0])->subDay()->format('Y-m-d');
            array_unshift($labels, $firstDate);
            array_unshift($values, 0.0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
