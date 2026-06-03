<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $transactions = DB::table('transactions')
            ->join('assets', 'assets.id', '=', 'transactions.asset_id')
            ->select(
                'transactions.id',
                'transactions.quantity',
                'transactions.price_per_unit',
                'assets.ticker',
                'assets.currency as asset_currency'
            )
            ->get();

        foreach ($transactions as $transaction) {
            $currency = strtoupper($transaction->asset_currency ?? 'USD');
            if (str_ends_with($transaction->ticker, '.WA')) {
                $currency = 'PLN';
            } elseif (str_ends_with($transaction->ticker, '.DE')) {
                $currency = 'EUR';
            }

            $exchangeRate = match ($currency) {
                'PLN' => 1.0,
                'EUR' => 4.3,
                default => 4.0,
            };

            $totalCostPln = (float) $transaction->quantity * (float) $transaction->price_per_unit * $exchangeRate;

            DB::table('transactions')
                ->where('id', $transaction->id)
                ->update([
                    'currency' => $currency,
                    'exchange_rate_pln' => $exchangeRate,
                    'total_cost_pln' => round($totalCostPln, 4),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Brak cofania - pola zostawiamy po backfillu.
    }
};
