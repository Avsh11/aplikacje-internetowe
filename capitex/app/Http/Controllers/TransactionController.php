<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TransactionService;
use App\Models\Asset;
use App\Models\Transaction;

// Kontroler transakcji buy (historia + dodawanie + usuwanie)
// Trasy w web.php (middleware auth):
//   GET    /transactions              -> index   (historia)
//   POST   /transactions              -> store   (z modala na dashboardzie)
//   DELETE /transactions/{transaction} -> destroy
// Zapis do tabeli transactions + wyliczenie total_cost_pln w TransactionService
// DEMO v1: tylko typ buy (sell wylaczony w walidacji i w PortfolioService)

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    // GET /transactions (name: transactions.index)
    // Osobna strona z tabela wszystkich transakcji usera

    public function index()
    {
        // whereHas portfolio.user_id = Auth::id() w serwisie - nie widzimy cudzych transakcji
        $transactions = $this->transactionService->getUserTransactions();

        // compact('transactions') = ['transactions' => $transactions] do blade
        return view('user.transactions', compact('transactions'));
    }

    // POST /transactions (name: transactions.store)
    // Dane leca z formularza/modalu na dashboardzie (portfolio_id, ticker, cena, data...)

    public function store(Request $request)
    {
        $validated = $request->validate([
            'portfolio_id'       => 'required|exists:portfolios,id',
            'asset_ticker'       => 'required|string',
            'asset_name'         => 'required|string',
            'asset_type'         => 'required|string',
            'asset_currency'     => 'required|string',
            'asset_price_source' => 'required|string',
            // DEMO v1: tylko buy - przy sell odkomentowac druga linie i logike w PortfolioService
            'type'               => 'required|in:buy',
            // 'type'            => 'required|in:buy,sell',
            // gt:0 = ilosc i cena musza byc wieksze od zera (ochrona przed smieciowymi danymi)
            'quantity'           => 'required|numeric|gt:0',
            'price_per_unit'     => 'required|numeric|gt:0',
            'transaction_date'   => 'required|date',
            // kurs aktywa -> PLN z frontu (NBP / recznie) - potrzebny do total_cost_pln
            'exchange_rate_pln'  => 'required|numeric|min:0.0001',
        ]);

        // Slownik aktywow: jeden ticker = jeden wiersz w tabeli assets
        // firstOrNew: jesli ticker istnieje - aktualizujemy metadane (np. currency po poprawce API)
        $asset = Asset::firstOrNew(['ticker' => $validated['asset_ticker']]);
        $asset->name = $validated['asset_name'];
        $asset->type = $validated['asset_type'];
        $asset->currency = strtoupper($validated['asset_currency']);
        $asset->price_source = $validated['asset_price_source'];
        $asset->save();

        // asset_id wymagany w Transaction::create w serwisie
        $validated['asset_id'] = $asset->id;

        // serwis liczy total_cost_pln = quantity * price_per_unit * exchange_rate_pln
        $this->transactionService->createTransaction($validated);

        return back()->with('status', 'Transakcja zapisana pomyślnie!');
    }

    // DELETE /transactions/{transaction} (name: transactions.destroy)
    // Transaction $transaction = Route Model Binding

    public function destroy(Transaction $transaction)
    {
        // sprawdzenie czy transakcja nalezy do portfela zalogowanego usera - w serwisie
        $this->transactionService->deleteTransaction($transaction);

        return back()->with('status', 'Transakcja została usunięta!');
    }
}
