<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->renameColumn('base_currency_id', 'from_currency_id');
            $table->renameColumn('currency_id', 'to_currency_id');
        });
    }

    public function down(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->renameColumn('from_currency_id', 'base_currency_id');
            $table->renameColumn('to_currency_id', 'currency_id');
        });
    }
};
