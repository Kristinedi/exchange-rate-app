<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\ExchangeRatesUpdated;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

#[Signature('exchange-rate:fetch')]
#[Description('Fetch hourly exchange rate data from a source and store it')]
class FetchExchangeRate extends Command
{
    public function handle()
    {
        try {
            retry(3, function() {
                $currencies = Currency::all()->keyBy('currency_code');
                $exchangeRateData = $this->fetchExchangeRatesForCurrency($currencies->keys()->toArray());
                    
                $this->saveCurrencyExchangeRates($exchangeRateData, $currencies);

                Cache::forget(ExchangeRate::CACHE_KEY_LATEST_RATES);

                // $rates = ExchangeRate::getLatest();
                // ExchangeRatesUpdated::dispatch($rates);
                ExchangeRatesUpdated::dispatch();
            }, 1000);

            $this->info('Exchange rates fetched and saved successfully!');

        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error($e);
        }
    }

    private function fetchExchangeRatesForCurrency(array $baseCodes): array
    {
        $exchangeRateData = [];

        foreach ($baseCodes as $code) {
            $response = Http::get('https://api.exchangerate.fun/latest', [
                'base' => $code,
            ]);

            if ($response->failed()) {
            throw new Exception("Failed to fetch data for {$code}");
        }

            $exchangeRateData[$code] = $response->json();
        }

        return $exchangeRateData;
    }

    private function saveCurrencyExchangeRates (array $exchangeRateData, Collection $currencies): void
    {
        $firstEntry = $exchangeRateData[array_key_first($exchangeRateData)];
        $recordedAt = Carbon::createFromTimestamp($firstEntry['timestamp']);

        $records = collect($exchangeRateData)
            ->flatMap(fn(array $data, string $baseCode) => 
                collect($data['rates'])
                    ->filter(fn(float $_rate, string $currencyCode) => 
                        isset($currencies[$currencyCode]) && 
                        $currencyCode !== $baseCode
                    )
                    ->map(fn(float $rate, string $currencyCode) => [
                        'from_currency_id' => $currencies[$baseCode]->id,
                        'to_currency_id'   => $currencies[$currencyCode]->id,
                        'recorded_at'      => $recordedAt,
                        'rate'             => $rate,
                    ])
                    ->values()
            )
            ->toArray();

        ExchangeRate::upsert(
            $records,
            ['from_currency_id', 'to_currency_id', 'recorded_at'],
            ['rate']
        );
    }
}
