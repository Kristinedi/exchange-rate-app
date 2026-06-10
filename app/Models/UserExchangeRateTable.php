<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['user_id', 'name', 'from_currency_id'])]
class UserExchangeRateTable extends Model
{   
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

     public function toCurrencies(): BelongsToMany
    {
        return $this->belongsToMany(Currency::class, 'user_exchange_rate_table_currencies');
    }   
}
