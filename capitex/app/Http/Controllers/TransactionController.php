<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\TransactionService;
use App\Models\Asset;
use App\Models\Portfolio;
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

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'ticker', 'portfolio', 'sort']);
        $transactions = $this->transactionService->getUserTransactions($filters);

        return view('user.transactions', [
            'transactions' => $transactions,
            'filters' => $filters,
        ]);
    }

    // POST /transactions (name: transactions.store)
    // Dane leca z formularza/modalu na dashboardzie (portfolio_id, ticker, cena, data...)

    public function store(Request $request)
    {
        $portfolio = Portfolio::where('id', $request->input('portfolio_id'))
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($portfolio->category === 'alternative') {
            return $this->storeAlternativeTransaction($request);
        }

        return $this->storeMarketTransaction($request);
    }

    private function storeMarketTransaction(Request $request)
    {
        $validated = $request->validate([
            'portfolio_id'       => 'required|exists:portfolios,id',
            'asset_ticker'       => 'required|string',
            'asset_name'         => 'required|string',
            'asset_type'         => 'required|string',
            'asset_currency'     => 'required|string',
            'asset_price_source' => 'required|string',
            'type'               => 'required|in:buy',
            'quantity'           => 'required|numeric|gt:0',
            'price_per_unit'     => 'required|numeric|gt:0',
            'transaction_date'   => 'required|date',
            'exchange_rate_pln'  => 'required|numeric|min:0.0001',
        ]);

        $this->persistTransaction($validated);

        return back()->with('status', 'Transakcja zapisana pomyślnie!');
    }

    private function storeAlternativeTransaction(Request $request)
    {
        $validated = $request->validate([
            'portfolio_id'           => 'required|exists:portfolios,id',
            'description'            => 'required|string|max:255',
            'amount'                 => 'required|numeric|gt:0',
            'alternative_quantity'   => 'nullable|numeric|gt:0',
            'asset_currency'         => 'required|string|in:PLN,USD,EUR',
            'transaction_date'       => 'required|date',
            'exchange_rate_pln'      => 'required|numeric|min:0.0001',
            'type'                   => 'required|in:buy',
        ]);

        $description = trim($validated['description']);
        $amount = (float) $validated['amount'];

        $validated['asset_ticker'] = $this->generateAlternativeTicker($description);
        $validated['asset_name'] = $description;
        $validated['asset_type'] = 'alternative';
        $validated['asset_price_source'] = 'manual';

        if (! empty($validated['alternative_quantity'])) {
            // np. samochod: ilosc 1, cena = kwota / ilosc
            $qty = (float) $validated['alternative_quantity'];
            $validated['quantity'] = $qty;
            $validated['price_per_unit'] = $amount / $qty;
        } else {
            // gotowka: brak ilosci – sumowanie kolejnych wplat tego samego opisu
            $validated['quantity'] = $amount;
            $validated['price_per_unit'] = 1;
        }

        $this->persistTransaction($validated);

        return back()->with('status', 'Transakcja alternatywna zapisana pomyślnie!');
    }

    private function persistTransaction(array $validated): void
    {
        $asset = Asset::firstOrNew(['ticker' => $validated['asset_ticker']]);
        $asset->name = $validated['asset_name'];
        $asset->type = $validated['asset_type'];
        $asset->currency = strtoupper($validated['asset_currency']);
        $asset->price_source = $validated['asset_price_source'];
        $asset->save();

        $validated['asset_id'] = $asset->id;

        $this->transactionService->createTransaction($validated);
    }

    private function generateAlternativeTicker(string $description): string
    {
        $slug = strtoupper(Str::slug($description, ''));
        $slug = preg_replace('/[^A-Z0-9]/', '', $slug) ?? '';

        if ($slug === '') {
            $slug = 'ASSET' . substr(md5($description), 0, 6);
        }

        $ticker = 'ALT-' . substr($slug, 0, 24);

        if (Asset::where('ticker', $ticker)->exists()) {
            return $ticker . '-' . substr(md5($description), 0, 4);
        }

        return $ticker;
    }

    // DELETE /transactions/{transaction} (name: transactions.destroy)
    // Transaction $transaction = Route Model Binding

    public function destroy(Transaction $transaction)
    {
        $this->transactionService->deleteTransaction($transaction);

        return back()->with('status', 'Transakcja została usunięta!');
    }
}
