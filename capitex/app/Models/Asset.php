<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

// Globalny slownik instrumentow (tabela assets) - wspolny dla wszystkich userow
// ticker unikalny (np. XTB.WA, BTCUSDT) - tworzony/aktualizowany przy zapisie transakcji (firstOrNew)
// type: crypto | stock | etf - decyduje ktore API ceny (Binance vs Yahoo)
// currency: waluta notowania aktywa (PLN dla .WA, USD dla AAPL)
// Relacja: Asset hasMany Transaction

#[Fillable(['ticker', 'name', 'type', 'currency', 'price_source'])]
class Asset extends Model
{
    // to samo aktywo moze byc w wielu transakcjach / u roznych userow
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
