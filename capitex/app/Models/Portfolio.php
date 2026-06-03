<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

// Portfel inwestycyjny jednego usera (tabela portfolios)
// category: broker | exchange | alternative - wplywa na wyszukiwanie aktyw w API (akcje vs krypto)
// cascadeOnDelete: usuniecie usera kasuje jego portfele (migracja portfolios)
// Relacje: Portfolio belongsTo User | Portfolio hasMany Transaction

#[Fillable(['user_id', 'name', 'category'])]
class Portfolio extends Model
{
    // wlasciciel portfela
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // wszystkie buy/transakcje w tym portfelu
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
