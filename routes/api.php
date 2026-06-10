<?php

use App\Http\Controllers\ExchangeRateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/exchange-rates/latest-exchange-rates', [ExchangeRateController::class, 'getLatestRates']);
Route::get('/exchange-rates/historical-rates', [ExchangeRateController::class, 'getHistoricalRates']);
