<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ExchangeRateResource;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;

class ExchangeRateController extends Controller
{
    public function getLatestRates() {
        try {
            $rates = Cache::remember(
                ExchangeRate::CACHE_KEY_LATEST_RATES,
                3600,
                fn() => ExchangeRateResource::collection(ExchangeRate::getLatest())->toJson()
            );

            return response($rates)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getHistoricalRates() {
        $latestTimes = ExchangeRate::select('recorded_at')
            ->distinct()
            ->orderBy('recorded_at', 'asc')
            ->limit(5);

        $rates = ExchangeRate::whereIn('recorded_at', $latestTimes)
            ->orderBy('recorded_at')
            ->get();

        return response()->json($rates);
    }
}
