<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

// Pojedyncza operacja buy w portfelu (tabela transactions)
// Laczy portfolio_id + asset_id; koszt w PLN w total_cost_pln (liczy TransactionService)
// Kolumny currency, exchange_rate_pln, total_cost_pln - migracja 2026_05_27_151000
// cascadeOnDelete na portfolio_id - usuniecie portfela kasuje jego transakcje
// DEMO v1: w aplikacji zapisujemy tylko type=buy
// Relacje: Transaction belongsTo Portfolio, belongsTo Asset

#[Fillable([
    'portfolio_id',
    'asset_id',
    'type',
    'quantity',
    'price_per_unit',
    'currency',
    'exchange_rate_pln',
    'total_cost_pln',
    'transaction_date',
])]
class Transaction extends Model
{
    // rzutowanie typow z bazy (decimal -> float w PHP)
    protected $casts = [
        'transaction_date' => 'datetime',
        'quantity' => 'float',
        'price_per_unit' => 'float',
        'exchange_rate_pln' => 'float',
        'total_cost_pln' => 'float',
    ];

    // w ktorym portfelu zostala zrobiona transakcja
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    // jakie aktywo (ticker przez relacje asset)
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
