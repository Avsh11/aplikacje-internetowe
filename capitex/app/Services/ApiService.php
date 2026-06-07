<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

// Integracja z API: binance (krypto) yahoo finance (akcje/ETF), NBP (kursy)
// Wywolywany z PortfolioController (/api/search, /api/price) i PortfolioService (wycena portfela)
// Przy bledzie zwraca null lub [] – aplikacja nie pada, jest fallback (reczna cena / srednia zakupu)
// Http::withoutVerifying() – dev lokalny; crypto vs stock wybiera PortfolioController po type/source

class ApiService
{
    // Cena krypto z Binance (BTC ==> BTCUSDT) dzisiaj: ticker/price i historia: klines [4] = close

    public function getCryptoPrice(string $ticker, $date = null)
    {
        try {
            $symbol = strtoupper($ticker) . 'USDT';

            if (!$date || Carbon::parse($date)->isToday()) {
                $response = Http::withoutVerifying()->get("https://api.binance.com/api/v3/ticker/price", ['symbol' => $symbol]);
                return $response->successful() ? (float) $response->json()['price'] : null;
            }

            $timestamp = Carbon::parse($date)->timestamp * 1000;
            $response = Http::withoutVerifying()->get("https://api.binance.com/api/v3/klines", [
                'symbol' => $symbol,
                'interval' => '1d',
                'startTime' => $timestamp,
                'limit' => 1,
            ]);

            if ($response->successful() && !empty($response->json())) {
                return (float) $response->json()[0][4];
            }
        } catch (\Exception $e) {
        }

        return null;
    }

    // Cena akcji/ETF z yahoo dzisiaj: regularMarketPrice i historia: ostatni close z tablicy
    // end() a nie reset() – reset bral stara pierwsza wartosc i psul ceny (np. XTB.WA)

    public function getStockPrice(string $ticker, $date = null)
    {
        try {
            $client = Http::withoutVerifying()->withHeaders([
                'User-Agent' => 'Mozilla/5.0',
            ]);

            $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$ticker}";
            $requestedDate = $date ? Carbon::parse($date) : null;

            $params = [];
            if (!$requestedDate || $requestedDate->isToday()) {
                $params = ['interval' => '1m', 'range' => '1d'];
            } else {
                $params = [
                    'period1' => $requestedDate->copy()->startOfDay()->timestamp,
                    'period2' => $requestedDate->copy()->endOfDay()->timestamp,
                    'interval' => '1d',
                ];
            }

            $response = $client->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                $result = $data['chart']['result'][0] ?? null;

                if (!$result) {
                    return null;
                }

                if (!$requestedDate || $requestedDate->isToday()) {
                    $marketPrice = $result['meta']['regularMarketPrice'] ?? null;
                    if (is_numeric($marketPrice)) {
                        return (float) $marketPrice;
                    }
                }

                if (isset($result['indicators']['quote'][0]['close']) && is_array($result['indicators']['quote'][0]['close'])) {
                    $closes = array_values(array_filter(
                        $result['indicators']['quote'][0]['close'],
                        fn ($v) => $v !== null
                    ));

                    if (!empty($closes)) {
                        return (float) end($closes);
                    }
                }
            } else {
                \Log::error("Yahoo API Error for {$ticker}: " . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error("ApiService Exception: " . $e->getMessage());
        }

        return null;
    }

    // Kursy NBP tabela A, PLN = 1.0. PortfolioService przelicza na walute usera
    // Fallback gdy NBP offline – ten sam pomysl co w JS na dashboardzie

    public function getExchangeRates()
    {
        try {
            $response = Http::withoutVerifying()->get("http://api.nbp.pl/api/exchangerates/tables/A/?format=json");
            if ($response->successful()) {
                $rates = [];
                foreach ($response->json()[0]['rates'] as $rate) {
                    $rates[$rate['code']] = (float) $rate['mid'];
                }
                $rates['PLN'] = 1.0;

                return $rates;
            }
        } catch (\Exception $e) {
        }

        return ['USD' => 4.00, 'EUR' => 4.30, 'PLN' => 1.0];
    }

    // Autocomplete akcji/ETF (Yahoo). .WA -> PLN, .DE -> EUR (Yahoo czesto myli walute)

    public function searchStocks(string $query)
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])->get("https://query2.finance.yahoo.com/v1/finance/search", [
                'q' => $query,
                'quotesCount' => 5,
                'newsCount' => 0,
            ]);

            if ($response->successful() && isset($response->json()['quotes'])) {
                $results = [];
                foreach ($response->json()['quotes'] as $quote) {
                    if (in_array($quote['quoteType'], ['EQUITY', 'ETF'])) {
                        $currency = $quote['currency'] ?? 'USD';
                        if (str_ends_with($quote['symbol'], '.WA')) {
                            $currency = 'PLN';
                        } elseif (str_ends_with($quote['symbol'], '.DE')) {
                            $currency = 'EUR';
                        }

                        $results[] = [
                            'ticker' => $quote['symbol'],
                            'name' => $quote['shortname'] ?? $quote['longname'] ?? $quote['symbol'],
                            'type' => strtolower($quote['quoteType']),
                            'currency' => $currency,
                            'price_source' => 'yahoo',
                        ];
                    }
                }

                return $results;
            }
        } catch (\Exception $e) {
        }

        return [];
    }

    // Krypto – user wpisuje ticker recznie, bez prawdziwego search API; cena i tak z Binance

    public function searchCrypto(string $query)
    {
        $query = strtoupper(trim($query));

        return [
            [
                'ticker' => $query,
                'name' => $query . ' Token',
                'type' => 'crypto',
                'currency' => 'USD',
                'price_source' => 'binance',
            ],
        ];
    }
}
