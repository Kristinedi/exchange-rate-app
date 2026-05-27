<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        Currency::insertOrIgnore([
            ['currency_code' => 'EUR', 'currency_name' => 'Euro'],
            ['currency_code' => 'GBP', 'currency_name' => 'Great British Pound'],
            ['currency_code' => 'USD', 'currency_name' => 'United States dollar'],
            ['currency_code' => 'JPY', 'currency_name' => 'Japanese Yen'],
            ['currency_code' => 'CHF', 'currency_name' => 'Swiss Franc'],
            ['currency_code' => 'CNY', 'currency_name' => 'Chinese Yuan'],
            ['currency_code' => 'AUD', 'currency_name' => 'Australian Dollar'],
            ['currency_code' => 'CAD', 'currency_name' => 'Canadian Dollar'],
        ]);
    }
}
