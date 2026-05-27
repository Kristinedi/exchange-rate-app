<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->renameColumn('base_currency', 'base_currency_id');
            $table->renameColumn('currency_code', 'currency_id');
        });
    }

    public function down(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->renameColumn('base_currency_id', 'base_currency');
            $table->renameColumn('currency_id', 'currency_code');
        });
    }
};
