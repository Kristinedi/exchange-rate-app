<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['currency_code', 'currency_name'])]
class Currency extends Model
{    
    public $timestamps = false;

    public function exchangeCurrencies(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'to_currency_id');
    }

    public function baseCurrencies(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'from_currency_id');
    }

    public function latestExchangeRate(): HasOne
    {
        return $this->hasOne(ExchangeRate::class, 'to_currency_id')->latestOfMany('recorded_at');
    }
}
