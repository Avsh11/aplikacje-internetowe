<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

// Serwis transakcji - zapis, lista, usuwanie
// Wywolywany z: TransactionController
// Kontroler najpierw tworzy/aktualizuje Asset, potem przekazuje tablice z asset_id tutaj

class TransactionService
{
    // POST /transactions - jeden wiersz w tabeli transactions

    public function createTransaction(array $data)
    {
        $exchangeRatePln = (float) $data['exchange_rate_pln'];

        // Koszt calkowity w PLN - uzywany pozniej w PortfolioService (P/L, wykres)
        // wzor: ilosc * cena_w_walucie_aktywa * kurs_waluty_aktywa_do_PLN
        // np. 2 * 370 PLN * 1.0 = 740 PLN (XTB.WA)
        // np. 0.5 * 90000 USD * 4.0 = 180000 PLN (BTC w USD, kurs z NBP)
        $totalCostPln = (float) $data['quantity']
            * (float) $data['price_per_unit']
            * $exchangeRatePln;

        return Transaction::create([
            'portfolio_id'      => $data['portfolio_id'],
            'asset_id'          => $data['asset_id'],
            'type'              => $data['type'],
            'quantity'          => $data['quantity'],
            'price_per_unit'    => $data['price_per_unit'],
            'currency'          => strtoupper($data['asset_currency']),
            'exchange_rate_pln' => $exchangeRatePln,
            'total_cost_pln'    => $totalCostPln,
            'transaction_date'  => $data['transaction_date'],
        ]);
    }

    // GET /transactions - historia dla zalogowanego usera

    public function getUserTransactions(array $filters = [])
    {
        $query = Transaction::with(['asset', 'portfolio'])
            ->whereHas('portfolio', function ($query) {
                $query->where('user_id', Auth::id());
            });

        if (! empty($filters['ticker'])) {
            $query->whereHas('asset', function ($query) use ($filters) {
                $query->where('ticker', 'like', '%' . $filters['ticker'] . '%');
            });
        }

        if (! empty($filters['name'])) {
            $query->whereHas('asset', function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            });
        }

        if (! empty($filters['portfolio'])) {
            $query->whereHas('portfolio', function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['portfolio'] . '%');
            });
        }

        $sort = $filters['sort'] ?? 'date_desc';

        if ($sort === 'ticker') {
            $query->join('assets as sort_assets', 'transactions.asset_id', '=', 'sort_assets.id')
                ->orderBy('sort_assets.ticker')
                ->select('transactions.*');
        } elseif ($sort === 'asset_name') {
            $query->join('assets as sort_assets', 'transactions.asset_id', '=', 'sort_assets.id')
                ->orderBy('sort_assets.name')
                ->select('transactions.*');
        } elseif ($sort === 'portfolio_name') {
            $query->join('portfolios as sort_portfolios', 'transactions.portfolio_id', '=', 'sort_portfolios.id')
                ->orderBy('sort_portfolios.name')
                ->select('transactions.*');
        } elseif ($sort === 'date_asc') {
            $query->orderBy('transaction_date', 'asc');
        } else {
            $query->orderBy('transaction_date', 'desc');
        }

        return $query->get();
    }

    // DELETE /transactions/{transaction}

    public function deleteTransaction(Transaction $transaction)
    {
        // portfolio->user_id musi byc nasz - inaczej 403 (ktos podal cudze ID w URL)
        if ($transaction->portfolio->user_id !== Auth::id()) {
            abort(403, 'Brak uprawnień do usunięcia tej transakcji.');
        }

        return $transaction->delete();
    }
}
