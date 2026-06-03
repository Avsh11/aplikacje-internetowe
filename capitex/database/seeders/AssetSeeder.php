<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        //updateOrCreate zeby bledu nie wyjebalo jak ponownie sie seeder odpali
        Asset::updateOrCreate(
            ['ticker' => 'BTC'],
            ['name' => 'Bitcoin', 'type' => 'crypto', 'currency' => 'USD', 'price_source' => 'binance']
        );

        Asset::updateOrCreate(
            ['ticker' => 'AAPL'],
            ['name' => 'Apple Inc.', 'type' => 'stock', 'currency' => 'USD', 'price_source' => 'twelve_data']
        );

        Asset::updateOrCreate(
            ['ticker' => 'CDR'],
            ['name' => 'CD Projekt', 'type' => 'stock', 'currency' => 'PLN', 'price_source' => 'yahoo']
        );
    }
}