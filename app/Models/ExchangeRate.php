<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

#[Fillable(['from_currency_id', 'to_currency_id', 'rate', 'recorded_at'])]
class ExchangeRate extends Model
{
    const CACHE_KEY_LATEST_RATES = 'latest_exchange_rates';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rate' => 'float',
            'recorded_at' => 'datetime',
        ];
    }

    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    public static function getLatest(): Collection
    {
        return self::whereIn('id',
            self::selectRaw('MAX(id)')
                ->groupBy('from_currency_id', 'to_currency_id')
        )
        ->with(['toCurrency', 'fromCurrency'])
        ->get();
    }
}
