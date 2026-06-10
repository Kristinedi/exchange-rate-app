<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\UserExchangeRateTableController;

Route::inertia('/', 'Home', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::inertia('/tables', 'Tables')->middleware(['auth'])->name('tables');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::get('/api/user-exchange-rate-tables', [UserExchangeRateTableController::class, 'index']);
    Route::put('/api/user-exchange-rate-tables', [UserExchangeRateTableController::class, 'sync']);
});

require __DIR__.'/settings.php';
